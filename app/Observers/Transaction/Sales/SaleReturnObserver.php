<?php

namespace App\Observers\Transaction\Sales;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Models\Product\Unit;
use App\Models\Transaction\SalesReturn;
use App\Models\Transaction\Sell;
use App\Models\Transaction\SellPurchase;
use App\Models\Transaction\Transaction;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Inventory\StockObserver;
use Illuminate\Http\Request;

class SaleReturnObserver
{
    protected $stockObserver;
    protected $ledgerObserver;
    protected $ledgerTransactionObserver;
    protected $salesPaymentObserver;
    protected $salesObserver;
    protected $shippingProductObserver;


    public function __construct(StockObserver $stockObserver, LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver, SalesPaymentObserver $salesPaymentObserver, ShippingProductObserver $shippingProductObserver, SalesObserver $salesObserver)
    {
        $this->stockObserver                = $stockObserver;
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
        $this->salesPaymentObserver         = $salesPaymentObserver;
        $this->salesObserver                = $salesObserver;
        $this->shippingProductObserver      = $shippingProductObserver;
    }

    public function getData(Request $request, $year = '', $month = '')
    {
        return Transaction::where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereDate("transaction_date", ">=", $request->start_date)->whereDate("transaction_date", "<=", $request->end_date);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : '';
            }
        })->where(function ($query) use ($request) {
            return $request->payment ?  $query->where('payment_status', $request->payment) : '';
        })->where(function ($query) use ($request) {
            return $request->ref ? $query->where('ref_no', 'like', '%' . $request->ref . '%') : '';
        })->where(function ($query) use ($request) {
            return $request->createdby ?  $query->where('created_by', $request->createdby) : '';
        })->where(function ($q) use ($year) {
            return $year != '' ? $q->whereYear("transaction_date", $year) : '';
        })->where(function ($q) use ($month) {
            return $month != '' ? $q->whereMonth("transaction_date", $month) : '';
        })->where('type', 'sales_return')->orderBy("created_at", "desc");
    }


    public function createUpdateInformation(Request $request, $condition, Transaction $sale,  Transaction $transaction = null)
    {

        if ($condition == 'create') {
            $getTransaction         = Transaction::where("type", "sales_return")->whereDate("created_at", date("Y-m-d"))->count() + 1;
            $invoiceNumber          = sprintf("%05d", $getTransaction);
            $refNo                  = Helper::transactionKey('PO_RTN', $invoiceNumber);

            $data                   = new Transaction();
            $data->invoice_no       = $invoiceNumber;
            $data->ref_no           = $refNo;
            $data->transaction_date = date('Y-m-d H:i:s');
        } else {
            $data = Transaction::find($transaction->id);
            $data->transaction_date = date('Y-m-d') . ' ' . $transaction->created_at->format('H:i:s');
        }

        $data->tax_amount           = $sale->tax_amount;
        $data->discount_amount      = $request->discount_percent;
        $data->return_parent        = $sale->id;
        $data->due_limit            = 0;
        $data->type                 = 'sales_return';
        $data->warehouse_id         = $request->warehouse['id'];
        $data->payment_status       = 'paid';
        $data->status               = 'final';
        $data->customer_id          = $sale->customer_id;
        $data->created_by           = auth()->user()->id;
        $data->save();

        $sale->update([
            'payment_status'        => 'due'
        ]);

        return $data;
    }

    public function createReturns(Request $request, Transaction $transaction)
    {
        try {
            $subtotal   = 0;

            foreach ($request->items as $product) {

                $sell           = Sell::find($product['id']);
                $qty            = $product['return_qty'];

                if ($product['unit'] != '' && $product['unit'] != null) {
                    $unit       = Unit::find($product['unit']);
                    if ($unit) {
                        $qty    = $qty * $unit->value;
                    }
                }

                if (($qty + $sell->qty_return) > $sell->qty) {
                    $variationName = $sell->product->variation->name ?? '';

                    if ($variationName == 'no-name') {
                        $variationName = '';
                    }

                    throw new \Exception('Qty Return Produk ' . $sell->product->name . ' ' . $variationName . ', Melebihi Qty yang dapat di return');
                }

                $sell->update([
                    'qty_return'    => $sell->qty_return + $qty
                ]);

                $subtotal += $sell->price_after_tax * $qty;

                $salesReturn = SalesReturn::create([
                    'transaction_id'        => $transaction->id,
                    'sell_id'               => $sell->id,
                    'return_qty'            => $qty,
                    'condition'             => 'good',
                    'unit_id'               => $unit ? $unit->id : null,
                    'unit_qty'              => $product['return_qty'],
                    'goverment_tax'         => $product['goverment_tax'],
                    'service_tax'           => $product['service_tax'],
                    'price'                 => $product['price'],
                    'tax_total'             => $product['tax_total']
                ]);


                // Algoritm For Accountant
                $purchasesReady     = qty_having($sell->product_id, $sell->variation_id);
                $this->shippingProductObserver->salesPurchaseCreate($purchasesReady, ($sell->qty - $sell->qty_return), $sell);

                // Accountant For Return Penjualan
                if ($sell->product->return_sale_account) {
                    $returnAccount = $sell->product->return_sale_account;

                    $returnAccountTransaction    = AccountTransaction::create([
                        'account_id'                    => $returnAccount->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $salesReturn->subtotal - ($transaction->customer->tax_default == 'yes' ? ($product['tax_total'] * $salesReturn->return_qty) : 0),
                        'item_id'                       => $salesReturn->id,
                        'type'                          => 'debit',
                        'sub_type'                      => 'return_sell',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('return_sell', $transaction->transaction_date->format('Y-m-d')),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Retur Penjualan - ' . $salesReturn->transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($returnAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($returnAccountTransaction);
                }

                // Persediaan Account
                if ($sell->product->supply_account) {
                    $accountSupply      = $sell->product->supply_account;
                    $supplyAccount      = AccountTransaction::create([
                        'account_id'                    => $accountSupply->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $sell->sell_purchase->avg('purchase_price') * $salesReturn->return_qty,
                        'item_id'                       => $salesReturn->id,
                        'type'                          => 'debit',
                        'sub_type'                      => 'return_sell',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('return_sell', $transaction->transaction_date->format('Y-m-d')),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Retur Penjualan - ' . $salesReturn->transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($accountSupply);
                    $this->ledgerTransactionObserver->logAccountTransaction($supplyAccount);
                }

                // Beban
                if ($sell->product->cost_account) {
                    $accountCost = $sell->product->cost_account;

                    $costAccount    = AccountTransaction::create([
                        'account_id'                    => $accountCost->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $sell->sell_purchase->avg('purchase_price') * $salesReturn->return_qty,
                        'item_id'                       => $salesReturn->id,
                        'type'                          => 'credit',
                        'sub_type'                      => 'return_sell',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('return_sell', $transaction->transaction_date->format('Y-m-d')),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Retur Penjualan - ' . $salesReturn->transaction->ref_no
                    ]);


                    $this->ledgerObserver->updateCashFlowAccount($accountCost);
                    $this->ledgerTransactionObserver->logAccountTransaction($costAccount);
                }

                if ($sell->product->is_stock == 'yes') {
                    if ($salesReturn->condition == 'good') {

                        $stock          = $this->stockObserver->createData($sell->variation, $transaction->warehouse_id);
                        $firstStock     = $sell->variation->all_stock->sum('qty_available');
                        $endStock       = $sell->variation->all_stock->sum('qty_available') - $qty;

                        $stock->update([
                            'qty_available'     => $stock->qty_available + ($transaction->warehouse_id == $transaction->transaction->warehouse_id ? $qty : 0)
                        ]);

                        if ($transaction->warehouse_id != $transaction->transaction->warehouse_id) {
                            $stock          = $this->stockObserver->createData($sell->variation, $transaction->warehouse_id);
                            if ($stock) {
                                $stock->update([
                                    'qty_available' => $stock->qty_available + $qty
                                ]);
                            }
                        }

                        $this->stockObserver->createHistoryStock('return_sell', $sell, $transaction->id, $qty, $firstStock, $endStock);
                    }
                }

                $this->stockObserver->updatePricing($sell->variation);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function subtotalTransactionChange(Transaction $transaction)
    {
        $subtotal                           = $transaction->sellreturn()->selectRaw("sum(price * return_qty) as jumlah")->first();
        $discountTotal                      = $transaction->discount_type == 'percent' && $transaction->discount_amount > 0 && $subtotal->jumlah > 0 ? (($transaction->discount_amount / 100) * $subtotal->jumlah) : $transaction->discount_amount;
        $taxFinal                           = $transaction->sellreturn()->selectRaw("sum(tax_total * return_qty) as jumlah")->first();
        $govermentTax                       = $transaction->sellreturn()->selectRaw('sum(goverment_tax * return_qty) as jumlah')->first();
        $serviceTax                         = $transaction->sellreturn()->selectRaw('sum(service_tax * return_qty) as jumlah')->first();

        $transaction->update([
            'tax_final'             => $taxFinal->jumlah,
            'discount_final'        => $discountTotal,
            'total_before_tax'      => (int)$subtotal->jumlah,
            'final_total'           => ((int)$subtotal->jumlah + $govermentTax->jumlah + $serviceTax->jumlah + $transaction->shipping_charges +  ($transaction->customer->tax_default != 'yes' ? $taxFinal->jumlah : 0)) - ($discountTotal)
        ]);

        if ($transaction->transaction->commission_contact_id != null && $transaction->transaction->commission_contact_id != '') {

            $totalUseModal = SellPurchase::whereHas('sell', function ($q) use ($transaction) {
                return $q->where("transaction_id", $transaction->transaction->id);
            })->selectRaw('sum(purchase_price * qty) as jumlah')->first();

            $totalPurchaseNull = SellPurchase::whereHas('sell', function ($q) use ($transaction) {
                return $q->where("transaction_id", $transaction->transaction->id);
            })->where("purchase_id", null)->count();

            if ($totalUseModal && $totalPurchaseNull == 0 && $transaction->transaction->commission_user) {

                if ($totalUseModal->jumlah > 0 && $transaction->transaction->commission_user) {
                    if ($transaction->transaction->commission_user->commission_percentase > 0) {
                        $totalProfit        = ($transaction->customer->tax_default == 'yes' ? $transaction->transaction->subtotal_sell_product : $transaction->transaction->subtotal_sell_product_without_tax) - (($totalUseModal != null ? $totalUseModal->jumlah : 0) + $discountTotal);
                        $totalCommission    = ($transaction->transaction->commission_user->commission_percentase / 100) * $totalProfit;

                        if ($totalProfit < 0) {
                            $totalCommission = $totalProfit;
                        }

                        $transaction->transaction->update([
                            'commission_contact_total'      => $totalCommission
                        ]);

                        if ($transaction->transaction->commission) {
                            $transaction->transaction->commission->update([
                                'commission_total'              => $totalCommission
                            ]);
                        }
                    }
                }
            }
        }

        $settings = AccountSetting::first(['tax_output', 'pph_two_two', 'pph_two_tree']);

        if ($settings) {
            if ($settings->tax_output_account) {

                // If Delete Account Tax
                $dataAccount = $settings->tax_output_account;

                $ppnKeluaran = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "tax_output")->where("tax_type", "1")->first();
                if ($ppnKeluaran && $taxFinal->jumlah == 0) {

                    $nextTransaction = AccountTransaction::where("id", ">", $ppnKeluaran->id)->where("account_id", $ppnKeluaran->account_id)->first();

                    $ppnKeluaran->forceDelete();

                    if ($nextTransaction) {
                        $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
                    }

                    if ($dataAccount) {
                        $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    }
                }

                // If Update Account
                if ($ppnKeluaran && $taxFinal->jumlah > 0) {

                    $ppnKeluaran->update([
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $taxFinal->jumlah,
                    ]);

                    $this->ledgerTransactionObserver->logAccountUpdate($ppnKeluaran);
                }

                // If Create New
                if (!$ppnKeluaran && $taxFinal->jumlah > 0 && (my_store_detail()->tax_option ?? '') == 'active') {

                    $ppnKeluaran = AccountTransaction::create([
                        'account_id'                    => $dataAccount->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $taxFinal->jumlah,
                        'type'                          => 'debit',
                        'tax_type'                      => '1',
                        'sub_type'                      => 'tax_output_return',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('tax_output', $transaction->transaction_date),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Retur Penjualan - ' . $transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($ppnKeluaran);
                }
            }

            if ($settings->tax_pph_account) {

                // If Delete Account Tax
                $dataAccount = $settings->tax_pph_account;

                $taxGoverment = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "tax_output")->where("tax_type", "2")->first();
                if ($taxGoverment && $govermentTax->jumlah == 0) {
                    $nextTransaction = AccountTransaction::where("id", ">", $taxGoverment->id)->where("account_id", $taxGoverment->account_id)->first();

                    $taxGoverment->forceDelete();

                    if ($nextTransaction) {
                        $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
                    } else {
                        if ($dataAccount) {
                            $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                        }
                    }
                }

                // If Update Account
                if ($taxGoverment && $govermentTax->jumlah > 0) {

                    $taxGoverment->update([
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $govermentTax->jumlah,
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($taxGoverment);
                }

                // If Create New
                if (!$taxGoverment && $govermentTax->jumlah > 0 && (my_store_detail()->tax_option ?? '') == 'active') {

                    $taxGoverment = AccountTransaction::create([
                        'account_id'                    => $dataAccount->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $govermentTax->jumlah,
                        'type'                          => 'credit',
                        'tax_type'                      => '2',
                        'sub_type'                      => 'tax_output_return',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('goverment_tax', $transaction->transaction_date),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'PPN Keluaran - ' . $transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($taxGoverment);
                }
            }

            // Service Tax
            if ($settings->tax_service_account) {

                // If Delete Account Tax
                $dataAccount = $settings->tax_service_account;

                $taxService = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "tax_output")->where("tax_type", "3")->first();
                if ($taxService && $serviceTax->jumlah == 0) {

                    $nextTransaction = AccountTransaction::where("id", ">", $taxService->id)->where("account_id", $taxService->account_id)->first();

                    $taxService->forceDelete();

                    if ($nextTransaction) {
                        $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
                    } else {
                        if ($dataAccount) {
                            $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                        }
                    }
                }

                // If Update Account
                if ($taxService && $serviceTax->jumlah > 0) {

                    $taxService->update([
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $serviceTax->jumlah,
                    ]);

                    $this->ledgerTransactionObserver->logAccountUpdate($taxService);
                }

                // If Create New
                if (!$taxService && $serviceTax->jumlah > 0 && (my_store_detail()->tax_option ?? '') == 'active') {

                    $taxService = AccountTransaction::create([
                        'account_id'                    => $dataAccount->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $serviceTax->jumlah,
                        'type'                          => 'credit',
                        'tax_type'                      => '3',
                        'sub_type'                      => 'tax_output_return',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('service_tax', $transaction->transaction_date),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'PPN Keluaran - ' . $transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($taxService);
                }
            }
        }
    }

    public function deleteItem(SalesReturn $return, Transaction $transaction)
    {

        if ($return->condition == 'good') {

            if ($return->sell->product->is_stock == 'yes') {
                $stocks     = $this->stockObserver->createData($return->sell->variation, $transaction->warehouse_id);
                $this->stockObserver->historyLogStock($transaction, $return->sell->variation_id)->delete();

                if ($stocks) {
                    $stocks->update([
                        'qty_available'     => ($stocks->qty_available - ($transaction->wareuse_id == $transaction->transaction->warehouse_id ? $return->return_qty : 0))
                    ]);
                }

                if ($transaction->warehouse_id != $transaction->transaction->warehouse_id) {
                    $stock          = $this->stockObserver->createData($return->sell->variation, $transaction->warehouse_id);
                    if ($stock) {
                        $stock->update([
                            'qty_available' => $stock->qty_available - $return->return_qty
                        ]);
                    }
                }
            }
        }

        $return->sell->update([
            'qty_return'    => $return->sell->qty_return - $return->return_qty
        ]);

        foreach ($return->account as $account) {

            $accountData = $account->account;
            $transactionNext    = AccountTransaction::where(function ($query) use ($account) {
                $query->where("operation_date", ">", $account->operation_date)
                    ->orWhere(function ($subQuery) use ($account) {
                        $subQuery->where("operation_date", "=", $account->operation_date)
                            ->where("id", "<", $account->id);
                    });
            })
                ->where("account_id", $account->account_id)
                ->orderBy("operation_date", 'asc')
                ->orderBy("id", 'asc')->first();

            $account->delete();

            if ($transactionNext) {
                $this->ledgerTransactionObserver->logAccountUpdate($transactionNext);
            } else {
                if ($accountData) {
                    $this->ledgerObserver->updateCashFlowAccount($accountData);
                }
            }
        }

        $return->forceDelete();

        $sell               = $return->sell;
        $purchasesReady     = qty_having($sell->product_id, $sell->variation_id);
        $this->shippingProductObserver->salesPurchaseCreate($purchasesReady, ($sell->qty - $sell->qty_return), $sell);


        $this->stockObserver->updatePricing($sell->variation);
        $this->subtotalTransactionChange($transaction);
    }

    public function deleteTransaction(Transaction $transaction)
    {
        foreach ($transaction->sellreturn as $return) {
            $this->deleteItem($return, $transaction);
        }

        if ($transaction->transaction_due != null) {

            foreach ($transaction->transaction_due->faktur as $faktur) {
                $this->salesPaymentObserver->deleteItem($faktur, $faktur->transaction);
            }

            if ($transaction->transaction->transaction_due) {
                if ($transaction->transaction->transaction_due->status == 'due') {
                    $transaction->transaction->transaction_due->update([
                        'amount'    => $transaction->transaction->transaction_due->amount + $transaction->transaction_due->amount,
                        'total_due_amount'  => $transaction->transaction->transaction_due->total_due_amount + $transaction->transaction_due->amount,
                    ]);
                }
            }

            $transaction->transaction_due->payment()->delete();
            $transaction->transaction_due()->delete();
        }

        foreach ($transaction->account_transaction as $account) {

            $accountData = $account->account;
            $transactionNext    = AccountTransaction::where(function ($query) use ($account) {
                $query->where("operation_date", ">", $account->operation_date)
                    ->orWhere(function ($subQuery) use ($account) {
                        $subQuery->where("operation_date", "=", $account->operation_date)
                            ->where("id", "<", $account->id);
                    });
            })
                ->where("account_id", $account->account_id)
                ->orderBy("operation_date", 'asc')
                ->orderBy("id", 'asc')->first();

            $account->delete();

            if ($transactionNext) {
                $this->ledgerTransactionObserver->logAccountUpdate($transactionNext);
            } else {
                if ($accountData) {
                    $this->ledgerObserver->updateCashFlowAccount($accountData);
                }
            }
        }

        $parentTransaction  = $transaction->transaction;
        $transaction->forceDelete();

        $this->salesObserver->changeCommission($parentTransaction);

        if ($parentTransaction->transaction_due) {
            if ($parentTransaction->transaction_due->status == 'due') {
                $parentTransaction->transaction_due->update([
                    'amount'            => $parentTransaction->due_total,
                    'total_due_amount'  => $parentTransaction->due_total,
                ]);
            }
        }
    }
}
