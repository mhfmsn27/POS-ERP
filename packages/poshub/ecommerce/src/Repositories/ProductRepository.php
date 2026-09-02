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
            $search = $request->name ?? $request->search ?? $request->q ?? '';
            $categoryId = $request->category ?? $request->category_id ?? '';

            $query = Product::where(function ($q) use ($search) {
                  if (!empty($search)) {
                        return $q->where('name', 'like', '%' . $search . '%')
                                 ->orWhere('sku', 'like', '%' . $search . '%')
                                 ->orWhereHas("variant", function ($vq) use ($search) {
                                       return $vq->where('name', 'like', '%' . $search . '%')
                                                 ->orWhere('sku', 'like', '%' . $search . '%');
                                 });
                  }
                  return $q;
            })->where('store_id', session()->get('dfstore'))
              ->where(function ($q) use ($categoryId) {
                  if (!empty($categoryId)) {
                        return $q->where("category_id", $categoryId);
                  }
                  return $q;
            })->where(function ($q) use ($request) {
                  if ($request->min_price) {
                        return $q->whereHas("variant", function ($vq) use ($request) {
                              $vq->where("selling_price", ">=", (float)$request->min_price);
                        });
                  }
                  return $q;
            })->where(function ($q) use ($request) {
                  if ($request->max_price) {
                        return $q->whereHas("variant", function ($vq) use ($request) {
                              $vq->where("selling_price", "<=", (float)$request->max_price);
                        });
                  }
                  return $q;
            });

            // Dynamic Sorting based on request
            $sort = $request->sort ?? 'newest';
            switch ($sort) {
                  case 'price_asc':
                        $query->orderBy('selling_price', 'asc');
                        break;
                  case 'price_desc':
                        $query->orderBy('selling_price', 'desc');
                        break;
                  case 'name_desc':
                        $query->orderBy('name', 'desc');
                        break;
                  case 'oldest':
                        $query->orderBy('id', 'asc');
                        break;
                  case 'name_asc':
                        $query->orderBy('name', 'asc');
                        break;
                  case 'newest':
                  default:
                        $query->orderBy('id', 'desc');
                        break;
            }

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
