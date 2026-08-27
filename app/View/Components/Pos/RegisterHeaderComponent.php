<?php

namespace App\View\Components\Pos;

use App\Models\Transaction\ShiftRegister;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;

class RegisterHeaderComponent extends Component
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
        $getShift = ShiftRegister::whereYear("created_at", date('Y'))
        ->whereMonth("created_at", date('m'))
        ->whereDay("created_at", date('d'))
        ->where("status", "open")
        ->where("store_id", my_store())
        ->first(); 
        return view('components.pos.register-header-component',compact('getShift'));
    }
}
