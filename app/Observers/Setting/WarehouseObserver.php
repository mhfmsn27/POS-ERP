<?php

namespace App\Observers\Setting;

use App\Models\Admin\Warehouse;
use Illuminate\Http\Request;

class WarehouseObserver
{
    public function getData(Request $request)
    {
        return Warehouse::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return Warehouse::create([
            'name'          => $request->name,
            'address'       => $request->address
        ]);
    }

    public function updateData(Request $request, Warehouse $warehouse)
    {
        $warehouse->update([
            'name'          => $request->name,
            'address'       => $request->address
        ]);
    }

    public function deleteData(Warehouse $warehouse)
    {

        $warehouse->stock()->delete();
        $warehouse->forceDelete();
        
    }
}
