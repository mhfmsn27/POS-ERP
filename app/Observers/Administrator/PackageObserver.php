<?php

namespace App\Observers\Administrator;

use App\Helper;
use App\Models\Admin\Package;
use App\Models\Admin\PackageDetail;
use Illuminate\Http\Request;

class PackageObserver
{
    public function getData(Request $request)
    {
        return Package::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return Package::create([
            'name'          => $request->name,
            'price'         => Helper::fresh_aprice($request->price),
            'limit_day'     => $request->limit_day,
            'description'   => $request->description
        ]);
    }

    public function updateData(Request $request, Package $package)
    {
        $package->update([
            'name'          => $request->name,
            'price'         => Helper::fresh_aprice($request->price),
            'limit_day'     => $request->limit_day,
            'description'   => $request->description
        ]);
    }

    public function createDetail(Request $request, Package $package)
    {
        if (isset($request->detail)) {
            $i  = 0;
            while ($i < count($request->detail)) {
                PackageDetail::create([
                    'name'              => $request->detail[$i],
                    'package_id'        => $package->id
                ]);

                $i++;
            }
        }
    }

    public function packageById($id)
    {
        return Package::find($id);
    }
}
