<?php

namespace App\Observers\Master;

use App\Models\Admin\Printer;
use App\Models\Admin\Store;
use Illuminate\Http\Request;

class PrinterObserver
{

    public function getData(Request $request)
    {
        return Printer::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%')->orWhere('code', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return Printer::create([
            'url'           => $request->url,
            'name'          => $request->name,
            'type'          => 'offline',
            'char_per_line' => 200,
        ]);
    }

    public function updateData(Request $request, Printer $courier)
    {
        $courier->update([
            'url'           => $request->url,
            'name'          => $request->name,
        ]);
    }

    public function createDefault(Store $store)
    {
        return Printer::create([
            'name'          => 'Printer Default',
            'type'          => 'offline',
            'char_per_line' => 200,
            'merchant_id'   => $store->id
        ]);
    }
}
