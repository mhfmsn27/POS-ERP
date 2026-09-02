<?php

namespace Poshub\Ecommerce\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Poshub\Ecommerce\Repositories\ProductRepository;
use Poshub\Ecommerce\Resources\VariationDetailResource;

class ShopController extends Controller
{

      protected $productRepository;

      public function __construct(ProductRepository $productRepository)
      {
            $this->productRepository      = $productRepository;
      }

      public function index(Request $request)
      {
            $request->limit ? $limit = $request->limit : $limit = 20;

            $data             = $this->productRepository->getData($request);
            $totalProducts    = $data->count();
            $products         = $data->paginate($limit)->withQueryString();

            $pagination       = array(
                  'current_page'      => $products->currentPage(),
                  'to_page'           => $products->lastPage(),
                  'per_page'          => $products->perPage(),
                  'first_item'        => $products->firstItem(),
                  'last_item'         => $products->lastItem(),
                  'links'             => $products->linkCollection()->toArray()
            );

            return view('ecommerce::shop.list', compact('products', 'limit', 'pagination', 'totalProducts'));
      }


      public function detail(Product $product)
      {
            return view('ecommerce::shop.detail', compact('product'));
      }

      public function getDetailVariation($id)
      {

            $variant = Variation::findOrFail($id);

            return response()->json([
                  'data'     => VariationDetailResource::make($variant),
                  'status'    => true
            ]);
      }

      public function topSells()
      {
            $lstMonth   = Carbon::today()->subDays(30);
            $data       = $this->productRepository->getPopularProducts(30);

            return view('ecommerce::shop.top', compact('data'));
      }

      public function getCategory(Request $request)
      {
            $data                   = $this->productRepository->getCategory($request);

            $response = array();

            foreach ($data as $c) {
                  $response[] = [
                        'id'        => $c->id,
                        'name'      => $c->name
                  ];
            }

            return response()->json($response);
      }

}
