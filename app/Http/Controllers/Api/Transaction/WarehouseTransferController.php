<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Resources\Transaction\WarehouseTransfer\DetailResource;
use App\Http\Resources\Transaction\WarehouseTransfer\ListResource;
use App\Models\Product\Unit;
use App\Models\Product\Variation;
use App\Models\Transaction\Transaction;
use App\Observers\Inventory\StockObserver;
use App\Observers\Transaction\WarehouseTransferObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class WarehouseTransferController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Warehouse Transfer Controller
    |--------------------------------------------------------------------------
    */

    protected $warehouseTransferObserver;
    protected $stockObserver;

    public function __construct(WarehouseTransferObserver $warehouseTransferObserver, StockObserver $stockObserver)
    {
        $this->warehouseTransferObserver        = $warehouseTransferObserver;
        $this->stockObserver                    = $stockObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. List Data
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        abort_if(Gate::denies('warehouse_transfer_view'), 403);

        $limit          = $request->limit ? $request->limit : 10;
        $data           = $this->warehouseTransferObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => ListResource::collection($transactions),
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | 2. Create Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {

        abort_if(Gate::denies('warehouse_transfer_create'), 403);

        if($request->from['id'] == $request->to['id']) {
            return response()->json([
                'message'   => "Asal dan tujuan gudang tidak boleh sama",
                'status'    => true
            ], 419);
        }

        try {

            DB::beginTransaction();

            // Get Ref Transaction
            $getTransaksi       = Transaction::where("type", "stock_adjustment")->whereDate("created_at", date("Y-m-d"))->count() + 1;
            $invoiceNumber      = sprintf("%05d", $getTransaksi);
            $refNo              = "TW" . date("Y/m/d") . "/" . $invoiceNumber;

            // Create New Transaction For Stock Opname
            $transaction        = $this->warehouseTransferObserver->createTransaction($request, $refNo, $invoiceNumber);

            // Item Stock Opname
            foreach ($request->items as $d) {

                $qtyAdjustment  = 0;

                $getUnits       = Unit::find($d['unit']);
                $variation      = Variation::find($d['variation_id']);
                $fromWarehouse  = $this->stockObserver->createData($variation, $transaction->from_warehouse_id);
                $toWarehouse    = $this->stockObserver->createData($variation, $transaction->to_warehouse_id);

                $qtyAdjustment  = $getUnits ? ((int)$d['qty'] * $getUnits->value) : (int)$d['qty'];
                $hasilQty       = $qtyAdjustment;

                $attribute      = array(
                    'type'          => 'min',
                    'quantity'      => $fromWarehouse->qty_available,
                    'variation_id'  => $variation->id,
                    'product_id'    => $variation->product_id,
                    'hasil_qty'     => $hasilQty
                );               

                $toWarehouse->update([
                    'qty_available'     => $toWarehouse->qty_available + $hasilQty
                ]); 

                $fromWarehouse->update([
                    'qty_available'     => $fromWarehouse->qty_available - $hasilQty
                ]);
 
                $this->warehouseTransferObserver->createItems($attribute, (int)$qtyAdjustment, $getUnits, $transaction);
            }

            DB::commit();

            return response()->json([
                'message'   => "Transaksi Transfer gudang berhasil dilakukan",
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage() . ' ' . $e->getLine(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'status' => false
            ], 400);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Detail Data
    |--------------------------------------------------------------------------
    */

    public function detail(Transaction $transaction)
    {
        abort_if(Gate::denies('warehouse_transfer_view'), 403);
        return response()->json([
            'details' => DetailResource::make($transaction)
        ], 200);
    }
}
