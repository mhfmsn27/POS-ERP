<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Observers\Notification\NotificationObserver;
use App\Observers\Notification\TemplateObserver;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
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
        $settings       = $this->notificationObserver->get();
        $templates      = $this->templateObserver->getData($request)->get(['id', 'name']);

        return response()->json([
            'settings'                  => $settings,
            'templates'                 => $templates,
        ]);
    }

    public function store(Request $request)
    {
        $settings   = $this->notificationObserver->get();
        if ($settings != null) {
            $this->notificationObserver->update($request, $settings);
        } else {
            $this->notificationObserver->create($request);
        }

        return response()->json([
            'status'        => true,
            'message'       => 'Perubahan berhasil di simpan',
        ]);
    }
}
