<?php

namespace Poshub\Ecommerce\Components\Mobile;

use App\Models\Product\Category;
use Illuminate\View\Component;

class MobileCategoryComponent extends Component
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
        $data = Category::limit(8)->get(['id', 'name', 'image']);
        return view('ecommerce::component.mobile.content.category-component', compact('data'));
    }
}
