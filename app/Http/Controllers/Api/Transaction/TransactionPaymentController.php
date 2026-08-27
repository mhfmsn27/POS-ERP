<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Resources\Transaction\TransactionPaymentResource;
use App\Models\Transaction\TransactionPayment;
use App\Observers\Transaction\TransactionPaymentObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionPaymentController extends Controller
{
    protected $transactionPaymentObserver;

    public function __construct(TransactionPaymentObserver $transactionPaymentObserver)
    {
        $this->transactionPaymentObserver   = $transactionPaymentObserver;
    }


    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->transactionPaymentObserver->getData($request);

        $totalRows      = $data->count();
        $payments       = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'payments'      => TransactionPaymentResource::collection($payments),
        ], 200);
    }


    public function delete(TransactionPayment $payment)
    {
        try { 

            if($payment->faktur_detail_id != null) {
                return response()->json([
                    'message'   => 'Pembayaran ini di tambahkan melalui pembayaran transaksi, silahkan melakukan hapus di transaksi asal', 
                    'status'    => false
                ], 422);
            }

            DB::beginTransaction();

            $this->transactionPaymentObserver->deleteData($payment);

            DB::commit();

            return response()->json([
                'message'       => "Pembayaran berhasil di hapus",
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
}
