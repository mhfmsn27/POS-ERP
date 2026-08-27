<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class StockAlertResource extends JsonResource
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
            'name'      => substr($this->product->name ?? '',0,50),
            'alert'     => (int)($this->product->alert_quantity ?? 0),
            'qty'       => (int)$this->qty_available,
            'category'  => $this->product->category->name ?? ''
        ];
    }
}
