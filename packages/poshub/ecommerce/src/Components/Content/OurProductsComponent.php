<?php

namespace Poshub\Ecommerce\Components\Content;

use App\Models\Product\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;

class OurProductsComponent extends Component
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
            $query = Product::orderBy("name", "desc");

            if (with_stock() == 'no') {
                  $query->where(function ($q) {
                        $q->whereHas('general_store', function ($query) {
                              return  $query->selectRaw("sum(qty_available) as qty")->havingRaw('qty > ?', [0])->where("store_id", Session::get('dfstore'));
                        });
                  });
            }

            $data = $query->where('store_id', session()->get('dfstore'))->limit(20)->get();

            return view('ecommerce::component.content.our-products-component', compact('data'));
      }
}
