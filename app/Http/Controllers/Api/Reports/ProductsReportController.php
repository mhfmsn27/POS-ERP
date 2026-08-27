<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reports\VariationListResource;
use App\Observers\Inventory\VariationObserver;
use Illuminate\Http\Request;

class ProductsReportController extends Controller
{

    protected $variationObserver;

    public function __construct(VariationObserver $variationObserver)
    {
        $this->variationObserver        = $variationObserver;
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $data   = $this->variationObserver->getData($request);

        $totalRows  = $data->count();
        $products   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'products'      => VariationListResource::collection($products),
        ]);
    }

    public function minus(Request $request)
    {
        $limit = $request->input('limit', 10);
        $data   = $this->variationObserver->getData($request, 'minus');

        $totalRows  = $data->count();
        $products   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'products'      => VariationListResource::collection($products),
        ]);
    }
}
