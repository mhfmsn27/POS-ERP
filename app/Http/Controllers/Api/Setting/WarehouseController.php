<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\Setting\WarehouseResource;
use App\Models\Admin\Warehouse;
use App\Observers\Setting\WarehouseObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WarehouseController extends Controller
{
    protected $warehouseObserver;

    public function __construct(WarehouseObserver $warehouseObserver)
    {
        $this->warehouseObserver        = $warehouseObserver;
    }

    public function index(Request $request)
    {

        abort_if(Gate::denies('warehouse_view'), 403);

        $limit  = $request->input('limit', 10);
        $data   = $this->warehouseObserver->getData($request);

        $totalRows  = $data->count();
        $warehouses = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'warehouses'    => WarehouseResource::collection($warehouses),
        ]);
    }

    public function forChoose(Request $request)
    {
        $limit      = $request->input('limit', 10);
        $data       = $this->warehouseObserver->getData($request);
        $warehouses = $data->paginate($limit);

        $listData   = [];

        $i['id']    = "";
        $i['name']  = 'Gudang Utama';
        $listData[] = $i;

        foreach ($warehouses as $warehouse) {
            $item['id']     = $warehouse->id;
            $item['name']   = $warehouse->name;
            $listData[]     = $item;
        }

        return response()->json([
            'warehouses'    => $listData,
        ]);
    }

    public function store(Request $request)
    {

        abort_if(Gate::denies('warehouse_create'), 403);

        try {

            $this->warehouseObserver->createData($request);

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

    public function update(Request $request, Warehouse $warehouse)
    {
        abort_if(Gate::denies('warehouse_update'), 403);

        try {

            $this->warehouseObserver->updateData($request, $warehouse);

            return response()->json([
                'message'   => 'Edit Data berhasil di lakukan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 409);
        }
    }

    public function delete(Warehouse $warehouse)
    {

        abort_if(Gate::denies('warehouse_delete'), 403);

        if ($warehouse->stock()->sum('qty_available') > 0) {
            return response()->json([
                'message'   => 'Gudang tidak dapat di hapus karena masih memiliki stok',
                'status'    => true
            ], 412);
        }

        try {

            $this->warehouseObserver->deleteData($warehouse);

            return response()->json([
                'message'   => 'Hapus Data berhasil di lakukan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 409);
        }
    }
}
