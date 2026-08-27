<?php

namespace Poshub\Ecommerce\Components;

use App\Models\Admin\Setting;
use App\Models\Admin\Store;
use App\Models\Product\Category;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;

class HeaderComponent extends Component
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
            $store      = Store::where("id",session()->get('dfstore'))->get(['id', 'name', 'phone']);
            $session    = Session::get("dfstore");
            $setting    = Store::where("id",session()->get('dfstore'))->first(['phone', 'name','logo']);
            $featured   = Category::where("store_id",session()->get('dfstore'))->where("featured_category", "yes")->limit(10)->get(["id", "name", "image"]);
            return view('ecommerce::component.header-component', compact('store', 'session', 'setting', 'featured'));
      }
}
