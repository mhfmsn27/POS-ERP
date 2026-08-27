<?php

namespace Poshub\Ecommerce\Repositories;

use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Models\Transaction\Sell;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class ProductRepository
{
      public function getData(Request $request)
      {
            $query = Product::where(function ($q) use ($request) {
                  return $request->name ? $q->where('name', 'like', '%' . $request->name . '%')->orWhereHas("variant", function ($q) use ($request) {
                        return  $q->where('name', 'like', '%' . $request->name . '%');
                  }) : '';
            })->where('store_id', session()->get('dfstore'))->where(function ($q) use ($request) {
                  return $request->category ? $q->where("category_id", $request->category) : '';
            })->where(function ($q) use ($request) {
                  return $request->min_price ? $q->whereHas("variant", function ($q) use ($request) {
                        $q->where("selling_price", ">=", $request->min_price);
                  }) : '';
            })->where(function ($q) use ($request) {
                  return $request->max_price ? $q->whereHas("variant", function ($q) use ($request) {
                        $q->where("selling_price", "<=", $request->max_price);
                  }) : '';
            })->orderBy("name", "asc");

            if (with_stock() == 'no') {
                  $query->whereHas('variant.stock_in_website', function ($query) {
                        return  $query->selectRaw("sum(qty_available) as qty")->havingRaw('sum(qty_available) > ?', [0]);
                  });
            }

            return $query;
      }

      public function getPopularProducts($limit)
      {
            $lstMonth = Carbon::today()->subDays(30);

            $query = Sell::with(['product'])
                  ->selectRaw('sum(qty) as quantity, product_id, store_id')
                  ->where("store_id", Session::get("dfstore"))
                  ->whereHas("transaction", function ($q) use ($lstMonth) {
                        return $q->where('transaction_date', ">=", $lstMonth);
                  });

            if (with_stock() == 'no') {
                  $query->where(function ($q) {
                        $q->whereHas('product.stock_in_website', function ($query) {
                              return  $query->selectRaw("sum(qty_available) as qty")->havingRaw('qty > ?', [0]);
                        });
                  });
            }

            $data = $query->groupBy('product_id')->orderBy("quantity", "desc")->limit($limit)->get();

            return $data;
      }

      public function getCategory(Request $request)
      {
            return Category::where(function ($q) use ($request) {
                  return $request->term ? $q->where('name', 'like', '%' . $request->term . '%') : '';
            })->where('store_id', session()->get('dfstore'))->where("show_in_ecommerce", "yes")->orderBy("name", "asc")->limit(20)->get();
      }
}
