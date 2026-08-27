<?php

namespace App\Observers\Transaction;

use App\Helper;
use App\Models\Stock\StockAdjusmentDetail;
use App\Models\Transaction\Transaction;
use Illuminate\Http\Request;

class WarehouseTransferObserver
{
    public function getData(Request $request)
    {
        return Transaction::where('type', 'warehouse_transfer')->where(function ($query) use ($request) {
            return $request->createdby ?  $query->where('created_by', $request->createdby) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereDate("transaction_date", ">=", $request->start_date)->whereDate("transaction_date", "<=", $request->end_date);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : "";
            }
        })->where(function ($query) use ($request) {
            return $request->ref ? $query->where('ref_no', 'like', '%' . $request->ref . '%')->orWhere('additional_notes', 'like', '%' . $request->ref . '%') : '';
        })->orderBy("created_at", "desc");
    }

    public function createTransaction(Request $request, String $noRef, String $invoiceNumber)
    {

        $transaction = Transaction::create([
            'type'              => 'warehouse_transfer',
            'created_by'        => auth()->user()->id,
            'invoice_no'        => $invoiceNumber,
            'ref_no'            => $request->ref_no ? $request->ref_no : $noRef,
            'transaction_date'  => $request->date ? Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s') : date("Y-m-d H:i:s"),
            'additional_notes'  => $request->note,
            'from_warehouse_id' => $request->from['id'],
            'to_warehouse_id'   => $request->to['id']
        ]);

        return $transaction;
    }

    public function createItems($item, Int $qtyAdjustment, $getUnits,  Transaction $transaction)
    {

        $adjustment = StockAdjusmentDetail::create([
            'transaction_id'        => $transaction->id,
            'stock_sistem'          => $item['quantity'],
            'actual_stock'          => $qtyAdjustment,
            'variation_id'          => $item['variation_id'],
            'product_id'            => $item['product_id'],
            'unit_id'               => $getUnits ? $getUnits->id : null,
            'unit_qty'              => $item['hasil_qty'],
            'qty_adjustment'        => $item['hasil_qty'],
        ]);

        return $adjustment;
    }
}
