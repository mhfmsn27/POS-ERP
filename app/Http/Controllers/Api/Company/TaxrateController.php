<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\TaxrateResource;
use App\Models\Admin\Taxrate;
use App\Observers\Master\TaxrateObserver;
use Illuminate\Http\Request;

class TaxrateController extends Controller
{
    protected $taxObserver; 

    public function __construct(TaxrateObserver $taxObserver)
    {
        $this->taxObserver      = $taxObserver; 
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->taxObserver->getData($request);

        $totalRows      = $data->count();
        $taxrates       = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'taxrates'      => TaxrateResource::collection($taxrates),
        ], 200);
    }

    public function store(Request $request)
    {
        try {

            $this->taxObserver->createData($request);

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

    public function update(Request $request, Taxrate $taxrate)
    {
        try {

            $this->taxObserver->updateData($request, $taxrate);

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

    public function delete(taxrate $taxrate)
    {
        try { 

            $taxrate->delete(); 

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
