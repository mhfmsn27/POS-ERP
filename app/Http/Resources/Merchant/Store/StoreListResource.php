<?php

namespace App\Http\Resources\Merchant\Store;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreListResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'address'       => $this->address,
            'package'       => $this->store_package ? array(
                'end_date'      => $this->store_package->end_date ?? '',
            ) : null
        ];
    }
}
