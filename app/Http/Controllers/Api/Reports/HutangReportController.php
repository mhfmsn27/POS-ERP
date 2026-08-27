<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reports\Supplier\SupplierDueResource;
use App\Http\Resources\Reports\Supplier\SupplierSaldoResource;
use App\Observers\Crm\SupplierObserver;
use Illuminate\Http\Request;

class HutangReportController extends Controller
{
    protected $supplierObserver;

    public function __construct(SupplierObserver $supplierObserver)
    {
        $this->supplierObserver     = $supplierObserver;
    }

    public function index(Request $request)
    {
        $limit  = $request->input('limit', 10);
        $data   = $this->supplierObserver->getData($request);

        $totalRows  = $data->count();
        $suppliers  = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'suppliers'     => SupplierDueResource::collection($suppliers),
        ]);
    }

    public function hutang(Request $request)
    {
        $limit  = $request->input('limit', 10);
        $data   = $this->supplierObserver->getData($request);

        $totalRows  = $data->count();
        $suppliers  = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'suppliers'     => SupplierSaldoResource::collection($suppliers),
        ]);
    }
}
