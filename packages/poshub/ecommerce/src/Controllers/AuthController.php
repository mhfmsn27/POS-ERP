<?php

namespace Poshub\Ecommerce\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{
      public function index()
      {
            return view('ecommerce::auth.login');
      }

      public function login(Request $request)
      {
            $this->validate($request, [
                  'email'           => 'required|email',
                  'password'        => 'required|min:6',
            ]);

            $customer = Customer::where("email", $request->email)->first();

            if ($customer) {
                  if ($customer->store_id != session()->get('dfstore')) {
                        return redirect()->back()->with(['gagal'  => 'Akun anda belum terdaftar di toko ini']);
                  }
            }
            
            $credentials            = $request->only('email', 'password');

            if (Auth::guard('customers')->attempt($credentials)) {

                  $device = new Agent();
                  if (!$device->isMobile()) {
                        return redirect()->route('ecommerce.dashboard');
                  } else {
                        return redirect()->route('ecommerce.mobile.dashboard');
                  }
            } else {
                  return redirect()->back()->withErrors([
                        'email'     => 'Password atau Kata Sandi Salah'
                  ]);
            }
      }

      public function logout()
      {
            Auth::guard('customers')->logout();

            return redirect()->route('ecommerce.home');
      }
}
