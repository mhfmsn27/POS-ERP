<?php

namespace Poshub\Ecommerce\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\Customer;
use App\Models\Transaction\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{

      public function index()
      {
            $data = array(
                  'pending'   => Transaction::where("customer_id", Auth::guard('customers')->user()->id)->where("status", "hold")->count(),
                  'process'   => Transaction::where("customer_id", Auth::guard('customers')->user()->id)->where("status", "ordered")->count(),
                  'transit'   => Transaction::where("customer_id", Auth::guard('customers')->user()->id)->where("status", "transit")->count(),
                  'complete'  => Transaction::where("customer_id", Auth::guard('customers')->user()->id)->where("status", "final")->count(),
                  'list'      => Transaction::where("customer_id", Auth::guard('customers')->user()->id)->orderBy("id","desc")->limit(10)->get(),
            );

            return view('ecommerce::account.dashboard', compact('data'));
      }

      public function address()
      {
            return view('ecommerce::account.address');
      }

      public function profile()
      {
            return view('ecommerce::account.profile');
      }

      public function changeProfile(Request $request)
      {
            $customer                     = Customer::find(Auth::guard('customers')->user()->id);

            $this->validate($request, [
                  'name'                  => 'required|regex:/^[\pL\s\-]+$/u|min:4|max:200',
                  'email'                 => "required|email|unique:users,email,{$customer->id},id,deleted_at,NULL",
                  'phone'                 => 'required|numeric|min:1',
            ]);

            $customer->update([
                  'name'                  => $request->name,
                  'email'                 => $request->email,
                  'phone'                 => $request->phone,
                  'address'               => $request->address
            ]);

            return redirect()->back()->with(['success' => 'Profil Berhasil di perbaharui']);
      }

      public function changePassword(Request $request)
      {
            $customer                     = Customer::find(Auth::guard('customers')->user()->id);

            $this->validate($request, [
                  'new_password'          => 'required|min:8',
                  'con_password'          => 'required|min:8',
                  'old_password'          => 'required'
            ]);

            if ($request->new_password != $request->con_password) {
                  return redirect()->back()->withErrors([
                        'email'           => 'Kombinasi Password baru dan konfirmasi password harus sama'
                  ]);
            }

            $checkPass = Customer::where("password", "!=", Hash::check($request->old_password, $customer->password))->first();

            if ($checkPass == null) {
                  return redirect()->back()->withErrors([
                        'email'           => 'Maaf, Password Lama Anda Salah'
                  ]);
            }

            $customer->update([
                  'password'              => Hash::make($request->new_password),
            ]);

            return redirect()->back()->with(['success' => 'Password Berhasil di perbaharui']);
      }
}
