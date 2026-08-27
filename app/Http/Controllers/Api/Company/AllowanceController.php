<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller; 
use App\Http\Resources\Company\AllowanceResource as CompanyAllowanceResource;
use App\Models\Salary\Allowance;
use App\Observers\Hrm\AllowanceObserver;
use Illuminate\Http\Request;

class AllowanceController extends Controller
{
    protected $allowanceObserver; 

    public function __construct(AllowanceObserver $allowanceObserver)
    {
        $this->allowanceObserver      = $allowanceObserver; 
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->allowanceObserver->getData($request);

        $totalRows      = $data->count();
        $allowances   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'allowances'    => CompanyAllowanceResource::collection($allowances),
        ], 200);
    }

    public function store(Request $request)
    {
        try {

            $this->allowanceObserver->createData($request);

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

    public function update(Request $request, Allowance $allowance)
    {
        try {

            $this->allowanceObserver->updateData($request, $allowance);

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

    public function delete(allowance $allowance)
    {
        try { 

            $allowance->delete(); 

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
