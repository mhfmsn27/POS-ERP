<?php

namespace Poshub\Ecommerce\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\EcommerceBankTransansfer;
use Poshub\Ecommerce\Requests\EcommerceBankRequest;
use Poshub\Ecommerce\Resources\Admin\EcommerceBankResource;

class EcommerceBankController extends Controller
{

      public $uploadImageProcess;

      public function __construct(UploadImageProcess $uploadImageProcess)
      {
            $this->uploadImageProcess   = $uploadImageProcess;
      }

      public function index(Request $request)
      {
            $limit  = $request->input('limit', 10);
            $data   = EcommerceBankTransansfer::where(function ($q) use ($request) {
                  return $request->bank_name ?  $q->where('bank_name', 'like', '%' . $request->bank_name . '%') : '';
            })->orderBy('bank_name', 'desc');

            $totalRows  = $data->count();
            $banks      = $data->paginate($limit);

            return response()->json([
                  'totalRows'       => $totalRows,
                  'banks'           => EcommerceBankResource::collection($banks),
            ]);
      }

      public function detail($id)
      {
            $bank       = EcommerceBankTransansfer::find($id);
            
      }


      public function store(EcommerceBankRequest $request)
      {

            try {
                  $image = '';


                  if ($request->logo) {

                        $icon = explode(',', $request->logo);
                        if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                              $imageData = base64_decode($icon[1]);

                              if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                                    $image = $this->uploadImageProcess->uploadFile($request->logo, $request->bank_name, 'uploads/' . auth()->user()->business_id . '/banks/');
                              }
                        }
                  }

                  if ($image == '') {
                        $image = $this->uploadImageProcess->createDafaultMedia($request->bank_name, 'uploads/' . auth()->user()->business_id . '/banks/');
                  }

                  EcommerceBankTransansfer::create([
                        'bank_name'             => $request->bank_name,
                        'no_rek'                => $request->no_rek,
                        'an'                    => $request->an,
                        'logo'                  => $image
                  ]);

                  return response()->json([
                        'message'         => 'Data Bank Berhasil Di Perbaharui',
                        'status'          => true
                  ], 200);
            } catch (\Exception $e) {
                  return response()->json([
                        'message'   => $e->getMessage(),
                        'status'    => false
                  ], 409);
            }
      }

      public function edit(Request $request, EcommerceBankTransansfer $bank)
      {

            $image = '';

            if ($request->logo) {
                  $icon = explode(',', $request->logo);
                  if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                        $imageData = base64_decode($icon[1]);

                        if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                              $this->uploadImageProcess->unlinkFile($bank->logo);
                              $image = $this->uploadImageProcess->uploadFile($request->logo, $request->bank_name, 'uploads/' . auth()->user()->business_id . '/banks/');
                        }
                  }
            }

            try {

                  $bank->update([
                        'bank_name'             => $request->bank_name,
                        'no_rek'                => $request->no_rek,
                        'an'                    => $request->an,
                        'logo'                  => $image
                  ]);

                  return response()->json([
                        'message'         => 'Data Bank Berhasil Di Perbaharui',
                        'status'          => true
                  ], 200);
            } catch (\Exception $e) {
                  return response()->json([
                        'message'   => $e->getMessage(),
                        'status'    => false
                  ], 409);
            }
      }

      public function delete(EcommerceBankTransansfer $bank)
      {
            $bank->delete();
            return response()->json([
                  'message'         => 'Data Bank Berhasil Di Hapus',
                  'status'          => true
            ], 200);
      }
}
