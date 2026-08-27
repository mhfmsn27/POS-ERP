<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\CuttingResource;
use App\Models\Salary\CuttingSalary;
use App\Observers\Hrm\CuttingObserver;
use Illuminate\Http\Request;

class CuttingController extends Controller
{
    protected $cuttingObserver; 

    public function __construct(CuttingObserver $cuttingObserver)
    {
        $this->cuttingObserver      = $cuttingObserver; 
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->cuttingObserver->getData($request);

        $totalRows      = $data->count();
        $cuttings       = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'cuttings'    => CuttingResource::collection($cuttings),
        ], 200);
    }

    public function store(Request $request)
    {
        try {

            $this->cuttingObserver->createData($request);

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

    public function update(Request $request, CuttingSalary $cutting)
    {
        try {

            $this->cuttingObserver->updateData($request, $cutting);

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

    public function delete(CuttingSalary $cutting)
    {
        try { 

            $cutting->delete(); 

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
