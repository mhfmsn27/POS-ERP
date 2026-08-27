<?php

namespace App\Observers\Rma;
 
use App\Models\Rma\RmaRecord;
use App\Models\Rma\RmaTransaction;
use Illuminate\Http\Request;

class RmaObserver
{
    public function getData(Request $request)
    {
        $query = RmaTransaction::with('customer')->where(function ($query) use ($request) {
            return $request->customer ? $query->whereIn('customer_id', explode(",",$request->customer)) : '';
        })->where(function ($query) use ($request) {
            return $request->status ?  $query->whereIn('status', explode(",",$request->status)) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : "";
            }
        })->where(function ($query) use ($request) {
            return $request->ref ? $query->where('ref_no', 'like', '%' . $request->ref . '%')->orWhere('customer_name', 'like', '%' . $request->ref . '%') : '';
        });

        if ($request->sort == 'date') {
            $query->orderBy('created_at', $request->sortby);
        } else if ($request->sort == 'ref_no') {
            $query->orderBy('ref_no', $request->sortby);
        } else if ($request->sort == 'customer.name') {
            $query->orderBy('customer_name', $request->sortby);
        }  

        return $query;
    }

    public function createData(Request $request)
    {
        $getTransaction         = RmaTransaction::whereYear("created_at", date("Y"))->count() + 1;
        $invoiceNumber          = sprintf("%03d", $getTransaction);
        $refNo                  = 'RMA' . '' . date("ym") . '/' . sprintf("%02d", my_store()) . '/' . $invoiceNumber;

        $transaction    = RmaTransaction::create([
            'customer_id'           => $request->customer['id'],
            'invoice'               => $invoiceNumber,
            'ref_no'                => $refNo,
            'note'                  => $request->note,
            'estimate_date'         => $request->estimate_date,
            'estimate_price'        => $request->estimate_price,
            'customer_name'         => $request->customer_name,
            'phone'                 => $request->phone
        ]);

        RmaRecord::create([
            'rma_transactions_id'   => $transaction->id,
            'subject'               => 'Pesanan di Buat',
            'type'                  => 'note',
            'note'                  => '-',
        ]);

        return $transaction;
    }

    public function updateData(Request $request, RmaTransaction $transaction)
    {
        $transaction->update([
            'note'                  => $request->note,
            'customer_id'           => $request->customer['id'],
            'estimate_date'         => $request->estimate_date,
            'estimate_price'        => $request->estimate_price,
            'customer_name'         => $request->customer_name,
            'phone'                 => $request->phone
        ]);
    }


    public function setRecord(Request $request, RmaTransaction $transaction)
    {
        return RmaRecord::create([
            'rma_transactions_id'   => $transaction->id,
            'subject'               => $request->subject,
            'type'                  => $request->type,
            'note'                  => $request->note,
        ]);

        if ($request->type != 'note') {
            $transaction->update([
                'status'            => $request->type,
            ]);
        }
    }
}
