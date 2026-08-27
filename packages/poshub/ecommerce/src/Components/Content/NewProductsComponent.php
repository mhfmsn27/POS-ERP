<?php

namespace Poshub\Ecommerce\Components\Content;

use App\Models\Product\Product;
use App\Models\Transaction\Sell;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;
use Poshub\Ecommerce\Models\Banner;

class NewProductsComponent extends Component
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

            $query = Product::where('store_id', session()->get('dfstore'))->orderBy("id", "desc");

            if (with_stock() == 'no') {
                  $query->where(function ($q) {
                        $q->whereHas('general_store', function ($query) {
                              return  $query->selectRaw("sum(qty_available) as qty")->havingRaw('qty > ?', [0])->where("store_id", Session::get('dfstore'));
                        });
                  });
            }

            $data = $query->limit(10)->get();

            $banner = Banner::where("position", "shop")->first();
            return view('ecommerce::component.content.products-new-component', compact('data', 'banner'));
      }
}
