<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('store.choose')->with([
            'sukses' => 'POSHUB Enterprise Edition: Seluruh fitur dan cabang telah aktif permanen (Unlimited Lifetime).'
        ]);
    }
}
