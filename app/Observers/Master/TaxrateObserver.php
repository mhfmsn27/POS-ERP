<?php

namespace App\Observers\Master;

use App\Models\Admin\Taxrate;
use Illuminate\Http\Request;

class TaxrateObserver
{
    public function getData(Request $request)
    {
        return Taxrate::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%')->orWhere('code', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return Taxrate::create([
            'name'          => $request->name,
            'taxrate'       => $request->taxrate,
            'code'          => $request->code,
        ]);
    }

    public function updateData(Request $request, Taxrate $taxrate)
    {
        $taxrate->update([
            'name'          => $request->name,
            'taxrate'       => $request->taxrate,
            'code'          => $request->code,
        ]);
    }
}
