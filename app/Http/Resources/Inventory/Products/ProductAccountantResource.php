<?php

namespace App\Http\Resources\Inventory\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductAccountantResource extends JsonResource
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
            'supply'        => array(
                'id'            => $this->supply_account->id ?? '',
                'name'          => $this->supply_account->name ?? ''
            ),
            'sale'          => array(
                'id'            => $this->sale_account->id ?? '',
                'name'          => $this->sale_account->name ?? ''
            ),
            'return_sale'   => array(
                'id'            => $this->return_sale_account->id ?? '',
                'name'          => $this->return_sale_account->name ?? ''
            ),
            'discount'      => array(
                'id'            => $this->discount_account->id ?? '',
                'name'          => $this->discount_account->name ?? ''
            ),
            'sent'          => array(
                'id'            => $this->sent_account->id ?? '',
                'name'          => $this->sent_account->name ?? ''
            ),
            'cost'          => array(
                'id'            => $this->cost_account->id ?? '',
                'name'          => $this->cost_account->name ?? ''
            ),
            'retur_purchase'    => array(
                'id'            => $this->retur_purchase_account->id ?? '',
                'name'          => $this->retur_purchase_account->name ?? ''
            ),
            'supplier_debt'     => array(
                'id'            => $this->supplier_debt_account->id ?? '',
                'name'          => $this->supplier_debt_account->name ?? ''
            ),
            
        ];
    }
}
