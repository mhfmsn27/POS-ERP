<?php

namespace App\Observers\Inventory;

use App\Models\Product\PriceVariationStore;
use App\Models\Product\Product;
use App\Models\Product\Stock;
use App\Models\Product\Variation;
use App\Observers\Transaction\Purchase\PurchaseObserver;
use Illuminate\Http\Request;

class VariationObserver
{

    protected $stockObserver;
    protected $purchaseObserver;

    public function __construct(StockObserver $stockObserver, PurchaseObserver $purchaseObserver)
    {
        $this->stockObserver        = $stockObserver;
        $this->purchaseObserver     = $purchaseObserver;
    }

    public function getData(Request $request, $condition = '')
    {
        return Variation::with([
            'product',
            'stock',
            'unit',
            'unit.unit_turunan',
            'unit_sell',
            'unitpo'
        ])->where(function ($query) use ($request) {
            return $request->name ? $query->where('name', 'like', '%' . $request->name . '%')->orWhere('sku', 'like', '%' . $request->name . '%')->orWhereHas('product', function ($q) use ($request) {
                return $q->where('name', 'like', '%' . $request->name . '%');
            }) : '';
        })->where(function ($query) use ($request) {
            return $request->product ? $query->where('product_id', $request->product) : '';
        })->whereHas('product', function ($q) use ($request) {
            return $request->with_stock == 'true' ? $q->where('is_stock', 'yes') : '';
        })->whereHas('product', function ($q) {
            return $q->where("is_active", "yes");
        })->where(function ($q) use ($condition) {
            return $condition == 'minus' ? $q->whereHas('all_stock', function ($q) {
                return $q->where('qty_available', '<', 0);
            }) : '';
        })->orderBy('name', 'asc');
    }

    public function byBarcode(Request $request)
    {
        return Variation::where('barcode', $request->barcode)->first();
    }

    public function createData($variation, Product $product)
    {

        $storeDetail = my_store_detail();
        $taxrate     = $product->is_stock == 'yes' ? ($storeDetail->tax_one ?? 0) : ($storeDetail->tax_two ?? 0);
        $variant     =  Variation::create([
            'barcode'           => empty($variation['barcode']) ? generate_ean() : $variation['barcode'],
            'name'              => $variation['name'],
            'sku'               => empty($variation['sku']) ? generate_sku($product->id) : $variation['sku'],
            'product_id'        => $product->id,
            'purchase_price'    => $product->is_stock == 'yes' ? ($variation['purchase_price'] ?? 0) : 0,
            'selling_price'     => $variation['selling_price'] ?? 0,
            'price_inc_tax'     => (($storeDetail->tax_option ?? '') == 'active' && !empty($variation['tax_sell']) && ($variation['purchase_price'] ?? 0) > 0 && $taxrate > 0) ? ($variation['purchase_price'] + ((int)$taxrate / 100 * $variation['purchase_price'])) : 0,
            'tax_sell'          => $variation['tax_sell'] ?? false,
            'tax_purchase'      => $variation['tax_purchase'] ?? false,
            'rak_id'            => $variation['rak']['id'] ?? null,
            'unit_id'           => $variation['unit'] ?? null,
            'unit_sale'         => $variation['unit_sale'] ?? null,
            'grocery'           => $variation['grocery'] ?? null
        ]);

        $product->is_stock == 'yes' ? $this->createPurchasePriceStore($variant) : '';

        if ($product->is_stock == 'yes' && $variation['stock'] > 0) {

            $stock = $this->stockObserver->createData($variant);

            if ((int)$variation['stock'] > 0) {
                $this->purchaseObserver->handlingFirstStock($variant, $stock, $variation['stock'], '', $variation['purchase_price'], 'open_stock');
            }
        }

        return $variant;
    }

    public function createPurchasePriceStore(Variation $variation)
    {
        PriceVariationStore::updateOrCreate(
            ['variation_id' => $variation->id],
            ['price' => averaging_price($variation)]
        );
    }

    public function updateData(Request $request, Product $product)
    {
        if ($request->id != null) {
            $variation  = Variation::find($request->id);
            $variation->update([
                'barcode'           => $request->barcode == '' || $request->barcode == null ? generate_ean() : $request->barcode,
                'name'              => $request->name,
                'sku'               => $request->sku == '' || $request->sku == null ? generate_sku($product->id) : $request->sku,
                'product_id'        => $product->id,
                'selling_price'     => $request->selling_price,
                'tax_sell'          => $request->tax_sell,
                'tax_purchase'      => $request->tax_purchase,
                'rak_id'            => $request->rak['id'],
                'unit_id'           => $request->unit,
                'grocery'           => $request->grocery,
            ]);


            if ($product->is_stock == 'yes' && $variation->open_stock) {
                $stock = $this->stockObserver->createData($variation);
                $this->purchaseObserver->handlingUpdateFirstStock($variation, $stock, $request->stock, '', $request->purchase_price, 'open_stock');
            } else {
                if ($product->is_stock == 'yes' &&  $request->stock > 0) {
                    $stock = $this->stockObserver->createData($variation);

                    if ((int)$request->stock > 0) {
                        $this->purchaseObserver->handlingFirstStock($variation, $stock, $request->stock, '', $request->purchase_price, 'open_stock');
                    }
                }
            }

            return $variation;
        } else {
            return $this->createData($request, $product);
        }
    }

    public function deleteData(Variation $variation)
    {
        Stock::where("variation_id", $variation->id)->delete();
        PriceVariationStore::where('variation_id', $variation->id)->delete();
        $variation->delete();
    }

    
}
