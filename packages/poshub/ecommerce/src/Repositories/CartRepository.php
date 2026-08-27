<?php

namespace Poshub\Ecommerce\Repositories;

use Illuminate\Support\Facades\Auth;
use Poshub\Ecommerce\Models\CustomerCart;

class CartRepository
{
      public function getCarts()
      {
            $carts      = CustomerCart::where("customer_id", Auth::guard('customers')->user()->id)->get();
            $subtotal   = CustomerCart::where("customer_carts.customer_id", Auth::guard('customers')->user()->id)->join("variations as v", "v.id", "=", "customer_carts.variation_id")->join("products as p", "p.id", "v.product_id")->selectRaw("sum(v.selling_price * customer_carts.quantity) as subtotal, sum(p.weight * customer_carts.quantity) as total_weight")->first();

            return array(
                  'carts'           => $carts,
                  'total'           => count($carts),
                  'subtotal'        => (int)$subtotal->subtotal,
                  'total_weight'    => (int)$subtotal->total_weight
            );
      }

      public function getCartByFilter($cart)
      {
            $carts      = CustomerCart::where("customer_id", Auth::guard('customers')->user()->id)->whereIn("id", $cart)->get();
            $subtotal   = CustomerCart::where("customer_carts.customer_id", Auth::guard('customers')->user()->id)->whereIn("customer_carts.id", $cart)->join("variations as v", "v.id", "=", "customer_carts.variation_id")->join("products as p", "p.id", "v.product_id")->selectRaw("sum(v.selling_price * customer_carts.quantity) as subtotal, sum(p.weight * customer_carts.quantity) as total_weight")->first();

            return array(
                  'carts'           => $carts,
                  'total'           => count($carts),
                  'subtotal'        => (int)$subtotal->subtotal,
                  'total_weight'    => (int)$subtotal->total_weight
            );
      }

      public function getCart($id)
      {
            return CustomerCart::where("customer_id", Auth::guard('customers')->user()->id)->where("variation_id", $id)->first();
      }

      public function update(CustomerCart $cart, $qty)
      {
            $cart->update([
                  'quantity'        => $qty,
            ]);
      }

      public function create(Object $data)
      {
            $cart = CustomerCart::create([
                  'customer_id'     => Auth::guard('customers')->user()->id,
                  'variation_id'    => $data->variationid,
                  'quantity'        => $data->quantity,
            ]);

            return $cart;
      }
}
