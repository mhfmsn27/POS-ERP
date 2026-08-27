<?php

namespace App\Http\Resources\Reports\Stocks;

use Illuminate\Http\Resources\Json\JsonResource;

class StockHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $varname = $this->variation->name ?? null;
        $proname = $this->product->name ?? null;

        if ($varname == 'no-name') {
            $varname = '';
        }

        return [
            'name'          => $proname . ' ' . $varname,
            'transaction'   => array(
                'ref_no'        => $this->transaction->ref_no ?? '',
                'id'            => $this->transaction->id ?? ''
            ),
            'customer'      => array(
                'name'          => $this->transaction->customer->name ?? '',
            ),
            'supplier'      => array(
                'name'          => $this->transaction->supplier->name ?? ''
            ),
            'date'          => $this->transaction->transaction_date->format('d/m/Y') ?? '',
            'time'          => $this->created_at->format('H:i:s'),
            'qty'           => (int)$this->qty,
            'unit'          => $this->unit->name ?? '',
            'type'          => $this->type,
            'from'          => (int)$this->from,
            'to'            => (int)$this->to,
            'store'         => array(
                'id'            => $this->transaction->store->id ?? '',
                'name'          => $this->transaction->store->name ?? ''
            )
        ];
    }
}
