<?php

namespace App\Observers\Transaction\Purchase;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Models\Admin\Setting;
use App\Models\Product\HistoryLogStock;
use App\Models\Product\PriceVariationStore;
use App\Models\Product\Supplier;
use App\Models\Product\Unit;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\ReturnDetail;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Inventory\StockObserver;
use Illuminate\Http\Request;

class PurchaseReturnObserver
{

    protected $stockObserver;
    protected $ledgerObserver;
    protected $ledgerTransactionObserver;
    protected $purchasePaymentObserver;
    protected $receivedProductObserver;

    public function __construct(StockObserver $stockObserver, LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver, PurchasePaymentObserver $purchasePaymentObserver, ReceivedProductObserver $receivedProductObserver)
    {
        $this->stockObserver                = $stockObserver;
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
        $this->purchasePaymentObserver      = $purchasePaymentObserver;
        $this->receivedProductObserver      = $receivedProductObserver;
    }

    public function getData(Request $request)
    {
        $query = Transaction::where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereDate("transaction_date", ">=", $request->start_date)->whereDate("transaction_date", "<=", $request->end_date);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : $q->whereDate("transaction_date", date("Y-m-d"));
            }
        })->where(function ($query) use ($request) {
            return $request->supplier ? $query->whereIn('supplier_id', explode(",", $request->supplier)) : '';
        })->where(function ($query) use ($request) {
            return $request->payment ?  $query->whereIn('payment_status', explode(",",$request->payment)) : '';
        })->where(function ($query) use ($request) {
            return $request->ref ? $query->where('ref_no', 'like', '%' . $request->ref . '%') : '';
        })->where(function ($query) use ($request) {
            return $request->createdby ?  $query->whereIn('created_by', explode(",",$request->createdby)) : '';
        })->where('type', 'purchase_return');

        if ($request->sort == 'date') {
            $query->orderBy('transaction_date', $request->sortby);
        } else if ($request->sort == 'ref_no') {
            $query->orderBy('ref_no', $request->sortby);
        } else if ($request->sort == 'supplier.name') {
            $query->orderBy(Supplier::select('name')->whereColumn('suppliers.id', 'transactions.supplier_id'), $request->sortby);
        } else if ($request->sort == 'final_total') {
            $query->orderBy('final_total', $request->sortby);
        } else if ($request->sort == 'created.name') {
            $query->orderBy(User::select('name')->whereColumn('users.id', 'transactions.created_by'), $request->sortby);
        }

        return $query;
    }

    public function createUpdateInformation(Request $request, $condition, Transaction $purchase,  Transaction $transaction = null)
    {

        if ($condition == 'create') {
            $getTransaction         = Transaction::where("type", "purchase_return")->whereDate("created_at", date("Y-m-d"))->count() + 1;
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

        $data->tax_amount           = $purchase->tax_amount;
        $data->discount_amount      = $request->discount_percent;
        $data->return_parent        = $purchase->id;
        $data->type                 = 'purchase_return';
        $data->payment_status       = 'paid';
        $data->status               = 'final';
        $data->supplier_id          = $purchase->supplier_id;
        $data->created_by           = auth()->user()->id;
        $data->save();

        $purchase->update([
            'payment_status'        => 'due'
        ]);

        return $data;
    }

    public function createReturns(Request $request, Transaction $transaction)
    {

        foreach ($request->items as $d) {

            $purchase       = Purchase::findOrFail($d['id']);
            $unit           = Unit::where("id", $d['unit'])->first();
            $qtyReturn      = $unit ? $d['return_qty'] * $unit->value : $d['return_qty'];
            $poReturn       = $purchase->qty_return + $qtyReturn;

            if ($poReturn >= $purchase->quantity) {
                $minGet                 = $poReturn - $purchase->quantity;
                $poReturn               = $poReturn - $minGet;
                $qtyReturn              = $poReturn;
                $purchase->qty_return   = $poReturn;
            } else {
                $purchase->qty_return   = $poReturn;
            }

            $purchase->save();


            $stock      = $this->stockObserver->createData($purchase->variation, $transaction->transaction->warehouse_id);
            $firstStock = $stock->variation->all_stock->sum('qty_available');
            $endStock   = $stock->variation->all_stock->sum('qty_available') - $qtyReturn;

            $stock->update([
                'qty_available'     => $stock->qty_available - $qtyReturn
            ]);

            $return     =  ReturnDetail::create([
                'transaction_id'        => $transaction->id,
                'purchase_id'           => $purchase->id,
                'return_qty'            => $qtyReturn,
                'unit_id'               => $d['unit'],
                'unit_qty'              => $d['return_qty'],
                'price'                 => $d['price'],
                'tax_total'             => $d['tax_total']
            ]);

            if ($purchase->product->supply_account) {

                $productName    = $purchase->product->name ?? '';
                $varName        = $purchase->variation->name ?? '';

                if ($varName == 'no-name') {
                    $varName    = '';
                }

                $depositTransaction = AccountTransaction::create([
                    'account_id'                    => $purchase->product->supply_account->id,
                    'transaction_id'                => $transaction->id,
                    'created_by'                    => auth()->user()->id,
                    'item_id'                       => $return->id,
                    'amount'                        => $purchase->purchase_price * $return->return_qty,
                    'type'                          => 'credit',
                    'sub_type'                      => 'return_purchase',
                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('return_purchase', $transaction->transaction_date),
                    'operation_date'                => $transaction->transaction_date,
                    'name'                          => 'Return Pembelian ' . $productName . ' ' . $varName
                ]);

                $this->ledgerObserver->updateCashFlowAccount($purchase->product->supply_account);
                $this->ledgerTransactionObserver->logAccountTransaction($depositTransaction);
            }

            $this->historyLogStock($purchase, $return, $firstStock, $endStock, $qtyReturn);

            $toRelocate     = $this->receivedProductObserver->changeOrDeletePurchase($purchase, ($purchase->quantity - $purchase->qty_return));
            $purchaseiD     = $purchase->sell_purchase_first->id ?? null;
            $this->stockObserver->updatePricing($purchase->variation);

            if ($toRelocate['status'] == true) {
                $this->receivedProductObserver->updateSellPurchases($toRelocate['nextPurchases'], $purchaseiD, $purchase->variation->id);
            }
        }
    }

    public function historyLogStock(Purchase $purchase, ReturnDetail $return, Int $firstStock, Int $endStock, Int $qty)
    {
        $history = HistoryLogStock::create([
            'product_id'        => $purchase->product_id,
            'variation_id'      => $purchase->variation_id,
            'type'              => 'return',
            'type_product'      => 'inventory',
            'unit_id'           => $purchase->variation->unit_id ?? null,
            'qty'               => $qty,
            'transaction_id'    => $return->transaction->id,
            'purchase_id'       => $return->id,
            'from'              => $firstStock,
            'to'                => $endStock
        ]);

        if (Setting::first(['stocking_system_type'])->stocking_system_type == 'averaging') {
            if ($history->variation->harga_modal) {
                $history->variation->harga_modal->update([
                    'price'     => averaging_price($history->variation)
                ]);
            } else {
                PriceVariationStore::create([
                    'variation_id'  => $history->variation->id,
                    'price'         => averaging_price($history->variation)
                ]);
            }
        }
    }

    public function subtotalTransactionChange(Transaction $transaction)
    {
        $subtotal                           = $transaction->returndetail()->selectRaw("sum(price * return_qty) as jumlah")->first();
        $discountTotal                      = $transaction->discount_type == 'percent' && $transaction->discount_amount > 0 && $subtotal->jumlah > 0 ? (($transaction->discount_amount / 100) * $subtotal->jumlah) : $transaction->discount_amount;
        $taxFinal                           = $transaction->returndetail()->selectRaw("sum(tax_total * return_qty) as jumlah")->first();

        $transaction->update([
            'tax_final'             => $taxFinal->jumlah,
            'discount_final'        => $discountTotal,
            'total_before_tax'      => (int)$subtotal->jumlah,
            'final_total'           => ((int)$subtotal->jumlah + $transaction->shipping_charges + ($transaction->supplier->tax_default != 'yes' ? $taxFinal->jumlah : 0)) - $discountTotal
        ]);

        $settings = AccountSetting::first(['tax_input']);

        if ($settings) {
            if ($settings->tax_input_account) {

                // If Delete Account Tax
                $dataAccount = $settings->tax_input_account;

                $ppnMasukan = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "tax_input")->first();
                if ($ppnMasukan && $taxFinal->jumlah == 0) {

                    $nextTransaction = AccountTransaction::where("id", ">", $transaction->id)->where("account_id", $ppnMasukan->account_id)->first();

                    $ppnMasukan->forceDelete();

                    if ($nextTransaction) {
                        $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
                    } else {
                        if ($dataAccount) {
                            $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                        }
                    }
                }

                // If Update Account
                if ($ppnMasukan && $taxFinal->jumlah > 0) {

                    $ppnMasukan->update([
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $taxFinal->jumlah,
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($ppnMasukan);
                }

                // If Create New
                if (!$ppnMasukan && $taxFinal->jumlah > 0 && (my_store_detail()->tax_option ?? '') == 'active') {

                    $ppnMasukan = AccountTransaction::create([
                        'account_id'                    => $dataAccount->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $taxFinal->jumlah,
                        'type'                          => 'credit',
                        'sub_type'                      => 'tax_input_return',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('tax_input', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Retur Pembelian - ' . $transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($ppnMasukan);
                }
            }
        }
    }

    public function deleteTransaction(Transaction $transaction)
    {
        foreach ($transaction->returndetail as $return) {
            $this->deleteItem($return, $transaction);
        }

        if ($transaction->transaction_due != null) {

            foreach ($transaction->transaction_due->faktur as $faktur) {
                $this->purchasePaymentObserver->deleteItem($faktur, $faktur->transaction);
            }

            $transaction->transaction_due->payment()->delete();
            $transaction->transaction_due()->delete();
        }

        foreach ($transaction->account_transaction as $account) {

            $dataAccount = $account->account;
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

            $account->forceDelete();

            if ($transactionNext) {
                $this->ledgerTransactionObserver->logAccountUpdate($transactionNext);
            } else {
                if ($dataAccount) {
                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                }
            }
        }

        $parentTransaction  = $transaction->transaction;
        $transaction->forceDelete();

        if ($parentTransaction->transaction_due) {
            if ($parentTransaction->transaction_due->status == 'due') {
                $parentTransaction->transaction_due->update([
                    'amount'            => $parentTransaction->due_total_po,
                    'total_due_amount'  => $parentTransaction->due_total_po,
                ]);
            }
        }
    }

    public function deleteItem(ReturnDetail $return, Transaction $transaction)
    {

        $stocks     = $this->stockObserver->createData($return->purchase->variation, $transaction->transaction->warehouse_id);
        $this->stockObserver->historyLogStock($transaction, $return->purchase->variation_id)->delete();


        $return->purchase->update([
            'qty_return'        => $return->purchase->qty_return - $return->return_qty
        ]);

        if ($stocks) {
            $stocks->update([
                'qty_available'     => ($stocks->qty_available + $return->return_qty)
            ]);
        }

        $toRelocate     = $this->receivedProductObserver->changeOrDeletePurchase($return->purchase, ($return->purchase->quantity - $return->purchase->qty_return));
        $purchaseiD     = $return->purchase->sell_purchase_first->id ?? null;
        $variation      = $return->purchase->variation;
        $return->forceDelete();

        if ($toRelocate['status'] == true) {
            $this->receivedProductObserver->updateSellPurchases($toRelocate['nextPurchases'], $purchaseiD, $variation->id);
        }

        $this->stockObserver->updatePricing($variation);
        $this->subtotalTransactionChange($transaction);
    }
}
