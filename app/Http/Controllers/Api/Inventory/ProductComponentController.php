<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Resources\Inventory\Products\VariatioDetailResource;
use App\Http\Resources\Inventory\RakComponentResource;
use App\Observers\Inventory\BrandObserver;
use App\Observers\Inventory\CategoryObserver;
use App\Observers\Inventory\RakObserver;
use App\Observers\Inventory\UnitObserver;
use App\Observers\Inventory\VariationObserver;
use Illuminate\Http\Request;

class ProductComponentController extends Controller
{
    protected $categoryObserver;
    protected $brandOberver;
    protected $unitObserver;
    protected $productObserver;
    protected $rakObserver;
    protected $variationObserver;

    public function __construct(CategoryObserver $categoryObserver, BrandObserver $brandOberver, UnitObserver $unitObserver, RakObserver $rakObserver, VariationObserver $variationObserver)
    {
        $this->categoryObserver         = $categoryObserver;
        $this->brandOberver             = $brandOberver;
        $this->unitObserver             = $unitObserver;
        $this->rakObserver              = $rakObserver;
        $this->variationObserver        = $variationObserver;
    }

    public function category(Request $request)
    {
        $data       = $this->categoryObserver->getSimple($request)->withCount(['children' => function ($query) {
            $query->withoutGlobalScopes();
        }])->where('store_id', my_store())->having('children_count', 0);
        $categories = $data->limit(10)->get(['id', 'name']);

        return response()->json([
            'categories'    => $categories,
        ]);
    }

    public function brands(Request $request)
    {
        $data       = $this->brandOberver->getData($request);
        $brands     = $data->limit(10)->get(['id', 'name']);

        return response()->json([
            'brands'    => $brands,
        ]);
    }

    public function units(Request $request)
    {
        $data       = $this->unitObserver->getData($request);
        $units      = $data->limit(20)->get(['id', 'name']);

        return response()->json([
            'units'    => $units,
        ]);
    }

    public function raks(Request $request)
    {
        $data       = $this->rakObserver->getData($request);
        $raks       = $data->limit(10)->get();

        return response()->json([
            'raks'    => RakComponentResource::collection($raks),
        ]);
    }

    public function variations(Request $request)
    {

        $limit  = $request->input('limit', 20);
        $data   = $this->variationObserver->getData($request);

        $totalRows  = $data->count();
        $variations = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'variations'    => VariatioDetailResource::collection($variations),
        ]);
    }
}
