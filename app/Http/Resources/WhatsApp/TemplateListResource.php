<?php

namespace App\Http\Resources\WhatsApp;

use Illuminate\Http\Resources\Json\JsonResource;

class TemplateListResource extends JsonResource
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
            'id'        => $this->id,
            'name'      => $this->name,
            'file'      => $this->file != null ? asset($this->file) : null
        ];
    }
}
