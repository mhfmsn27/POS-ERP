<?php

namespace App\Http\Resources\Transaction\WarehouseTransfer;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailResource extends JsonResource
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
            'ref_no'        => $this->ref_no,
            'from'          => array(
                'name'          => $this->from_warehouse->name ?? 'Gudang Utama',
            ),
            'to'            => array(
                'name'          => $this->to_warehouse->name ?? 'Gudang Utama'
            ),
            'store'         => array(
                'id'            => $this->store->id ?? '',
                'name'          => $this->store->name ?? ''
            ),
            'created'       => array(
                'id'            => $this->createdby->id ?? '',
                'name'          => $this->createdby->name ?? ''
            ),
            'created_at'    => $this->created_at->format("Y-m-d"),
            'time'          => $this->created_at->format('H:i:s'),
            'date'          => $this->transaction_date,
            'note'          => $this->additional_notes,
            'items'         => ItemsResource::collection($this->adjustment),
        ];
    }
}
