<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class UserActiveResource extends JsonResource
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
            'name'          => $this->name,
            'last_active'   => $this->last_active_at->format('Y-m-d H:i') == now()->format('Y-m-d H:i') ? 'Online' : $this->getLastActiveMessage($this->last_active_at),
            'photo'         => asset($this->photo),
        ];
    }

    private function getLastActiveMessage($lastActiveAt)
    {
        $now = now();
        $diffInMinutes = $lastActiveAt->diffInMinutes($now);
        $diffInHours = $lastActiveAt->diffInHours($now);

        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' Menit Yang Lalu';
        } elseif ($diffInHours < 24) {
            return $diffInHours . ' Jam Yang Lalu';
        } else {
            return $lastActiveAt->diffForHumans();
        }
    }
}
