<?php

namespace Poshub\Ecommerce\Repositories;

use App\Models\Admin\Courier;
use App\Models\Admin\Store;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Poshub\Ecommerce\Models\CustomerAddress;
use Poshub\Ecommerce\Models\EcommerceApiSetting; 

class CourierRepository
{
      public function getAll()
      {
            return Courier::all();
      } 

      public function getCost(CustomerAddress $address, $weight)
      {
            $storeId    = Session::get('dfstore') ?? 1;
            $setting    = EcommerceApiSetting::where('store_id', $storeId)->first(['rajaongkir']);
            $store      = Store::where("id", $storeId)->first(['sub_district_id']);

            $costs      = array();

            // 1. Opsi BOPIS (Buy Online, Pick Up In Store) - Bebas Ongkir
            $costs[] = [
                  'curir_id'  => 0,
                  'name'      => 'Ambil Langsung di Toko (BOPIS)',
                  'code'      => 'pickup_store',
                  'image'     => '',
                  'service'   => 'Self-Pickup (Ambil Mandiri)',
                  'etd'       => 'Siap 1-2 Jam',
                  'note'      => 'Bebas Ongkir - Ambil langsung di kasir toko',
                  'price'     => 0
            ];

            $originSubdistrict = $store->sub_district_id ?? 0;
            $destSubdistrict   = $address->sub_district_id ?? 0;

            foreach ($this->getAll() as $curir) {
                  $cacheKey = "ongkir_{$originSubdistrict}_{$destSubdistrict}_{$weight}_{$curir->code}";

                  $courierCosts = \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400, function () use ($setting, $originSubdistrict, $destSubdistrict, $weight, $curir) {
                        try {
                              $response = Http::timeout(5)->withHeaders([
                                    'content-type' => 'application/x-www-form-urlencoded',
                                    'key' => $setting->rajaongkir ?? '',
                              ])->asForm()->post('https://pro.rajaongkir.com/api/cost', [
                                    'origin'          => $originSubdistrict,
                                    'originType'      => 'subdistrict',
                                    'destination'     => $destSubdistrict,
                                    'destinationType' => 'subdistrict',
                                    'weight'          => $weight,
                                    'courier'         => $curir->code
                              ]);

                              if ($response->successful()) {
                                    $responseData = json_decode($response->body());
                                    $subCosts = [];
                                    if (isset($responseData->rajaongkir->results)) {
                                          foreach ($responseData->rajaongkir->results as $couerier) {
                                                foreach ($couerier->costs as $cost) {
                                                      $subCosts[] = [
                                                            'name'    => $couerier->name,
                                                            'code'    => $couerier->code,
                                                            'service' => $cost->service,
                                                            'etd'     => $cost->cost[0]->etd ?? '1-3 hari',
                                                            'note'    => $cost->cost[0]->note ?? '',
                                                            'price'   => $cost->cost[0]->value ?? 0,
                                                      ];
                                                }
                                          }
                                    }
                                    return $subCosts;
                              }
                        } catch (\Throwable $e) {}
                        return [];
                  });

                  foreach ($courierCosts as $c) {
                        $c['curir_id'] = $curir->id;
                        $c['image']    = asset($curir->logo);
                        $costs[]       = $c;
                  }
            }

            return new Collection($costs);
      }

      public function getCostByCode(CustomerAddress $address, array $data)
      {

            $setting    = EcommerceApiSetting::where('store_id',session()->get('dfstore'))->first(['rajaongkir']);
            $costs      = array();

            $response = Http::withHeaders([
                  'content-type' => 'application/x-www-form-urlencoded',
                  'key' => $setting->rajaongkir ?? '',
            ])->asForm()->post('https://pro.rajaongkir.com/api/cost', [
                  'origin'          => $data['district'],
                  'originType'      => 'subdistrict',
                  'destination'     => $address->sub_district_id,
                  'destinationType' => 'subdistrict',
                  'weight'          => $data['weight'],
                  'courier'         => $data['code']
            ]);

            $responseData = json_decode($response->body());

            foreach ($responseData->rajaongkir->results as $couerier) {

                  foreach ($couerier->costs as $cost) {
                        $item['name']           = $couerier->name;
                        $item['code']           = $couerier->code;
                        $item['service']        = $cost->service;
                        $item['etd']            = $cost->cost[0]->etd;
                        $item['note']           = $cost->cost[0]->note;
                        $item['price']          = $cost->cost[0]->value;
                        $costs[]                 = $item;
                  }
            }

            return new Collection($costs);
      }

      public function getTracking(Transaction $transaction)
      {
            $setting    = EcommerceApiSetting::where('store_id',session()->get('dfstore'))->first(['rajaongkir']);
            $tracking   = array();

            $response = Http::withHeaders([
                  'content-type' => 'application/x-www-form-urlencoded',
                  'key' => $setting->rajaongkir ?? '',
            ])->asForm()->post('https://pro.rajaongkir.com/api/waybill', [
                  'waybill'               => $transaction->shipping_detail->resi_no ?? '',
                  'courier'               => $transaction->shipping_detail->curir_code ?? '',
            ]);

            $responseData = json_decode($response->body());

            if ($responseData->rajaongkir->status->code != 200) {
                  return array(
                        'status'    => false,
                        'message'   => 'No Resi Sudah tidak aktif atau kadaluarsa'
                  );
            }

            $detailData       = $responseData->rajaongkir->result;

            foreach ($detailData->manifest as $track) {
                  $item['date']           = $track['manifest_date'];
                  $item['time']           = $track['manifest_time'];
                  $item['city']           = $track['city_name'];
                  $item['desc']           = $track['manifest_description'];
                  $tracking[]             = $item;
            }

            return array(
                  'status'    => true,
                  'summary'   => array(
                        'courier_name'    => $detailData->summary->courier_name,
                        'service_code'    => $detailData->summary->service_code,
                        'shipper_name'    => $detailData->summary->shipper_name,
                        'receiver_name'   => $detailData->summary->receiver_name,
                        'status'          => $detailData->summary->status
                  ),
                  'trackings' => $tracking
            );
      }
}
