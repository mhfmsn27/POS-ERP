<?php

namespace Poshub\Ecommerce\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\AccountSetting;
use App\Models\Admin\Customer;
use App\Models\Admin\Store;
use App\Models\Admin\TermPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;
use Poshub\Ecommerce\Notifications\EmailVerifyNotify;

class RegisterController extends Controller
{

      public function index()
      {
            return view('ecommerce::auth.register');
      }

      public function verify()
      {
            return view('ecommerce::auth.verify');
      }

      public function signup(Request $request)
      {
            $this->validate($request, [
                  'name'                                      => 'required|regex:/^[\pL\s\-]+$/u|min:4|max:200',
                  'password'                                  => 'required|min:8|max:20',
                  'password_confirmation'                     => 'required',
                  'phone'                                     => 'numeric',
            ]);

            $customer = Customer::where("email", $request->email)->first();

            if ($customer) {
                  if ($customer->store_id == session()->get('dfstore')) {
                        return redirect()->back()->with(['gagal'  => 'Akun anda telah terdaftar sebelumnya di toko ini']);
                  }
            }

            if ($request->password != $request->password_confirmation) {
                  return redirect()->back()->withErrors([
                        'email'     => 'Password dan Konfirmasi Password Harus Sama'
                  ]);
            }

            $store          = Store::find(session()->get('dfstore'));
            $settingAccount = AccountSetting::first(['customer_debt', 'customer_debt_imprest']);
            $defaultTerm    = TermPayment::where("default", "yes")->first();

            $customer = Customer::create([
                  'name'                  => $request->name,
                  'password'              => Hash::make($request->password),
                  'email'                 => $request->email,
                  'term_payment'          => $defaultTerm ? $defaultTerm->id : null,
                  'phone'                 => $request->phone,
                  'store_id'              => session()->get('dfstore'),
                  'debt'                  => $settingAccount ? $settingAccount->customer_debt : null,
                  'debt_imprest'          => $settingAccount ? $settingAccount->customer_debt_imprest : null,
                  'tax_option'            => $store->tax_option == 'active' ? 'yes' : 'no',
                  'tax_default'           => 'yes'
            ]);

            $customer->generateTwoFactorCode();
            $customer->notify(new EmailVerifyNotify());

            $credentials      = $request->only('email', 'password');

            if (Auth::guard('customers')->attempt($credentials)) {
                  $device = new Agent();
                  if (!$device->isMobile()) {
                        return redirect()->route('ecommerce.verify');
                  } else {
                        return redirect()->route('ecommerce.mobile.verify');
                  }
            }
      }

      public function emailVerify(Request $request)
      {
            $this->validate($request, [
                  'code'            => 'required|min:6'
            ]);

            $user = Customer::find(Auth::guard('customers')->user()->id);

            if ($user->code_verify_email) {
                  if ($user->verify_expire->lt(now())) {

                        $user->resetTwoFactorCode();

                        return redirect()->back()->withErrors([
                              'email'     => 'Kode dua faktor telah kedaluwarsa. Silakan Coba kembali.'
                        ]);
                  }
            }

            if ($user->email_verify != null) {
                  return redirect()->back()->withErrors([
                        'email'     => 'Anda Sudah melakukan verifikasi sebelumnya'
                  ]);
            }

            if ($request->input('code') == $user->code_verify_email) {

                  $user->resetTwoFactorCode();
                  $user->email_verify = now();
                  $user->save();

                  $device = new Agent();
                  if (!$device->isMobile()) {
                        return redirect()->route('ecommerce.dashboard');
                  } else {
                        return redirect()->route('ecommerce.mobile.dashboard');
                  }
            }

            return redirect()->back()->withErrors([
                  'email'     => 'Kode dua faktor yang Anda masukkan tidak cocok'
            ]);
      }

      public function reSendEmailVerify(Request $request)
      {
            $user = Customer::find(Auth::guard('customers')->user()->id);

            if ($user->email_verify != null) {
                  return response()->json([
                        'status' => false,
                        'message' => 'Email anda sudah ter-verifikasi sebelumnya',
                  ], 200);
            }

            $user->generateTwoFactorCode();
            $user->notify(new EmailVerifyNotify());

            return redirect()->back()->with(['success' => 'Kode Verifikasi berhasil di kirimkan kembali']);
      }
}
