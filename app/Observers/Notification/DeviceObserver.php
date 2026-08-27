<?php

namespace App\Observers\Notification;

use App\Models\Admin\WhatsappDevice;
use Illuminate\Http\Request;

class DeviceObserver
{
    public function getData(Request $request)
    {
        return WhatsappDevice::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function getDevice($settings)
    {
        if($settings->store_id != null) {
            return WhatsappDevice::first();
        }

        return WhatsappDevice::withoutGlobalScopes()->first();
    }

    public function createData(Request $request)
    {
        return WhatsappDevice::create([
            'name'          => $request->name,
            'apikey'        => $request->api,
            'deviceid'      => $request->device
        ]);
    }

    public function updateData(Request $request, WhatsappDevice $device)
    {
        $device->update([
            'name'          => $request->name,
            'apikey'        => $request->api,
            'deviceid'      => $request->device
        ]);
    }

    public function delete(WhatsappDevice $device) {
        $device->delete();
    }
}
