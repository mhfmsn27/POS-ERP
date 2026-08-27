<?php

namespace Poshub\Ecommerce\Components\Content;

use App\Models\Transaction\Sell;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;
use Poshub\Ecommerce\Repositories\ProductRepository;

class ProductPopularComponent extends Component
{
      /**
       * Create a new component instance.
       *
       * @return void
       */

      protected $productRepository;

      public function __construct(ProductRepository $productRepository)
      {
            $this->productRepository      = $productRepository;
      }

      /**
       * Get the view / contents that represent the component.
       *
       * @return \Illuminate\Contracts\View\View|\Closure|string
       */
      public function render()
      {
            $lstMonth   = Carbon::today()->subDays(30);
            $data       = $this->productRepository->getPopularProducts(10);
            
            return view('ecommerce::component.content.products-popular-component', compact('data'));
      }
}
