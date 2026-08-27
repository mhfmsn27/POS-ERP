<?php

namespace Poshub\Ecommerce\Repositories;

use App\Models\Transaction\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Poshub\Ecommerce\Models\TransactionShippingDetail;

class OrderRepository
{
    public function getData()
    {
        return Transaction::where("customer_id", Auth::guard('customers')->user()->id)->where('type', 'sell')->orderBy("id", "desc")->get();
    }

    public function getDetail($id)
    {
        return Transaction::where("customer_id", Auth::guard('customers')->user()->id)->where("id", $id)->first();
    }

    public function getShippingDetail($id)
    {
        return TransactionShippingDetail::where("transaction_id", $id)->first();
    }

    public function getTransaction(Request $request)
    {
        return Transaction::where('type', 'sell')->where("type_sell", "ecommerce")
            ->where(function ($query) use ($request) {
                return $request->store ? $query->where('store_id', $request->store) : '';
            })->where(function ($query) use ($request) {
                return $request->customer ? $query->where('customer_id', $request->customer) : '';
            })->where(function ($query) use ($request) {
                if ($request->status == 'due') {
                    return $query->where("payment_status", "due");
                } else {
                    return $request->status ? $query->where("status", $request->status) : '';
                }
            })->where(function ($query) use ($request) {
                if ($request->end_date && $request->start_date) {
                    return $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                }
                if ($request->date_now) {
                    return $query->whereDate('created_at', $request->date_now);
                }
            })->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })
            ->orderBy('id', 'desc');
    }

    public function getPurchaseReady($sell)
    {
        return DB::select(DB::raw("SELECT s.* FROM (SELECT t.id, quantity, qty_sold, product_id, variation_id, t.store_id, SUM(IFNULL(t.qty_sold,0) + IFNULL(t.qty_adjusted,0) + IFNULL(t.qty_return,0) + IFNULL(t.qty_transfer,0) + IFNULL(t.qty_expire,0)) AS qty_sum FROM purchases t  GROUP BY t.id, t.quantity, t.qty_sold, t.product_id, t.variation_id,t.store_id) AS s WHERE s.quantity > s.qty_sum AND s.product_id=" . $sell->product_id . " AND s.variation_id=" . $sell->variation_id . "  AND s.store_id=" . $sell->store_id . " ORDER BY s.id ASC "));
    }
}
