<?php

namespace Poshub\Ecommerce\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\Blog;
use Poshub\Ecommerce\Repositories\BlogRepository;

class BlogController extends Controller
{

      protected $blogRepository;
      public function __construct(BlogRepository $blogRepository)
      {
            $this->blogRepository   = $blogRepository;
      }

      public function index(Request $request)
      {
            $orderBy    = array(
                  'value'     => 'id',
                  'type'      => 'desc'
            );

            $request->limit ? $limit = $request->limit : $limit = 10;

            $data             = $this->blogRepository->getData($request, $orderBy);
            $totalProducts    = $data->count();
            $blogs            = $data->paginate($limit);

            $pagination       = array(
                  'current_page'      => $blogs->currentPage(),
                  'to_page'           => $blogs->lastPage(),
                  'per_page'          => $blogs->perPage(),
                  'first_item'        => $blogs->firstItem(),
                  'last_item'         => $blogs->lastItem(),
                  'links'             => $blogs->linkCollection()->toArray()
            );

            return view('ecommerce::blog.list', compact('blogs', 'pagination'));
      }

      public function detail(Blog $blog)
      {
            $data             = $this->blogRepository->getDetail($blog);

            return view('ecommerce::blog.detail', compact('data'));
      }
}
