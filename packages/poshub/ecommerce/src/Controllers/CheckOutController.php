<?php

namespace Poshub\Ecommerce\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Poshub\Ecommerce\Models\CustomerAddress;
use Poshub\Ecommerce\Models\EcommerceApiSetting;
use Poshub\Ecommerce\Repositories\AddressRepository;
use Poshub\Ecommerce\Repositories\CartRepository;
use Poshub\Ecommerce\Repositories\CourierRepository;

class CheckOutController extends Controller
{

      protected $cartRepository;
      protected $addressRepository;
      protected $couerierRepository;

      public function __construct(CartRepository $cartRepository, AddressRepository $addressRepository, CourierRepository $couerierRepository)
      {
            $this->addressRepository      = $addressRepository;
            $this->cartRepository         = $cartRepository;
            $this->couerierRepository     = $couerierRepository;
      }


      public function index(Request $request)
      {

            $carts            = $this->cartRepository->getCarts();

            if (!empty($request->choose_cart)) {
                  $carts      = $this->cartRepository->getCartByFilter($request->choose_cart);
            }

            $stores           = Store::findOrFail(Session::get("dfstore"));
            $settings         = EcommerceApiSetting::where('store_id',session()->get('dfstore'))->first(['client_key']);
            $tax_total        = 0;
            $grandTotal       = $carts['subtotal'];
            $address          = $this->addressRepository->getData();

            // if ($carts['subtotal'] > 0 && $stores->tax > 0) {
            //       $tax_total  = $stores->tax / 100   * $carts['subtotal'];
            //       $grandTotal = $carts['subtotal'] + $tax_total;
            // }

            return view('ecommerce::account.checkout', compact('carts', 'stores', 'tax_total', 'grandTotal', 'address', 'settings'));
      }

      public function getShippingCost(Request $request)
      {
            $carts            = $this->cartRepository->getCarts();
            $address          = $this->addressRepository->defaultAddress();

            if ($request->address_id != null && $request->address_id != 'null') {
                  $address    = CustomerAddress::findOrFail($request->address_id);
            }

            $getCost          = $this->couerierRepository->getCost($address, $carts['total_weight']);

            return response()->json([
                  'data'            => $getCost,
                  'status'          => true
            ]);
      }
}
