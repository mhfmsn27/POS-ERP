<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\Controller;
use App\Http\Resources\Hrm\CommissionResource;
use App\Observers\Transaction\CommissionObserver;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    protected $commissionObserver; 

    public function __construct(CommissionObserver $commissionObserver)
    {
        $this->commissionObserver            = $commissionObserver;
    }

    public function index(Request $request)
    {

        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->commissionObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => CommissionResource::collection($transactions),
        ], 200);
    }
}
