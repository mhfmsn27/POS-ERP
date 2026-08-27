<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hrm\GenerateSalaryRequest;
use App\Http\Resources\Hrm\SalaryDetailResource;
use App\Http\Resources\Hrm\SalaryGenerateResource;
use App\Http\Resources\Hrm\SalaryListResource;
use App\Models\Salary\Salary;
use App\Observers\Hrm\EmployeeObserver;
use App\Observers\Hrm\SalaryObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SalaryController extends Controller
{
    protected $salaryObserver;
    protected $employeeObserver;

    public function __construct(SalaryObserver $salaryObserver, EmployeeObserver $employeeObserver)
    {
        $this->salaryObserver       = $salaryObserver;
        $this->employeeObserver     = $employeeObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Salaries List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        abort_if(Gate::denies('salary_view'), 403);

        $limit  = $request->input('limit', 10);
        $data   = $this->salaryObserver->getData($request)->orderBy("created_at", "desc");

        $totalRows      = $data->count();
        $salaries       = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'salaries'      => SalaryListResource::collection($salaries),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | 2. Salary Detail
    |--------------------------------------------------------------------------
    */

    public function detail(Salary $salary)
    {
        abort_if(Gate::denies('salary_view'), 403);

        return response()->json([
            'detail'    => SalaryDetailResource::make($salary),
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | 3. Generate Salary
    |--------------------------------------------------------------------------
    */

    public function generate(GenerateSalaryRequest $request)
    {

        abort_if(Gate::denies('salary_create'), 403);

        $employees = $this->employeeObserver->getData($request)->get();

        return response()->json([
            'message'   => 'Data Gaji berhasil di ambil!',
            'status'    => true,
            'data'      => SalaryGenerateResource::collection($employees)
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | 4. Salary Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        abort_if(Gate::denies('salary_create'), 403);

        try {

            DB::beginTransaction();

            $this->salaryObserver->createData($request);

            DB::commit();

            return response()->json([
                'message'   => 'Slip Gaji Pegawai berhasil di simpan',
                'status'    => true,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 5. Update Salary
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Salary $salary)
    {

        abort_if(Gate::denies('salary_update'), 403);

        try {
            $transaction = DB::transaction(function () use ($request, $salary) {

                $this->salaryObserver->paySalary($request, $salary);

                return response()->json([
                    'message'   => 'Pembayaran Sudah di lakukan',
                    'status'    => true
                ], 200);
            });

            return $transaction;
        } catch (\Exception $e) {
            return response()->json([
                'line'      => $e->getLine(),
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Salary
    |--------------------------------------------------------------------------
    */

    public function delete(Salary $salary)
    {

        abort_if(Gate::denies('salary_delete'), 403);

        try {
            $transaction = DB::transaction(function () use ($salary) {

                $this->salaryObserver->delete($salary);

                return response()->json([
                    'message'   => 'Data berhasil di hapus',
                    'status'    => true
                ], 200);
            });

            return $transaction;
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }
}
