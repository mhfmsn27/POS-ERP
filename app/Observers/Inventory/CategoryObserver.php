<?php

namespace App\Observers\Inventory;

use App\Models\Product\Category;
use Illuminate\Http\Request;

class CategoryObserver
{
    public function getData(Request $request)
    {
        return Category::with(['parent', 'children'])->orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->only_parent != null ? $q->where('is_root_parent', $request->only_parent) : '';
        })->where(function($q) use ($request) {
            return $request->show_ecommerce ? $q->where('show_in_ecommerce',$request->show_ecommerce) : '';
        });
    }

    public function getSimple(Request $request)
    {
        return Category::select(['id', 'name'])->orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request, String $image)
    {
        return Category::create([
            'name'              => $request->name,
            'is_root_parent'    => $request->is_root_parent == true ? 1 : 0,
            'parent_id'         => $request->is_root_parent == true ? $request->parent['id'] : null,
            'detail'            => $request->detail,
            'image'             => $image
        ]);
    }

    public function updateData(Request $request, Category $category, String $image)
    {
  
        $category->update([
            'name'              => $request->name,
            'is_root_parent'    => $request->is_root_parent == true ? 1 : 0,
            'parent_id'         => $request->is_root_parent == true ? $request->parent['id'] : '',
            'detail'            => $request->detail,
            'image'             => $image == '' ? $category->image : $image
        ]);
    }
}
