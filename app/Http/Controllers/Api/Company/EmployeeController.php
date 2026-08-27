<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\EmployeeDetailResource;
use App\Http\Resources\Company\EmployeeListResource;
use App\Models\Hrm\Employee;
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
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->employeeObserver->getData($request);

        $totalRows  = $data->count();
        $employees  = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'employees'     => EmployeeListResource::collection($employees),
        ], 200);
    }

    public function detail(Employee $employee)
    {
        return response()->json(EmployeeDetailResource::make($employee), 200);
    }

    public function store(Request $request)
    {
        try {
            $this->employeeObserver->createData($request);
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

    public function update(Request $request, Employee $employee)
    {
        try {
            $this->employeeObserver->updateData($request, $employee);
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

    public function delete(Employee $employee)
    {
        try {

            $this->employeeObserver->deleteData($employee);

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
