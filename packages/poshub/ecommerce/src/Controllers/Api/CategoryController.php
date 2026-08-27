<?php

namespace Poshub\Ecommerce\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product\Category;
use App\Observers\Inventory\CategoryObserver;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Resources\Admin\CategoryResource;

class CategoryController extends Controller
{

      protected $categoryObserver;

      public function __construct(CategoryObserver $categoryObserver)
      {
            $this->categoryObserver     = $categoryObserver;
      }

      /*
    |--------------------------------------------------------------------------
    | 1. Categories List
    |--------------------------------------------------------------------------
    */

      public function index(Request $request)
      {

            $limit  = $request->input('limit', 10);
            $data   = $this->categoryObserver->getData($request);

            $totalRows  = $data->count();
            $categories = $data->paginate($limit);

            return response()->json([
                  'totalRows'     => $totalRows,
                  'categories'    => CategoryResource::collection($categories),
            ]);
      }

      public function store(Request $request)
      {
            try {

                  Category::whereIn("id", array_column($request->categories, 'id'))->update([
                        'show_in_ecommerce'           => 'yes'
                  ]);

                  return response()->json([
                        'message'         => 'Data Kategori Berhasil Di Perbaharui',
                        'status'          => true
                  ], 200);
            } catch (\Exception $e) {
                  return response()->json([
                        'message'   => $e->getMessage(),
                        'status'    => false
                  ], 409);
            }
      }

      public function delete($id)
      {
            $category   = Category::find($id);

            $category->update([
                  'show_in_ecommerce'     => 'no'
            ]);

            return response()->json([
                  'message'         => 'Data Kategori Berhasil Di Hapus',
                  'status'          => true
            ], 200);
      }

      public function changeFeatures($id)
      {
            $category   = Category::find($id);

            $category->update([
                  'featured_category'     => $category->featured_category == 'yes' ? 'no' : 'yes'
            ]);

            return response()->json([
                  'message'         => 'Data Kategori Berhasil Di Perbaharui',
                  'status'          => true
            ], 200);
      }
}
