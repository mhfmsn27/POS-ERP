<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Observers\Saas\MerchantObserver;
use App\Observers\Saas\StoreObserver;
use Illuminate\Http\Request;

class AdministratorComponentController extends Controller
{
    protected $merchantObserver;
    protected $storeObserver;

    public function __construct(MerchantObserver $merchantObserver, StoreObserver $storeObserver)
    {
        $this->merchantObserver         = $merchantObserver;
        $this->storeObserver            = $storeObserver;
    }

    public function stores(Request $request)
    {
        $stores     = $this->storeObserver->getData($request)->limit(20)->get(['id', 'name']);

        return response()->json($stores);
    }

    public function merchants(Request $request)
    {
        $merchants      = $this->merchantObserver->getData($request)->limit(20)->get(['id', 'name']);

        return response()->json($merchants);
    }
}
