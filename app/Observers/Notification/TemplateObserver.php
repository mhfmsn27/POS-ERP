<?php

namespace App\Observers\Notification;

use App\Models\Admin\WhatsappTemplate;
use Illuminate\Http\Request;

class TemplateObserver
{
    public function getData(Request $request)
    {
        return WhatsappTemplate::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function createData(Request $request, String $image)
    {
        return WhatsappTemplate::create([
            'name'      => $request->name,
            'message'   => $request->message,
            'file'      => $image
        ]);
    }

    public function updateData(Request $request, WhatsappTemplate $template, String $image)
    {
        $template->update([
            'name'      => $request->name,
            'message'   => $request->message,
            'file'      => $image != '' ? $image : $template->file
        ]);
    }

    public function delete(WhatsappTemplate $template) {
        $template->delete();
    }
}
