<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Purchase\ReceivedProductRequest;
use App\Http\Resources\Transaction\Purchase\Po\PoDetailResource;
use App\Http\Resources\Transaction\Purchase\Po\PoListResource;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\Transaction;
use App\Observers\Transaction\Purchase\POObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POController extends Controller
{
    protected $poObserver;

    public function __construct(POObserver $poObserver)
    {
        $this->poObserver      = $poObserver;
    }

    public function index(Request $request)
    {

        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->poObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => PoListResource::collection($transactions),
        ], 200);
    }

    public function detail(Transaction $transaction)
    {
        return response()->json([
            'details'  => PoDetailResource::make($transaction),
        ], 200);
    }

    public function createData(ReceivedProductRequest $request)
    {


        try {

            DB::beginTransaction();

            $transaction    = $this->poObserver->createUpdateInformation($request, 'create');
            $this->poObserver->savingItems($request, $transaction);

            DB::commit();

            return response()->json([
                'message'       => "Transaksi PO produk berhasil di lakukan",
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

        try {

            if ($transaction->status != 'open') {
                return response()->json([
                    'message'       => "Berkas ini sudah tidak dapat di edit kembali karena sudah masuk faktur",
                    'status'        => false
                ], 422);
            }

            if ($transaction->po()->where(function ($q) {
                return $q->where("transaction_id", "!=", null)->orWhere('transaction_received_id', "!=", null);
            })->count() > 0) {
                return response()->json([
                    'message'       => "Tidak dapat edit data, karena sebagian item telah masuk dalam faktur",
                    'status'        => false
                ], 422);
            }
            DB::beginTransaction();

            $transaction    = $this->poObserver->createUpdateInformation($request, 'update', $transaction);

            foreach ($request->items as $rp) {
                $purchase   = Purchase::find($rp['id']);
                if ($purchase) {
                    $this->poObserver->updateItems($rp, $purchase);
                } else {
                    $this->poObserver->createItems($rp, $transaction);
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
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }

    public function deleteItem(Purchase $purchase)
    {

        try {

            if ($purchase->transaction_received->status != 'open' || $purchase->transaction_id != null) {
                return response()->json([
                    'message'       => "Berkas ini sudah tidak dapat di edit kembali karena sudah masuk faktur",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            $this->poObserver->deleteItems($purchase);

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

        try {

            if ($transaction->status != 'open') {
                return response()->json([
                    'message'       => "Berkas ini sudah tidak dapat di edit kembali karena sudah masuk faktur",
                    'status'        => false
                ], 422);
            }

            if ($transaction->po()->where(function ($q) use ($transaction) {
                return $q->where("transaction_id", "!=", null)->orWhere('transaction_received_id', "!=", null);
            })->count() > 0) {
                return response()->json([
                    'message'       => "Tidak dapat menghapus data, karena sebagian item telah masuk dalam faktur",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            foreach ($transaction->po as $purchase) {
                $this->poObserver->deleteItems($purchase);
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
