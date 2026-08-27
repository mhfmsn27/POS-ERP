<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reports\Commission\UserCommissionDetailResource;
use App\Http\Resources\Reports\Commission\UserCommissionResource;
use App\Models\User;
use App\Observers\Master\UserObserver;
use App\Observers\Transaction\CommissionObserver;
use App\Observers\Transaction\Sales\SalesObserver;
use Illuminate\Http\Request;

class CommissionReportController extends Controller
{
    protected $userObserver;
    protected $commissionObserver;
    protected $salesObserver;

    public function __construct(UserObserver $userObserver, CommissionObserver $commissionObserver, SalesObserver $salesObserver)
    {
        $this->userObserver         = $userObserver;
        $this->commissionObserver   = $commissionObserver;
        $this->salesObserver        = $salesObserver;
    }

    public function index(Request $request)
    {
        $limit      = $request->input('limit', 20);
        $data       = $this->userObserver->getData($request);

        $totalRows      = $data->count();
        $taxrates       = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => UserCommissionResource::collection($taxrates),
        ]);
    }

    public function detail(User $user, Request $request)
    {
        $limit      = $request->input('limit', 20);
        $data       = $this->commissionObserver->getData($request);
        $sales      = $this->salesObserver->getData($request, $user->id);

        $summary    = array(
            'total_faktur'          => $sales->where("payment_status", "paid")->sum('final_total'),
            'total_commission'      => $sales->where("payment_status", "paid")->sum('commission_contact_total'),
            'total_transaction'     => $sales->where("payment_status", "paid")->count()
        );

        $totalRows      = $data->count();
        $reports        = $data->paginate($limit);
        

        return response()->json([
            'totalRows'     => $totalRows,
            'summary'       => $summary,
            'transactions'  => UserCommissionDetailResource::collection($reports),
        ]);
    }
}
