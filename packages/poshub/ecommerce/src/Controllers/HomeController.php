<?php

namespace Poshub\Ecommerce\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin\Store; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session; 
use Poshub\Ecommerce\Models\EcommerceApiSetting; 
use Poshub\Ecommerce\Models\SmallFeature; 
use Poshub\Ecommerce\Repositories\AddressRepository;
use Poshub\Ecommerce\Repositories\CartRepository;

class HomeController extends Controller
{

      protected $addressRepository;
      protected $cardRepository;

      public function __construct(AddressRepository $addressRepository, CartRepository $cardRepository)
      {
            $this->addressRepository      = $addressRepository;
            $this->cardRepository         = $cardRepository;
      }


      public function index()
      {
           
            return view('ecommerce::home');
      }

      public function branch()
      {
            $data = Store::where('id',session()->get('dfstore'))->get();
            return view('ecommerce::branch', compact('data'));
      }

      public function about()
      {
            $data       = EcommerceApiSetting::where('store_id',session()->get('dfstore'))->first(['about_title', 'about_image', 'about_text']);
            $featured   = SmallFeature::where("position", "about")->get();
            return view('ecommerce::about', compact('data', 'featured'));
      }

      public function getProvinces(Request $request)
      {

            $data                   = $this->addressRepository->getProvince($request);
            $response               = array();

            foreach ($data as $province) {
                  $response[] = [
                        'id'        => $province->id,
                        'name'      => $province->name
                  ];
            }

            return response()->json($response);
      }



      public function getCities(Request $request)
      {
            $data                   = $this->addressRepository->getCity($request);
            $response               = array();

            foreach ($data as $city) {
                  $response[] = [
                        'id'        => $city->id,
                        'name'      => $city->type . ' ' . $city->name
                  ];
            }

            return response()->json($response);
      }

      public function district(Request $request)
      {
            $data                   = $this->addressRepository->getSubdistrict($request);
            $response               = array();

            foreach ($data as $sub) {
                  $response[] = [
                        'id'        => $sub->id,
                        'name'      => $sub->name
                  ];
            }

            return response()->json($response);
      }

      public function changeSession($id)
      {
            $store = Store::findOrFail($id);

            if ($store) {
                  Session::put("dfstore", $id);

                  return redirect()->route('ecommerce.home');
            }
      }
}
