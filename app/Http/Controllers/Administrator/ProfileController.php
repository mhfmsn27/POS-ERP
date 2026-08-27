<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('super.profile', ['page' => 'Profil Saya']);
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'old_password'      => 'required',
            'new_password'      => 'required',
            'confirm_password'  => 'required'
        ]);

        $user       = User::where("id", auth()->user()->id)->first();

        $checking   = User::where("password", "!=", Hash::check($request->old_password, $user->password))->first();

        if (!$checking) {
            return redirect()->back()->with(['gagal'    => 'Maaf, Password lama anda salah!']);
        }

        if ($request->new_password != $request->confirm_password) {
            return redirect()->back()->with(['gagal'    => 'Password baru dan konfirmasi password harus sama']);
        }

        $user->update([
            'password'      => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with(['flash' => 'Password berhasil di ubah']);
    }

    public function changeProfile(Request $request)
    {
        $this->validate($request, [
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email,' . auth()->user()->id,
            'phone'         => 'required|numeric',
            'jk'            => 'required|in:pria,wanita',
            'photo'         => 'mimes:png,jpg,jpeg'
        ]);

        $image      = $request->photo ? $this->uploadImage($request, 'photo', 'users') : null;

        User::where("id", auth()->user()->id)->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'jk'        => $request->jk,
            'photo'     => $image != null ? $image : auth()->user()->photo
        ]);

        return redirect()->back()->with(['flash' => 'Data profile berhasil di perbaharui']);
    }
}
