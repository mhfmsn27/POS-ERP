<?php

namespace App\Observers\Account;

use App\Models\Account\AccountType;
use Illuminate\Http\Request;

class TypeObserver
{
    public function getData(Request $request)
    {
        return AccountType::orderBy('coa_code', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%')->orWhere('coa_code', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function getByType(String $code)
    {
        return AccountType::where(function($q) use ($code) {
            return $code != '' ? $q->where("coa_code",$code) : '';
        })->first();
    }

    public function createData(Request $request)
    {
        return AccountType::create([
            'name'              => $request->name,
            'edit_option'       => 'yes',
            'coa_code'          => $request->coa_code,
            'with_price'        => $request->price == true ? 'yes' : 'no',
            'with_modal'        => $request->modal == true ? 'yes' : 'no',
            'type'              => $request->type
        ]);
    }

    public function updateData(Request $request, AccountType $type)
    {
        $type->update([
            'name'              => $request->name,
            'coa_code'          => $request->coa_code,
            'with_price'        => $request->price == true ? 'yes' : 'no',
            'with_modal'        => $request->modal == true ? 'yes' : 'no',
            'type'              => $request->type,
        ]);
    }
}
