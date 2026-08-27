<?php

namespace App\Http\Resources\Rma;

use Illuminate\Http\Resources\Json\JsonResource;

class RmaRecordResource extends JsonResource
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
            'subject'       => $this->subject,
            'status'        => $this->status_name,
            'type'          => $this->type,
            'note'          => $this->note,
            'date'          => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
