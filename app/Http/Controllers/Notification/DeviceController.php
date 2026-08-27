<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Admin\WhatsappDevice;
use App\Observers\Notification\DeviceObserver;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    protected $deviceObserver;

    public function __construct(DeviceObserver $deviceObserver)
    {
        $this->deviceObserver       = $deviceObserver;
    }

    public function index(Request $request)
    {
        $devices        = $this->deviceObserver->getData($request)->get(['id', 'name', 'apikey','deviceid']);
        return view('admin.notification.device.index', ['page'   => 'Whatsapp Device'], compact('devices'));
    }

    public function create()
    {
        return view('admin.notification.device.create', ['page'  => 'Tambah Whatsapp Device']);
    }

    public function update(WhatsappDevice $device)
    {
        return view('admin.notification.device.update', ['page'  => 'Edit Whatsapp Device'], compact('device'));
    }

    public function delete(WhatsappDevice $device)
    {
        $this->deviceObserver->delete($device);
        return back()->with(['flash'    => 'Berhasil menghapus data']);
    }

    public function store(Request $request)
    {
        $this->deviceObserver->createData($request);
        return redirect()->route('device')->with(['flash' => 'Berhasil menambahkan data']);
    }

    public function edit(Request $request, WhatsappDevice $device)
    {
        $this->deviceObserver->updateData($request, $device);
        return redirect()->route('device')->with(['flash' => 'Berhasil memperbaharui data']);
    }
}
