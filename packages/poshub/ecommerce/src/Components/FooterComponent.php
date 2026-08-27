<?php

namespace Poshub\Ecommerce\Components;

use App\Models\Admin\Setting;
use App\Models\Admin\Store;
use Illuminate\View\Component;
use Poshub\Ecommerce\Models\EcommerceApiSetting;
use Poshub\Ecommerce\Models\SmallFeature;

class FooterComponent extends Component
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
            $setting    = EcommerceApiSetting::where("store_id",session()->get('dfstore'))->first(['copyright', 'facebook_url', 'twitter_url', 'instagram_url', 'youtube_url','store_id']);
            $phoneCs    = Store::where("id",session()->get('dfstore'))->first(['phone']);
            $featured   = SmallFeature::where("store_id",session()->get('dfstore'))->where("position", "footer")->get(); 
            return view('ecommerce::component.footer-component', compact('featured', 'setting', 'phoneCs'));
      }
}
