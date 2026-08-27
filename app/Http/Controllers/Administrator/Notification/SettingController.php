<?php

namespace App\Http\Controllers\Administrator\Notification;

use App\Http\Controllers\Controller;
use App\Observers\Notification\NotificationObserver;
use App\Observers\Notification\TemplateObserver;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $notificationObserver;
    protected $templateObserver;

    public function __construct(NotificationObserver $notificationObserver, TemplateObserver $templateObserver)
    {
        $this->notificationObserver     = $notificationObserver;
        $this->templateObserver         = $templateObserver;
    }

    public function index(Request $request)
    {
        $settings   = $this->notificationObserver->get();
        $templates  = $this->templateObserver->getData($request)->get(['id', 'name']);
        return view('super.notification.setting', ['page'    => 'Pengaturan Notifikasi'], compact('settings', 'templates'));
    }

    public function store(Request $request)
    {
        $settings   = $this->notificationObserver->get();
        if ($settings != null) {
            $this->notificationObserver->update($request, $settings);
        } else {
            $this->notificationObserver->create($request);
        }

        return redirect()->route('admin.notification')->with(['flash' => 'Berhasil memperbaharui data']);
    }
}
