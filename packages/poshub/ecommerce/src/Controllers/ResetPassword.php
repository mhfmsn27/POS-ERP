<?php

namespace Poshub\Ecommerce\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;
use Poshub\Ecommerce\Notifications\ForgetPasswordNotify;

class ResetPassword extends Controller
{

      public function index()
      {
            return view('ecommerce::auth.forget');
      }

      public function reset()
      {
            return view('ecommerce::auth.reset');
      }

      public function forgetPassword(Request $request)
      {
            $this->validate($request, [
                  'email'           => 'required|email',
            ]);

            $users            = Customer::where("email", $request->email)->first();
 
            if ($users) {
                  if ($users->store_id != session()->get('dfstore')) {
                        return redirect()->back()->with(['gagal'  => 'Akun anda belum terdaftar di toko ini']);
                  }
            }

            if ($users) {
                  $users->generateTwoFactorCode();
                  $users->notify(new ForgetPasswordNotify());

                  $device = new Agent();
                  if (!$device->isMobile()) {
                        return redirect()->route('ecommerce.reset');
                  } else {
                        return redirect()->route('ecommerce.mobile.reset');
                  }
            }

            return redirect()->back()->withErrors([
                  'email'     => 'Email ini tidak valid.'
            ]);
      }

      public function resetPassword(Request $request)
      {
            $this->validate($request, [
                  'password'                                => 'required|min:8|max:20',
                  'password_confirmation'                   => 'required',
                  'code'                                    => 'required|min:6'
            ]);

            $user = Customer::where("code_verify_email", $request->code)->first();

            if (!$user) {
                  return redirect()->back()->withErrors([
                        'email'     => 'Kode dua faktor yang Anda masukkan tidak cocok'
                  ]);
            }

            if ($request->password != $request->password_confirmation) {
                  return redirect()->back()->withErrors([
                        'email'     => 'Password dan Konfirmasi Password Harus Sama'
                  ]);
            }

            if ($user->code_verify_email) {
                  if ($user->verify_expire->lt(now())) {

                        $user->resetTwoFactorCode();

                        return redirect()->back()->withErrors([
                              'email'     => 'Kode dua faktor telah kedaluwarsa. Silakan Coba kembali.'
                        ]);
                  }
            }


            if ($request->input('code') == $user->code_verify_email) {

                  $user->update([
                        'password'  => Hash::make($request->password)
                  ]);

                  $device = new Agent();
                  if (!$device->isMobile()) {
                        return redirect()->route('ecommerce.login')->with(['success' => 'Reset Password Berhasil di lakukan']);
                  } else {
                        return redirect()->route('ecommerce.mobile.login')->with(['success' => 'Reset Password Berhasil di lakukan']);
                  }
            }
      }
}
