<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\WhatsApp\WhatsAppDeviceResource;
use App\Models\Admin\WhatsappDevice;
use App\Observers\Notification\DeviceObserver;
use Illuminate\Http\Request;

class WhatsappDeviceController extends Controller
{
    protected $deviceObserver;

    public function __construct(DeviceObserver $deviceObserver)
    {
        $this->deviceObserver       = $deviceObserver;
    }

    public function index(Request $request)
    {
 
        $limit  = $request->input('limit', 10);
        $data   = $this->deviceObserver->getData($request);

        $totalRows  = $data->count();
        $devices    = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'devices'       => WhatsAppDeviceResource::collection($devices),
        ]);
    }

    public function store(Request $request)
    {
        $this->deviceObserver->createData($request);

        return response()->json([
            'status'    => true,
            'message'   => 'Berhasil menambahkan device',
        ]);
    }

    public function update(Request $request, WhatsappDevice $device)
    {
        $this->deviceObserver->updateData($request, $device);
        return response()->json([
            'status'    => true,
            'message'   => 'Berhasil memperbaharui device',
        ]);
    }

    public function delete(WhatsappDevice $device)
    {
        $this->deviceObserver->delete($device);
        return response()->json([
            'status'    => true,
            'message'   => 'Berhasil menghapus device',
        ]); 
    } 

    
}
