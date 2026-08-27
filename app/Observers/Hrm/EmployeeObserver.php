<?php

namespace App\Observers\Hrm;

use App\Models\Hrm\Employee;
use Illuminate\Http\Request;

class EmployeeObserver
{
    public function getData(Request $request)
    {
        return Employee::with('user')->whereHas('user', function ($query) use ($request) {
            return $request->name ?  $query->where('name', 'like', '%' . $request->name . '%') : '';
        })->whereHas('designation.department', function ($query) use ($request) {
            return $request->department ?  $query->where('id', $request->department) : '';
        })->orderBy("created_at", "desc");
    }


    public function createData(Request $request)
    {
        return Employee::create([
            'designation_id'        => $request->designation['id'],
            'user_id'               => $request->user['id'],
            'salary'                => $request->salary,
            'address'               => $request->address,
            'date_birth'            => $request->date_birth
        ]);
    }

    public function updateData(Request $request, Employee $employee)
    {
        $employee->update([
            'designation_id'        => $request->designation['id'],
            'user_id'               => $request->user['id'],
            'salary'                => $request->salary,
            'address'               => $request->address,
            'date_birth'            => $request->date_birth
        ]);
    }

    public function deleteData(Employee $employee) {
        $employee->delete();
    }
}
