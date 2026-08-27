<?php

namespace App\Observers\Transaction;

use App\Models\Crm\SalesCommission;
use App\Models\Transaction\Transaction;
use Illuminate\Http\Request;

class CommissionObserver
{

    public function getData(Request $request)
    {
        return SalesCommission::with('transaction')->where(function ($query) use ($request) { 
            return $request->user ? $query->where('commission_contact_id', $request->user) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : "";
            }
        })->where(function ($query) use ($request) {
            return $request->status ? $query->where('status', $request->status) : '';
        })->whereHas('transaction', function ($query) use ($request) {
            return $request->ref ? $query->where('ref_no', 'like', '%' . $request->ref . '%')->orWhere('additional_notes', 'like', '%' . $request->ref . '%')->orWhere(function ($q) use ($request) {
                $q->whereHas('customer', function ($q) use ($request) {
                    return $request->ref ? $q->where('name', 'like', '%' . $request->ref . '%') : '';
                });
            }) : '';
        })->orderBy("created_at", "desc");
    }

    public function createData(Transaction $transaction)
    {
        $commission = SalesCommission::create([
            'transaction_id'            => $transaction->id,
            'commission_contact_id'     => $transaction->commission_contact_id,
            'commission_contact_type'   => 'user',
            'commission_total'          => $transaction->commission_contact_total ?? 0,
        ]);

        return $commission;
    }

    public function updateData(Transaction $transaction, SalesCommission $salesCommission)
    {
        if ($salesCommission->status != 'pay') {
            $salesCommission->update([
                'transaction_id'            => $transaction->id,
                'commission_contact_id'     => $transaction->commission_contact_id,
                'commission_contact_type'   => 'user',
                'commission_total'          => $transaction->commission_contact_total ?? 0,
            ]);
        }
    }
}
