<?php

namespace App\Observers\Hrm;

use App\Models\Salary\Allowance;
use Illuminate\Http\Request;

class AllowanceObserver
{
    public function getData(Request $request)
    {
        return Allowance::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return Allowance::create([
            'name'              => $request->name,
            'designation_id'    => $request->designation['id'],
            'priode'            => $request->priode,
            'amount'            => $request->amount
        ]);
    }

    public function updateData(Request $request, Allowance $allowance)
    {
        $allowance->update([
            'name'              => $request->name,
            'designation_id'    => $request->designation['id'],
            'priode'            => $request->priode,
            'amount'            => $request->amount
        ]);
    }
 
}
