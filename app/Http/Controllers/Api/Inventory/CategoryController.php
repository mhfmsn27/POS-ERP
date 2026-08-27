<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\CategoryRequest;
use App\Http\Resources\Inventory\CategoryResource;
use App\Imports\Inventory\CategoryImport;
use App\Models\Product\Category;
use App\Observers\Inventory\CategoryObserver;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    protected $categoryObserver;
    protected $uploadImageProcess;

    public function __construct(CategoryObserver $categoryObserver, UploadImageProcess $uploadImageProcess)
    {
        $this->categoryObserver     = $categoryObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
    }


    /*
    |--------------------------------------------------------------------------
    | 1. Categories List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        abort_if(Gate::denies('category_view'), 403);

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

        abort_if(Gate::denies('category_create'), 403);

        try {

            $image = '';

            if ($request->image) {

                $icon = explode(',', $request->image);
                if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                    $imageData = base64_decode($icon[1]);

                    if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                        $image = $this->uploadImageProcess->uploadFile($request->image, $request->name, 'uploads/' . auth()->user()->business_id . '/products/category/');
                    }
                }
            }

            if ($image == '') {
                $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/' . auth()->user()->business_id . '/products/category/');
            }


            $this->categoryObserver->createData($request, $image);

            return response()->json([
                'message'   => 'Tambah data berhasil di lakukan',
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

    public function update(CategoryRequest $request, Category $category)
    {

        abort_if(Gate::denies('category_update'), 403);

        try {

            $image = '';

            if ($request->image) {
                $icon = explode(',', $request->image);
                if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                    $imageData = base64_decode($icon[1]);

                    if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                        $this->uploadImageProcess->unlinkFile($category->image);
                        $image = $this->uploadImageProcess->uploadFile($request->image, $request->name, 'uploads/products/category/');
                    }
                }
            } else {
                $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/products/category/');
            }

            $this->categoryObserver->updateData($request, $category, $image);

            return response()->json([
                'message'   => 'Edit Data berhasil dilakukan',
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

    public function delete(Category $category)
    {

        abort_if(Gate::denies('category_delete'), 403);
        
        $this->uploadImageProcess->unlinkFile($category->image);
        $category->delete();

        return response()->json([
            'message'   => 'Hapus data berhasil di lakukan',
            'status'    => true
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Import Category
    |--------------------------------------------------------------------------
    */

    public function import(Request $request)
    {

        abort_if(Gate::denies('category_create'), 403);

        $import = Excel::toArray(new CategoryImport(), request()->file('file'));

        if (count($import[0]) > 0) {

            if (count($import[0]) > 200) {
                return response()->jsown([
                    'message'   => 'Batas limit import kategori adalah 200 baris',
                    'status'    => false
                ], 409);
            }

            try {

                DB::beginTransaction();

                foreach ($import[0] as $d) {

                    Category::firstOrNew(
                        ['name'     =>  $d['nama_kategori']],
                        ['detail'   => $d['detail']]
                    )->save();
                }

                DB::commit();

                return response()->json([
                    'status'    => true,
                    'message'   => 'Import data berhasil di lakukan'
                ], 200);
            } catch (\Exception $e) {

                DB::rollBack();
                return response()->json(
                    [
                        'status'    => false,
                        'message'   => $e->getMessage(),
                    ],
                    409
                );
            }
        } else {

            return response()->jsown([
                'message'   => 'Terjadi kesalahan import, silahkan coba ulangi',
                'status'    => false
            ], 409);
        }

        return response()->json([
            'message'   => 'Terjadi kesalahan import, silahkan coba ulangi',
            'status'    => false
        ], 409);
    }

     /*
    |--------------------------------------------------------------------------
    | 6. Import Product Sample
    |--------------------------------------------------------------------------
    */

    public function downloadSample()
    {

        $file = public_path('berkas/import_category_sample.xlsx');
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return Response::download($file, 'import_category_sample.xlsx', $headers);
    }
 
}
