<?php

namespace App\Observers\Transaction\Sales;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\Customer;
use App\Models\Product\Unit;
use App\Models\Transaction\Sell;
use App\Models\Transaction\SellPurchase;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Inventory\StockObserver;
use Illuminate\Http\Request;

class ShippingProductObserver
{
    protected $stockObserver;
    protected $ledgerTransactionObserver;
    protected $ledgerObserver;
    protected $offerObserver;

    public function __construct(StockObserver $stockObserver, LedgerTransactionObserver $ledgerTransactionObserver, LedgerObserver $ledgerObserver, OfferObserver $offerObserver)
    {
        $this->stockObserver                = $stockObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
        $this->ledgerObserver               = $ledgerObserver;
        $this->offerObserver                = $offerObserver;
    }

    public function getData(Request $request)
    {
        $query = Transaction::with('customer')->where(function ($query) use ($request) {
            return $request->customer ? $query->whereIn('customer_id', explode(",", $request->customer)) : '';
        })->where(function ($query) use ($request) {
            return $request->status ?  $query->whereIn('status', explode(",", $request->status)) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('transaction_date', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : '';
            }
        })->where(function ($query) use ($request) {
            return $request->ref ? $query->where('ref_no', 'like', '%' . $request->ref . '%')->orWhere('supplier_ref', 'like', '%' . $request->ref . '%')->orWhere(function ($q) use ($request) {
                $q->whereHas('customer', function ($q) use ($request) {
                    return $request->ref ? $q->where('name', 'like', '%' . $request->ref . '%') : '';
                });
            }) : '';
        })->where(function ($query) use ($request) {
            return $request->createdby ?  $query->whereIn('created_by', explode(",", $request->createdby)) : '';
        })->where(function ($query) use ($request) {
            return $request->warehouse ?  $query->where('warehouse_id', $request->warehouse) : ($request->with_warehouse == 'yes' ? $query->where("warehouse_id", null)->orWhere("warehouse_id", "") : '');
        })->where('type', 'shipping_product');

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

    public function createUpdateInformation(Request $request, $condition, Transaction $transaction = null)
    {
        if ($condition == 'create') {
            $getTransaction         = Transaction::where("type", "shipping_product")->whereDate("created_at", date("Y-m-d"))->count() + 1;
            $invoiceNumber          = sprintf("%05d", $getTransaction);
            $refNo                  = Helper::transactionKey('RCP', $invoiceNumber);

            $data                   = new Transaction();
            $data->invoice_no       = $invoiceNumber;
            $data->status           = 'shipping_not_use';
            $data->ref_no           = $request->no_ref != null ? $request->no_ref : $refNo;
            $data->old_warehouse_id = $request->warehouse['id'];
            $data->transaction_date = $request->date ? Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s') : date('Y-m-d H:i:s');
        } else {
            $data                   = $transaction;
            $data->ref_no           = $request->no_ref != null ? $request->no_ref : $transaction->ref_no;
            $data->transaction_date = $request->date ? Helper::setTimeZoneLocal($request->date) . ' ' . $transaction->created_at->format("H:i:s") : date('Y-m-d') . ' ' . $transaction->created_at->format('H:i:s');
            $data->old_warehouse_id = $data->warehouse_id;
        }


        $data->customer_id          = $request->customer['id'];
        $data->warehouse_id         = $request->warehouse['id'];
        $data->address              = $request->address ?? '';
        $data->courier_id           = $request->courier['id'];
        $data->type                 = 'shipping_product';
        $data->created_by           = auth()->user()->id;
        $data->additional_notes     = $request->note;
        $data->save();

        return $data;
    }

    public function savingItems(Request $request, Transaction $transaction)
    {
        $listData = array();
        foreach ($request->items as $d) {
            $sell = Sell::find($d['id']);

            if ($sell) {
                $this->updateItems($d, $sell, $transaction);
            } else {
                $this->createItems($d, $transaction);
            }
        }

        return $listData;
    }

    public function createItems($d, Transaction $transaction)
    {
        $unit       = null;
        $quantity   = $d['qty'];

        if ($d['unit']) {
            $unit           = Unit::where("id", $d['unit'])->first();
            if ($unit) {
                $quantity   = $d['qty'] * $unit->value;
            }
        }

        $sell = Sell::create([
            'item_position'                     => $d['item_position'],
            'item_name'                         => $d['name'],
            'transaction_received_id'           => $transaction->id,
            'product_id'                        => $d['product_id'],
            'variation_id'                      => $d['variation_id'],
            'unit_qty'                          => $d['qty'],
            'qty'                               => $quantity,
            'unit_price'                        => $d['without_discount'],
            'unit_price_before_disc'            => $d['without_discount'],
            'unit_id'                           => $d['unit'],
        ]);

        if ($sell->product->is_stock == 'yes') {

            $stocks     = $this->stockObserver->createData($sell->variation, $transaction->warehouse_id);
            $firstStock = $sell->variation->all_stock->sum('qty_available');
            $endStock   = $firstStock - $sell->qty;

            if ($stocks) {
                $stocks->update([
                    'qty_available'     => $stocks->qty_available - $sell->qty
                ]);
            }

            // Algoritm Accountant Trigger
            $purchasesReady     = qty_having($sell->product_id, $sell->variation_id);
            $this->salesPurchaseCreate($purchasesReady, $sell->qty, $sell);
            $this->stockObserver->createHistoryStock('shipping_product', $sell, $transaction->id, $sell->qty, $firstStock, $endStock);

            $this->ledgerTransactionObserver->addShippingAccount($sell);
            $this->stockObserver->updatePricing($sell->variation);
        }

        $this->subtotalTransactionChange($transaction);
    }

    public function subtotalTransactionChange(Transaction $transaction)
    {
        $subtotal                           = $transaction->sale_shipping()->selectRaw("sum(unit_price * qty) as jumlah")->first();
        $discountTotal                      = $transaction->discount_type == 'percent' && $transaction->discount_amount > 0 && $subtotal->jumlah > 0 ? (($transaction->discount_amount / 100) * $subtotal->jumlah) : $transaction->discount_amount;
        $taxFinal                           = $transaction->sale_shipping()->selectRaw('sum(tax_total * qty) as jumlah')->first();
        $govermentTax                       = $transaction->sale_shipping()->selectRaw('sum(goverment_tax * qty) as jumlah')->first();
        $serviceTax                         = $transaction->sale_shipping()->selectRaw('sum(service_tax * qty) as jumlah')->first();

        $transaction->update([
            'tax_final'             => $taxFinal->jumlah,
            'goverment_tax'         => $govermentTax->jumlah,
            'service_tax'           => $serviceTax->jumlah,
            'discount_final'        => $discountTotal,
            'total_before_tax'      => (int)$subtotal->jumlah,
            'final_total'           => ((int)$subtotal->jumlah + $transaction->shipping_charges + ($transaction->customer->tax_default == 'yes' ? 0 : $taxFinal->jumlah)) - ($discountTotal + $govermentTax->jumlah + $serviceTax->jumlah)
        ]);

        if ($transaction->sale_shipping()->where("transaction_id", "!=", null)->count() == $transaction->sale_shipping()->count()) {
            $transaction->update([
                'status'        => 'shipping_used'
            ]);
        } else {
            $transaction->update([
                'status'        => 'shipping_not_use'
            ]);
        }
    }

    public function updateItems($request, Sell $sell, Transaction $transaction)
    {

        $unit           = null;
        $quantity       = $request['qty'];

        if ($request['unit']) {
            $unit           = Unit::where("id", $request['unit'])->first();
            if ($unit) {
                $quantity   = $request['qty'] * $unit->value;
            }
        }

        $sell->update([
            'transaction_received_id'           => $transaction->id,
            'item_position'                     => $request['item_position'],
            'item_name'                         => $request['name'],
            'unit_qty'                          => $request['qty'],
            'qty'                               => $quantity,
            'unit_id'                           => $unit != null ? $unit->id : null,
            'unit_price'                        => $request['without_discount'],
            'unit_price_before_disc'            => $request['without_discount'],
        ]);

        if ($sell->product->is_stock == 'yes') {

            $stocks     = $this->stockObserver->createData($sell->variation, $transaction->warehouse_id);
            $history    = $this->stockObserver->historyLogStock($transaction, $sell->variation_id);
            if ($history) {
                $endStock   = $history->from + $sell->qty;

                if ($stocks) {
                    $stocks->update([
                        'qty_available'     => ($stocks->qty_available + ($transaction->warehouse_id == $transaction->old_warehouse_id ? $history->qty : 0)) - $sell->qty
                    ]);
                }

                if ($transaction->warehouse_id != $transaction->old_warehouse_id) {
                    $stocks     = $this->stockObserver->createData($sell->variation, $transaction->old_warehouse_id);
                    if ($stocks) {
                        $stocks->update([
                            'qty_available' => $stocks->qty_available + $history->qty
                        ]);
                    }
                }

                $purchasesReady     = qty_having($sell->product_id, $sell->variation_id);
                $this->salesPurchaseCreate($purchasesReady, ($sell->qty - $sell->qty_return), $sell);

                $this->ledgerTransactionObserver->updateShippingAccount($sell->transaction_shipping, $sell, $endStock);
                $this->stockObserver->updateHistoryLog($history, $sell, $sell->qty, $endStock);
                $this->stockObserver->updatePricing($sell->variation);
            } else {

                if ($sell->offer_id != null) {
                    $firstStock = $sell->variation->all_stock->sum('qty_available');
                    $endStock   = $firstStock - $sell->qty;

                    if ($stocks) {
                        $stocks->update([
                            'qty_available'     => $stocks->qty_available - $sell->qty
                        ]);
                    }

                    $purchasesReady     = qty_having($sell->product_id, $sell->variation_id);
                    $this->salesPurchaseCreate($purchasesReady, $sell->qty, $sell);
                    $this->stockObserver->createHistoryStock('shipping_product', $sell, $transaction->id, $sell->qty, $firstStock, $endStock);

                    $this->ledgerTransactionObserver->addShippingAccount($sell);
                    $this->stockObserver->updatePricing($sell->variation);
                }
            }
        }

        if ($sell->offer_id != null) {
            $this->offerObserver->subtotalTransactionChange($sell->transaction_offer);
        }

        $this->subtotalTransactionChange($transaction);
    }

    public function deleteItems(Sell $sell)
    {

        $sellAccount    = $sell->sale_account;
        $stocks         = $this->stockObserver->createData($sell->variation, $sell->transaction_shipping->warehouse_id);
        $history        = $this->stockObserver->historyLogStock($sell->transaction_shipping, $sell->variation_id);

        $stocks->update([
            'qty_available'     => $stocks->qty_available +  $sell->qty
        ]);

        $this->salesPurchaseCreate([], 0, $sell);

        if($sell->offer_id != null) {
            $sell->update([
                'transaction_received_id'       => null
            ]);
            $this->offerObserver->subtotalTransactionChange($sell->transaction_offer);
        } else {
            $sell->forceDelete();
        }
       

        if ($history) {
            $history->delete();
        }

        foreach ($sellAccount as $account) {

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

        $this->stockObserver->updatePricing($sell->variation);
        $this->subtotalTransactionChange($sell->transaction_shipping);
    }

    public function salesPurchaseCreate($purchases = array(), int $totalQty, Sell $sell)
    {


        $previousQtySold    = $sell->sell_purchase->sum('qty');
        $difference         = $totalQty - $previousQtySold;
        $pricingUpdate      = false;


        if ($difference < 0) {

            foreach ($sell->sell_purchase as $sp) {
                $qtyToReduce = min($difference * -1, $sp->qty);

                $sp->update([
                    'qty'   => $sp->qty - $qtyToReduce
                ]);

                if ($sp->purchase) {
                    $sp->purchase->update([
                        'qty_sold'  => $sp->purchase->qty_sold - $qtyToReduce
                    ]);

                    if ($sp->purchase->qty_sold < 1) {
                        $pricingUpdate      = true;
                    }
                }

                $difference += $qtyToReduce;
                if ($difference >= 0) {
                    break;
                }
            }

            $sell->sell_purchase()->where('qty', 0)->delete();
        } else if ($difference > 0) {

            if (count($purchases) > 0) {
                foreach ($purchases as $purchase) {
                    $readyQty       = $purchase->qty_total - $purchase->qty_sum;
                    $allocatedQty   = min($difference, $readyQty);

                    if ($allocatedQty > 0) {

                        $purchase->update([
                            'qty_sold' => $purchase->qty_sold + $allocatedQty
                        ]);

                        SellPurchase::create([
                            'sell_id'           => $sell->id,
                            'purchase_id'       => $purchase->id,
                            'purchase_price'    => $purchase->variation->modal_price ?? 0,
                            'qty'               => $allocatedQty,
                        ]);

                        $difference -= $allocatedQty;
                    }

                    if ($difference <= 0) {
                        break;
                    }
                }
            }


            if ($difference > 0) {
                SellPurchase::create([
                    'sell_id'   => $sell->id,
                    'qty'       => $difference,
                ]);

                $difference = 0;
            }
        }

        return $pricingUpdate;
    }


    
}
