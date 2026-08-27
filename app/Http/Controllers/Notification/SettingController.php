<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Observers\Notification\NotificationObserver;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $notificationObserver;

    public function __construct(NotificationObserver $notificationObserver)
    {
        $this->notificationObserver       = $notificationObserver;
    }

    public function index(Request $request)
    {
        $settings        = $this->notificationObserver->get();
        return view('notification.setting', ['page'   => 'Pengaturan Notifikasi'], compact('settings'));
    }


    public function store(Request $request)
    {
        $settings   = $this->notificationObserver->get();
        if ($settings != null) {
            $this->notificationObserver->update($request, $settings);
        } else {
            $this->notificationObserver->create($request);
        }

        return redirect()->route('notification_setting')->with(['flash' => 'Berhasil memperbaharui data']);
    }
}
