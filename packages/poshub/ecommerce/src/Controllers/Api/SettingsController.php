<?php

namespace Poshub\Ecommerce\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\Store; 
use Poshub\Ecommerce\Models\EcommerceApiSetting;
use Poshub\Ecommerce\Requests\SettingRequest;

class SettingsController extends Controller
{

      public function index()
      {
            $data       = EcommerceApiSetting::first(['id', 'payment_method', 'rajaongkir', 'merchant_id', 'client_key', 'server_key', 'kurir_manual', 'price_per_km', 'domain_site', 'show_stock', 'with_stock']);
            $store      = Store::find(my_store());

            return response()->json([
                  'id'                    => $data->id ?? '',
                  'payment_method'        => $data->payment_method ?? '',
                  'rajaongkir'            => $data->rajaongkir ?? '',
                  'merchant_id'           => $data->merchant_id ?? '',
                  'client_key'            => $data->client_key ?? '',
                  'server_key'            => $data->server_key ?? '',
                  'kurir_manual'          => $data->kurir_manual ?? '',
                  'price_per_km'          => $data->price_per_km ?? '',
                  'domain_site'           => $data->domain_site ?? '',
                  'show_stock'            => $data->show_stock ?? '',
                  'with_stock'            => $data->with_stock ?? '',
                  'status'                => $data->ecommerce_activation ?? '',
                  'store'                 => array(
                        'district'              => array(
                              'id'                    => $store->subdistrict->id ?? '',
                              'name'                  => $store->subdistrict->name ?? '',
                        ),
                        'city'                  => array(
                              'id'                    => $store->subdistrict->city->id ?? '',
                              'name'                  => $store->subdistrict->city->name ?? '',
                        ),
                        'province'              => array(
                              'id'                    => $store->subdistrict->city->province->id ?? '',
                              'name'                  => $store->subdistrict->city->province->name ?? '',
                        )
                  )
            ], 200);
      }


      public function store(SettingRequest $request)
      {

            try {
                 
                  $data                   = EcommerceApiSetting::first();

                  if (!$data) {
                        $data = EcommerceApiSetting::create([
                              'rajaongkir'      => $request->rajaongkir,
                              'merchant_id'     => $request->merchant_id,
                              'client_key'      => $request->client_key,
                              'server_key'      => $request->server_key,
                              'payment_method'  => $request->payment_method,
                              'price_per_km'    => $request->price_per_km,
                              'kurir_manual'    => $request->kurir_manual,
                              'domain_site'     => $request->domain_site,
                              'ecommerce_activation'        => $request->status,
                              'show_stock'      => $request->show_stock,
                              'with_stock'      => $request->with_stock
                        ]);
                  } else {
                        $data->update([
                              'rajaongkir'      => $request->rajaongkir,
                              'merchant_id'     => $request->merchant_id,
                              'client_key'      => $request->client_key,
                              'server_key'      => $request->server_key,
                              'payment_method'  => $request->payment_method,
                              'price_per_km'    => $request->price_per_km,
                              'kurir_manual'    => $request->kurir_manual,
                              'domain_site'     => $request->domain_site,
                              'ecommerce_activation'        => $request->status,
                              'show_stock'      => $request->show_stock,
                              'with_stock'      => $request->with_stock
                        ]);
                  }

                  Store::where("id", my_store())->update([
                        'sub_district_id'             => $request->store['district']['id'],
                  ]);


                  return response()->json([
                        'message'         => 'Pembaharuan data berhasil di lakukan',
                        'status'          => true
                  ], 200);
            } catch (\Exception $e) {
                  return response()->json([
                        'message'   => $e->getMessage(),
                        'line'      => $e->getLine(),
                        'status'    => false
                  ], 409);
            }
      }
}
