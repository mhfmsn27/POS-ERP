<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Admin\InternalSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = InternalSetting::first();
        return view('super.settings', ['page' => 'Pengaturan'], compact('settings'));
    }

    public function update(Request $request)
    {

        $whitelogo      = $request->white_logo ? $this->uploadImage($request, 'white_logo', 'settings') : '';
        $darkLogo       = $request->dark_logo ? $this->uploadImage($request, 'dark_logo', 'settings') : '';
        $settings       = InternalSetting::first();

        $settings->update([
            'white_logo'        => $whitelogo != '' ? $whitelogo : $settings->white_logo,
            'dark_logo'         => $darkLogo != '' ? $darkLogo : $settings->dark_logo,
            'tax'               => $request->tax,
            'midtrans_key'      => $request->midtrans_key,
            'midtrans_client'   => $request->midtrans_client,
            'midtrans_server'   => $request->midtrans_server,
            'whatsapp_server'   => $request->whatsapp_server,
            'whatsapp_phone'    => $request->whatsapp_phone,
        ]);

        return redirect()->back()->with(['flash' => 'Data pengaturan berhasil di perbaharui']);
    }
}
