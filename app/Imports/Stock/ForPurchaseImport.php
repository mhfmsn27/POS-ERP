<?php

namespace App\Imports\Stock;

use App\Helper;
use App\Models\Product\Stock;
use App\Models\Product\Variation;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ForPurchaseImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function rules(): array
    {
        return [
            'tambah_stok'    => 'required',
            'product_id'    => 'required',
            'harga_modal'    => 'required'
        ];
    }

    public function collection(Collection $rows)
    {

        $total = 0;
        foreach ($rows as $row) {
            if ($row['variation_id'] != null && $row['tambah_stok'] > 0) {

                $total += Helper::fresh_aprice($row['harga_modal']) * $row['tambah_stok'];
                $purchase = new Purchase();
                $purchase->transaction_id = $this->data->id;
                $purchase->store_id       = $this->data->store_id;
                $purchase->product_id     = $row['product_id'];
                $purchase->variation_id   = $row['variation_id'];
                $purchase->quantity = $row['tambah_stok'];


                $purchase->discount_percent = 0;
                $purchase->purchase_price          = Helper::fresh_aprice($row['harga_modal']);
                $purchase->without_discount        = Helper::fresh_aprice($row['harga_modal']);
                $purchase->purchase_price_inc_tax =  Helper::fresh_aprice($row['harga_modal']);
                $purchase->item_tax       = 0;
                $purchase->save();

                $getVariation = Variation::findOrFail($row['variation_id']);
                $getVariation->purchase_price = Helper::fresh_aprice(Helper::fresh_aprice($row['harga_modal']));
                $getVariation->save();


                $CheckSkus = Stock::where('product_id', $row['product_id'])
                    ->where('variation_id', $row['variation_id'])
                    ->where('store_id', $this->data->store_id)->first();
                if ($CheckSkus == null) {
                    $skus = new Stock();
                    $skus->qty_available          = $row['tambah_stok'];
                } else {
                    $skus = Stock::findOrFail($CheckSkus->id);
                    $skus->qty_available          = $skus->qty_available + $row['tambah_stok'];
                }
                $skus->product_id     = $row['product_id'];
                $skus->variation_id   = $row['variation_id'];
                $skus->store_id       = $this->data->store_id;
                $skus->save();
            }
        }

        $update = Transaction::find($this->data->id);
        $update->total_before_tax = $total;
        $update->tax_amount       = 0;
        $update->final_total = $total;
        $update->save();
    }
}
