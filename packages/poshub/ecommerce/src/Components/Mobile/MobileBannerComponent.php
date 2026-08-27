<?php

namespace Poshub\Ecommerce\Components\Mobile;
 
use Illuminate\View\Component;
use Poshub\Ecommerce\Models\Banner;

class MobileBannerComponent extends Component
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
        $data = Banner::where("position", "mobile")->limit(3)->get(['image', 'title',  'button_url', 'button_name', 'button']);
        return view('ecommerce::component.mobile.content.banner-component', compact('data'));
    }
}
