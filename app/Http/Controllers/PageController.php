<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{

    public function index()
    {
        return view('pages.app', ['page' => 'Faktur Online']);
    }

    public function panel()
    {
        return view('pages.panel', ['page' => 'Faktur Online']);
    }


    public function auth()
    {
        return view('pages.login', ['page'      => 'Authentication']);
    }

    public function starter()
    {
        return view('pages.starter', ['page'     => 'Memulai Faktur Online']);
    }
}
