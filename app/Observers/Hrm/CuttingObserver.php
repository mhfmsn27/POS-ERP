<?php

namespace App\Observers\Hrm;

use App\Models\Salary\CuttingSalary;
use Illuminate\Http\Request;

class CuttingObserver
{
    public function getData(Request $request)
    {
        return CuttingSalary::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return CuttingSalary::create([
            'name'              => $request->name,
            'designation_id'    => $request->designation['id'],
            'priode'            => $request->priode,
            'amount'            => $request->amount
        ]);
    }

    public function updateData(Request $request, CuttingSalary $cutting)
    {
        $cutting->update([
            'name'              => $request->name,
            'designation_id'    => $request->designation['id'],
            'priode'            => $request->priode,
            'amount'            => $request->amount
        ]);
    }
}
