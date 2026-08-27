<?php

namespace App\Observers\Transaction\Purchase;

use App\Helper;
use App\Models\Product\Supplier;
use App\Models\Product\Unit;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class POObserver
{
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
        })->where('type', 'po')->orderBy("created_at", "desc");

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
            $getTransaction         = Transaction::where("type", "po")->whereDate("created_at", date("Y-m-d"))->count() + 1;
            $invoiceNumber          = sprintf("%05d", $getTransaction);
            $refNo                  = Helper::transactionKey('PO', $invoiceNumber);

            $data                   = new Transaction();
            $data->invoice_no       = $invoiceNumber;
            $data->status           = 'open';
            $data->ref_no           = $request->no_ref != null ? $request->no_ref : $refNo;
            $data->transaction_date = $request->date ? Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s') : date('Y-m-d H:i:s');
        } else {
            $data                       = $transaction;
            $data->ref_no               = $request->no_ref != null ? $request->no_ref : $transaction->ref_no;

            $data->transaction_date = $request->date ? Helper::setTimeZoneLocal($request->date) . ' ' . $transaction->created_at->format('H:i:s') : date('Y-m-d') . ' ' . $transaction->created_at->format('H:i:s');
            $data->old_warehouse_id     = $data->warehouse_id;
        }

        $data->type                 = 'po';
        $data->supplier_id          = $request->supplier['id'];
        $data->address              = $request->address;
        $data->created_by           = auth()->user()->id;
        $data->additional_notes     = $request->note;
        $data->save();

        return $data;
    }

    public function savingItems(Request $request, Transaction $transaction)
    {
        $listData = array();
        foreach ($request->items as $d) {
            $this->createItems($d,$transaction);
        }

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

        Purchase::create([
            'po_id'                             => $transaction->id,
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

        $this->subtotalTransactionChange($transaction);
    }

    public function subtotalTransactionChange(Transaction $transaction)
    {
        $subtotal                           = $transaction->po()->selectRaw("sum(without_discount * quantity) as jumlah")->first();
        $totalUseData                       = $transaction->po()->where(function ($q) {
            return $q->where("transaction_id", "!=", null)->orWhere("transaction_received_id", "!=", null);
        })->count();
        $totalPurchase                      = $transaction->po()->count();

  
        $transaction->update([
            'total_before_tax'      => (int)$subtotal->jumlah,
            'final_total'           => (int)$subtotal->jumlah,
            'status'                => $totalUseData == $totalPurchase ? 'close' : 'open'
        ]);
    }

    public function updateItems($request, Purchase $purchase)
    {
        $transaction    = $purchase->po;
        $unit           = null;
        $quantity       = $request['qty'];

        if ($request['unit']) {
            $unit           = Unit::where("id", $request['unit'])->first();
            if ($unit) {
                $quantity   = $request['qty'] * $unit->value;
            }
        }

        $purchase->update([
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

        $this->subtotalTransactionChange($transaction);
    }

    public function deleteItems(Purchase $purchase)
    {

        $this->subtotalTransactionChange($purchase->po);
    }
}
