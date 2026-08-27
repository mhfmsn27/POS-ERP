<?php

namespace App\Observers\Saas;

use App\Models\Admin\Merchant;
use App\Models\User;
use Illuminate\Http\Request;

class MerchantObserver
{

    public function getData(Request $request)
    {
        return Merchant::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : "";
            }
        })->orderBy('name', 'asc');
    }

    public function createData(Request $request, User $user)
    {
        return Merchant::create([
            'name'          => $request->name,
            'owner_id'      => $user->id
        ]);
    }
}
