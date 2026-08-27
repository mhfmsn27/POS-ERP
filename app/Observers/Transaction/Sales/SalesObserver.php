<?php

namespace App\Observers\Transaction\Sales;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Models\Admin\Customer;
use App\Models\Product\Unit;
use App\Models\Transaction\FakturPaymentDetail;
use App\Models\Transaction\PaymentMethod;
use App\Models\Transaction\Sell;
use App\Models\Transaction\SellPurchase;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionPayment;
use App\Models\User;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Inventory\StockObserver;
use App\Observers\Transaction\CommissionObserver;
use App\Observers\Transaction\TransactionDueObserver;
use Illuminate\Http\Request;

class SalesObserver
{
    protected $stockObserver;
    protected $ledgerTransactionObserver;
    protected $shippingProductObserver;
    protected $salesPaymentObserver;
    protected $ledgerObserver;
    protected $transactionDueObserver;
    protected $commissionObserver;
    protected $offerObserver;

    public function __construct(StockObserver $stockObserver, LedgerTransactionObserver $ledgerTransactionObserver, SalesPaymentObserver $salesPaymentObserver, ShippingProductObserver $shippingProductObserver, LedgerObserver $ledgerObserver, TransactionDueObserver $transactionDueObserver, CommissionObserver $commissionObserver, OfferObserver $offerObserver)
    {
        $this->stockObserver                    = $stockObserver;
        $this->ledgerTransactionObserver        = $ledgerTransactionObserver;
        $this->salesPaymentObserver             = $salesPaymentObserver;
        $this->shippingProductObserver          = $shippingProductObserver;
        $this->ledgerObserver                   = $ledgerObserver;
        $this->transactionDueObserver           = $transactionDueObserver;
        $this->commissionObserver               = $commissionObserver;
        $this->offerObserver                    = $offerObserver;
    }

    public function getData(Request $request, $userId = null, $year = '', $month = '')
    {
        $query = Transaction::with('customer')->where(function ($query) use ($request) {
            return $request->customer ? $query->whereIn('customer_id', explode(",", $request->customer)) : '';
        })->where(function ($query) use ($request) {
            return $request->payment ?  $query->whereIn('payment_status', explode(",", $request->payment)) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('transaction_date', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : "";
            }
        })->where(function ($query) use ($request) {
            return $request->status ? $query->whereIn('status', explode(",", $request->status)) : '';
        })->where(function ($query) use ($request) {
            return $request->ref ? $query->where('ref_no', 'like', '%' . $request->ref . '%')->orWhere('supplier_ref', 'like', '%' . $request->ref . '%')->orWhere(function ($q) use ($request) {
                $q->whereHas('customer', function ($q) use ($request) {
                    return $request->ref ? $q->where('name', 'like', '%' . $request->ref . '%') : '';
                });
            }) : '';
        })->where(function ($query) use ($request) {
            return $request->createdby ?  $query->whereIn('created_by', explode(",", $request->createdby)) : '';
        })->where(function ($q) use ($userId) {
            return $userId != null ? $q->where("commission_contact_id", $userId) : '';
        })->where(function ($q) use ($year) {
            return $year != '' ? $q->whereYear("transaction_date", $year) : '';
        })->where(function ($q) use ($month) {
            return $month != '' ? $q->whereMonth("transaction_date", $month) : '';
        })->where('type', 'sell');

        if ($request->sort == 'date') {
            $query->orderBy('transaction_date', $request->sortby);
        } else if ($request->sort == 'ref_no') {
            $query->orderBy('ref_no', $request->sortby);
        } else if ($request->sort == 'customer.name') {
            $query->orderBy(Customer::select('name')->whereColumn('customers.id', 'transactions.customer_id'), $request->sortby);
        } else if ($request->sort == 'final_total') {
            $query->orderBy('final_total', $request->sortby);
        } else if ($request->sort == 'created.name') {
            $query->orderBy(User::select('name')->whereColumn('users.id', 'transactions.created_by'), $request->sortby);
        }

        return $query;
    }

    public function getSalesProducts(Request $request, $type = '', $year = '', $month = '')
    {
        return Sell::with("transaction", "product", "variation")->whereHas('transaction', function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('transaction_date', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : '';
            }
        })->where(function ($q) use ($request) {
            return $q->whereHas('product', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('variation', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('transaction', function ($query) use ($request) {
                return $request->name ? $query->where('ref_no', 'like', '%' . $request->name . '%') : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                $request->supplier ? $query->where('supplier_id', $request->supplier) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                return $request->createdby ? $query->where('created_by', $request->createdby) : '';
            });
        })->whereHas('variation', function ($q) use ($request) {
            return $request->variation ? $q->where('variation_id', $request->variation) : '';
        })->whereHas('product', function ($q) use ($type) {
            return $type != '' ? $q->where('is_stock', $type) : '';
        })->whereHas('transaction', function ($q) use ($year) {
            return $year != '' ? $q->whereYear("transaction_date", $year) : '';
        })->whereHas('transaction', function ($q) use ($month) {
            return $month != '' ? $q->whereMonth("transaction_date", $month) : '';
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                return $request->status ? $query->where('status', $request->status) : '';
            });
        })->orderBy("created_at", "desc");
    }

    public function getSellPurchase(Request $request, $year = '', $month = '')
    {
        return SellPurchase::whereHas('sell.transaction', function ($q) use ($year) {
            return $year != '' ? $q->whereYear("transaction_date", $year) : '';
        })->whereHas('sell.transaction', function ($q) use ($month) {
            return $month != '' ? $q->whereMonth("transaction_date", $month) : '';
        })->whereHas('sell.transaction', function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->where('transaction_date', '>=', $request->start_date)
                    ->where('transaction_date', '<=', $request->end_date);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : '';
            }
        });
    }

    public function createUpdateInformation(Request $request, $condition, Transaction $transaction = null)
    {
        $generalInformation = $request->general_information;

        if ($condition == 'create') {
            $getTransaction         = Transaction::where("type", "sell")->whereYear("created_at", date("Y"))->count() + 1;
            $invoiceNumber          = sprintf("%05d", $getTransaction);
            $refNo                  = 'SL' . '' . date("ym") . '/' . $invoiceNumber;

            $data                   = new Transaction();
            $data->invoice_no       = $invoiceNumber;
            $data->ref_no           = $generalInformation['no_ref'] != null ? $generalInformation['no_ref'] : $refNo;
            $data->transaction_date = $generalInformation['date'] ? Helper::setTimeZoneLocal($generalInformation['date']) . ' ' . date('H:i:s') : date('Y-m-d H:i:s');
        } else {
            $data = Transaction::find($transaction->id);
            $data->ref_no           = $generalInformation['no_ref'] != null ? $generalInformation['no_ref'] : $transaction->ref_no;
            $data->transaction_date = $generalInformation['date'] ? Helper::setTimeZoneLocal($generalInformation['date']) . ' ' . $transaction->created_at->format('H:i:s')   : date('Y-m-d') . ' ' . $transaction->created_at->format('H:i:s');
        }

        $data->type                     = 'sell';
        $data->payment_status           = 'due';
        $data->status                   = 'received';
        $data->due_limit                = $generalInformation['due_limit'];
        $data->due_end                  = $generalInformation['due_limit'] > 0 ? now()->addDays($generalInformation['due_limit']) : null;
        $data->commission_contact_id    = !empty($generalInformation['user']['id']) ? ($generalInformation['user']['id'] != null && $generalInformation['user']['id'] != '' ? $generalInformation['user']['id'] : null) : null;
        $data->customer_id              = $generalInformation['customer']['id'];
        $data->commission_contact_type  = 'user';
        $data->warehouse_id             = $generalInformation['warehouse']['id'];
        $data->address                  = $generalInformation['address'] ?? '';
        $data->courier_id               = $generalInformation['courier']['id'];
        $data->created_by               = auth()->user()->id;
        $data->save();


        return $data;
    }

    public function createOrUpdateTransaction(Request $request, Transaction $transaction)
    {

        $discountSales      = 0;
        $listData           = collect($request->items);
        $subtotal           = $listData->sum('subtotal');
        $paymentInformation = $request->payment_information;


        if ($paymentInformation['discount'] > 0) {
            $discountSales   = $paymentInformation['discount_type'] == 'percent' && $paymentInformation['discount'] > 0 && $subtotal > 0 ? (($paymentInformation['discount'] / 100) * $subtotal) : $paymentInformation['discount'];
            $discountSales   = $discountSales / count($request->items);
        }

        foreach ($request->items as $item) {
            $unit           = null;
            $quantity       = $item['qty'];
            $endStock       = 0;
            $discItem       = $discountSales + $item['discount_amount'];
            $itemDiscount   = $discItem > 0 ? $discItem / $quantity : $discItem;
            $taxdiscount    = $transaction->customer->tax_default == 'yes' && $itemDiscount > 0 && $item['tax'] > 0 ? ($itemDiscount / (($item['tax'] / 100) + 1)) : $itemDiscount;

            if ($item['unit']) {
                $unit           = Unit::where("id", $item['unit'])->first();
                if ($unit) {
                    $quantity   = $item['qty'] * $unit->value;
                }
            }

            if ($item['item_id'] != null) {
                $sell   = Sell::find($item['item_id']);
                $sell->update([
                    'item_position'                     => $item['item_position'],
                    'item_name'                         => $item['name'],
                    'transaction_id'                    => $transaction->id,
                    'product_id'                        => $item['product_id'],
                    'variation_id'                      => $item['variation_id'],
                    'unit_qty'                          => $item['qty'],
                    'tax_total'                         => $item['tax'] > 0 && $discItem > 0 ? $item['total_tax'] - (($item['tax'] / 100) * $taxdiscount)  : $item['total_tax'],
                    'item_tax'                          => $item['tax'],
                    'goverment_tax'                     => $item['goverment_tax'],
                    'service_tax'                       => $item['service_tax'],
                    'qty'                               => $quantity,
                    'disc_type'                         => 'fixed',
                    'disc_amount'                       => $item['discount_amount'],
                    'unit_price'                        => $item['unit_price'],
                    'unit_price_before_disc'            => $item['without_discount'],
                    'unit_id'                           => $item['unit'],
                    'discount_subtotal'                 => $discItem
                ]);

                $this->subtotalTransactionChange($transaction);

                // Algoritm Accountant Trigger
                $purchasesReady     = qty_having($sell->product_id, $sell->variation_id);
                $this->shippingProductObserver->salesPurchaseCreate($purchasesReady, ($sell->qty - $sell->qty_return), $sell);

                if ($sell->transaction_received_id != null) {
                    if ($sell->product->is_stock == 'yes') {

                        $stocks     = $this->stockObserver->createData($sell->variation, $transaction->warehouse_id);
                        $history    = $this->stockObserver->historyLogStock($sell->transaction_shipping, $sell->variation_id);
                        $endStock   = $history->from - $sell->qty;

                        if ($stocks) {
                            $stocks->update([
                                'qty_available'     => ($stocks->qty_available + $history->qty) - $sell->qty
                            ]);
                        }

                        $this->ledgerTransactionObserver->updateShippingAccount($sell->transaction_shipping, $sell, $endStock);
                        $this->stockObserver->updateHistoryLog($history, $sell, $sell->qty, $endStock);
                    }

                    $this->shippingProductObserver->subtotalTransactionChange($sell->transaction_shipping);
                } else {

                    if ($sell->product->is_stock == 'yes') {
                        $stocks     = $this->stockObserver->createData($sell->variation, $transaction->warehouse_id);
                        $history    = $this->stockObserver->historyLogStock($sell->transaction, $sell->variation_id);
                        if ($history) {
                            $endStock   = $history->from - $sell->qty;

                            if ($stocks) {
                                $stocks->update([
                                    'qty_available'     => ($stocks->qty_available + $history->qty) - $sell->qty
                                ]);
                            }

                            $this->ledgerTransactionObserver->updateShippingAccount($sell->transaction, $sell, $endStock);
                            $this->stockObserver->updateHistoryLog($history, $sell, $sell->qty, $endStock);
                        } else {
                            $firstStock = $sell->variation->all_stock->sum('qty_available');
                            $endStock   = $sell->variation->all_stock->sum('qty_available') - $sell->qty;

                            if ($stocks) {
                                $stocks->update([
                                    'qty_available'     => $stocks->qty_available - $sell->qty
                                ]);
                            }


                            $this->ledgerTransactionObserver->addShippingAccount($sell, $endStock);
                            $this->stockObserver->createHistoryStock('sell', $sell, $transaction->id, $sell->qty, $firstStock, $endStock);
                        }
                    }
                }

                // Sale Account
                if ($sell->product->sale_account) {
                    $accountSale = $sell->product->sale_account;
                    $saleAccount = AccountTransaction::where("transaction_id", $transaction->id)->where("account_id", $accountSale->id)->where("type", "credit")->where("sub_type", "sale_faktur")->where("item_id", $sell->id)->first();

                    if ($saleAccount) {
                        $saleAccount->update([
                            'operation_date'                => $transaction->transaction_date,
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => ($sell->unit_price - ($sell->transaction->customer->tax_default == 'yes' ? $sell->tax_total : 0)) * $sell->qty,
                            'item_id'                       => $sell->id,
                        ]);

                        $this->ledgerTransactionObserver->logAccountUpdate($saleAccount);
                    } else {
                        $saleAccount    = AccountTransaction::create([
                            'account_id'                    => $accountSale->id,
                            'transaction_id'                => $transaction->id,
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => ($sell->unit_price - ($sell->transaction->customer->tax_default == 'yes' ? $sell->tax_total : 0)) * $sell->qty,
                            'item_id'                       => $sell->id,
                            'type'                          => 'credit',
                            'sub_type'                      => 'sale_faktur',
                            'ref_no'                        => $sell->transaction->ref_no ?? '',
                            'operation_date'                => $transaction->transaction_date,
                            'name'                          => 'Faktur Penjualan - (' . $sell->product->name . ' ' . (float)$sell->qty . ' )'
                        ]);

                        $this->ledgerObserver->updateCashFlowAccount($accountSale);
                        $this->ledgerTransactionObserver->logAccountTransaction($saleAccount);
                    }
                }

                // Cost Account
                if ($sell->product->cost_account) {
                    $accountCost = $sell->product->cost_account;
                    $costAccount = AccountTransaction::where("transaction_id", $transaction->id)->where("account_id", $accountCost->id)->where("type", "debit")->where("sub_type", "sale_faktur")->where("item_id", $sell->id)->first();

                    if ($costAccount) {
                        $costAccount->update([
                            'operation_date'                => $transaction->transaction_date,
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => (float)sell_purchase_total($sell->id),
                            'item_id'                       => $sell->id,
                            'qty_history'                   => $endStock < 0 ?  (abs($endStock) > $sell->qty ? ($sell->qty - ($sell->qty * 2)) : $endStock) : 0,
                        ]);

                        $this->ledgerTransactionObserver->logAccountUpdate($costAccount);
                    } else {
                        $costAccount    = AccountTransaction::create([
                            'account_id'                    => $accountCost->id,
                            'transaction_id'                => $transaction->id,
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => (float)sell_purchase_total($sell->id),
                            'item_id'                       => $sell->id,
                            'type'                          => 'debit',
                            'sub_type'                      => 'sale_faktur',
                            'qty_history'                   => $endStock < 0 ?  (abs($endStock) > $sell->qty ? ($sell->qty - ($sell->qty * 2)) : $endStock) : 0,
                            'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('sale_faktur', $sell->created_at),
                            'operation_date'                => $transaction->transaction_date,
                            'name'                          => 'Faktur Penjualan - (' . $sell->product->name . ' ' . (float)$sell->qty . ' )'
                        ]);

                        $this->ledgerObserver->updateCashFlowAccount($accountCost);
                        $this->ledgerTransactionObserver->logAccountTransaction($costAccount);
                    }
                }

                if ($sell->offer_id != null) {
                    $this->offerObserver->subtotalTransactionChange($sell->transaction_offer);
                }
            } else {

                $sell = Sell::create([
                    'transaction_id'                    => $transaction->id,
                    'item_position'                     => $item['item_position'],
                    'item_name'                         => $item['name'],
                    'product_id'                        => $item['product_id'],
                    'disc_type'                         => 'fixed',
                    'disc_amount'                       => $item['discount_amount'],
                    'variation_id'                      => $item['variation_id'],
                    'tax_total'                         => $item['tax'] > 0 && $discItem > 0 ? $item['total_tax'] - (($item['tax'] / 100) * $taxdiscount)  : $item['total_tax'],
                    'item_tax'                          => $item['tax'],
                    'unit_qty'                          => $item['qty'],
                    'goverment_tax'                     => $item['goverment_tax'],
                    'service_tax'                       => $item['service_tax'],
                    'qty'                               => $quantity,
                    'unit_price'                        => $item['unit_price'],
                    'unit_price_before_disc'            => $item['without_discount'],
                    'unit_id'                           => $item['unit'],
                    'discount_subtotal'                 => $discItem
                ]);

                $this->subtotalTransactionChange($transaction);

                // Algoritm Accountant Trigger
                $purchasesReady     = qty_having($sell->product_id, $sell->variation_id);
                $this->shippingProductObserver->salesPurchaseCreate($purchasesReady, ($sell->qty - $sell->qty_return), $sell);


                $stocks     = $this->stockObserver->createData($sell->variation, $transaction->warehouse_id);
                $firstStock = $sell->variation->all_stock->sum('qty_available');
                $endStock   = $sell->variation->all_stock->sum('qty_available') - $sell->qty;

                if ($stocks) {
                    $stocks->update([
                        'qty_available'     => $stocks->qty_available - $sell->qty
                    ]);
                }


                $this->ledgerTransactionObserver->addShippingAccount($sell, $endStock);
                $this->stockObserver->createHistoryStock('sell', $sell, $transaction->id, $sell->qty, $firstStock, $endStock);

                // Sale Account
                if ($sell->product->sale_account) {
                    $accountSale = $sell->product->sale_account;

                    $saleAccount    = AccountTransaction::create([
                        'account_id'                    => $accountSale->id,
                        'transaction_id'                => $sell->transaction_id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => ($sell->unit_price - ($sell->transaction->customer->tax_default == 'yes' ? $sell->tax_total : 0)) * $sell->qty,
                        'item_id'                       => $sell->id,
                        'type'                          => 'credit',
                        'sub_type'                      => 'sale_faktur',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('sale_faktur', $sell->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Faktur Penjualan - (' . $sell->product->name . ' ' . (float)$sell->qty . ' )'
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($accountSale);
                    $this->ledgerTransactionObserver->logAccountTransaction($saleAccount);
                }

                // Cost Account
                if ($sell->product->cost_account) {
                    $accountCost = $sell->product->cost_account;

                    $costAccount    = AccountTransaction::create([
                        'account_id'                    => $accountCost->id,
                        'transaction_id'                => $sell->transaction_id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => (float)sell_purchase_total($sell->id),
                        'item_id'                       => $sell->id,
                        'type'                          => 'debit',
                        'sub_type'                      => 'sale_faktur',
                        'qty_history'                   => $endStock < 0 ?  (abs($endStock) > $sell->qty ? ($sell->qty - ($sell->qty * 2)) : $endStock) : 0,
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('sale_faktur', $sell->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Faktur Penjualan - (' . $sell->product->name . ' ' . (float)$sell->qty . ' )'
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($accountCost);
                    $this->ledgerTransactionObserver->logAccountTransaction($costAccount);
                }
            }

            $this->stockObserver->updatePricing($sell->variation);
        }
    }

    public function deleteItem(Sell $sell, Transaction $transaction)
    {

        $saleAccount    = $sell->sale_account_item;

        foreach ($saleAccount as $account) {
            if ($sell->transaction_received_id != null) {
                if ($account->sub_type != 'sent_product_to_customer') {
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
                        $this->ledgerObserver->updateCashFlowAccount($accountData);
                    }
                }
            } else {
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
                    $this->ledgerObserver->updateCashFlowAccount($accountData);
                }
            }
        }

        if ($sell->transaction_received_id != null) {
            $sell->update([
                'transaction_id'    => null,
                'tax_total'         => 0,
                'item_tax'          => 0,
                'goverment_tax'     => 0,
                'service_tax'       => 0,
                'disc_amount'       => 0,
                'discount_subtotal' => 0
            ]);

            if ($sell->transaction_shipping) {
                $sell->transaction_shipping->update([
                    'status'        => 'shipping_not_use'
                ]);
            }
        } else {

            $stocks     = $this->stockObserver->createData($sell->variation,  $transaction->warehouse_id);
            $history    = $this->stockObserver->historyLogStock($sell->transaction, $sell->variation_id);

            if ($history) {
                $history->delete();
            }

            if ($stocks) {
                $stocks->update([
                    'qty_available'     => ($stocks->qty_available + $sell->qty)
                ]);
            }

            $this->ledgerTransactionObserver->deleteShippingAccount($sell);
            $this->shippingProductObserver->salesPurchaseCreate([], 0, $sell);
            $variation      = $sell->variation;

            if ($sell->offer_id != null) {
                $sell->update([
                    'transaction_id'    => null,
                    'tax_total'         => 0,
                    'item_tax'          => 0,
                    'goverment_tax'     => 0,
                    'service_tax'       => 0,
                    'disc_amount'       => 0,
                    'discount_subtotal' => 0
                ]);

                if ($sell->offer_id != null) {
                    $this->offerObserver->subtotalTransactionChange($sell->transaction_offer);
                }
            } else {
                $sell->forceDelete();
            }

            $this->stockObserver->updatePricing($variation);
            $this->subtotalTransactionChange($transaction);
        }
    }

    public function subtotalTransactionChange(Transaction $transaction)
    {
        $subtotal                           = $transaction->sell()->selectRaw("sum(unit_price * qty) as jumlah, sum(disc_amount * qty) as discsell")->first();
        $discountTotal                      = $transaction->discount_type == 'percent' && $transaction->discount_amount > 0 && $subtotal->jumlah > 0 ? (($transaction->discount_amount / 100) * $subtotal->jumlah) : $transaction->discount_amount;
        $taxFinal                           = $transaction->sell()->selectRaw('sum(tax_total * qty) as jumlah')->first();
        $govermentTax                       = $transaction->sell()->selectRaw('sum(goverment_tax * qty) as jumlah')->first();
        $serviceTax                         = $transaction->sell()->selectRaw('sum(service_tax * qty) as jumlah')->first();

        $transaction->update([
            'tax_final'             => $taxFinal->jumlah,
            'goverment_tax'         => $govermentTax->jumlah,
            'service_tax'           => $serviceTax->jumlah,
            'discount_final'        => $discountTotal + $subtotal->discsell,
            'total_before_tax'      => (int)$subtotal->jumlah,
            'final_total'           => ((int)$subtotal->jumlah + $transaction->shipping_charges + ($transaction->customer->tax_default == 'yes' ? 0 : $taxFinal->jumlah)) - ($discountTotal + $govermentTax->jumlah + $serviceTax->jumlah)
        ]);



        // Account for beban pengiriman
        $settings = AccountSetting::first(['cost_shipping_transaction', 'discount_sale', 'tax_output', 'pph_two_two', 'pph_two_tree']);

        if ($transaction->commission_user) {

            $totalUseModal = SellPurchase::whereHas('sell', function ($q) use ($transaction) {
                return $q->where("transaction_id", $transaction->id);
            })->selectRaw('sum(purchase_price * qty) as jumlah')->first();

            $totalPurchaseNull = SellPurchase::whereHas('sell', function ($q) use ($transaction) {
                return $q->where("transaction_id", $transaction->id);
            })->where("purchase_id", null)->count();

            if ($totalUseModal && $totalPurchaseNull == 0 && $transaction->commission_user) {

                if ($totalUseModal->jumlah > 0 && $transaction->commission_user->commission_percentase > 0) {
                    $totalProfit        = ($transaction->customer->tax_default == 'yes' ? $transaction->subtotal_sell_product : $transaction->subtotal_sell_product_without_tax)  - (($totalUseModal != null ? $totalUseModal->jumlah : 0) + $discountTotal);
                    $totalCommission    = ($transaction->commission_user->commission_percentase / 100) * $totalProfit;

                    if ($totalProfit < 0) {
                        $totalCommission = $totalProfit;
                    }

                    $transaction->update([
                        'commission_contact_total'      => $totalCommission
                    ]);


                    if ($transaction->commission) {
                        $transaction->commission->update([
                            'commission_total'              => $totalCommission
                        ]);
                    }
                }
            }
        }

        if ($settings) {
            if ($settings->transaction_shipping_account) {

                $dataAccount = $settings->transaction_shipping_account;

                $accountShippingTransaction = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "sell_shipping")->first();

                if ($accountShippingTransaction && $transaction->shipping_charges == 0) {
                    $accountShippingTransactionNext = AccountTransaction::where(function ($query) use ($accountShippingTransaction) {
                        $query->where("operation_date", ">", $accountShippingTransaction->operation_date)
                            ->orWhere(function ($subQuery) use ($accountShippingTransaction) {
                                $subQuery->where("operation_date", "=", $accountShippingTransaction->operation_date)
                                    ->where("id", "<", $accountShippingTransaction->id);
                            });
                    })
                        ->where("account_id", $accountShippingTransaction->account_id)
                        ->orderBy("operation_date", 'asc')
                        ->orderBy("id", 'asc')->first();

                    $accountShippingTransaction->forceDelete();

                    if ($accountShippingTransactionNext) {
                        $this->ledgerTransactionObserver->logAccountUpdate($accountShippingTransactionNext);
                    } else {
                        if ($dataAccount) {
                            $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                        }
                    }
                }

                if ($accountShippingTransaction && $transaction->shipping_charges > 0) {

                    $accountShippingTransaction->update([
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $transaction->shipping_charges,
                    ]);

                    $this->ledgerTransactionObserver->logAccountUpdate($accountShippingTransaction);
                }

                if (!$accountShippingTransaction && $transaction->shipping_charges > 0) {

                    $accountShippingTransaction = AccountTransaction::create([
                        'account_id'                    => $dataAccount->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $transaction->shipping_charges,
                        'type'                          => 'credit',
                        'sub_type'                      => 'sell_shipping',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('sale_faktur', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Faktur Penjualan - ' . $transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($accountShippingTransaction);
                }
            }

            if ($settings->discount_account) {
                $saleDiscount           = $settings->discount_account;
                $discountTotal          = $transaction->discount_final;
                $discountTransaction    = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "sell_discount")->first();

                if ($discountTransaction && $discountTotal == 0) {
                    $discountTransactionNext = AccountTransaction::where(function ($query) use ($discountTransaction) {
                        $query->where("operation_date", ">", $discountTransaction->operation_date)
                            ->orWhere(function ($subQuery) use ($discountTransaction) {
                                $subQuery->where("operation_date", "=", $discountTransaction->operation_date)
                                    ->where("id", "<", $discountTransaction->id);
                            });
                    })
                        ->where("account_id", $discountTransaction->account_id)
                        ->orderBy("operation_date", 'asc')
                        ->orderBy("id", 'asc')->first();

                    $discountTransaction->forceDelete();

                    if ($discountTransactionNext) {
                        $this->ledgerTransactionObserver->logAccountUpdate($discountTransactionNext);
                    } else {
                        if ($saleDiscount) {
                            $this->ledgerObserver->updateCashFlowAccount($saleDiscount);
                        }
                    }
                }

                if ($discountTransaction && $discountTotal > 0) {

                    $discountTransaction->update([
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $discountTotal,
                    ]);

                    $this->ledgerTransactionObserver->logAccountUpdate($discountTransaction);
                }

                if (!$discountTransaction && $discountTotal > 0) {

                    $discountTransaction = AccountTransaction::create([
                        'account_id'                    => $saleDiscount->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $discountTotal,
                        'type'                          => 'debit',
                        'sub_type'                      => 'sell_discount',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('sell_discount', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Diskon Penjualan - ' . $transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($saleDiscount);
                    $this->ledgerTransactionObserver->logAccountTransaction($discountTransaction);
                }
            }

            if ($settings->tax_output_account) {

                // If Delete Account Tax
                $dataAccount = $settings->tax_output_account;

                $ppnKeluaran = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "tax_output")->where("tax_type", "1")->first();
                if ($ppnKeluaran && $taxFinal->jumlah == 0) {

                    $ppnKeluaranNext = AccountTransaction::where(function ($query) use ($ppnKeluaran) {
                        $query->where("operation_date", ">", $ppnKeluaran->operation_date)
                            ->orWhere(function ($subQuery) use ($ppnKeluaran) {
                                $subQuery->where("operation_date", "=", $ppnKeluaran->operation_date)
                                    ->where("id", "<", $ppnKeluaran->id);
                            });
                    })
                        ->where("account_id", $ppnKeluaran->account_id)
                        ->orderBy("operation_date", 'asc')
                        ->orderBy("id", 'asc')->first();

                    $ppnKeluaran->forceDelete();

                    if ($ppnKeluaranNext) {
                        $this->ledgerTransactionObserver->logAccountUpdate($ppnKeluaranNext);
                    } else {
                        if ($dataAccount) {
                            $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                        }
                    }
                }

                // If Update Account
                if ($ppnKeluaran && $taxFinal->jumlah > 0) {

                    $ppnKeluaran->update([
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $taxFinal->jumlah,
                        'tax_gunggung'                  => $transaction->customer->npwp == null || $transaction->customer->npwp == '' ? 'yes' : 'no'
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
                        'tax_type'                      => '1',
                        'type'                          => 'credit',
                        'sub_type'                      => 'tax_output',
                        'tax_gunggung'                  => $transaction->customer->npwp == null || $transaction->customer->npwp == '' ? 'yes' : 'no',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('tax_output', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'PPN Keluaran - ' . $transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($ppnKeluaran);
                }
            }

            // Goverment Tax
            if ($settings->tax_pph_account) {

                // If Delete Account Tax
                $dataAccount = $settings->tax_pph_account;

                $taxGoverment = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "tax_output")->where("tax_type", "2")->first();
                if ($taxGoverment && $govermentTax->jumlah == 0) {
                    $taxGovermentNext = AccountTransaction::where(function ($query) use ($taxGoverment) {
                        $query->where("operation_date", ">", $taxGoverment->operation_date)
                            ->orWhere(function ($subQuery) use ($taxGoverment) {
                                $subQuery->where("operation_date", "=", $taxGoverment->operation_date)
                                    ->where("id", "<", $taxGoverment->id);
                            });
                    })
                        ->where("account_id", $taxGoverment->account_id)
                        ->orderBy("operation_date", 'asc')
                        ->orderBy("id", 'asc')->first();

                    $taxGoverment->forceDelete();

                    if ($taxGovermentNext) {
                        $this->ledgerTransactionObserver->logAccountUpdate($taxGovermentNext);
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
                        'type'                          => 'debit',
                        'sub_type'                      => 'tax_output',
                        'tax_type'                      => '2',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('goverment_tax', $transaction->created_at),
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
                    $taxServiceNext = AccountTransaction::where(function ($query) use ($taxService) {
                        $query->where("operation_date", ">", $taxService->operation_date)
                            ->orWhere(function ($subQuery) use ($taxService) {
                                $subQuery->where("operation_date", "=", $taxService->operation_date)
                                    ->where("id", "<", $taxService->id);
                            });
                    })
                        ->where("account_id", $taxService->account_id)
                        ->orderBy("operation_date", 'asc')
                        ->orderBy("id", 'asc')->first();
                    $taxService->forceDelete();

                    if ($taxServiceNext) {
                        $this->ledgerTransactionObserver->logAccountUpdate($taxServiceNext);
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

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($taxService);
                }

                // If Create New
                if (!$taxService && $serviceTax->jumlah > 0 && (my_store_detail()->tax_option ?? '') == 'active') {

                    $taxService = AccountTransaction::create([
                        'account_id'                    => $dataAccount->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $serviceTax->jumlah,
                        'type'                          => 'debit',
                        'tax_type'                      => '3',
                        'sub_type'                      => 'tax_output',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('service_tax', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'PPN Keluaran - ' . $transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($taxService);
                }
            }
        }
    }

    public function updateOtherInformation(Request $request, Transaction $transaction)
    {

        $paymentInformation = $request->payment_information;
        $transaction->update([
            'tax_amount'        => $paymentInformation['tax'],
            'discount_amount'   => $paymentInformation['discount'],
            'discount_type'     => $paymentInformation['discount_type'],
            'shipping_charges'  => $paymentInformation['shipping_cost'],
            'additional_notes'  => $paymentInformation['note']
        ]);

        $this->subtotalTransactionChange($transaction);
    }

    public function deleteTransaction(Transaction $transaction)
    {

        foreach ($transaction->sell as $sell) {
            $this->deleteItem($sell, $transaction);
        }

        if ($transaction->transaction_due != null) {

            foreach ($transaction->transaction_due->faktur as $faktur) {
                $this->salesPaymentObserver->deleteItem($faktur, $faktur->transaction);
            }

            $transaction->transaction_due->payment()->delete();
            $transaction->transaction_due()->delete();
        }

        foreach ($transaction->account_transaction as $account) {



            $accountData        = $account->account;
            // $transactionNext    = AccountTransaction::where("operation_date", ">", $account->operation_date)->where("account_id", $account->account_id)->first();
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
                $this->ledgerObserver->updateCashFlowAccount($accountData);
            }
        }

        $transaction->forceDelete();
    }

    public function createByTransaction(Request $request, Transaction $transaction)
    {
        $paymentInformation     = $request->payment_information;

        $getTransaction         = Transaction::where("type", "sales_payment")->whereDate("created_at", date("Y-m-d"))->count() + 1;
        $invoiceNumber          = sprintf("%05d", $getTransaction);
        $refNo                  = Helper::transactionKey('PSL', $invoiceNumber);

        $data                   = new Transaction();
        $data->invoice_no       = $invoiceNumber;
        $data->ref_no           = $refNo;
        $data->transaction_date = Helper::setTimeZoneLocal($paymentInformation['date']) . ' ' . date('H:i:s');

        $data->type                 = 'sales_payment';
        $data->payment_status       = 'due';
        $data->status               = 'final';
        $data->customer_id          = $transaction->customer_id;
        $data->method_id            = $paymentInformation['method']['id'];
        $data->created_by           = auth()->user()->id;
        $data->save();


        $dueDetail      = $this->transactionDueObserver->getByTransaction($transaction->id);

        $totalPay       = $paymentInformation['pay_total'];
        $allocatedQty   = min($totalPay, $dueDetail->total_due);
        $method         = PaymentMethod::select('id', 'name')->where("id", $paymentInformation['method']['id'])->first();

        $data->update([
            'final_total'       => $allocatedQty
        ]);

        if ($allocatedQty > 0) {
            $fakturDetail =  FakturPaymentDetail::create([
                'transaction_id'        => $data->id,
                'transaction_due_id'    => $dueDetail->id,
                'pay_amount'            => $allocatedQty,
            ]);

            $payments       = TransactionPayment::create([
                'method'                => $method->name,
                'transaction_id'        => $transaction->id,
                'payment_method_id'     => $method->id,
                'amount'                => $allocatedQty,
                'created_by'            => auth()->user()->id,
                'transaction_type'      => 'transaction',
                'account_id'            => $method->account->id ?? null,
                'transaction_due_id'    => $dueDetail->id,
                'date'                  => $paymentInformation['date'],
                'faktur_detail_id'      => $fakturDetail->id,
                'note'                  => "",
            ]);

            $type           = 'debit';
            $subType        = 'pay_customer_faktur';

            $this->ledgerTransactionObserver->createPaymentFaktur($payments, $type, $subType);

            $dueDetail->update([
                'total_due_amount'  => $dueDetail->total_due,
                'status'            => $dueDetail->total_due < 1 ? 'paid' : 'due'
            ]);

            if ($dueDetail->total_due < 1) {
                $this->commissionObserver->createData($transaction);
            }

            if ($dueDetail->transaction) {
                $dueDetail->transaction->update([
                    'payment_status'        => $dueDetail->transaction->due_total < 1 ? 'paid' : 'due'
                ]);
            }
        }
    }

    public function changeCommission(Transaction $transaction)
    {

        $subtotal                           = $transaction->sell()->selectRaw("sum(unit_price * qty) as jumlah")->first();
        $discountTotal                      = $transaction->discount_type == 'percent' && $transaction->discount_amount > 0 && $subtotal->jumlah > 0 ? (($transaction->discount_amount / 100) * $subtotal->jumlah) : $transaction->discount_amount;

        $totalUseModal = SellPurchase::whereHas('sell', function ($q) use ($transaction) {
            return $q->where("transaction_id", $transaction->id);
        })->selectRaw('sum(purchase_price * qty) as jumlah')->first();

        $totalPurchaseNull = SellPurchase::whereHas('sell', function ($q) use ($transaction) {
            return $q->where("transaction_id", $transaction->id);
        })->where("purchase_id", null)->count();

        if ($totalUseModal && $totalPurchaseNull == 0 && $transaction->commission_user) {

            if ($totalUseModal->jumlah > 0 && $transaction->commission_user->commission_percentase > 0) {
                $totalProfit        = $transaction->subtotal_sell_product - (($totalUseModal != null ? $totalUseModal->jumlah : 0) + $discountTotal);
                $totalCommission    = ($transaction->commission_user->commission_percentase / 100) * $totalProfit;

                if ($totalProfit < 0) {
                    $totalCommission = $totalProfit;
                }

                $transaction->update([
                    'commission_contact_total'      => $totalCommission
                ]);

                if ($transaction->commission) {
                    $transaction->commission->update([
                        'commission_total'              => $totalCommission
                    ]);
                }
            }
        }
    }
}
