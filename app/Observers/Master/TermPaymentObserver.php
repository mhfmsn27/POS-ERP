<?php

namespace App\Observers\Master;

use App\Models\Admin\Store;
use App\Models\Admin\TermPayment;
use Illuminate\Http\Request;

class TermPaymentObserver
{
    public function getData(Request $request)
    {
        return TermPayment::where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        $paymentMethod = TermPayment::create([
            'name'          => $request->name,
            'day'           => $request->day,
            'discount'      => $request->discount,
            'due_date'      => $request->due_date,
            'note'          => $request->note,
        ]);
    }

    public function updateData(Request $request, TermPayment $term)
    {
        $term->update([
            'name'          => $request->name,
            'day'           => $request->day,
            'day'           => $request->day,
            'discount'      => $request->discount,
            'due_date'      => $request->due_date,
            'note'          => $request->note,
        ]);
    }

    public function createDefault(Store $store)
    {
        TermPayment::create([
            'name'          => 'COD',
            'day'           => 0,
            'discount'      => 0,
            'due_date'      => 0,
            'default'       => 'yes',
            'store_id'      => $store->id
        ]);

        TermPayment::create([
            'name'          => 'NET 7',
            'day'           => 7,
            'discount'      => 0,
            'due_date'      => 7,
            'store_id'      => $store->id
        ]);

        TermPayment::create([
            'name'          => 'NET 14',
            'day'           => 14,
            'discount'      => 0,
            'due_date'      => 14,
            'store_id'      => $store->id
        ]);

        TermPayment::create([
            'name'          => 'NET 21',
            'day'           => 21,
            'discount'      => 0,
            'due_date'      => 21,
            'store_id'      => $store->id
        ]);

        TermPayment::create([
            'name'          => 'NET 30',
            'day'           => 30,
            'discount'      => 0,
            'due_date'      => 30,
            'store_id'      => $store->id
        ]);

        return true;
 
    }
}
