<?php

namespace App\Observers\User;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogActivityObserver
{
    public function getData(Request $request)
    {
        return Activity::where(function ($q) use ($request) {
            return $request->user ? $q->where("causer_id", $request->user) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereDate("created_at", ">=", $request->start_date)->whereDate("created_at", "<=", $request->end_date);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : $q->whereDate("created_at",date('Y-m-d'));
            }
        })->where(function ($query) use ($request) {
            return $request->subject_type ?  $query->where('log_name', $request->subject_type) : '';
        })->where(function ($query) use ($request) {
            return $request->event ?  $query->where('event', $request->event) : '';
        })->where(function ($query) use ($request) {
            return $request->name ?  $query->where('description', 'like', '%' . $request->name . '%')->orWhere('log_name', 'like', '%' . $request->name . '%') : '';
        })->when(my_store(), function ($q, $storeId) {
            return $q->where("store_id", $storeId);
        })->orderBy("created_at", "desc");
    }

    public function getWithoutFilter()
    {
        return Activity::when(my_store(), function ($q, $storeId) {
            return $q->where("store_id", $storeId);
        });
    }
}
