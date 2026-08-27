<?php

namespace Poshub\Ecommerce\Components;
 
use Illuminate\View\Component;
use Poshub\Ecommerce\Models\BlogCategory;
use Poshub\Ecommerce\Repositories\BlogRepository;

class SidebarBlogComponent extends Component
{
      /**
       * Create a new component instance.
       *
       * @return void
       */
      protected $blogRepository;
      public function __construct(BlogRepository $blogRepository)
      {
            $this->blogRepository   = $blogRepository;
      }

      /**
       * Get the view / contents that represent the component.
       *
       * @return \Illuminate\Contracts\View\View|\Closure|string
       */
      public function render()
      {
            $category   = BlogCategory::where("store_id",session()->get('dfstore'))->orderBy("name", "asc")->limit(10)->get();
            $orderBy    = array(
                  'value'     => 'views',
                  'type'      => 'desc'
            );

            $data       = $this->blogRepository->getData(array(), $orderBy)->where("store_id",session()->get('dfstore'))->limit(5)->get();
            return view('ecommerce::component.sidebar-blog-component', compact('category', 'data'));
      }
}
