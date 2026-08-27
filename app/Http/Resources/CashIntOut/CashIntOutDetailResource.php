<?php

namespace App\Http\Resources\CashIntOut;

use Illuminate\Http\Resources\Json\JsonResource;

class CashIntOutDetailResource extends JsonResource
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
            'type'          => $this->type,
            'method'        => array(
                'id'            => $this->method_id,
                'name'          => $this->method->name ?? ''
            ),
            'category'      => array(
                'id'            => $this->category_id,
                'name'          => $this->category->name ?? ''
            ),
            'date'          => $this->created_at->format('Y-m-d'),
            'note'          => $this->detail,
            'summary'       => array(
                'subtotal'      => (float)$this->list->sum('amount')
            ),
            'items'         => CashIntOutItemResource::collection($this->list),

        ];
    }
}
