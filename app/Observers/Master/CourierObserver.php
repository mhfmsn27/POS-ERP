<?php

namespace App\Observers\Master;

use App\Models\Admin\Courier;
use Illuminate\Http\Request;

class CourierObserver
{
    public function getData(Request $request)
    {
        return Courier::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%')->orWhere('code', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request, String $image)
    {
        return Courier::create([
            'name'          => $request->name,
            'code'          => $request->code,
            'logo'          => $image
        ]);
    }

    public function updateData(Request $request, Courier $courier,  String $image)
    {
        $courier->update([
            'name'          => $request->name,
            'code'          => $request->code,
            'logo'          => $image != '' ? $image : $courier->logo
        ]);
    }
    
}
