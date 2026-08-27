<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Admin\WhatsappTemplate;
use App\Observers\Notification\TemplateObserver;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    protected $templateObserver;

    public function __construct(TemplateObserver $templateObserver)
    {
        $this->templateObserver       = $templateObserver;
    }

    public function index(Request $request)
    {
        $templates        = $this->templateObserver->getData($request)->get(['id', 'name', 'file']);
        return view('admin.notification.template.index', ['page'   => 'Template Notifikasi Whatsapp'], compact('templates'));
    }

    public function create()
    {
        return view('admin.notification.template.create', ['page'  => 'Tambah Template Notifikasi Whatsapp']);
    }

    public function update(WhatsappTemplate $template)
    {
        return view('admin.notification.template.update', ['page'  => 'Edit Template Notifikasi Whatsapp'], compact('template'));
    }

    public function delete(WhatsappTemplate $template)
    {
        $this->templateObserver->delete($template);
        return back()->with(['flash'    => 'Berhasil menghapus data']);
    }

    public function store(Request $request)
    {
        $image      = $request->image ? $this->uploadImage($request, 'image', 'templates') : '';
        $this->templateObserver->createData($request, $image);
        return redirect()->route('template')->with(['flash' => 'Berhasil menambahkan data']);
    }

    public function edit(Request $request, WhatsappTemplate $device)
    {
        $image      = $request->image ? $this->uploadImage($request, 'image', 'templates') : '';
        $this->templateObserver->updateData($request, $device, $image);
        return redirect()->route('template')->with(['flash' => 'Berhasil memperbaharui data']);
    }
}
