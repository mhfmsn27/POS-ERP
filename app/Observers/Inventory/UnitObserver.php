<?php

namespace App\Observers\Inventory;

use App\Models\Admin\Store;
use App\Models\Product\Unit;
use Illuminate\Http\Request;

class UnitObserver
{
    public function getData(Request $request)
    {
        return Unit::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->only_parent != null && $request->unit_parent == null ? $q->where('is_root_parent', $request->only_parent) : '';
        })->where(function ($q) use ($request) {
            return $request->unit_parent ? $q->where('parent_id', $request->unit_parent)->orWhere("id", $request->unit_parent) : '';
        });
    }

    public function createData(Request $request)
    {
        return Unit::create([
            'name'              => $request->name,
            'code'              => $request->code,
            'value'             => $request->is_root_parent == true ? $request->value : 1,
            'is_root_parent'    => $request->is_root_parent == true ? 1 : 0,
            'parent_id'         => $request->is_root_parent == true ? $request->parent['id'] : null,
        ]);
    }

    public function updateData(Request $request, Unit $unit)
    {
        $unit->update([
            'name'              => $request->name,
            'code'              => $request->code,
            'value'             => $request->is_root_parent == true ? $request->value : 1,
            'is_root_parent'    => $request->is_root_parent == true ? 1 : 0,
            'parent_id'         => $request->is_root_parent == true ? $request->parent['id'] : null,
        ]);
    }

    public function createDefault(Store $store)
    {
        Unit::create([
            'name'              => 'Unit',
            'code'              => 'unit',
            'value'             => 1,
            'is_root_parent'    => 0,
            'parent_id'         => null,
            'store_id'          => $store->id
        ]);

        Unit::create([
            'name'              => 'Pcs',
            'code'              => 'pcs',
            'value'             => 1,
            'is_root_parent'    => 0,
            'parent_id'         => null,
            'store_id'          => $store->id
        ]);
    }
}
