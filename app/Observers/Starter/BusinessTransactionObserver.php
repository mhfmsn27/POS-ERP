<?php

namespace App\Observers\Starter;

use App\Models\Transaction\TransactionPackage;
use Illuminate\Http\Request;

class BusinessTransactionObserver
{
    public function getData(Request $request)
    {
        $query = TransactionPackage::where(function ($q) {
            return auth()->user()->role_type == 'user' ? $q->where("merchant_id", auth()->user()->merchant_id) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : "";
            }
        })->where(function ($q) use ($request) {
            return $request->status ? $q->where("status", $request->statusA) : '';
        })->where(function ($q) use ($request) {
            return $request->store ? $q->where("store_id", $request->store) : '';
        })->whereHas('store', function ($q) use ($request) {
            return $request->merchant ? $q->where("merchant_id", $request->merchant) : '';
        })->orderBy("created_at", "desc");


        return $query;
    }
}
