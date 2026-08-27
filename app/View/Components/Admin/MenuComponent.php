<?php

namespace App\View\Components\Admin;

use App\Models\Plugins;
use Illuminate\View\Component;

class MenuComponent extends Component
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
        $plugin = Plugins::where('status', '1')->get();
        return view('components.admin.menu-component', compact("plugin"));
    }
}
