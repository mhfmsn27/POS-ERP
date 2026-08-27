<?php

namespace App\View\Components\Admin\Modal;

use App\Models\Product\Category;
use Illuminate\View\Component;

class CategoryComponent extends Component
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
        $category = Category::where('is_root_parent', "1")->orderBy("id", "desc")->get(['id', 'name']);
        return view('components.admin.modal.category-component', compact("category"));
    }
}
