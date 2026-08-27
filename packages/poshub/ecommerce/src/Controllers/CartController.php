<?php

namespace Poshub\Ecommerce\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\Store;
use App\Models\Product\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Poshub\Ecommerce\Models\CustomerCart;
use Poshub\Ecommerce\Repositories\CartRepository;
use Poshub\Ecommerce\Resources\CartResource;

class CartController extends Controller
{

      protected $cartRepository;
      public function __construct(CartRepository $cartRepository)
      {
            $this->cartRepository   = $cartRepository;
      }


      public function index()
      {
            $data       = array();
            $subtotal   = 0;

            if (Auth::guard('customers')->check() == true) {
                  $getCartData      = $this->cartRepository->getCarts();
                  $data             = CartResource::collection($getCartData['carts']);
                  $subtotal         = $getCartData['subtotal'];
            }

            return response()->json([
                  'total'     => count($data),
                  'cart'      => $data,
                  'subtotal'  => $subtotal,
                  'status'    => true
            ]);
      }

      public function add(Request $request)
      {

            $this->validate($request, [
                  'quantity'        => 'numeric|min:1|required',
                  'variationid'     => 'numeric|min:1|required'
            ]);

            $data             = Variation::findOrFail($request->variationid);
            $cartReady        = $this->cartRepository->getCart($data->id);
            $qtyReady         = 0;

            if ($cartReady) {
                  $qtyReady = $cartReady->quantity;
            }

            if (show_stock() == 'yes') {
                  if ($data->stock_in_website->sum("qty_available") < ($request->quantity + (int)$qtyReady)) {
                        return response()->json([
                              'message' => 'Qty Produk ini tidak tersedia',
                              'status' => false
                        ]);
                  }
            }


            if ($cartReady) {
                  $totalQty          = $request->quantity + (int)$qtyReady;
                  $this->cartRepository->update($cartReady, $totalQty);
            } else {
                  $cartReady = $this->cartRepository->create($request);
            }

            $getCartData      = $this->cartRepository->getCarts();
            $total            = $getCartData['total'];
            $subtotal         = $getCartData['subtotal'];

            // Auto-track Abandoned Cart via WhatsApp Recovery Engine
            try {
                  $customer = Auth::guard('customers')->user();
                  if ($customer) {
                        $storeId = Session::get('dfstore') ?? 1;
                        app(\App\Services\Ecommerce\AbandonedCartRecoveryService::class)->trackCart(
                              (int)$storeId,
                              $getCartData['carts']->toArray(),
                              (int)$customer->id,
                              $customer->phone ?? null,
                              $customer->name ?? null
                        );
                  }
            } catch (\Throwable $trackEx) {}

            return response()->json([
                  'message'   => 'Berhasil menambahkan item ke keranjang',
                  'cart'      => CartResource::make($cartReady),
                  'subtotal'  => $subtotal,
                  'total'     => (int)$total,
                  'status'    => true
            ]);
      }

      public function update(CustomerCart $cart, Request $request)
      {
            $this->validate($request, [
                  'quantity'  => 'required|min:1'
            ]);

            $cartReady        = $cart;

            if ($cartReady == null) {
                  return response()->json([
                        'message' => 'Kami tidak menemukan data keranjang ini',
                        'status' => false
                  ]);
            }


            if (show_stock() == 'yes') {
                  if ($cart->variation->stock_in_website->sum("qty_available") < $request->quantity) {
                        return response()->json([
                              'message' => 'Qty Produk ini tidak tersedia',
                              'status' => false
                        ]);
                  }
            }

            if ($cartReady) {
                  $this->cartRepository->update($cartReady, $request->quantity);
            }

            return response()->json([
                  'message' => '',
                  'status' => true
            ]);
      }

      public function delete(CustomerCart $cart)
      {
            $cart->delete();

            $getCartData      = $this->cartRepository->getCarts();
            $total            = $getCartData['total'];
            $subtotal         = $getCartData['subtotal'];

            return response()->json([
                  'message'   => 'Berhasil menghapus item di keranjang',
                  'subtotal'  => $subtotal,
                  'total'     => (int)$total,
                  'status'    => true
            ]);
      }

      public function deleteAll()
      {
            CustomerCart::where("customer_id", Auth::guard('customers')->user()->id)->delete();

            return response()->json([
                  'message'   => 'Berhasil menghapus semua item di keranjang',
                  'subtotal'  => 0,
                  'total'     => 0,
                  'status'    => true
            ]);
      }

      public function cart()
      {

            $carts            = $this->cartRepository->getCarts();
            $stores           = Store::findOrFail(Session::get("dfstore"));
            $tax_total        = 0;
            $grandTotal       = $carts['subtotal'];

            // if ($carts['subtotal'] > 0 && $stores->tax > 0) {
            //       $tax_total  = $stores->tax / 100   * $carts['subtotal'];
            //       $grandTotal = $carts['subtotal'] + $tax_total;
            // }

            return view('ecommerce::account.cart', compact('carts', 'stores', 'tax_total', 'grandTotal'));
      }
}
