<?php

namespace Poshub\Ecommerce\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\Banner;
use Poshub\Ecommerce\Requests\BannerRequest;
use Poshub\Ecommerce\Resources\Admin\BannerResource;

class BannersController extends Controller
{

      public $uploadImageProcess;

      public function __construct(UploadImageProcess $uploadImageProcess)
      {
            $this->uploadImageProcess   = $uploadImageProcess;
      }

      public function index(Request $request)
      {

            $limit  = $request->input('limit', 10);
            $data   = Banner::where(function ($q) use ($request) {
                  return $request->name ?  $q->where('title', 'like', '%' . $request->name . '%') : '';
            })->orderBy('title', 'asc');

            $totalRows  = $data->count();
            $banners    = $data->paginate($limit);

            return response()->json([
                  'totalRows' => $totalRows,
                  'banners'   => BannerResource::collection($banners),
            ]);
      }

      public function detail($id)
      {
            $banner     = Banner::find($id);
            return response()->json(BannerResource::make($banner));
      }

      public function store(BannerRequest $request)
      {

            try {

                  $image = '';


                  if ($request->image) {

                        $icon = explode(',', $request->image);
                        if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                              $imageData = base64_decode($icon[1]);

                              if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                                    $image = $this->uploadImageProcess->uploadFile($request->image, $request->title, 'uploads/' . auth()->user()->business_id . '/banners/');
                              }
                        }
                  }

                  if ($image == '') {
                        $image = $this->uploadImageProcess->createDafaultMedia($request->title, 'uploads/' . auth()->user()->business_id . '/banners/');
                  }

                  Banner::create([
                        'title'           => $request->title,
                        'position'        => $request->position,
                        'button'          => $request->button,
                        'button_name'     => $request->button == 'yes' ? $request->button_name : '',
                        'button_url'      => $request->button == 'yes' ? $request->button_url : '',
                        'image'           => $image,
                  ]);

                  return response()->json([
                        'message'         => 'Data Banner Berhasil Di Tambahkan',
                        'status'          => true
                  ], 200);
            } catch (\Exception $e) {
                  return response()->json([
                        'message'   => $e->getMessage(),
                        'status'    => false
                  ], 409);
            }
      }

      public function edit(BannerRequest $request, $id)
      {
            $banner     = Banner::find($id);
            try {

                  $image = '';

                  if ($request->image) {
                        $icon = explode(',', $request->image);
                        if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                              $imageData = base64_decode($icon[1]);

                              if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                                    $this->uploadImageProcess->unlinkFile($banner->image);
                                    $image = $this->uploadImageProcess->uploadFile($request->image, $request->title, 'uploads/' . auth()->user()->business_id . '/banners/');
                              }
                        }
                  }

                  $banner->update([
                        'title'           => $request->title,
                        'position'        => $request->position,
                        'button'          => $request->button,
                        'button_name'     => $request->button == 'yes' ? $request->button_name : '',
                        'button_url'      => $request->button == 'yes' ? $request->button_url : '',
                        'image'           => $image != '' ? $image : $banner->image,
                  ]);

                  return response()->json([
                        'message'         => 'Data Banner Berhasil Di Perbaharui',
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
            $banner     = Banner::find($id);
            $banner->delete();
            return response()->json([
                  'message'         => 'Data Banner Berhasil Di Hapus',
                  'status'          => true
            ], 200);
      }
}
