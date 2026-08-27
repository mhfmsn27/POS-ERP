<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\RakRequest;
use App\Http\Resources\Inventory\RakResource;
use App\Models\Product\Rak;
use App\Observers\Inventory\RakObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RakController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Products Rak Controller
    |--------------------------------------------------------------------------
    */

    public $rakObserver;

    public function __construct(RakObserver $rakObserver)
    {
        $this->rakObserver        = $rakObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Raks List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        abort_if(Gate::denies('rak_view'), 403);

        $limit = $request->input('limit', 10);
        $data   = $this->rakObserver->getData($request);

        $totalRows  = $data->count();
        $raks       = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'raks'          => RakResource::collection($raks),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Rak
    |--------------------------------------------------------------------------
    */

    public function create(RakRequest $request)
    {
        abort_if(Gate::denies('rak_create'), 403);

        try {

            $this->rakObserver->createData($request);

            return response()->json([
                'message'   => 'Tambah Data berhasil di lakukan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 409);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 3. Update Rak
    |--------------------------------------------------------------------------
    */

    public function update(RakRequest $request, Rak $rak)
    {

        abort_if(Gate::denies('rak_update'), 403);
        try {

            $this->rakObserver->updateData($request, $rak);

            return response()->json([
                'message'   =>  'Edit Data berhasil di lakukan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Delete Rak
    |--------------------------------------------------------------------------
    */

    public function delete(Rak $rak)
    {
        abort_if(Gate::denies('rak_delete'), 403);

        $rak->delete();

        return response()->json([
            'message'   =>  'Hapus Data berhasil di lakukan',
            'status'    => true
        ], 200);
    }
}
