<?php

namespace Poshub\Ecommerce\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{

    public function login()
    {
        return view('ecommerce::mobile.authentication.login', ['page' => 'Login']);
    }

    public function register()
    {
        return view('ecommerce::mobile.authentication.register',['page' => 'Daftar Akun']);
    }

    public function forgetPass()
    {
        return view('ecommerce::mobile.authentication.request',['page' => 'Lupa Password']);
    }

    public function resetPass()
    {
        return view('ecommerce::mobile.authentication.reset',['page' => 'Ganti Password']);
    }

    public function verify()
    {
        return view('ecommerce::mobile.authentication.verify',['page' => 'Verifikasi Email']);
    }

    
}
