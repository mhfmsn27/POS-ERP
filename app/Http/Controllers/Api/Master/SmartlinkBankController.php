<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\SmartLinkBankRequest;
use App\Http\Resources\Master\SmartlinkResource;
use App\Models\Transaction\SmartlinkBank;
use App\Observers\Master\SmartlinkBankObserver;
use Illuminate\Http\Request;

class SmartlinkBankController extends Controller
{
    protected $smartlinkBankObserver;

    public function __construct(SmartlinkBankObserver $smartlinkBankObserver)
    {
        $this->smartlinkBankObserver        = $smartlinkBankObserver;
    }


    public function index(Request $request)
    {

        $limit  = $request->input('limit', 20);
        $data   = $this->smartlinkBankObserver->getData($request);

        $totalRows      = $data->count();
        $smartlinks     = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'smartlinks'    => SmartlinkResource::collection($smartlinks),
        ]);
    }

    public function detail(SmartlinkBank $smartlink)
    {
        return response()->json(SmartlinkResource::make($smartlink));
    }


    public function create(SmartLinkBankRequest $request)
    {

        $smartLink = SmartlinkBank::where('account_id', $request->account['id'])->first(['id']);

        if ($smartLink) {
            return response()->json([
                'message'   => 'Bank ini sudah di tambahkan',
                'status'    => false
            ], 422);
        }

        try {

            $this->smartlinkBankObserver->createData($request);

            return response()->json([
                'message'   => 'Data berhasil di tambahkan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function update(SmartLinkBankRequest $request, SmartlinkBank $smartlink)
    {

        $smartLink = SmartlinkBank::where('account_id', $request->account['id'])->where('id', "!=", $smartlink->id)->first(['id']);

        if ($smartLink) {
            return response()->json([
                'message'   => 'Bank ini sudah di tambahkan',
                'status'    => false
            ], 422);
        }

        try {

            $this->smartlinkBankObserver->updateData($request, $smartlink);

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function delete(SmartlinkBank $smartlink)
    {

        try {


            $smartlink->delete();

            return response()->json([
                'message'   => 'Data berhasil di hapus',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }
}
