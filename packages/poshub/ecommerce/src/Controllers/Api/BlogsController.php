<?php

namespace Poshub\Ecommerce\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Poshub\Ecommerce\Models\Blog;
use Poshub\Ecommerce\Requests\BlogRequest;
use Poshub\Ecommerce\Resources\Admin\BlogDetailResource;
use Poshub\Ecommerce\Resources\Admin\BlogResource;

class BlogsController extends Controller
{
      public $uploadImageProcess;

      public function __construct(UploadImageProcess $uploadImageProcess)
      {
            $this->uploadImageProcess   = $uploadImageProcess;
      }

      public function index(Request $request)
      {
            $limit  = $request->input('limit', 10);
            $data   = Blog::where(function ($q) use ($request) {
                  return $request->title ?  $q->where('title', 'like', '%' . $request->title . '%') : '';
            })->orderBy('created_at', 'desc');

            $totalRows  = $data->count();
            $blogs      = $data->paginate($limit);

            return response()->json([
                  'totalRows'       => $totalRows,
                  'blogs'           => BlogResource::collection($blogs),
            ]);
      }

      public function detail($id)
      {
            $blog = Blog::find($id);
            return response()->json(BlogDetailResource::make($blog));
      }


      public function store(BlogRequest $request)
      {

            try {

                  $image = '';


                  if ($request->image) {

                        $icon = explode(',', $request->image);
                        if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                              $imageData = base64_decode($icon[1]);

                              if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                                    $image = $this->uploadImageProcess->uploadFile($request->image, $request->title, 'uploads/' . auth()->user()->business_id . '/blogs/');
                              }
                        }
                  }

                  if ($image == '') {
                        $image = $this->uploadImageProcess->createDafaultMedia($request->title, 'uploads/' . auth()->user()->business_id . '/blogs/');
                  }

                  Blog::create([
                        'title'                 => $request->title,
                        'category_id'           => $request->category,
                        'description'           => $request->description,
                        'short_description'     => $request->short_description,
                        'user_id'               => Auth::user()->id,
                        'thumbnail'             => $image,
                  ]);

                  return response()->json([
                        'message'         => 'Data Blog Berhasil Di Publish',
                        'status'          => true
                  ], 200);
            } catch (\Exception $e) {
                  return response()->json([
                        'message'   => $e->getMessage(),
                        'status'    => false
                  ], 409);
            }
      }

      public function edit(BlogRequest $request, $id)
      {

            $blog = Blog::find($id);
            try {
                  $image = '';

                  if ($request->image) {
                        $icon = explode(',', $request->image);
                        if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                              $imageData = base64_decode($icon[1]);

                              if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                                    $this->uploadImageProcess->unlinkFile($blog->thumbnail);
                                    $image = $this->uploadImageProcess->uploadFile($request->image, $request->title, 'uploads/' . auth()->user()->business_id . '/blogs/');
                              }
                        }
                  }

                  $blog->update([
                        'title'                 => $request->title,
                        'category_id'           => $request->category,
                        'description'           => $request->description,
                        'short_description'     => $request->short_description,
                        'user_id'               => Auth::user()->id,
                        'thumbnail'             => $image != '' ? $image : $blog->thumbnail,
                  ]);

                  return response()->json([
                        'message'         => 'Data Blog Berhasil Di Perbaharui',
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
            $blog = Blog::find($id);
            $blog->delete();
            return response()->json([
                  'message'         => 'Data Blog Berhasil Di Hapus',
                  'status'          => true
            ], 200);
      }
}
