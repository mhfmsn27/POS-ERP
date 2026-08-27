<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class BankRekonsiliationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $percentase     = $this->total_reconciled_count > 0 ? $this->total_unreconciled_count / $this->total_reconciled_count * 100 : 0;
        return [
            'bank'              => $this->account->name ?? '',
            'percent'           => (int)round(abs($percentase) / 5) * 5,
            'rekening'          => array(
                'amount'            => number_format($this->total_reconciled_amount),
                'total'             => number_format($this->total_unreconciled_count)
            ),
            'fakturco'          => array(
                'amount'            => number_format($this->total_unreconciled_amount),
                'total'             => number_format($this->total_reconciled_count)
            )
        ];
    }
}
