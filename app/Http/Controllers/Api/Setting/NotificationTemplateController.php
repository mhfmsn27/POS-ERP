<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\WhatsApp\TemplateDetailResource;
use App\Http\Resources\WhatsApp\TemplateListResource;
use App\Models\Admin\WhatsappTemplate;
use App\Observers\Notification\TemplateObserver;
use Illuminate\Http\Request; 

class NotificationTemplateController extends Controller
{
    protected $templateObserver;

    public function __construct(TemplateObserver $templateObserver)
    {
        $this->templateObserver       = $templateObserver;
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->templateObserver->getData($request);

        $totalRows  = $data->count();
        $templates  = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'templates'     => TemplateListResource::collection($templates),
        ], 200);
    }

    public function detail(WhatsappTemplate $template)
    {
        return response()->json(TemplateDetailResource::make($template), 200);
    }

    public function store(Request $request)
    {
        try {
            $this->templateObserver->createData($request, '');
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

    public function update(Request $request, WhatsappTemplate $template)
    {
        try {
            $this->templateObserver->updateData($request, $template, '');
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

    public function delete(WhatsappTemplate $template)
    {
        try {
            $template->delete();
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
