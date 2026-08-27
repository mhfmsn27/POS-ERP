<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'default'           => $this->default == 'yes' ? true : false,
            'name'              => $this->name,
            'email'             => $this->email,
            'phone'             => $this->phone ?? '',
            'address'           => $this->address,
            'is_account'        => $this->is_account == 'yes' ? true : false,
            'type'              => $this->type,
            'tax_default'       => $this->tax_default,
            'npwp'              => $this->npwp,
            'tax_option'        => $this->tax_option,
            'term'              => array(
                'id'                => $this->term->id ?? '',
                'name'              => $this->term->name ?? ''
            ),
            'debt'              => array(
                'id'                => $this->debt_account->id ?? '',
                'name'              => $this->debt_account->name ?? ''
            ),
            'debt_imprest'      => array(
                'id'                => $this->debt_imprest_account->id ?? '',
                'name'              => $this->debt_imprest_account->name ?? ''
            ),
            'detail'            => $this->detail,
            'total_due'         => (int)$this->total_due,
            'total_saldo'       => (int)$this->total_saldo,
        ];
    }
}
