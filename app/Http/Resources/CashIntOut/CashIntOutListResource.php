<?php

namespace App\Http\Resources\CashIntOut;

use Illuminate\Http\Resources\Json\JsonResource;

class CashIntOutListResource extends JsonResource
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
            'id'                        => $this->id,
            'date'                      => $this->created_at->format('Y-m-d H:i:s'),
            'ref_no'                    => $this->ref_no,
            'type'                      => $this->type,
            'category'                  => array(
                'id'                        => $this->category->id ?? '',
                'name'                      => $this->category->name ?? ''
            ),
            'method'                    => array(
                'id'                        => $this->method_id,
                'name'                      => $this->method->name ?? ''
            ),
            'amount'                    => (float)$this->amount,
            'detail'                    => $this->detail,
        ];
    }
}
