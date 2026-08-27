<?php

namespace Poshub\Ecommerce\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Admin\Setting;
use Illuminate\Http\Request;

class AccountController extends Controller
{

      public function index()
      {
            $setting = Setting::first(['default_phone']);
            return view('ecommerce::mobile.account.dashboard', ['page' => 'Akun Saya'], compact('setting'));
      }

      public function changeProfile()
      {
            return view('ecommerce::mobile.account.profile', ['page' => 'Ubah Profil']);
      }

      public function changePassword()
      {
            return view('ecommerce::mobile.account.password', ['page' => 'Ubah Password']);
      }
}
