<?php

namespace App\Http\Resources\Rma;

use Illuminate\Http\Resources\Json\JsonResource;

class RmaItemsResource extends JsonResource
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
            'name'          => $this->product_name,
            'complaint'     => $this->complaint,
            'completeness'  => $this->completeness,
            'status'        => $this->status,
            'records'       => RmaRecordResource::collection($this->record)
        ];
    }
}
