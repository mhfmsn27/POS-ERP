<?php

namespace App\Http\Controllers\Api\Tax;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tax\TaxNoRefRequest;
use App\Http\Resources\Tax\TaxNoRefDetailResource;
use App\Http\Resources\Tax\TaxNoRefResource;
use App\Models\Tax\TaxNoRef;
use App\Models\Tax\TaxNoRefDetail;
use App\Observers\Tax\TaxNoRefObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxNoRefController extends Controller
{
    protected $taxNoRefObserver;

    public function __construct(TaxNoRefObserver $taxNoRefObserver)
    {
        $this->taxNoRefObserver     = $taxNoRefObserver;
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->taxNoRefObserver->getData($request);

        $totalRows      = $data->count();
        $taxs           = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'taxs'          => TaxNoRefResource::collection($taxs),
        ], 200);
    }

    public function details(Request $request, TaxNoRef $taxes)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->taxNoRefObserver->getDetail($request, $taxes);

        $totalRows      = $data->count();
        $details        = $data->paginate($limit);
        

        return response()->json([
            'totalRows'     => $totalRows,
            'details'       => TaxNoRefDetailResource::collection($details),
        ], 200);
    }

    public function store(TaxNoRefRequest $request)
    {
        try {

            $validation = TaxNoRefDetail::where(function ($q) use ($request) {
                return $q->where("number", $request->from)->orWhere("number", $request->to);
            })->count();

            if ($validation > 0) {
                return response()->json([
                    'message'   => 'Nomor ini sudah ada sebelumnya',
                    'status'    => false
                ], 422);
            }

            DB::beginTransaction();

            $taxes    = $this->taxNoRefObserver->createData($request);
            $this->taxNoRefObserver->createDetails($taxes);


            DB::commit();

            return response()->json([
                'message'       => "Penomoran pajak berhasil di simpan",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }

    public function delete(TaxNoRef $taxes)
    {
        try {

            $validation = TaxNoRefDetail::where(function ($q) use ($taxes) {
                return $q->where("tax_no_ref_id", $taxes->id)->where("transaction_id", "!=", null);
            })->count();

            if ($validation > 0) {
                return response()->json([
                    'message'   => 'Nomor ini sudah tidak dapat di hapus',
                    'status'    => false
                ], 422);
            }

            DB::beginTransaction();

            $taxes->details()->delete();
            $taxes->delete();

            DB::commit();

            return response()->json([
                'message'       => "Penomoran pajak berhasil di hapus",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }

    public function getNumber()
    {
        $data = $this->taxNoRefObserver->getNumberNew();

        return response()->json([ 
            'number'          => $data ? $data->number : null,
        ], 200);
    }
}
