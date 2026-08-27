<?php

namespace Poshub\Ecommerce\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\BlogCategory;
use Poshub\Ecommerce\Requests\CategoryBlogRequest;
use Poshub\Ecommerce\Resources\Admin\CategoryBlogResource;

class BlogCategoryController extends Controller
{

      public $uploadImageProcess;

      public function __construct(UploadImageProcess $uploadImageProcess)
      {
            $this->uploadImageProcess   = $uploadImageProcess;
      }

      public function index(Request $request)
      {
            $limit  = $request->input('limit', 10);
            $data   = BlogCategory::where(function ($q) use ($request) {
                  return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
            })->orderBy('name', 'asc');

            $totalRows  = $data->count();
            $categories = $data->paginate($limit);

            return response()->json([
                  'totalRows'       => $totalRows,
                  'categories'      => CategoryBlogResource::collection($categories),
            ]);
      }

      public function detail($id)
      {
            $category   = BlogCategory::find($id);
            return response()->json(CategoryBlogResource::make($category));
      }


      public function store(CategoryBlogRequest $request)
      {

            try {

                  $image = '';


                  if ($request->image) {

                        $icon = explode(',', $request->image);
                        if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                              $imageData = base64_decode($icon[1]);

                              if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                                    $image = $this->uploadImageProcess->uploadFile($request->image, $request->name, 'uploads/' . auth()->user()->business_id . '/categories/');
                              }
                        }
                  }

                  if ($image == '') {
                        $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/' . auth()->user()->business_id . '/categories/');
                  }

                  BlogCategory::create([
                        'name'            => $request->name,
                        'image'           => $image,
                  ]);

                  return response()->json([
                        'message'         => 'Data KAtegori Berhasil Di Tambahkan',
                        'status'          => true
                  ], 200);
            } catch (\Exception $e) {
                  return response()->json([
                        'message'   => $e->getMessage(),
                        'status'    => false
                  ], 409);
            }
      }

      public function edit(CategoryBlogRequest $request, $id)
      {

            $category   = BlogCategory::find($id);
            try {
                  $image = '';

                  if ($request->image) {
                        $icon = explode(',', $request->image);
                        if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                              $imageData = base64_decode($icon[1]);

                              if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                                    $this->uploadImageProcess->unlinkFile($category->image);
                                    $image = $this->uploadImageProcess->uploadFile($request->image, $request->name, 'uploads/' . auth()->user()->business_id . '/categories/');
                              }
                        }
                  }

                  $category->update([
                        'name'            => $request->name,
                        'image'           => $image != '' ? $image : $category->image,
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
            $category   = BlogCategory::find($id);
            $category->delete();
            return response()->json([
                  'message'         => 'Data Kategori Berhasil Di Perbaharui',
                  'status'          => true
            ], 200);
      }
}
