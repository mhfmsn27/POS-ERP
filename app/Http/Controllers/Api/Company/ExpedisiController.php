<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\CouerierResource;
use App\Models\Admin\Courier;
use App\Observers\Master\CourierObserver;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;

class ExpedisiController extends Controller
{
    protected $courierObserver;
    protected $uploadImageProcess;

    public function __construct(CourierObserver $courierObserver, UploadImageProcess $uploadImageProcess)
    {
        $this->courierObserver      = $courierObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->courierObserver->getData($request);

        $totalRows  = $data->count();
        $couriers      = $data->paginate($limit);

        return response()->json([
            'totalRows' => $totalRows,
            'couriers'  => CouerierResource::collection($couriers),
        ], 200);
    }

    public function store(Request $request)
    {
        try {

            $image = '';

            if ($request->logo) {

                $icon = explode(',', $request->logo);
                if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                    $imageData = base64_decode($icon[1]);

                    if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                        $image = $this->uploadImageProcess->uploadFile($request->logo, $request->name, 'uploads/' . auth()->user()->business_id . '/couriers/');
                    }
                }
            }

            if ($image == '') {
                $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/' . auth()->user()->business_id . '/couriers/');
            }

            $this->courierObserver->createData($request, $image);

            return response()->json([
                'message'   => 'Data berhasil di tambahkan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }

    public function update(Request $request, Courier $courier)
    {
        try {

            $image = '';

            if ($request->logo) {
                $icon = explode(',', $request->logo);
                if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                    $imageData = base64_decode($icon[1]);

                    if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                        $this->uploadImageProcess->unlinkFile($courier->logo);
                        $image = $this->uploadImageProcess->uploadFile($request->logo, $request->name, 'uploads/couriers/');
                    }
                }
            } else {
                $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/couriers/');
            }

            $this->courierObserver->updateData($request, $courier, $image);

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

    public function delete(Courier $courier)
    {
        try {

            $this->uploadImageProcess->unlinkFile($courier->logo);
            $courier->delete(); 

            return response()->json([
                'message'   => 'Data berhasil di hapus',
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
