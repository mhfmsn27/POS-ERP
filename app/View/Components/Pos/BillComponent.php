<?php

namespace App\View\Components\Pos;

use App\Models\Admin\Customer;
use App\Models\Admin\Taxrate;
use App\Models\Product\Discount;
use Illuminate\View\Component;

class BillComponent extends Component
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
        $discount = Discount::orderBy("discount_amount", "asc")->get();
        $taxrate = Taxrate::orderBy("taxrate", "asc")->get();
        return view('components.pos.bill-component', compact('discount', 'taxrate'));
    }
}
