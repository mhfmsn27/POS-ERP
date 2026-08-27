<?php

namespace App\Observers\Transaction\Sales;

use App\Helper;
use App\Models\Admin\Customer;
use App\Models\Product\Unit;
use App\Models\Transaction\Sell;
use App\Models\Transaction\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class OfferObserver
{
    public function getData(Request $request)
    {
        $query = Transaction::with('customer')->where(function ($query) use ($request) {
            return $request->customer ? $query->whereIn('customer_id', explode(",", $request->customer)) : '';
        })->where(function ($query) use ($request) {
            return $request->status ?  $query->whereIn('status', explode(",", $request->status)) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('transaction_date', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : '';
            }
        })->where(function ($query) use ($request) {
            return $request->ref ? $query->where('ref_no', 'like', '%' . $request->ref . '%')->orWhere('supplier_ref', 'like', '%' . $request->ref . '%')->orWhere(function ($q) use ($request) {
                $q->whereHas('customer', function ($q) use ($request) {
                    return $request->ref ? $q->where('name', 'like', '%' . $request->ref . '%') : '';
                });
            }) : '';
        })->where(function ($query) use ($request) {
            return $request->createdby ?  $query->whereIn('created_by', explode(",", $request->createdby)) : '';
        })->where(function ($query) use ($request) {
            return $request->warehouse ?  $query->where('warehouse_id', $request->warehouse) : ($request->with_warehouse == 'yes' ? $query->where("warehouse_id", null)->orWhere("warehouse_id", "") : '');
        })->where('type', 'offer');

        if ($request->sort == 'date') {
            $query->orderBy('transaction_date', $request->sortby);
        } else if ($request->sort == 'ref_no') {
            $query->orderBy('ref_no', $request->sortby);
        } else if ($request->sort == 'customer.name') {
            $query->orderBy(Customer::select('name')->whereColumn('customers.id', 'transactions.customer_id'), $request->sortby);
        } else if ($request->sort == 'final_total') {
            $query->orderBy('final_total', $request->sortby);
        } else if ($request->sort == 'created.name') {
            $query->orderBy(User::select('name')->whereColumn('users.id', 'transactions.created_by'), $request->sortby);
        }

        return $query;
    }

    public function createUpdateInformation(Request $request, $condition, Transaction $transaction = null)
    {
        if ($condition == 'create') {
            $getTransaction         = Transaction::where("type", "offer")->whereDate("created_at", date("Y-m-d"))->count() + 1;
            $invoiceNumber          = sprintf("%05d", $getTransaction);
            $refNo                  = Helper::transactionKey('RCP', $invoiceNumber);

            $data                   = new Transaction();
            $data->invoice_no       = $invoiceNumber;
            $data->status           = 'open';
            $data->ref_no           = $request->no_ref != null ? $request->no_ref : $refNo;
            $data->old_warehouse_id = $request->warehouse['id'];
            $data->transaction_date = $request->date ? Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s') : date('Y-m-d H:i:s');
        } else {
            $data                   = $transaction;
            $data->ref_no           = $request->no_ref != null ? $request->no_ref : $transaction->ref_no;
            $data->transaction_date = $request->date ? Helper::setTimeZoneLocal($request->date) . ' ' . $transaction->created_at->format("H:i:s") : date('Y-m-d') . ' ' . $transaction->created_at->format('H:i:s');
            $data->old_warehouse_id = $data->warehouse_id;
        }


        $data->customer_id          = $request->customer['id'];
        $data->warehouse_id         = $request->warehouse['id'];
        $data->address              = $request->address ?? '';
        $data->courier_id           = $request->courier['id'];
        $data->type                 = 'offer';
        $data->created_by           = auth()->user()->id;
        $data->additional_notes     = $request->note;
        $data->save();

        return $data;
    }

    public function savingItems(Request $request, Transaction $transaction)
    {
        $listData = array();
        foreach ($request->items as $d) {
            $this->createItems($d, $transaction);
        }

        return $listData;
    }

    public function createItems($d, $transaction)
    {
        $unit       = null;
        $quantity   = $d['qty'];

        if ($d['unit']) {
            $unit           = Unit::where("id", $d['unit'])->first();
            if ($unit) {
                $quantity   = $d['qty'] * $unit->value;
            }
        }

        $sell = Sell::create([
            'item_name'                         => $d['name'],
            'offer_id'                          => $transaction->id,
            'product_id'                        => $d['product_id'],
            'variation_id'                      => $d['variation_id'],
            'unit_qty'                          => $d['qty'],
            'qty'                               => $quantity,
            'unit_price'                        => $d['without_discount'],
            'unit_price_before_disc'            => $d['without_discount'],
            'unit_id'                           => $d['unit'],
        ]);

        $this->subtotalTransactionChange($transaction);
    }

    public function subtotalTransactionChange(Transaction $transaction)
    {
        $subtotal                           = $transaction->offer()->selectRaw("sum(unit_price * qty) as jumlah")->first();
        $discountTotal                      = $transaction->discount_type == 'percent' && $transaction->discount_amount > 0 && $subtotal->jumlah > 0 ? (($transaction->discount_amount / 100) * $subtotal->jumlah) : $transaction->discount_amount;
        $taxFinal                           = $transaction->offer()->selectRaw('sum(tax_total * qty) as jumlah')->first();
        $govermentTax                       = $transaction->offer()->selectRaw('sum(goverment_tax * qty) as jumlah')->first();
        $serviceTax                         = $transaction->offer()->selectRaw('sum(service_tax * qty) as jumlah')->first();

        $transaction->update([
            'tax_final'             => $taxFinal->jumlah,
            'goverment_tax'         => $govermentTax->jumlah,
            'service_tax'           => $serviceTax->jumlah,
            'discount_final'        => $discountTotal,
            'total_before_tax'      => (int)$subtotal->jumlah,
            'final_total'           => ((int)$subtotal->jumlah + $transaction->shipping_charges + ($transaction->customer->tax_default == 'yes' ? 0 : $taxFinal->jumlah)) - ($discountTotal + $govermentTax->jumlah + $serviceTax->jumlah)
        ]);

        if ($transaction->offer()->where(function ($q) {
            return $q->where("transaction_id", "!=", null)->orWhere('transaction_received_id', "!=", null);
        })->count() == $transaction->offer()->count()) {
            $transaction->update([
                'status'        => 'close'
            ]);
        } else {
            $transaction->update([
                'status'        => 'open'
            ]);
        }
    }

    public function updateItems($request, Sell $sell)
    {
        $transaction    = $sell->transaction_offer;
        $unit           = null;
        $quantity       = $request['qty'];

        if ($request['unit']) {
            $unit           = Unit::where("id", $request['unit'])->first();
            if ($unit) {
                $quantity   = $request['qty'] * $unit->value;
            }
        }

        $sell->update([
            'item_position'                     => $request['item_position'],
            'item_name'                         => $request['name'],
            'unit_qty'                          => $request['qty'],
            'qty'                               => $quantity,
            'unit_id'                           => $unit != null ? $unit->id : null,
            'unit_price'                        => $request['without_discount'],
            'unit_price_before_disc'            => $request['without_discount'],
        ]);

        $this->subtotalTransactionChange($transaction);
    }

    public function deleteItems(Sell $sell)
    {

        $sell->forceDelete();
        $this->subtotalTransactionChange($sell->transaction_offer);
    }
}
