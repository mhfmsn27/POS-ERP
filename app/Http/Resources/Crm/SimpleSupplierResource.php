<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleSupplierResource extends JsonResource
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
            'address'           => $this->address,
            'default'           => $this->tax_default == 'yes' ? true : false,
            'tax_option'        => $this->tax_option == 'yes' ? true : false,
            'customer_type'     => $this->type,
            'due_date'          => $this->term ? (int)$this->term->due_date : 0
        ];
    }
}
