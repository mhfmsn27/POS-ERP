<?php

namespace Poshub\Ecommerce\Components\Mobile;

use App\Models\Admin\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;
use Poshub\Ecommerce\Models\CustomerCart;

class MobileHeaderOneComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $totalCart = 0;
        if (Auth::guard('customers')->check()) {
            $totalCart  = CustomerCart::where("customer_id", Auth::guard('customers')->user()->id)->count();
        }

        $store  = Store::find(Session::get('dfstore'));

        return view('ecommerce::component.mobile.header-style-1', compact('totalCart', 'store'));
    }
}
