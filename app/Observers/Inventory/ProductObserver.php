<?php

namespace App\Observers\Inventory;

use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductObserver
{
    protected $stockObserver;
    protected $variationObserver;

    public function __construct(StockObserver $stockObserver, VariationObserver $variationObserver)
    {
        $this->stockObserver        = $stockObserver;
        $this->variationObserver    = $variationObserver;
    }

    public function getData(Request $request)
    {
        $query = Product::with(['variant', 'variant.stock', 'stock'])->where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%')->orWhere(function ($query) use ($request) {
                $query->whereHas('variant', function ($q) use ($request) {
                    return  $q->where('name', 'like', '%' . $request->name . '%');
                });
            }) : '';
        })->where(function ($query) use ($request) {
            return $request->brand ?
                $query->whereIn('brand_id', explode(",", $request->brand)) : '';
        })->where(function ($q) use ($request) {
            return $request->category ?
                $q->whereIn('category_id', explode(",", $request->category)) : '';
        })->where(function ($q) use ($request) {
            return $request->status ?
                $q->where('is_active', $request->status) : '';
        })->where(function ($q) use ($request) {
            return $request->must_stock == 'true' ?  $q->whereHas('variant.stock', function ($query) {
                return  $query->selectRaw("sum(qty_available) as qty")->havingRaw('sum(qty_available) > ?', [0]);
            })->orWhere("is_stock", "no") : '';
        });

        if ($request->sort == 'name') {
            $query->orderBy('name', $request->sortby);
        } else if ($request->sort == 'category') {
            $query->orderBy(Category::select('name')->whereColumn('categories.id', 'products.category_id'), $request->sortby);
        } else if ($request->sort == 'brand') {
            $query->orderBy(Brand::select('name')->whereColumn('brands.id', 'products.brand_id'), $request->sortby);
        } else if ($request->sort == 'product_all_stock') {
            $query->withCount(['stock as max_qty_available' => function (Builder $query) {
                $query->select(DB::raw('max(qty_available)'));
            }]);

            $query->orderBy('max_qty_available', $request->sortby);
        }

        return $query;
    }

    public function createData(Request $request)
    {

        $generalInformation = $request->info_general;
        $otherInformation   = $request->other_detail;


        $products = Product::create([
            'name'              => $generalInformation['name'],
            'sku'               => rand(),
            'type'              => $generalInformation['is_variant'] == false ? 'single' : 'variable',
            'category_id'       => $generalInformation['category']['id'],
            'is_stock'          => $generalInformation['is_stock'] == false ? 'no' : 'yes',
            'barcode_type'      => $generalInformation['barcode_type'],
            'alert_quantity'    => $generalInformation['is_stock'] == true ? $generalInformation['alert_qty'] : 0,
            'brand_id'          => $otherInformation['brand']['id'],
            'weight'            => $otherInformation['weight'],
            'description'       => $otherInformation['description'],
            'is_account'        => $generalInformation['is_account'] == true ? 'yes' : 'no',
            'supply'            => $generalInformation['is_account'] == true ? $generalInformation['supply']['id'] : null,
            'sale'              => $generalInformation['is_account'] == true ? $generalInformation['sale']['id'] : null,
            'retur_sale'        => $generalInformation['is_account'] == true ? $generalInformation['return_sale']['id'] : null,
            'discount_sale'     => $generalInformation['is_account'] == true ? $generalInformation['discount']['id'] : null,
            'sent'              => $generalInformation['is_account'] == true ? $generalInformation['sent']['id'] : null,
            'cost'              => $generalInformation['is_account'] == true ? $generalInformation['cost']['id'] : null,
            'retur_purchase'    => $generalInformation['is_account'] == true ? $generalInformation['retur_purchase']['id'] : null,
            'supplier_debt'     => $generalInformation['is_account'] == true ? $generalInformation['supplier_debt']['id'] : null,
        ]);

        return $products;
    }

    public function updateProduct(Request $request, Product $product)
    {
        $product->update([
            'name'              => $request->name,
            'type'              => $request->is_variant == false ? 'single' : 'variable',
            'category_id'       => $request->category['id'],
            'barcode_type'      => $request->barcode_type,
            'alert_quantity'    => $product->is_stock == 'yes' ? $request->alert_qty : 0,
            'brand_id'          => $request->brand['id'] ?? null,
            'weight'            => $request->weight,
            'is_active'         => $request->is_active == true ? 'yes' : 'no',
            'description'       => $request->description,
        ]);
    }

    public function deleteData(Product $product)
    {
        $product->variant()->delete();
        $product->allstock()->delete();
        $product->delete();
    }
}
