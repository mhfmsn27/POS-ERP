<?php

use App\Models\Admin\Setting;
use App\Models\Admin\Store;
use App\Models\Product\Variation;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\SellPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Poshub\Ecommerce\Models\EcommerceApiSetting;

/**
 * Helper
 */

 if (!function_exists('my_store')) {

    function my_store()
    {
        $headerStore = request()->header('Storeid') ?? request()->header('storeId') ?? request()->header('store_id');
        if (!empty($headerStore)) {
            return $headerStore;
        }

        if (function_exists('session') && session()->has('mystore') && !empty(session()->get('mystore'))) {
            return session()->get('mystore');
        }

        if (auth()->check()) {
            $user = auth()->user();
            if (!empty($user->store_id) && $user->store_id !== '0') {
                $stores = explode(',', (string)$user->store_id);
                $first = trim($stores[0]);
                if (!empty($first)) {
                    if (function_exists('session')) {
                        session(['mystore' => $first]);
                    }
                    return $first;
                }
            }
        }

        try {
            $firstStore = \App\Models\Admin\Store::first();
            if ($firstStore) {
                if (function_exists('session')) {
                    session(['mystore' => $firstStore->id]);
                }
                return $firstStore->id;
            }
        } catch (\Throwable $e) {
            // ignore if table not loaded
        }

        return null;
    }
}


if (!function_exists('my_store_detail')) {

    function my_store_detail()
    {
        $storeId = my_store();
        if (empty($storeId)) {
            return null;
        }

        static $storeDetailCache = [];
        if (isset($storeDetailCache[$storeId])) {
            return $storeDetailCache[$storeId];
        }

        if (class_exists(\App\Services\Cache\MasterDataCacheService::class)) {
            $store = app(\App\Services\Cache\MasterDataCacheService::class)->getStoreDetail($storeId);
        } else {
            $store = Store::withoutGlobalScopes()->where('id', $storeId)->first(['name','email','phone','address','tax_one','tax_two','tax_option','id','accountant_use']);
        }

        if ($store) {
            $storeDetailCache[$storeId] = $store;
        }
        return $store; 
    }
}



if (!function_exists('store_array')) {

    function store_array($data)
    {

        $listStore = Store::where(function ($query) use ($data) {
            return $query->whereIn('id', $data);
        })->orderBy("name", "asc")->get(['id', 'name']);

        return $listStore;
    }
}

if (!function_exists('tanggal_indo')) {

    function tanggal_indo($tanggal)
    {
        if ($tanggal != null) {
            $bulan = array(
                1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
            $split = explode('-', $tanggal);
            return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
        }
    }
}

if (!function_exists('show_stock')) {

    function show_stock()
    {
        $settings = EcommerceApiSetting::where('store_id', session()->get('dfstore'))->first(['show_stock']);

        if($settings) {
          return  $settings->show_stock;
        }

        return 'yes';
    }
}

if (!function_exists('with_stock')) {

    function with_stock()
    {
        $settings = EcommerceApiSetting::where('store_id', session()->get('dfstore'))->first(['with_stock']);

        if($settings) {
          return  $settings->with_stock;
        }

        return 'no';
    }
}

if (!function_exists('short_date')) {

    function short_date($tanggal)
    {
        if ($tanggal != null) {
            $bulan = array(
                1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
            $split = explode('-', $tanggal);
            return $split[2] . ' ' . substr($bulan[(int)$split[1]], 0, 3) . ' ' . $split[0];
        }
    }
}

if (!function_exists('my_currency')) {

    function my_currency($price)
    {
        $data = Store::findOrFail(Session::get('mystore'));
        $symbol = $data->currency->symbol ?? '';
        if ($data->currency_position == 1) {
            return $symbol . ' ' . number_format($price);
        } elseif ($data->currency_position == 2) {
            return number_format($price) . ' ' . $symbol;
        }
    }
} 

if (!function_exists('my_date')) {

    function my_date($date)
    {
        return $date;
    }
}


if (!function_exists('generate_ean')) {
    function generate_ean()
    {
        $code = '200' . str_pad(random_code(), 9, '0');
        $weightflag = true;
        $sum = 0;
        for ($i = strlen($code) - 1; $i >= 0; $i--) {
            $sum += (int)$code[$i] * ($weightflag ? 3 : 1);
            $weightflag = !$weightflag;
        }
        $code .= (10 - ($sum % 10)) % 10;
        return $code;
    }
}

if (!function_exists('random_code')) {
    function random_code($length = 8)
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


if (!function_exists('generate_sku')) {
    function generate_sku($product)
    {

        $allVariation   = Variation::where("product_id", $product)->count() + 1;
        $skuNumber      = sprintf("%03d", $product) . '' . sprintf("%03d", $allVariation);

        return $skuNumber;
    }
}

if (!function_exists('excelDateToDateTime')) {
    function excelDateToDateTime($excelDate)
    {
        $unixDate = ($excelDate - 25569) * 86400;
        return (new \DateTime())->setTimestamp($unixDate);
    }
}


if (!function_exists('averaging_price')) {

    function averaging_price($variation, $purchasePrice = 0, $date = null)
    {

        if ($variation->product != null) {
            if ($variation->product->is_stock == 'yes') {
                $setting = Setting::first(['stocking_system_type']);
                if ($setting->stocking_system_type == 'averaging') {

                    $price = DB::table('purchases')
                        ->selectRaw("SUM(((quantity + IFNULL(qty_adjusted_add, 0)) -  (IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_adjusted, 0))) * purchase_price) as weighted_sum,
                        SUM((quantity - IFNULL(qty_return, 0)) * purchase_price) as total_capital,
                        SUM(quantity - IFNULL(qty_return, 0)) as total_qty_purchase, 
                        SUM((quantity - IFNULL(qty_return,0)) * purchase_price) as total_capital,
                        (SUM((quantity - IFNULL(qty_return, 0)) * purchase_price) /  SUM(quantity - IFNULL(qty_return, 0))) as average_price,
                        SUM(qty_sold) as total_sales,
                        SUM(quantity + IFNULL(qty_adjusted_add, 0) - (IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_adjusted, 0))) as total_qty")
                        ->where("store_id", my_store())
                        ->where('variation_id', $variation->id)
                        ->where(function ($q) use ($date) {
                            return $date != null ? $q->where("created_at", "<=", $date) : '';
                        })
                        ->groupBy('variation_id')
                        ->first();

                    if (!$price) {
                        $price = DB::table('purchases')
                            ->selectRaw("SUM(((quantity + IFNULL(qty_adjusted_add, 0)) -  (IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_adjusted, 0))) * purchase_price) as weighted_sum,
                            SUM((quantity - IFNULL(qty_return, 0)) * purchase_price) as total_capital,
                            SUM(quantity - IFNULL(qty_return, 0)) as total_qty_purchase, 
                            SUM((quantity - IFNULL(qty_return,0)) * purchase_price) as total_capital,
                            (SUM((quantity - IFNULL(qty_return, 0)) * purchase_price) /  SUM(quantity - IFNULL(qty_return, 0))) as average_price,
                            SUM(qty_sold) as total_sales,
                            SUM(quantity + IFNULL(qty_adjusted_add, 0) - (IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_adjusted, 0))) as total_qty")
                            ->where("store_id", my_store())
                            ->where('variation_id', $variation->id)
                            ->groupBy('variation_id')
                            ->first();
                    }

                    $purchasePrice = SellPurchase::whereHas("sell", function ($q) use ($variation) {
                        return $q->where("variation_id", $variation->id);
                    })->whereHas('sell', function ($q) use ($date) {
                        return $date != null ? $q->where("created_at", "<", $date) : '';
                    })->selectRaw('sum(qty) as total_qty, sum(purchase_price * qty) as total_use_capital')->first();

                    if ($price) {
                        $totalQty   = $price->total_qty;

                        if ($purchasePrice) {
                            $totalQty   = $price->total_qty - $purchasePrice->total_qty;
                        }

                        $weightedAverage    = $price->total_capital - ($purchasePrice != null ? $purchasePrice->total_use_capital : 0);
                        $realCapital        = $weightedAverage > 0  && $totalQty > 0 ? $weightedAverage / $totalQty : 0;
                        return (float)$realCapital;
                    } else {
                        return 0;
                    }
                }
            }
        }

        // $totalQty        = $price->total_qty;

        // if ($date != null) {
        //     $totalQty  = $price->total_qty + ($totalSales != null ? $totalSales->total_qty : 0);
        // }

        // if ($totalSales) {
        //     if ($totalSales->total_qty > $price->total_sales) {
        //         $totalQty  = $price->total_qty;
        //     }
        // } 

        $finalPrice = $purchasePrice > 0 ? $purchasePrice : $variation->purchase_price;
        return (float)$finalPrice;
    }
}

if (!function_exists('sell_purchase_total')) {
    function sell_purchase_total($sellId)
    {

        $sellPurchases = SellPurchase::where("sell_id", $sellId)->selectRaw('sum(purchase_price * qty) as total')->first();

        if ($sellPurchases) {
            return (float)$sellPurchases->total;
        }

        return 0;
    }
}

if (!function_exists('averaging_price')) {

    function averaging_price($variation, $purchasePrice = 0, $date = null)
    {

        if ($variation->product != null) {
            if ($variation->product->is_stock == 'yes') {
                $setting = Setting::first(['stocking_system_type']);
                if ($setting->stocking_system_type == 'averaging') {

                    $price = DB::table('purchases')
                        ->selectRaw("SUM(((quantity + IFNULL(qty_adjusted_add, 0)) -  (IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_adjusted, 0))) * purchase_price) as weighted_sum,
                        SUM((quantity - IFNULL(qty_return, 0)) * purchase_price) as total_capital,
                        SUM(quantity - IFNULL(qty_return, 0)) as total_qty_purchase, 
                        SUM((quantity - IFNULL(qty_return,0)) * purchase_price) as total_capital,
                        (SUM((quantity - IFNULL(qty_return, 0)) * purchase_price) /  SUM(quantity - IFNULL(qty_return, 0))) as average_price,
                        SUM(qty_sold) as total_sales,
                        SUM(quantity + IFNULL(qty_adjusted_add, 0) - (IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_adjusted, 0))) as total_qty")
                        ->where("store_id", my_store())
                        ->where('variation_id', $variation->id)
                        ->where(function ($q) use ($date) {
                            return $date != null ? $q->where("created_at", "<=", $date) : '';
                        })
                        ->groupBy('variation_id')
                        ->first();

                    if (!$price) {
                        $price = DB::table('purchases')
                            ->selectRaw("SUM(((quantity + IFNULL(qty_adjusted_add, 0)) -  (IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_adjusted, 0))) * purchase_price) as weighted_sum,
                            SUM((quantity - IFNULL(qty_return, 0)) * purchase_price) as total_capital,
                            SUM(quantity - IFNULL(qty_return, 0)) as total_qty_purchase, 
                            SUM((quantity - IFNULL(qty_return,0)) * purchase_price) as total_capital,
                            (SUM((quantity - IFNULL(qty_return, 0)) * purchase_price) /  SUM(quantity - IFNULL(qty_return, 0))) as average_price,
                            SUM(qty_sold) as total_sales,
                            SUM(quantity + IFNULL(qty_adjusted_add, 0) - (IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_adjusted, 0))) as total_qty")
                            ->where("store_id", my_store())
                            ->where('variation_id', $variation->id)
                            ->groupBy('variation_id')
                            ->first();
                    }

                    $purchasePrice = SellPurchase::whereHas("sell", function ($q) use ($variation) {
                        return $q->where("variation_id", $variation->id);
                    })->whereHas('sell', function ($q) use ($date) {
                        return $date != null ? $q->where("created_at", "<", $date) : '';
                    })->selectRaw('sum(qty) as total_qty, sum(purchase_price * qty) as total_use_capital')->first();

                    if ($price) {
                        $totalQty   = $price->total_qty;

                        if ($purchasePrice) {
                            $totalQty   = $price->total_qty - $purchasePrice->total_qty;
                        }

                        $weightedAverage    = $price->total_capital - ($purchasePrice != null ? $purchasePrice->total_use_capital : 0);
                        $realCapital        = $weightedAverage > 0  && $totalQty > 0 ? $weightedAverage / $totalQty : 0;
                        return (float)$realCapital;
                    } else {
                        return 0;
                    }
                }
            }
        }

        // $totalQty        = $price->total_qty;

        // if ($date != null) {
        //     $totalQty  = $price->total_qty + ($totalSales != null ? $totalSales->total_qty : 0);
        // }

        // if ($totalSales) {
        //     if ($totalSales->total_qty > $price->total_sales) {
        //         $totalQty  = $price->total_qty;
        //     }
        // } 

        $finalPrice = $purchasePrice > 0 ? $purchasePrice : $variation->purchase_price;
        return (float)$finalPrice;
    }
}

if (!function_exists('qty_having')) {

    function qty_having($productId, $variationId)
    {
        return Purchase::select(DB::raw(
            "id, quantity, qty_sold, qty_transfer, qty_adjusted, qty_adjusted_add, qty_expire, product_id, variation_id, store_id,
            SUM(IFNULL(qty_sold, 0) + IFNULL(qty_adjusted, 0) + IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_expire, 0)) AS qty_sum,
            SUM(quantity + qty_adjusted_add) as qty_total, SUM((quantity + qty_adjusted_add) - (IFNULL(qty_sold, 0) + IFNULL(qty_adjusted, 0) + IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_expire, 0))) as sisa_qty, purchase_price_inc_tax"
        ))
            ->where('product_id', $productId)
            ->where('variation_id', $variationId)
            ->havingRaw("qty_total > qty_sum")
            ->groupBy("id")
            ->orderBy('id', 'asc')
            ->get();
    }
}

if (!function_exists('date_format_indo')) {

    function date_format_indo($date)
    {
        $operationDate = $date;
        $date = new DateTime($operationDate);
        $formattedDate = $date->format('d/m/Y');

        return $formattedDate;
    }
}


