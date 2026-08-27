<?php

namespace App\Observers\Hrm;

use App\Models\Admin\Store;
use App\Models\Hrm\Department;
use App\Models\Hrm\Designation;
use Illuminate\Http\Request;

class DepartmentObserver
{
    public function getData(Request $request)
    {
        return Department::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return Department::create([
            'name'      => $request->name, 
        ]);
    }

    public function updateData(Request $request, Department $department)
    {
        $department->update([
            'name'      => $request->name
        ]); 
    }

    public function createDefault(Store $store)
    {
        return Department::create([
            'name'      => 'Default',
            'store_id'  => $store->id
        ]);
    }

    public function createDesignationDefault(Department $department)
    {
        return Designation::create([
            'name'          => 'Default',
            'store_id'      => $department->store_id,
            'department_id' => $department->id
        ]);
    }
}
