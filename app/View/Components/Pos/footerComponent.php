<?php

namespace App\View\Components\pos;

use App\Models\Transaction\PaymentMethod;
use Illuminate\View\Component;

class FooterComponent extends Component
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
        $payment        = PaymentMethod::get(['id', 'name', 'service', 'amount']);
        return view('components.pos.footer-component', compact('payment'));
    }
}
