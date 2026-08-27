<?php

namespace App\Http\Resources\Master\PaymentMethod;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodListResource extends JsonResource
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
            'service'       => $this->service == 'yes' ? true : false,
            'sync'          => $this->automatic_sync == 'yes' ? false : true,
            'amount'        => (int)$this->amount,
            'an'            => $this->an,
            'no_rek'        => $this->no_rek,
            'image'         => asset($this->image_data),
            'account'       => array(
                'id'            => $this->account->id ?? '',
                'name'          => $this->account->name ?? '',
                'code'          => $this->account->coa ?? ''
            ),
        ];
    }
}
