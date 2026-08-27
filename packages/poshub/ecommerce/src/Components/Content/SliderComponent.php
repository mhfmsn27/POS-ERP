<?php

namespace Poshub\Ecommerce\Components\Content;

use Illuminate\View\Component;
use Poshub\Ecommerce\Models\Slider;

class SliderComponent extends Component
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
            $data = Slider::where("store_id",session()->get('dfstore'))->get(['image', 'title', 'subtitle', 'button_url', 'button_name', 'button']);
            return view('ecommerce::component.content.slider-component', compact('data'));
      }
}
