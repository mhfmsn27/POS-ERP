<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\KeySetting;
use App\Models\Admin\Printer;
use App\Models\Admin\SettingsHrm;
use App\Models\Admin\Store;
use App\Models\Admin\Taxrate;
use App\Models\Admin\Warehouse;
use App\Observers\Notification\NotificationObserver;
use App\Observers\Notification\TemplateObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

        $key = KeySetting::first();
        $hrm = SettingsHrm::first();

        $data = [
            'printer'       => Printer::all(),
            'taxrate'       => Taxrate::all(),
            'warehouses'    => Warehouse::all()
        ];

        $settings       = $this->notificationObserver->get();
        $templates      = $this->templateObserver->getData($request)->get(['id', 'name']);

        $store  = Store::findOrFail(my_store());

        return view('admin.settings.index', ['page' => __('sidebar.general')], compact('key', 'hrm', 'data', 'store', 'settings', 'templates'));
    }

    

    public function store(Request $request)
    {
        $settings   = $this->notificationObserver->get();
        if ($settings != null) {
            $this->notificationObserver->update($request, $settings);
        } else {
            $this->notificationObserver->create($request);
        }

        return redirect()->route('sett.index')->with(['flash' => 'Berhasil memperbaharui data']);
    }
}
