<?php

namespace App\Http\Controllers\Api\Account\CashIntOut;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashIntOut\CategoryRequest;
use App\Http\Resources\CashIntOut\CategoryResource;
use App\Models\Account\ExpenseCategory;
use App\Observers\CashIntOut\CategoryObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Expense Category Controller
    |--------------------------------------------------------------------------
    */

    public $categoryObserver;

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

        abort_if(Gate::denies('payment_category_view'), 403);

        $limit  = $request->input('limit', 10);
        $data   = $this->categoryObserver->getData($request);

        $totalRows  = $data->count();
        $categories = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'categories'    => CategoryResource::collection($categories),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Category
    |--------------------------------------------------------------------------
    */

    public function create(CategoryRequest $request)
    {

        abort_if(Gate::denies('payment_category_create'), 403);

        try {

            $this->categoryObserver->createData($request);

            return response()->json([
                'message'   => __('validation.success_add_data'),
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 409);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 3. Update Category
    |--------------------------------------------------------------------------
    */

    public function update(CategoryRequest $request, ExpenseCategory $category)
    {

        abort_if(Gate::denies('payment_category_update'), 403);

        try {


            $this->categoryObserver->updateData($request, $category);

            return response()->json([
                'message'   => __('validation.success_update_data'),
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Delete Category
    |--------------------------------------------------------------------------
    */

    public function delete(ExpenseCategory $category)
    {
    
        abort_if(Gate::denies('payment_category_delete'), 403);

        $category->delete();

        return response()->json([
            'message'   => __('validation.success_delete_data'),
            'status'    => true
        ], 200);
    }
}
