<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Sales\ShippingProductRequest;
use App\Http\Resources\Transaction\Sales\Offer\OfferDetailResource;
use App\Http\Resources\Transaction\Sales\Offer\OfferListResource;
use App\Models\Transaction\Sell;
use App\Models\Transaction\Transaction;
use App\Observers\Transaction\Sales\OfferObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    protected $OfferObserver;

    public function __construct(OfferObserver $OfferObserver)
    {
        $this->OfferObserver      = $OfferObserver;
    }

    public function index(Request $request)
    {

        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->OfferObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => OfferListResource::collection($transactions),
        ], 200);
    }

    public function detail(Transaction $transaction)
    {
        return response()->json([
            'details'  => OfferDetailResource::make($transaction),
        ], 200);
    }

    public function createData(ShippingProductRequest $request)
    {

        try {

            DB::beginTransaction();

            $transaction    = $this->OfferObserver->createUpdateInformation($request, 'create');
            $this->OfferObserver->savingItems($request, $transaction);

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

        try {

            if ($transaction->status != 'open') {
                return response()->json([
                    'message'       => "Berkas ini sudah tidak dapat di edit kembali karena sudah masuk faktur",
                    'status'        => false
                ], 422);
            }

            if ($transaction->offer()->where(function ($q) {
                return $q->where("transaction_id", "!=", null)->orWhere('transaction_received_id', "!=", null);
            })->count() > 0) {
                return response()->json([
                    'message'       => "Tidak dapat edit data, karena sebagian item telah masuk dalam faktur",
                    'status'        => false
                ], 422);
            }


            DB::beginTransaction();

            $transaction    = $this->OfferObserver->createUpdateInformation($request, 'update', $transaction);

            foreach ($request->items as $items) {
                $sell = Sell::find($items['id']);
                if ($sell) {
                    $this->OfferObserver->updateItems($items, $sell);
                } else {
                    $this->OfferObserver->createItems($items, $transaction);
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


        try {

            if ($sales->transaction_shipping->status != 'open' || $sales->transaction_id != null) {
                return response()->json([
                    'message'       => "Berkas ini sudah tidak dapat di edit kembali karena sudah masuk faktur",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            $this->OfferObserver->deleteItems($sales);

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

            if ($transaction->offer()->where(function ($q) {
                return $q->where("transaction_id", "!=", null)->orWhere('transaction_received_id', "!=", null);
            })->count() > 0) {
                return response()->json([
                    'message'       => "Tidak dapat menghapus data, karena sebagian item telah masuk dalam faktur",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            foreach ($transaction->offer as $sales) {
                $this->OfferObserver->deleteItems($sales);
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
