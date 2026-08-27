<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reports\Customer\CustomerDueResource;
use App\Http\Resources\Reports\Customer\CustomerSaldoResource;
use App\Observers\Crm\CustomerObserver;
use Illuminate\Http\Request;

class PiutangReportController extends Controller
{
    protected $customerObserver;

    public function __construct(CustomerObserver $customerObserver)
    {
        $this->customerObserver     = $customerObserver;
    }

    public function index(Request $request)
    {
        $limit  = $request->input('limit', 10);
        $data   = $this->customerObserver->getData($request);

        $totalRows  = $data->count();
        $customers  = $data->paginate($limit); 

        return response()->json([
            'totalRows'     => $totalRows,
            'customers'     => CustomerDueResource::collection($customers),
        ]);
    }

    public function hutang(Request $request)
    {
        $limit  = $request->input('limit', 10);
        $data   = $this->customerObserver->getData($request);

        $totalRows  = $data->count();
        $customers  = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'customers'     => CustomerSaldoResource::collection($customers),
        ]);
    }
}
