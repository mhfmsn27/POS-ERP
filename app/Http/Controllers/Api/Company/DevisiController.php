<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\DepartmentResource;
use App\Models\Hrm\Department;
use App\Observers\Hrm\DepartmentObserver;
use Illuminate\Http\Request;

class DevisiController extends Controller
{
    protected $departmentObserver; 

    public function __construct(DepartmentObserver $departmentObserver)
    {
        $this->departmentObserver      = $departmentObserver; 
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->departmentObserver->getData($request);

        $totalRows      = $data->count();
        $departments       = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'departments'   => DepartmentResource::collection($departments),
        ], 200);
    }

    public function store(Request $request)
    {
        try {


            $this->departmentObserver->createData($request);

            return response()->json([
                'message'   => 'Data berhasil di tambahkan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }

    public function update(Request $request, Department $department)
    {
        try {

            $this->departmentObserver->updateData($request, $department);

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }

    public function delete(Department $department)
    {
        try { 

            $department->delete(); 

            return response()->json([
                'message'   => 'Data berhasil di hapus',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }
}
