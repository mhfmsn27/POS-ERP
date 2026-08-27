<?php

namespace App\Observers\Transaction\Purchase;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Product\Supplier;
use App\Models\Product\Unit;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\SellPurchase;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Inventory\StockObserver;
use App\Observers\Transaction\Sales\SalesObserver;
use Illuminate\Http\Request;

class ReceivedProductObserver
{

    protected $stockObserver;
    protected $ledgerTransactionObserver;
    protected $ledgerObserver;
    protected $salesObserver;
    protected $PoObserver;

    public function __construct(StockObserver $stockObserver, LedgerTransactionObserver $ledgerTransactionObserver, LedgerObserver $ledgerObserver, SalesObserver $salesObserver, POObserver $PoObserver)
    {
        $this->stockObserver                = $stockObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
        $this->ledgerObserver               = $ledgerObserver;
        $this->salesObserver                = $salesObserver;
        $this->PoObserver                   = $PoObserver;
    }

    public function getData(Request $request)
    {
        $query = Transaction::with('supplier')->where(function ($query) use ($request) {
            return $request->supplier ? $query->whereIn('supplier_id', explode(",", $request->supplier)) : '';
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
                $q->whereHas('supplier', function ($q) use ($request) {
                    return $request->ref ? $q->where('name', 'like', '%' . $request->ref . '%') : '';
                });
            }) : '';
        })->where(function ($query) use ($request) {
            return $request->createdby ?  $query->whereIn('created_by', explode(",", $request->createdby)) : '';
        })->where(function ($query) use ($request) {
            return $request->warehouse ?  $query->where('warehouse_id', $request->warehouse) : ($request->with_warehouse == 'yes' ? $query->where("warehouse_id", null)->orWhere("warehouse_id", "") : '');
        })->where('type', 'received_product')->orderBy("created_at", "desc");

        if ($request->sort == 'date') {
            $query->orderBy('transaction_date', $request->sortby);
        } else if ($request->sort == 'ref_no') {
            $query->orderBy('ref_no', $request->sortby);
        } else if ($request->sort == 'supplier_ref') {
            $query->orderBy('supplier_ref', $request->sortby);
        } else if ($request->sort == 'supplier.name') {
            $query->orderBy(Supplier::select('name')->whereColumn('suppliers.id', 'transactions.supplier_id'), $request->sortby);
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
            $getTransaction         = Transaction::where("type", "received_product")->whereDate("created_at", date("Y-m-d"))->count() + 1;
            $invoiceNumber          = sprintf("%05d", $getTransaction);
            $refNo                  = Helper::transactionKey('RCP', $invoiceNumber);

            $data                   = new Transaction();
            $data->invoice_no       = $invoiceNumber;
            $data->status           = 'received_not_use';
            $data->ref_no           = $request->no_ref != null ? $request->no_ref : $refNo;
            $data->old_warehouse_id = $request['warehouse']['id'];
            $data->transaction_date = $request->date ? Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s') : date('Y-m-d H:i:s');
        } else {
            $data                       = $transaction;
            $data->ref_no               = $request->no_ref != null ? $request->no_ref : $transaction->ref_no;

            $data->transaction_date = $request->date ? Helper::setTimeZoneLocal($request->date) . ' ' . $transaction->created_at->format('H:i:s') : date('Y-m-d') . ' ' . $transaction->created_at->format('H:i:s');
            $data->old_warehouse_id     = $data->warehouse_id;
        }

        $data->type                 = 'received_product';
        $data->warehouse_id         = $request['warehouse']['id'];
        $data->supplier_id          = $request->supplier['id'];
        $data->address              = $request->address;
        $data->supplier_ref         = $request->supplier_ref;
        $data->created_by           = auth()->user()->id;
        $data->additional_notes     = $request->note;
        $data->save();

        return $data;
    }

    public function savingItems(Request $request, Transaction $transaction)
    {
        $listData = array();
        foreach ($request->items as $d) {
            $sell = Purchase::find($d['id']);

            if ($sell) {
                $this->updateItems($d, $sell, $transaction);
            } else {
                $this->createItems($d, $transaction);
            }
        }

        $this->subtotalTransactionChange($transaction);

        return $listData;
    }

    public function createItems($d, $transaction)
    {
        $unit       = null;
        $quantity   = $d['qty'];

        if ($d['unit']) {
            $unit           = Unit::where("id", $d['unit'])->first();
            if ($unit) {
                $quantity   = $d['qty'] * $unit->value;
            }
        }

        $purchase = Purchase::create([
            'transaction_received_id'           => $transaction->id,
            'product_id'                        => $d['product_id'],
            'variation_id'                      => $d['variation_id'],
            'unit_qty'                          => $d['qty'],
            'quantity'                          => $quantity,
            'unit_id'                           => $unit != null ? $unit->id : null,
            'purchase_price'                    => 0,
            'without_discount'                  => $d['without_discount'],
            'purchase_price'                    => $d['without_discount'],
            'discount_type'                     => $d['discount_type'],
            'publish'                           => 'not_use'
        ]);

        $stocks     = $this->stockObserver->createData($purchase->variation, $transaction->warehouse_id);
        $firstStock = $purchase->variation->all_stock->sum('qty_available');
        $endStock   = $purchase->variation->all_stock->sum('qty_available') + $purchase->quantity;

        if ($stocks) {
            $stocks->update([
                'qty_available'     => $stocks->qty_available + $purchase->quantity
            ]);
        }

        $purchase->variation->update([
            'purchase_price'    => $purchase->purchase_price,
            'price_inc_tax'     => $purchase->purchase_price_inc_tax
        ]);

        $nextPurchases  = $this->changeOrDeletePurchase($purchase, ($purchase->quantity - $purchase->qty_return));
        $purchaseiD     = $purchase->sell_purchase_first->id ?? null;

        if ($nextPurchases['status'] == true) {
            $this->updateSellPurchases($nextPurchases['nextPurchases'], $purchaseiD, $purchase->variation_id);
        }

        $this->stockObserver->createHistoryStock('received_product', $purchase, $transaction->id, $purchase->quantity, $firstStock, $endStock);
        $this->stockObserver->updatePricing($purchase->variation);
        $this->ledgerTransactionObserver->addSupplyAccount($purchase);

        $this->subtotalTransactionChange($transaction);
    }

    public function subtotalTransactionChange(Transaction $transaction)
    {
        $subtotal                           = $transaction->purchase_received()->selectRaw("sum(without_discount * quantity) as jumlah")->first();

        $totalUseData                       = $transaction->purchase_received()->where("transaction_id", "!=", null)->count();
        $totalPurchase                      = $transaction->purchase_received()->count();

        $transaction->update([
            'total_before_tax'      => (int)$subtotal->jumlah,
            'final_total'           => (int)$subtotal->jumlah,
            'status'                => $totalUseData == $totalPurchase ? 'received_use' : 'received_not_use'
        ]);
    }

    public function updateItems($request, Purchase $purchase, Transaction $transaction)
    {
        $unit           = null;
        $quantity       = $request['qty'];

        if ($request['unit']) {
            $unit           = Unit::where("id", $request['unit'])->first();
            if ($unit) {
                $quantity   = $request['qty'] * $unit->value;
            }
        }

        $oldPrice       = $purchase->purchase_price;
        $purchase->update([
            'transaction_received_id'           => $transaction->id,
            'unit_qty'                          => $request['qty'],
            'quantity'                          => $quantity,
            'unit_id'                           => $unit != null ? $unit->id : null,
            'purchase_price'                    => 0,
            'without_discount'                  => $request['without_discount'],
            'purchase_price'                    => $request['without_discount'],
            'purchase_price_inc_tax'            => 0,
            'discount_type'                     => $request['discount_type'],
            'publish'                           => 'not_use'
        ]);

        if ($purchase->product->is_stock == 'yes') {

            $stocks     = $this->stockObserver->createData($purchase->variation, $transaction->warehouse_id);
            $history    = $this->stockObserver->historyLogStock($transaction, $purchase->variation_id);

            if ($history) {
                $endStock   = $history->from + $purchase->quantity;

                if ($stocks) {
                    $stocks->update([
                        'qty_available'     => ($stocks->qty_available - ($transaction->warehouse_id == $transaction->old_warehouse_id ? $history->qty : 0)) + $purchase->quantity
                    ]);
                }

                if ($transaction->warehouse_id != $transaction->old_warehouse_id) {
                    $stocks     = $this->stockObserver->createData($purchase->variation, $transaction->old_warehouse_id);
                    if ($stocks) {
                        $stocks->update([
                            'qty_available' => $stocks->qty_available - $history->qty
                        ]);
                    }
                }

                $purchase->variation->update([
                    'purchase_price'    => $purchase->purchase_price,
                    'price_inc_tax'     => $purchase->purchase_price_inc_tax
                ]);

                $nextPurchases  = $this->changeOrDeletePurchase($purchase, ($purchase->quantity - $purchase->qty_return));
                $purchaseiD     = $purchase->sell_purchase_first->id ?? null;

                if ($nextPurchases['status'] == true) {
                    $this->updateSellPurchases($nextPurchases['nextPurchases'], $purchaseiD, $purchase->variation_id);
                }

                $this->changePricingUpdate($purchase, $oldPrice);

                $this->stockObserver->updateHistoryLog($history, $purchase, $purchase->quantity, $endStock);
                $this->stockObserver->updatePricing($purchase->variation);
                $this->ledgerTransactionObserver->updateSupplyAccount($purchase->transaction_received, $purchase);
            } else {
                $firstStock = $purchase->variation->all_stock->sum('qty_available');
                $endStock   = $purchase->variation->all_stock->sum('qty_available') + $purchase->quantity;

                if ($stocks) {
                    $stocks->update([
                        'qty_available'     => $stocks->qty_available + $purchase->quantity
                    ]);
                }

                $purchase->variation->update([
                    'purchase_price'    => $purchase->purchase_price,
                    'price_inc_tax'     => $purchase->purchase_price_inc_tax
                ]);

                $nextPurchases  = $this->changeOrDeletePurchase($purchase, ($purchase->quantity - $purchase->qty_return));
                $purchaseiD     = $purchase->sell_purchase_first->id ?? null;

                if ($nextPurchases['status'] == true) {
                    $this->updateSellPurchases($nextPurchases['nextPurchases'], $purchaseiD, $purchase->variation_id);
                }

                $this->stockObserver->createHistoryStock('received_product', $purchase, $transaction->id, $purchase->quantity, $firstStock, $endStock);
                $this->stockObserver->updatePricing($purchase->variation);
                $this->ledgerTransactionObserver->addSupplyAccount($purchase);
            }
        }

        if ($purchase->po_id != null) {
            $this->PoObserver->subtotalTransactionChange($purchase->po);
        }

        $this->subtotalTransactionChange($transaction);
    }

    public function deleteItems(Purchase $purchase)
    {

        $stocks     = $this->stockObserver->createData($purchase->variation, $purchase->transaction_received->warehouse_id);
        $history    = $this->stockObserver->historyLogStock($purchase->transaction_received, $purchase->variation_id);
        $variation  = $purchase->variation;

        if ($history) {
            $history->delete();
        }

        $stocks->update([
            'qty_available'     => $stocks->qty_available -  $purchase->quantity
        ]);

        $purchaseiD     = $purchase->sell_purchase_first->id ?? null;
        $toRelocate     = $this->changeOrDeletePurchase($purchase, 0);

        foreach ($purchase->purchase_account as $account) {
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

        if ($purchase->po_id != null) {
            $purchase->update([
                'transaction_received_id'       => null
            ]); 
            $this->PoObserver->subtotalTransactionChange($purchase->po);
        } else {
            $purchase->forceDelete();
        }


        if ($toRelocate['status'] == true) {
            $this->updateSellPurchases($toRelocate['nextPurchases'], $purchaseiD, $variation->id);
        }

        $this->stockObserver->updatePricing($variation);
        $this->subtotalTransactionChange($purchase->transaction_received);
    }

    public function changeOrDeletePurchase(Purchase $purchase, Int $totalQty)
    {

        $previousQtySold    = $purchase->sell_purchase->sum('qty');
        $difference         = $totalQty - $previousQtySold;
        $reLocate           = false;
        $nextPurchases      = null;

        if ($difference < 0) {

            $nextPurchases  = Purchase::where("id", ">", $purchase->id)->where("variation_id", $purchase->variation_id)->first(['id', 'quantity', 'qty_sold', 'qty_return', 'variation_id']);

            $reLocate       = true;
            foreach ($purchase->sell_purchase as $sp) {
                $qtyToReduce    = min($difference * -1, $sp->qty);
                $result         = $sp->qty - $qtyToReduce;

                if ($sp->purchase) {
                    $sp->purchase->update([
                        'qty_sold'  => $purchase->qty_sold - $qtyToReduce
                    ]);

                    if ($sp->purchase->qty_sold == 0) {
                        $this->stockObserver->updatePricing($purchase->variation);
                    }
                }

                if ($result < 1) {
                    $sp->update([
                        'purchase_id'       => $nextPurchases != null ? $nextPurchases->id : null,
                        'purchase_price'    => $nextPurchases != null ? $nextPurchases->variation->modal_price : 0,
                        'sell_id'           => $sp->sell_id,
                        'qty'               => $sp->qty
                    ]);

                    if ($nextPurchases) {
                        $nextPurchases->update([
                            'qty_sold'  => $nextPurchases->qty_sold + $sp->qty
                        ]);
                    }
                } else {

                    $sp->update([
                        'qty'       => $result
                    ]);

                    SellPurchase::create([
                        'purchase_id'       => $nextPurchases != null ? $nextPurchases->id : null,
                        'purchase_price'    => $nextPurchases != null ? $nextPurchases->variation->modal_price : 0,
                        'sell_id'           => $sp->sell_id,
                        'qty'               => $qtyToReduce
                    ]);

                    if ($nextPurchases) {
                        $nextPurchases->update([
                            'qty_sold'  => $nextPurchases->qty_sold + $qtyToReduce
                        ]);
                    }
                }

                $difference += $qtyToReduce;

                if ($difference >= 0) {
                    break;
                }
            }
        } else if ($difference > 0) {
            $totalQty           = $totalQty - $previousQtySold;

            $purchaseToUpdate   = SellPurchase::whereHas('sell', function ($q) use ($purchase) {
                return $q->where("store_id", $purchase->store_id)->where("variation_id", $purchase->variation_id);
            })->where("purchase_id", null)->orderBy("id", "asc")->get();

            if (count($purchaseToUpdate) > 0) {
                $reLocate           = true;
            }

            foreach ($purchaseToUpdate as $sp) {
                $readyQty       = $sp->qty;
                $allocatedQty   = min($totalQty, $readyQty);

                if ($allocatedQty > 0) {

                    $purchase->update([
                        'qty_sold' => $purchase->qty_sold + $allocatedQty
                    ]);

                    if ($purchase->variation->modal_price == 0 && $sp->purchase_id == null) {
                        $purchasePrice  = $purchase->purchase_price;
                    } else {
                        $purchasePrice  = $purchase->variation->modal_price;
                    }

                    if ($sp->qty > $allocatedQty) {

                        $sp->update([
                            'purchase_id'       => null,
                            'qty'               => $sp->qty - $allocatedQty,
                        ]);

                        SellPurchase::create([
                            'sell_id'           => $sp->sell_id,
                            'purchase_id'       => $purchase->id,
                            'qty'               => $allocatedQty,
                            'purchase_price'    => $purchasePrice
                        ]);
                    } else {
                        $sp->update([
                            'purchase_id'       => $purchase->id,
                            'purchase_price'    => $purchasePrice
                        ]);
                    }

                    $totalQty -= $allocatedQty;
                }



                if ($totalQty <= 0) {
                    break;
                }
            }
        }

        // Check Lebih besar qty sold atau qty purchase
        if (($purchase->qty_sold + $purchase->qty_return) > $totalQty) {
            $adjustQtySold  = ($purchase->qty_sold + $purchase->qty_return) - $totalQty;
            $forSale        = $adjustQtySold > $purchase->qty_sold ? $adjustQtySold - $purchase->qty_sold : $adjustQtySold;

            $purchase->update([
                'qty_sold'  => $forSale
            ]);
        }

        return array(
            'status'        => $reLocate,
            'nextPurchases' => $nextPurchases
        );
    }

    public function updateSellPurchases($paramData = null, $purchaseId = null, $variationId)
    {

        $nextPurchases = null;

        if ($paramData != null) {
            $nextPurchases  = $paramData['nextPurchases'];
        }

        if ($nextPurchases) {

            if ((($nextPurchases->quantity - $nextPurchases->qty_return) - $nextPurchases->qty_sold) < 1) {
                $allPurchases   = Purchase::where("id", ">=", $nextPurchases->id)->where("variation_id", $nextPurchases->variation_id)->get(['id', 'qty_sold', 'quantity', 'qty_return', 'qty_adjusted_add']);
                foreach ($allPurchases as $key => $pnext) {
                    $previousQtySold    = $pnext->sell_purchase->sum('qty');
                    $totalQty           = ($pnext->quantity + (int)$pnext->qty_adjusted_add) - ($pnext->qty_return);
                    $difference         = $totalQty - $previousQtySold;

                    if ($key < count($allPurchases) - 1) {
                        $nextPurchase = $allPurchases[$key + 1];
                    } else {
                        $nextPurchase = null;
                    }

                    if ($difference < 0) {
                        foreach ($pnext->sell_purchase as $sp) {
                            $qtyToReduce    = min($difference * -1, $sp->qty);
                            $result         = $sp->qty - $qtyToReduce;

                            if ($result < 1) {
                                $sp->update([
                                    'purchase_id'       => $nextPurchase != null ? $nextPurchase->id : null,
                                    'sell_id'           => $sp->sell_id,
                                    'purchase_price'    => $nextPurchase != null ? $nextPurchase->variation->modal_price : 0,
                                    'qty'               => $sp->qty
                                ]);
                            } else {

                                $sp->update([
                                    'qty'       => $result
                                ]);

                                SellPurchase::create([
                                    'purchase_id'       => $nextPurchase != null ? $nextPurchase->id : null,
                                    'sell_id'           => $sp->sell_id,
                                    'purchase_price'    => $nextPurchase != null ? $nextPurchase->variation->modal_price : 0,
                                    'qty'               => $qtyToReduce
                                ]);
                            }

                            $difference += $qtyToReduce;

                            if ($difference >= 0) {
                                break;
                            }
                        }
                    }
                }
            }
        }


        $sellPurchasesData  = SellPurchase::where(function ($q) use ($purchaseId) {
            return $purchaseId != null ? $q->where("id", ">=", $purchaseId) : '';
        })->whereHas('sell', function ($q) use ($variationId) {
            return $q->where("variation_id", $variationId);
        })->groupBy('sell_id')->get();

        $accountToChange    = collect();
        $commissionToChange = collect();

        foreach ($sellPurchasesData as $sellp) {
            if ($sellp->sell) {
                foreach ($sellp->sell->sale_account->where("item_id", $sellp->sell->id) as $account) {

                    if ($account->item_id == $sellp->sell->id) {
                        $saleAccount = $sellp->sell->product->sale_account->id ?? null;
                        if ($account->account_id == $saleAccount) {
                            $price  = ($sellp->sell->unit_price - ($sellp->sell->transaction->customer->tax_default == 'yes' ? $sellp->sell->tax_total : 0)) * $sellp->sell->qty;
                        } else {
                            $price  = sell_purchase_total($sellp->sell->id);
                        }

                        $account->update([
                            'amount'        => $price
                        ]);


                        if ($accountToChange->where("id", $account->account_id)->count() == 0) {
                            $item['id']         = $account->account_id;
                            $item['account']    = $account;
                            $accountToChange[]  = $item;
                        }
                    }
                }


                // Update Commission if sale have in sales
                if ($sellp->sell->transaction)
                    if ($sellp->sell->transaction->commission_contact_id != null) {
                        if ($commissionToChange->where("id", $sellp->sell->transaction_id)->count() == 0) {
                            $i['id']            = $sellp->sell->transaction_id;
                            $i['transaction']   = $sellp->sell->transaction;
                            $commissionToChange[]   = $i;
                        }
                    }
            }
        }

        foreach ($commissionToChange as $commission) {
            $this->salesObserver->changeCommission($commission['transaction']);
        }


        foreach ($accountToChange as $account) {
            $this->ledgerTransactionObserver->logAccountUpdate($account['account']);
        }
    }

    public function changePricingUpdate(Purchase $purchase, $oldPrice)
    {
        if ($oldPrice != $purchase->purchase_price && $purchase->sell_purchase->count() > 0) {
            if ($purchase->sell_purchase->avg('purchase_price') == $oldPrice) {
                $purchase->sell_purchase()->update([
                    'purchase_price'        => $purchase->purchase_price
                ]);
            } else {
                if ($purchase->sell_purchase()->where("purchase_price", $oldPrice)->count() > 0) {
                    $purchase->sell_purchase()->where("purchase_price", $oldPrice)->update([
                        'purchase_price'    => $purchase->purchase_price
                    ]);
                }
            }

            $this->updateSellPurchases(null, ($purchase->sell_purchase_first->id ?? null), $purchase->variation_id);

            if ($purchase->transaction_id != null) {
                $this->stockObserver->updatePricing($purchase->variation);
            }
        }
    }
}
