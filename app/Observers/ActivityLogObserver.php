<?php

namespace App\Observers;

use App\Models\Admin\LogActivity;
use Illuminate\Http\Request;

class ActivityLogObserver
{
    public function logsData(Request $request)
    {
        return LogActivity::where(function ($q) use ($request) {
            return $request->user ? $q->where('causer_id', $request->user) : '';
        })->where('store_id', my_store())->orderBy('created_at', 'desc');
    }
}
