<?php

namespace Poshub\Ecommerce\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
      /**
       * Transform the resource into an array.
       *
       * @param  \Illuminate\Http\Request  $request
       * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
       */
      public function toArray($request)
      {

            return [
                  'id'                => $this->id,
                  'name'              => $this->name,
                  'sub_district'      => array(
                        'id'              => $this->sub_district_id,
                        'name'            => $this->subdistrict->name ?? ''
                  ),
                  'city'             => array(
                        'id'              => $this->subdistrict->city_id ?? '',
                        'name'            => $this->subdistrict->city->name ?? '',
                  ),
                  'province'        => array(
                        'id'              => $this->subdistrict->city->province_id ?? '',
                        'name'            => $this->subdistrict->city->province->name ?? ''
                  ),
                  'address'         => $this->address,
                  'postal_code'     => $this->postal_code,
                  'phone'           => $this->phone,
                  'default'         => $this->default
            ];
      }
}
