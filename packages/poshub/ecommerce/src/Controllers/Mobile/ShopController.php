<?php

namespace Poshub\Ecommerce\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Poshub\Ecommerce\Repositories\CartRepository;
use Poshub\Ecommerce\Repositories\ProductRepository;
use Poshub\Ecommerce\Resources\VariationDetailResource;

class ShopController extends Controller
{

      protected $productRepository;
      protected $cartRepository;

      public function __construct(ProductRepository $productRepository, CartRepository $cartRepository)
      {
            $this->productRepository      = $productRepository;
            $this->cartRepository         = $cartRepository;
      }

      public function index(Request $request)
      {
            $request->limit ? $limit = $request->limit : $limit = 10;

            $data             = $this->productRepository->getData($request);
            $totalProducts    = $data->count();
            $products         = $data->paginate($limit);

            $pagination       = array(
                  'current_page'      => $products->currentPage(),
                  'to_page'           => $products->lastPage(),
                  'per_page'          => $products->perPage(),
                  'first_item'        => $products->firstItem(),
                  'last_item'         => $products->lastItem(),
                  'links'             => $products->linkCollection()->toArray()
            );

            return view('ecommerce::mobile.shop.list', ['page' => 'Belanja'], compact('products', 'limit', 'pagination', 'totalProducts'));
      }

      public function getDetailVariation($id)
      {

            $variant = Variation::findOrFail($id);

            return response()->json([
                  'data'     => VariationDetailResource::make($variant),
                  'status'    => true
            ]);
      }

      public function detail(Product $product)
      {
            if(Auth::guard('customers')->check()) {
                  $cartData   = $this->cartRepository->getCarts();
                  $totalCart  = (int)$cartData['total'];
            } else {
                  $totalCart  = 0;
            }
          
            return view('ecommerce::mobile.shop.detail', ['page' => 'Detail Produk ' . $product->name], compact('product', 'totalCart'));
      }
}
