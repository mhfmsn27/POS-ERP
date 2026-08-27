<?php

namespace App\Http\Resources\Transaction\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleOtherInformationResource extends JsonResource
{
    /**
     * Transform the resource floato an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $discountPercentse  = (float)$this->discount_amount;

        return [
            'discount_product_total'    => 0,
            'tax_product_total'         => 0,
            "date"              => "",
            "method"            => array(
                "id"                => "",
                "name"              => ""
            ),
            'discount_percent'  => (float)$discountPercentse,
            "subtotal"          => (float)$this->total_before_tax,
            'goverment_tax'     => (float)$this->goverment_tax,
            'service_tax'       => (float)$this->service_tax,
            "discount_type"     => $this->discount_type ?? 'percent',
            "discount"          => (float)$this->discount_amount,
            "discount_total"    => (float)$this->discount_final,
            "tax"               => (float)$this->tax_amount,
            "tax_total"         => (float)$this->tax_final,
            "shipping_cost"     => (float)$this->shipping_charges,
            "note"              => $this->additional_notes,
            "finalTotal"        => (float)$this->final_total,
            'pay_total'         => (float)$this->payment()->sum("amount"),
            'payment'           => array(
                'pay_total'         => (float)$this->payment()->sum("amount"),
                'due_total'         => (float)$this->due_total
            ),
            'void'              => array(
                'reason'        => $this->void->reason ?? '',
                'created'       => $this->void->user->name ?? ''
            )
        ];
    }
}
