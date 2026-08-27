<?php

namespace Poshub\Ecommerce\Components\Mobile;

use App\Models\Admin\Store;
use Illuminate\View\Component;

class MobileFooterComponent extends Component
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
        $stores = Store::all();
        return view('ecommerce::component.mobile.footer-component', compact('stores'));
    }
}
