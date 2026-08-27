<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\PrinterResource;
use App\Models\Admin\Printer;
use App\Observers\Master\PrinterObserver;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    protected $printerObserver; 

    public function __construct(PrinterObserver $printerObserver)
    {
        $this->printerObserver      = $printerObserver; 
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->printerObserver->getData($request);

        $totalRows      = $data->count();
        $printers       = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'printers'      => PrinterResource::collection($printers),
        ], 200);
    }

    public function store(Request $request)
    {
        try {


            $this->printerObserver->createData($request);

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

    public function update(Request $request, Printer $printer)
    {
        try {

            $this->printerObserver->updateData($request, $printer);

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

    public function delete(Printer $printer)
    {
        try { 

            $printer->delete(); 

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
