<?php

namespace Poshub\Ecommerce\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\EcommerceApiSetting;
use Poshub\Ecommerce\Requests\AboutRequest;

class AboutUsController extends Controller
{

      public $uploadImageProcess;

      public function __construct(UploadImageProcess $uploadImageProcess)
      {
            $this->uploadImageProcess   = $uploadImageProcess;
      }


      public function index()
      {
            $data = EcommerceApiSetting::first(['about_title', 'copyright', 'about_image', 'about_text']);

            return response()->json([
                  'about_title'     => $data->about_title ?? '',
                  'copyright'       => $data->copyright ?? '',
                  'about_image'     => asset($data->about_image ?? ''),
                  'about_text'      => $data->about_text ?? ''
            ], 200);
      }

      public function social()
      {
            $data = EcommerceApiSetting::first(['facebook_url', 'twitter_url', 'instagram_url', 'youtube_url']);

            return response()->json([
                  'facebook_url'    => $data->facebook_url ?? '',
                  'twitter_url'     => $data->twitter_url ?? '',
                  'instagram_url'   => $data->instagram_url ?? '',
                  'youtube_url'     => $data->youtube_url ?? ''
            ], 200);
      }


      public function socialStore(Request $request)
      {

            try {

                  $data                         = EcommerceApiSetting::first(['id', 'facebook_url', 'instagram_url', 'twitter_url', 'youtube_url']);
                  $arrayData                    = [
                        'facebook_url'          => $request->facebook_url,
                        'instagram_url'         => $request->instagram_url,
                        'twitter_url'           => $request->twitter_url,
                        'youtube_url'           => $request->youtube_url,
                  ];

                  if (!$data) {
                        EcommerceApiSetting::create($arrayData);
                  } else {
                        $data->update($arrayData);
                  }

                  return response()->json([
                        'message'   => 'Data berhasil di perbaharui',
                        'status'    => true
                  ], 200);
            } catch (\Exception $e) {
                  return response()->json([
                        'message'   => $e->getMessage(),
                        'status'    => false
                  ], 409);
            }
      }


      public function store(AboutRequest $request)
      {

            try {

                  $data                         = EcommerceApiSetting::first(['id', 'about_title', 'copyright', 'about_text', 'about_image']);

                  $image = '';

                  if ($request->image) {
                        $icon = explode(',', $request->image);
                        if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                              $imageData = base64_decode($icon[1]);

                              if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                                    if ($data) {
                                          $this->uploadImageProcess->unlinkFile($data->about_image);
                                    }

                                    $image = $this->uploadImageProcess->uploadFile($request->image, $request->about_title, 'uploads/' . auth()->user()->business_id . '/ecommerce/settings/');
                              }
                        }
                  }

                  $arrayData        = [
                        'about_title'           => $request->about_title,
                        'copyright'             => $request->copyright,
                        'about_text'            => $request->about_text,
                        'about_image'           => $image != '' ? $image : ($data ? $data->about_image : '')
                  ];

                  if (!$data) {
                        EcommerceApiSetting::create($arrayData);
                  } else {
                        $data->update($arrayData);
                  }

                  return response()->json([
                        'message'   => 'Data berhasil di perbaharui',
                        'status'    => true
                  ], 200);
            } catch (\Exception $e) {
                  return response()->json([
                        'message'   => $e->getMessage(),
                        'status'    => false
                  ], 409);
            }
      }
}
