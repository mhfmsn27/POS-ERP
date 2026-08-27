<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Sales\ShippingProductRequest;
use App\Http\Resources\Transaction\Sales\ShippingProductDetailResource;
use App\Http\Resources\Transaction\Sales\ShippingProductListResource;
use App\Models\Transaction\Sell;
use App\Models\Transaction\Transaction;
use App\Observers\Transaction\Sales\ShippingProductObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ShippingProductController extends Controller
{
    protected $shippingProductObserver;

    public function __construct(ShippingProductObserver $shippingProductObserver)
    {
        $this->shippingProductObserver      = $shippingProductObserver;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('shipping_view'), 403);

        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->shippingProductObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => ShippingProductListResource::collection($transactions),
        ], 200);
    }

    public function detail(Transaction $transaction)
    {

        abort_if(Gate::denies('shipping_view'), 403);

        return response()->json([
            'details'  => ShippingProductDetailResource::make($transaction),
        ], 200);
    }

    public function createData(ShippingProductRequest $request)
    {

        abort_if(Gate::denies('add_shipping'), 403);

        try {

            DB::beginTransaction();

            $transaction    = $this->shippingProductObserver->createUpdateInformation($request, 'create');
            $this->shippingProductObserver->savingItems($request, $transaction);

            DB::commit();
            return response()->json([
                'transaction'   => $transaction->id,
                'message'       => "Transaksi penerimaan produk berhasil di lakukan",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }

    public function updateData(ShippingProductRequest $request, Transaction $transaction)
    {

        abort_if(Gate::denies('update_shipping'), 403);

        try {

            if ($transaction->status != 'shipping_not_use') {
                return response()->json([
                    'message'       => "Berkas ini sudah tidak dapat di edit kembali karena sudah masuk faktur",
                    'status'        => false
                ], 422);
            }

            if ($transaction->sale_shipping()->where("transaction_id", "!=", null)->count() > 0) {
                return response()->json([
                    'message'       => "Tidak dapat edit data, karena sebagian item telah masuk dalam faktur",
                    'status'        => false
                ], 422);
            }


            DB::beginTransaction();

            $transaction    = $this->shippingProductObserver->createUpdateInformation($request, 'update', $transaction);

            foreach ($request->items as $items) {
                $sell = Sell::find($items['id']);
                if ($sell) {
                    $this->shippingProductObserver->updateItems($items, $sell, $transaction);
                } else {
                    $this->shippingProductObserver->createItems($items, $transaction);
                }
            }


            DB::commit();

            return response()->json([
                'message'       => "Data berhasil di perbaharui",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }

    public function deleteItem(Sell $sales)
    {

        abort_if(Gate::denies('delete_shipping'), 403);

        try {

           
            if ($sales->transaction_shipping->status != 'shipping_not_use' || $sales->transaction_id != null) {
                return response()->json([
                    'message'       => "Berkas ini sudah tidak dapat di edit kembali karena sudah masuk faktur",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            $this->shippingProductObserver->deleteItems($sales);

            DB::commit();

            return response()->json([
                'message'       => "Item berhasil di hapus",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }

    public function delete(Transaction $transaction)
    {

        abort_if(Gate::denies('delete_shipping'), 403);

        try {
 
            if ($transaction->status != 'shipping_not_use') {
                return response()->json([
                    'message'       => "Berkas ini sudah tidak dapat di edit kembali karena sudah masuk faktur",
                    'status'        => false
                ], 422);
            }

            if ($transaction->sale_shipping()->where("transaction_id", "!=", null)->count() > 0) {
                return response()->json([
                    'message'       => "Tidak dapat menghapus data, karena sebagian item telah masuk dalam faktur",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            foreach ($transaction->sale_shipping as $sales) {
                $this->shippingProductObserver->deleteItems($sales);
            }

            $transaction->forceDelete();

            DB::commit();
            return response()->json([
                'message'       => "Transaksi berhasil di hapus",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }
}
