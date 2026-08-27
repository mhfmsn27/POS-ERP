<?php

namespace Poshub\Ecommerce\Components\Admin;
 
use Illuminate\View\Component;

class TabMediaComponent extends Component
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
            return view('ecommerce::component.admin.tab-media-component');
      }
}
