<?php

namespace Poshub\Ecommerce\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Poshub\Ecommerce\Models\CustomerCart;
use Poshub\Ecommerce\Repositories\CartRepository;

class HomeController extends Controller
{

    protected $cardRepository;

    public function __construct(CartRepository $cardRepository)
    {
        $this->cardRepository         = $cardRepository;
    }


    public function index()
    {
        $totalCart = 0;
        if (Auth::guard('customers')->check()) {
            $totalCart  = CustomerCart::where("customer_id", Auth::guard('customers')->user()->id)->count();
        }

        return view('ecommerce::mobile.home', ['page' => 'Home Page'], compact('totalCart'));
    }
}
