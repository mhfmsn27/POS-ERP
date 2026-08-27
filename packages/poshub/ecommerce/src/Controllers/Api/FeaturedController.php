<?php

namespace Poshub\Ecommerce\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\SmallFeature;
use Poshub\Ecommerce\Requests\FeaturesRequest;
use Poshub\Ecommerce\Resources\Admin\FeaturesResource;

class FeaturedController extends Controller
{

      public $uploadImageProcess;

      public function __construct(UploadImageProcess $uploadImageProcess)
      {
            $this->uploadImageProcess   = $uploadImageProcess;
      }

      public function index(Request $request)
      {

            $limit  = $request->input('limit', 10);
            $data   = SmallFeature::where(function ($q) use ($request) {
                  return $request->title ?  $q->where('title', 'like', '%' . $request->title . '%') : '';
            })->orderBy('title', 'asc');

            $totalRows  = $data->count();
            $features   = $data->paginate($limit);

            return response()->json([
                  'totalRows'       => $totalRows,
                  'featureds'        => FeaturesResource::collection($features),
            ]);
      }

      public function detail($id)
      {
            $featured   = SmallFeature::find($id);
            return response()->json(FeaturesResource::make($featured));     
      }


      public function store(FeaturesRequest $request)
      {

            try {
                  $image = '';


                  if ($request->image) {

                        $icon = explode(',', $request->image);
                        if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                              $imageData = base64_decode($icon[1]);

                              if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                                    $image = $this->uploadImageProcess->uploadFile($request->image, $request->title, 'uploads/' . auth()->user()->business_id . '/features/');
                              }
                        }
                  }

                  if ($image == '') {
                        $image = $this->uploadImageProcess->createDafaultMedia($request->title, 'uploads/' . auth()->user()->business_id . '/features/');
                  }


                  SmallFeature::create([
                        'title'           => $request->title,
                        'subtitle'        => $request->subtitle,
                        'position'        => $request->position,
                        'image'           => $image,
                  ]);

                  return response()->json([
                        'message'         => 'Data Content Feature Berhasil Di Tambahkan',
                        'status'          => true
                  ], 200);
            } catch (\Exception $e) {
                  return response()->json([
                        'message'   => $e->getMessage(),
                        'status'    => false
                  ], 409);
            }
      }

      public function edit(FeaturesRequest $request, $id)
      {

            $featured   = SmallFeature::find($id);

            try {

                  $image      = '';
                  if ($request->image) {
                        $icon = explode(',', $request->image);
                        if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                              $imageData = base64_decode($icon[1]);
      
                              if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                                    $this->uploadImageProcess->unlinkFile($featured->image);
                                    $image = $this->uploadImageProcess->uploadFile($request->image, $request->bank_name, 'uploads/' . auth()->user()->business_id . '/features/');
                              }
                        }
                  }
      
                  $featured->update([
                        'title'           => $request->title,
                        'subtitle'        => $request->subtitle,
                        'position'        => $request->position,
                        'image'           => $image != '' ? $image : $featured->image,
                  ]);

                  return response()->json([
                        'message'         => 'Data Content Feature Berhasil Di Perbaharui',
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

            $featured   = SmallFeature::find($id);
            $featured->delete();
            return response()->json([
                  'message'         => 'Data Content Feature Berhasil Di Hapus',
                  'status'          => true
            ], 200);
      }
}
