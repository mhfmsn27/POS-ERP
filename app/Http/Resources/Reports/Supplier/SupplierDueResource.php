<?php

namespace App\Http\Resources\Reports\Supplier;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierDueResource extends JsonResource
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
            'name'      => $this->name,
            'total_due' => $this->total_due_umur($request->umur),
            'children'  => SupplierDueItemResource::collection($this->due_history()->where("status", "due")->where(function ($q) use ($request) {
                return $request->umur ? $q->whereRaw('DATEDIFF(NOW(), created_at) >= ?', [$request->umur]) : '';
            })->get())
        ];
    }
}
