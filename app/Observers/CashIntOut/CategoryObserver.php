<?php

namespace App\Observers\CashIntOut;

use App\Models\Account\ExpenseCategory;
use App\Models\Admin\Store;
use Illuminate\Http\Request;

class CategoryObserver
{
    public function getData(Request $request)
    {
        return ExpenseCategory::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return ExpenseCategory::create([
            'name'              => $request->name,
            'detail'            => $request->detail,
        ]);
    }

    public function updateData(Request $request, ExpenseCategory $category)
    {
        $category->update([
            'name'              => $request->name,
            'detail'            => $request->detail,
        ]);
    }

    public function createDefault(Store $store)
    {
        return ExpenseCategory::create([
            'name'      => 'Default',
            'detail'    => '-',
            'store_id'  => $store->id
        ]);
    }
}
