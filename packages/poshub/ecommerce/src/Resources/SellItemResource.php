<?php

namespace Poshub\Ecommerce\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SellItemResource extends JsonResource
{
      /**
       * Transform the resource into an array.
       *
       * @param  \Illuminate\Http\Request  $request
       * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
       */
      public function toArray($request)
      {

            $varname = $this->variation->name ?? '';

            if ($varname == 'no-name') {
                  $varname = '';
            }

            return [
                  'product_name'          => $this->product->name . ' ' . $varname,
                  'qty'                   => (int)$this->qty,
                  'price'                 => (int)$this->unit_price,
                  'subtotal'              => (int)$this->subtotal_price
            ];
      }
}
