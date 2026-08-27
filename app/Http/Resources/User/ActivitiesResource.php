<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivitiesResource extends JsonResource
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
            'name'              => $this->log_name,
            'date'              => $this->created_at->format('Y-m-d H:i:s'),
            'description'       => $this->description,
            'event'             => $this->event,
            'user'              => array(
                'name'              => $this->causer->name ?? '',
                'email'             => $this->causer->email ?? '',
                'photo'             => asset($this->causer->image_data ?? 'uploads/image-default.jpeg')
            )
        ];
    }
}
