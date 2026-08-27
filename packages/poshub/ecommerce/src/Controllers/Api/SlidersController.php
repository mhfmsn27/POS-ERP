<?php

namespace Poshub\Ecommerce\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\Slider;
use Poshub\Ecommerce\Requests\SliderRequest;
use Poshub\Ecommerce\Resources\Admin\SliderResource;

class SlidersController extends Controller
{

      public $uploadImageProcess;

      public function __construct(UploadImageProcess $uploadImageProcess)
      {
            $this->uploadImageProcess   = $uploadImageProcess;
      }

      public function index(Request $request)
      {

            $limit  = $request->input('limit', 10);
            $data   = Slider::where(function ($q) use ($request) {
                  return $request->name ?  $q->where('title', 'like', '%' . $request->name . '%') : '';
            })->orderBy('title', 'asc');

            $totalRows  = $data->count();
            $sliders    = $data->paginate($limit);

            return response()->json([
                  'totalRows'       => $totalRows,
                  'sliders'         => SliderResource::collection($sliders),
            ]);
      }

      public function detail($id)
      {
            $slider     = Slider::find($id);
            return response()->json(SliderResource::make($slider));
      }

      public function store(SliderRequest $request)
      {

            $image = '';


            if ($request->image) {

                  $icon = explode(',', $request->image);
                  if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                        $imageData = base64_decode($icon[1]);

                        if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                              $image = $this->uploadImageProcess->uploadFile($request->image, $request->title, 'uploads/' . auth()->user()->business_id . '/sliders/', false);
                        }
                  }
            }

            if ($image == '') {
                  $image = $this->uploadImageProcess->createDafaultMedia($request->title, 'uploads/' . auth()->user()->business_id . '/sliders/');
            }

            try {

                  Slider::create([
                        'title'           => $request->title,
                        'subtitle'        => $request->subtitle,
                        'button'          => $request->button,
                        'button_name'     => $request->button == 'yes' ? $request->button_name : '',
                        'button_url'      => $request->button == 'yes' ? $request->button_url : '',
                        'image'           => $image
                  ]);

                  return response()->json([
                        'message'         => 'Data Slider Berhasil Di Tambahkan',
                        'status'          => true
                  ], 200);
            } catch (\Exception $e) {
                  return response()->json([
                        'message'   => $e->getMessage(),
                        'status'    => false
                  ], 409);
            }
      }

      public function edit(SliderRequest $request, $id)
      {

            $slider     = Slider::find($id);
            $image = '';

            if ($request->image) {
                  $icon = explode(',', $request->image);
                  if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                        $imageData = base64_decode($icon[1]);

                        if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                              $this->uploadImageProcess->unlinkFile($slider->image);
                              $image = $this->uploadImageProcess->uploadFile($request->image, $request->title, 'uploads/' . auth()->user()->business_id . '/sliders/', false);
                        }
                  }
            }

            try {

                  $slider->update([
                        'title'           => $request->title,
                        'subtitle'        => $request->subtitle,
                        'button'          => $request->button,
                        'button_name'     => $request->button == 'yes' ? $request->button_name : '',
                        'button_url'      => $request->button == 'yes' ? $request->button_url : '',
                        'image'           => $image != '' ? $image : $slider->image,
                  ]);

                  return response()->json([
                        'message'         => 'Data Slider Berhasil Di Perbaharui',
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
            $slider     = Slider::find($id);
            $slider->delete();
            return response()->json([
                  'message'         => 'Data Slider Berhasil Di Hapus',
                  'status'          => true
            ], 200);
      }
}
