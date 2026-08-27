<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\DesignationResource;
use App\Models\Hrm\Designation;
use App\Observers\Hrm\DesignationObserver;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    protected $designationObserver; 

    public function __construct(DesignationObserver $designationObserver)
    {
        $this->designationObserver      = $designationObserver; 
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->designationObserver->getData($request);

        $totalRows      = $data->count();
        $designations   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'designations'  => DesignationResource::collection($designations),
        ], 200);
    }

    public function store(Request $request)
    {
        try {


            $this->designationObserver->createData($request);

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

    public function update(Request $request, Designation $designation)
    {
        try {

            $this->designationObserver->updateData($request, $designation);

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

    public function delete(Designation $designation)
    {
        try { 

            $designation->delete(); 

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
