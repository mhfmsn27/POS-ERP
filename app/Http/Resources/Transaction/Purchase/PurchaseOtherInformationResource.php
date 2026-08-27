<?php

namespace App\Http\Resources\Transaction\Purchase;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOtherInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $discountPercentse  = (int)$this->discount_amount;
        $subtotal           =  $this->purchase()->selectRaw("sum(purchase_price_inc_tax * quantity) as total")->first();

        if ($this->discount_type == 'fixed') {
            if ($this->discount_final > 0) {
                $total              = $subtotal->total;
                $discountPercentse  = $this->discount_final / $total * 100;
            }
        }

        return [
            'discount_product_total'    => 0,
            'tax_product_total'         => 0,
            "date"              => "",
            "method"            => array(
                "id"                => "",
                "name"              => ""
            ),
            'discount_percent'  => (int)$discountPercentse,
            "subtotal"          => (int)$this->total_before_tax,
            "discount_type"     => $this->discount_type ?? 'percent',
            "discount"          => (int)$this->discount_amount,
            "discount_total"    => (float)$this->discount_final,
            "tax"               => (int)$this->tax_amount,
            "tax_total"         => (float)$this->tax_final,
            "shipping_cost"     => (int)$this->shipping_charges,
            "note"              => $this->additional_notes,
            "finalTotal"        => (float)$this->final_total,
            'shipping_alocation'    => $this->shipping_alocation,
            'pay_total'         => (float)$this->payment()->sum("amount"),
            'payment'           => array(
                'pay_total'         => (float)$this->payment()->sum("amount"),
                'due_total'         => (float)$this->due_total_po
            ),
            'void'              => array(
                'reason'        => $this->void->reason ?? '',
                'created'       => $this->void->user->name ?? ''
            )
        ];
    }
}
