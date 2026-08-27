<?php

namespace Poshub\Ecommerce\Components\Content;

use Illuminate\View\Component;
use Poshub\Ecommerce\Models\Banner;

class DeafultBannerComponent extends Component
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
            $data = Banner::where("store_id",session()->get('dfstore'))->where("position", "home")->limit(3)->get(['image', 'title',  'button_url', 'button_name', 'button']);
 
            return view('ecommerce::component.content.home-banner-component', compact('data'));
      }
}
