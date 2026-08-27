<?php

namespace Poshub\Ecommerce\Components\Content;

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
            $data       = Category::where("store_id", session()->get('dfstore'))->withCount(['children' => function ($query) {
                  $query->withoutGlobalScopes();
            }])->having('children_count', 0)->limit(15)->get(['id', 'name', 'image']);
            $featured   = Category::where("store_id", session()->get('dfstore'))->withCount(['children' => function ($query) {
                  $query->withoutGlobalScopes();
            }])->having('children_count', 0)->limit(5)->get(["id", "name"]);
            return view('ecommerce::component.content.category-component', compact('data', 'featured'));
      }
}
