<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\Controller;
use App\Http\Resources\Hrm\DepartmentResource;
use App\Observers\Hrm\DepartmentObserver;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    protected $departmentObserver;

    public function __construct(DepartmentObserver $departmentObserver)
    {
        $this->departmentObserver       = $departmentObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Departments List
    |--------------------------------------------------------------------------
    */

    public function departments(Request $request)
    {

        $limit  = $request->input('limit', 10);
        $data   = $this->departmentObserver->getData($request);

        $totalRows      = $data->count();
        $departments    = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'departments'   => DepartmentResource::collection($departments),
        ]);
    }
}
