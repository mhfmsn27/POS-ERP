<?php

namespace App\Http\Controllers\Api\Starter;

use App\Http\Controllers\Controller;
use App\Http\Resources\Starter\Package\PackageResources;
use App\Models\Admin\InternalSetting;
use App\Models\Admin\Package;
use App\Observers\Administrator\PackageObserver;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    protected $packageObserver;

    public function __construct(PackageObserver $packageObserver)
    {
        $this->packageObserver      = $packageObserver;
    }


    public function index(Request $request)
    {
        $packages         = $this->packageObserver->getData($request)->get();

        return response()->json([ 
            'packages'  => PackageResources::collection($packages),
        ], 200);
    }

    public function detail(Package $package)
    {

        $tax    = InternalSetting::first(['tax']);
        return response()->json([
            'tax'       => (float)$tax->tax,
            'detail'    => PackageResources::make($package),

        ]);
    }
}
