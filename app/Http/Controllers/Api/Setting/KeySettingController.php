<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\KeySettingRequest;
use App\Models\Admin\KeySetting;
use Illuminate\Support\Facades\Gate;

class KeySettingController extends Controller
{

    public function data()
    {
        return   KeySetting::first([
            'id',
            'purchase_key',
            'purchase_return_key',
            'sell_key',
            'sell_return_key',
            'adjustment_key',
            'stock_transfer_key',
            'expense_key',
            'purchase_payment_key',
            'sell_payment_key',
            'expense_payment_key'
        ]);
    }


    public function index()
    {

        $settings   = $this->data();

        return response()->json([
            'purchase'              => $settings->purchase_key,
            'purchase_return'       => $settings->purchase_return_key,
            'sell'                  => $settings->sell_key,
            'sell_return'           => $settings->sell_return_key,
            'adjustment'            => $settings->adjustment_key,
            'stock_transfer'        => $settings->stock_transfer_key,
            'expense'               => $settings->expense_key,
            'purchase_payment'      => $settings->purchase_payment_key,
            'sell_payment'          => $settings->sell_payment_key,
        ]);
    }

    public function store(KeySettingRequest $request)
    {

        abort_if(Gate::denies('key'), 403);

        $settings = $this->data();

        $settings->update([
            'purchase_key'              => $request->purchase,
            'purchase_return_key'       => $request->purchase_return,
            'sell_key'                  => $request->sell,
            'sell_return_key'           => $request->sell_return,
            'adjustment_key'            => $request->adjustment,
            'stock_transfer_key'        => $request->stock_transfer,
            'expense_key'               => $request->expense,
            'purchase_payment_key'      => $request->purchase_payment,
            'sell_payment_key'          => $request->sell_payment,
        ]);

        return response()->json([
            'status'        => true,
            'message'       => 'Perubahan berhasil di simpan',
        ]);
    }
}
