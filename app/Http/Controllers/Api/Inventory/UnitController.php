<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\UnitRequest;
use App\Http\Resources\Inventory\UnitResource;
use App\Models\Product\Unit;
use App\Observers\Inventory\UnitObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UnitController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Products Unit Controller
    |--------------------------------------------------------------------------
    */

    public $unitObserver;

    public function __construct(UnitObserver $unitObserver)
    {
        $this->unitObserver        = $unitObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Units List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        abort_if(Gate::denies('unit_view'), 403);

        $limit = $request->input('limit', 10);
        $data   = $this->unitObserver->getData($request);

        $totalRows  = $data->count();
        $units      = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'units'         => UnitResource::collection($units),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Unit
    |--------------------------------------------------------------------------
    */

    public function create(UnitRequest $request)
    {

        abort_if(Gate::denies('unit_create'), 403);

        try {

            $this->unitObserver->createData($request);

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
    | 3. Update Unit
    |--------------------------------------------------------------------------
    */

    public function update(UnitRequest $request, Unit $unit)
    {

        abort_if(Gate::denies('unit_update'), 403);

        try {

            $this->unitObserver->updateData($request, $unit);

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
    | 4. Delete Unit
    |--------------------------------------------------------------------------
    */

    public function delete(Unit $unit)
    {

        abort_if(Gate::denies('unit_delete'), 403);

        $unit->delete();

        return response()->json([
            'message'   =>  'Hapus Data berhasil di lakukan',
            'status'    => true
        ], 200);
    }

   
}
