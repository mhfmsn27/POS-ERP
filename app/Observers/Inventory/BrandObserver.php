<?php

namespace App\Observers\Inventory;

use App\Models\Product\Brand;
use Illuminate\Http\Request;

class BrandObserver
{
    public function getData(Request $request)
    {
        return Brand::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request, String $image)
    {
        return Brand::create([
            'name'              => $request->name,
            'detail'            => $request->detail,
            'image'             => $image
        ]);
    }

    public function updateData(Request $request, Brand $brand, String $image)
    {
        $brand->update([
            'name'              => $request->name,
            'detail'            => $request->detail,
            'image'             => $image == '' ? $brand->image : $image
        ]);
    }
}
