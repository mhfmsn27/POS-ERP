<?php

namespace App\Observers\Hrm;

use App\Models\Hrm\Designation;
use Illuminate\Http\Request;

class DesignationObserver
{
    public function getData(Request $request)
    {
        return Designation::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->department ?  $q->where('department_id',$request->department) : '';
        });
    }

    public function createData(Request $request)
    {
        return Designation::create([
            'name'              => $request->name, 
            'department_id'     => $request->department['id']
        ]);
    }

    public function updateData(Request $request, Designation $designation)
    {
        $designation->update([
            'name'              => $request->name,
            'department_id'     => $request->department['id']
        ]); 
    }
}
