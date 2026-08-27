<?php

namespace App\Observers\Inventory;

use App\Models\Product\Rak;
use Illuminate\Http\Request;

class RakObserver
{
    public function getData(Request $request)
    {
        return Rak::orderBy('floor', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('rak', 'like', '%' . $request->name . '%')->orWhere('room', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createData(Request $request)
    {
        return Rak::create([
            'floor'         => $request->floor,
            'room'          => $request->room,
            'rak'           => $request->rak,
        ]);
    }

    public function updateData(Request $request, Rak $rak)
    {
        $rak->update([
            'floor'         => $request->floor,
            'room'          => $request->room,
            'rak'           => $request->rak,
        ]);
    }
}
