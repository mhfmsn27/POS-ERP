<?php

namespace Poshub\Ecommerce\Components\Mobile;

use App\Models\Product\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;

class MobileProductComponent extends Component
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
        $data = Product::where(function ($q) {
            $q->whereHas('general_store', function ($query) {
                return  $query->selectRaw("sum(qty_available) as qty")->havingRaw('qty > ?', [0])->where("store_id", Session::get('dfstore'));
            });
        })->orderBy("name", "desc")->limit(20)->get();
        return view('ecommerce::component.mobile.content.product-component', compact('data'));
    }
}
