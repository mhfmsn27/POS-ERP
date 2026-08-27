<?php

namespace App\Http\Controllers;

use App\Models\Admin\Setting;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::check()) {
            return $this->redirect();
        }

        $data = Setting::first();
        return view('auth.login', ['page' => __('signin')], compact('data'));
    }

    /**
     * Redirect authenticated users to appropriate enterprise dashboards.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect()
    { 
        if (!auth()->check()) {
            return redirect()->route('page.auth');
        }

        if (auth()->user()->role_type == 'administrator') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('page.home');
    }
}
