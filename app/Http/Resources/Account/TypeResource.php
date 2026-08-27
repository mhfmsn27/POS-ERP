<?php

namespace App\Http\Resources\Account;

use Illuminate\Http\Resources\Json\JsonResource;

class TypeResource extends JsonResource
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
            'coa_code'      => $this->coa_code,
            'price'         => $this->with_price == 'yes' ? true : false,
            'modal'         => $this->with_modal == 'yes' ? true : false,
            'account'       => (int)$this->account()->count(),
            'type'          => $this->type
        ];
    }
}
