<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Http\Resources\Master\TaxrateResource;
use App\Models\Admin\Store;
use App\Observers\Master\TaxrateObserver;
use Illuminate\Http\Request;

class TaxrateController extends Controller
{
    protected $taxrateObserver;

    public function __construct(TaxrateObserver $taxrateObserver)
    {
        $this->taxrateObserver      = $taxrateObserver;
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 20);
        $data   = $this->taxrateObserver->getData($request);

        $totalRows      = $data->count();
        $taxrates       = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'taxrates'      => TaxrateResource::collection($taxrates),
        ]);
    }

    public function settings()
    {
        $store = Store::where("id", my_store())->first(['tax_option', 'tax_one', 'tax_two', 'tax_tree', 'accountant_use']);

        return response()->json([
            'with_tax'          => $store->tax_option == 'active' ? true : false,
            'tax_one'           => (float)$store->tax_one,
            'tax_two'           => (float)$store->tax_two,
            'tax_tree'          => (float)$store->tax_tree,
            'default'           => false,
            'accountant_use'    => $store->accountant_use,
            'type'              => 'add',
            'customer_type'     => 'general'
        ]);
    }
}
