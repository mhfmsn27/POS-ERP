<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Purchase\ReceivedProductRequest;
use App\Http\Resources\Transaction\Purchase\ReceivedProductDetailResource;
use App\Http\Resources\Transaction\Purchase\ReceivedProductListResource;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\Transaction;
use App\Observers\Transaction\Purchase\ReceivedProductObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReceivedProductController extends Controller
{
    protected $receivedProductObserver;

    public function __construct(ReceivedProductObserver $receivedProductObserver)
    {
        $this->receivedProductObserver      = $receivedProductObserver;
    }

    public function index(Request $request)
    {

        abort_if(Gate::denies('received_view'), 403);

        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->receivedProductObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => ReceivedProductListResource::collection($transactions),
        ], 200);
    }

    public function detail(Transaction $transaction)
    {
        abort_if(Gate::denies('received_view'), 403);

        return response()->json([
            'details'  => ReceivedProductDetailResource::make($transaction),
        ], 200);
    }

    public function createData(ReceivedProductRequest $request)
    {

        abort_if(Gate::denies('add_received'), 403);

        try {

            DB::beginTransaction();

            $transaction    = $this->receivedProductObserver->createUpdateInformation($request, 'create');
            $this->receivedProductObserver->savingItems($request, $transaction);

            DB::commit();

            return response()->json([
                'message'       => "Transaksi penerimaan produk berhasil di lakukan",
                'transaction'   => $transaction->id,
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }

    public function updateData(ReceivedProductRequest $request, Transaction $transaction)
    {

        abort_if(Gate::denies('update_received'), 403);

        try {

            if ($transaction->status != 'received_not_use') {
                return response()->json([
                    'message'       => "Berkas ini sudah tidak dapat di edit kembali karena sudah masuk faktur",
                    'status'        => false
                ], 422);
            }

            if ($transaction->purchase_received()->where("transaction_id", "!=", null)->count() > 0) {
                return response()->json([
                    'message'       => "Tidak dapat edit data, karena sebagian item telah masuk dalam faktur",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            $transaction    = $this->receivedProductObserver->createUpdateInformation($request, 'update', $transaction);

            foreach ($request->items as $rp) {
                $purchase   = Purchase::find($rp['id']);
                if ($purchase) {
                    $this->receivedProductObserver->updateItems($rp, $purchase, $transaction);
                } else {
                    $this->receivedProductObserver->createItems($rp, $transaction);
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
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }

    public function deleteItem(Purchase $purchase)
    {

        abort_if(Gate::denies('update_received'), 403);

        try {

            if ($purchase->transaction_received->status != 'received_not_use' || $purchase->transaction_id != null) {
                return response()->json([
                    'message'       => "Berkas ini sudah tidak dapat di edit kembali karena sudah masuk faktur",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            $this->receivedProductObserver->deleteItems($purchase);

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

        abort_if(Gate::denies('delete_received'), 403);

        try {

            if ($transaction->status != 'received_not_use') {
                return response()->json([
                    'message'       => "Berkas ini sudah tidak dapat di edit kembali karena sudah masuk faktur",
                    'status'        => false
                ], 422);
            }

            if ($transaction->purchase_received()->where("transaction_id", "!=", null)->count() > 0) {
                return response()->json([
                    'message'       => "Tidak dapat menghapus data, karena sebagian item telah masuk dalam faktur",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            foreach ($transaction->purchase_received as $purchase) {
                $this->receivedProductObserver->deleteItems($purchase);
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
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }
}
