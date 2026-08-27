<?php

namespace Poshub\Ecommerce\Components;

use App\Models\Product\Category;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;

class SidebarShopComponent extends Component
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
            $category   = Category::where("store_id", session()->get('dfstore'))->orderBy("name", "asc")->withCount(['children' => function ($query) {
                  $query->withoutGlobalScopes();
            }])->having('children_count', 0)->get();
            
            $products   = Product::where(function ($q) {
                  $q->whereHas('general_store', function ($query) {
                        return  $query->selectRaw("sum(qty_available) as qty")->havingRaw('qty > ?', [0])->where("store_id", Session::get('dfstore'));
                  });
            })->orderBy("id", "desc")->limit(5)->get();
            return view('ecommerce::component.sidebar-shop-component', compact('category', 'products'));
      }
}
