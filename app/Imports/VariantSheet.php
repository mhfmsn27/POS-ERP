<?php

namespace App\Imports;

use App\Helper;
use App\Models\Admin\Store;
use App\Models\Product\Stock;
use App\Models\Product\Variation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class VariantSheet implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */



    public function model(array $row)
    {

        if ($row['id'] != null) {
            if (
                $row['product_id'] == null  || $row['purchase_price'] == null || $row['name'] == null || $row['selling_price'] == null
                || $row['tax_type'] == null || $row['tax'] == null || $row['unit_id'] == null
            ) {
                Validator::make($row, [
                    'id'        => 'required|unique:variations,id',
                    'product_id'      => 'required',
                    'purchase_price'   => 'required',
                    'name'   => 'required',
                    'selling_price' => 'required',
                    'tax_type'  => 'required',
                    'tax'       => 'required',
                    'unit_id'   => 'required'
                ])->validate();
            }
 
            $g_margin = 0;
            $price = 0;
            $getStore = Store::all();
            $getMargin = 0;
            $sku = $row['sku'] ? $row['sku'] : $this->generateEAN();
            $rak = $row['rak_id'] ? $row['rak_id'] : null;
            $tax = $row['tax'] ? $row['tax'] : 0;

            if ($row['selling_price'] > 0) {
                $margin = ((int)$row['selling_price'] / (int)$row['purchase_price']) * 100 - 100;
                $getMargin = ceil($margin);
 
                $g_margin = $getMargin;

                if ($row['selling_price_grosir'] != null || $row['selling_price_grosir'] > 0) {
                    $price = $row['selling_price_grosir'];
                    $gm = ($row['selling_price_grosir'] / $row['purchase_price']) * 100 - 100;
                    $g_margin = ceil($gm);
                }
            }


            $variation = new Variation();
            $variation->id = $row['id'];
            $variation->sku = $sku;
            $variation->product_id = $row['product_id'];
            $variation->price_inc_tax = $row['purchase_price'];
            $variation->purchase_price = $row['purchase_price'];
            $variation->name = $row['name'];
            $variation->selling_price = $row['selling_price'];
            $variation->grocery = $price;
            $variation->margin = $getMargin;
            $variation->margin_grocery = $g_margin;
            $variation->unit_id = $row['unit_id'];
            $variation->rak_id = $rak;
            $variation->taxrate = $tax;
            $variation->tax_type = $row['tax_type'];
            $variation->save();
 
            foreach ($getStore as $s) {
                $CheckSkus = Stock::where('product_id', $row['product_id'])->where('variation_id', $row['id'])->where('store_id', $s->id)->first();
                if ($CheckSkus == null) {
                    $stock_store = new Stock();
                    $stock_store->product_id =  $row['product_id'];
                    $stock_store->variation_id = $row['id'];
                    $stock_store->store_id = $s->id;
                    $stock_store->qty_available = 0;
                    $stock_store->save();
                }
            }
        }
    }

    function generateEAN()
    {
        $code = '200' . str_pad($this->generateRandomCode(), 9, '0');
        $weightflag = true;
        $sum = 0;
        for ($i = strlen($code) - 1; $i >= 0; $i--) {
            $sum += (int)$code[$i] * ($weightflag ? 3 : 1);
            $weightflag = !$weightflag;
        }
        $code .= (10 - ($sum % 10)) % 10;
        return $code;
    }

    function generateRandomCode($length = 8)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
