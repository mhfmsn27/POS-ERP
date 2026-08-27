<?php

namespace App\Observers\Master;

use App\Models\Transaction\SmartlinkBank;
use Illuminate\Http\Request;

class SmartlinkBankObserver
{
    public function getData(Request $request)
    {
        return SmartlinkBank::orderBy('rekening', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('rekening', 'like', '%' . $request->name . '%') : '';
        });
    }


    public function createData(Request $request)
    {
        return SmartlinkBank::create([
            'type'              => $request->type,
            'rekening'          => $request->rekening,
            'account_id'        => $request->account['id']
        ]);
    }

    public function updateData(Request $request, SmartlinkBank $smartlink)
    {
        $smartlink->update([
            'type'              => $request->type,
            'rekening'          => $request->rekening,
            'account_id'        => $request->account['id']
        ]);
    }
}
