<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\Controller;
use App\Http\Resources\Hrm\EmployeeResoyrce;
use App\Observers\Hrm\EmployeeObserver;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeObserver;
    public function __construct(EmployeeObserver $employeeObserver)
    {
        $this->employeeObserver     = $employeeObserver;
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $data   = $this->employeeObserver->getData($request);

        $totalRows  = $data->count();
        $employees  = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'employees'       => EmployeeResoyrce::collection($employees),
        ]);
    }
    
    
}
