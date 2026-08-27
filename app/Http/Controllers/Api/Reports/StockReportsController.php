<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reports\Stocks\StockHistoryResource;
use App\Http\Resources\Reports\Stocks\StockResource;
use App\Observers\Inventory\StockObserver;
use Illuminate\Http\Request;

class StockReportsController extends Controller
{
    protected $stockObserver;

    public function __construct(StockObserver $stockObserver)
    {
        $this->stockObserver    = $stockObserver;
    }

    public function histories(Request $request)
    { 

        $limit  = $request->input('limit', 20);
        $data   = $this->stockObserver->getHistory($request);

        $totalRows      = $data->count();
        $histories      = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'histories'     => StockHistoryResource::collection($histories),
        ]);
    }

    public function stocks(Request $request)
    {
        // permission_check(Gate::denies('rak_view'), 403);

        $limit  = $request->input('limit', 20);
        $data   = $this->stockObserver->getData($request);

        $totalRows      = $data->count();
        $stocks         = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'stocks'        => StockResource::collection($stocks),
        ]);
    }
}
