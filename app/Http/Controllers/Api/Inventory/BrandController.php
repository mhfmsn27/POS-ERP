<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\BrandRequest;
use App\Http\Resources\Inventory\BrandResource;
use App\Models\Product\Brand;
use App\Observers\Inventory\BrandObserver;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BrandController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Products Brand Controller
    |--------------------------------------------------------------------------
    */

    public $brandObserver;
    public $uploadImageProcess;

    public function __construct(BrandObserver $brandObserver, UploadImageProcess $uploadImageProcess)
    {
        $this->brandObserver        = $brandObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Brands List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        abort_if(Gate::denies('brand_view'), 403);

        $limit  = $request->input('limit', 10);
        $data   = $this->brandObserver->getData($request);

        $totalRows  = $data->count();
        $brands     = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'brands'        => BrandResource::collection($brands),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Brand
    |--------------------------------------------------------------------------
    */

    public function create(BrandRequest $request)
    {

        abort_if(Gate::denies('brand_create'), 403);

        try {

            $image = '';


            if ($request->image) {

                $icon = explode(',', $request->image);
                if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                    $imageData = base64_decode($icon[1]);

                    if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                        $image = $this->uploadImageProcess->uploadFile($request->image, $request->name, 'uploads/' . auth()->user()->business_id . '/products/brand/');
                    }
                }
            }

            if ($image == '') {
                $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/' . auth()->user()->business_id . '/products/category/');
            }


            $this->brandObserver->createData($request, $image);

            return response()->json([
                'message'   => 'Tambah Data berhasil di lakukan',
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
    | 3. Update Brand
    |--------------------------------------------------------------------------
    */

    public function update(BrandRequest $request, Brand $brand)
    {

        abort_if(Gate::denies('brand_update'), 403);

        try {

            $image = '';

            if ($request->image) {
                $icon = explode(',', $request->image);
                if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                    $imageData = base64_decode($icon[1]);

                    if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                        $this->uploadImageProcess->unlinkFile($brand->image);
                        $image = $this->uploadImageProcess->uploadFile($request->image, $request->name, 'uploads/' . auth()->user()->business_id . '/products/brand/');
                    }
                }
            } else {
                $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/' . auth()->user()->business_id . '/products/category/');
            }


            $this->brandObserver->updateData($request, $brand, $image);

            return response()->json([
                'message'   =>  'Edit Data berhasil di lakukan',
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
    | 4. Delete Brand
    |--------------------------------------------------------------------------
    */

    public function delete(Brand $brand)
    {

        abort_if(Gate::denies('brand_delete'), 403);

        $this->uploadImageProcess->unlinkFile($brand->image);
        $brand->delete();

        return response()->json([
            'message'   => 'Hapus data berhasil di lakukan',
            'status'    => true
        ], 200);
    }

}
