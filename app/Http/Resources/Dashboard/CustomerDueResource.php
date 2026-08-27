<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class CustomerDueResource extends JsonResource
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
            'id'            => $this->customer_id,
            'customer'      => $this->customer->name ?? '',
            'due_date'      => substr($this->due_date,0,10),
            'progress'      => (int)round(abs($this->days_left) / 5) * 5,
            'day_left'      => (int)abs($this->umur($this->date)),
            'amount'        => (float)$this->total_due_amount
        ];
    }

    function umur($date)
    {
         // Parse the transaction date
         $createdDate = Carbon::parse($date);

         // Get the current date
         $now = Carbon::now();
 
         // Calculate the difference in days
         $umur = $createdDate->diffInDays($now);
 
         // Return the age in days
         return $umur;
    }
}
