<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Payment\TransactionDueRequest;
use App\Http\Resources\Transaction\TransactionDueResource;
use App\Models\Transaction\TransactionDue;
use App\Models\Transaction\TransactionPayment;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Transaction\TransactionDueObserver;
use App\Observers\Transaction\TransactionPaymentObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionDueController extends Controller
{
    protected $transactionDueObserver;
    protected $ledgerTransactionObserver;
    protected $transactionPaymentObserver;

    public function __construct(TransactionDueObserver $transactionDueObserver, LedgerTransactionObserver $ledgerTransactionObserver, TransactionPaymentObserver $transactionPaymentObserver)
    {
        $this->transactionDueObserver       = $transactionDueObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
        $this->transactionPaymentObserver   = $transactionPaymentObserver;
    }


    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->transactionDueObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => TransactionDueResource::collection($transactions),
        ], 200);
    }

    public function addPay(TransactionDueRequest $request, TransactionDue $transaction)
    {
        try {

            DB::beginTransaction();

            if ($transaction->type == 'hutang' && $request->type == 'supplier') {
                $type       = 'credit';
                $subType    = 'pay_supplier_faktur';
                $payType    = 'supplier_due';
            } else if ($transaction->type == 'saldo' && $request->type == 'supplier') {
                $type       = 'debit';
                $subType    = 'wd_supplier';
                $payType    = 'supplier_wd';
            } else if ($transaction->type == 'hutang' && $request->type == 'customer') {
                $type       = 'debit';
                $subType    = 'pay_customer_faktur';
                $payType    = 'customer_due';
            } else if ($transaction->type == 'saldo' && $request->type == 'customer') {
                $type       = 'credit';
                $subType    = 'wd_customer';
                $payType    = 'customer_wd';
            }

            $payments       = $this->transactionPaymentObserver->createData($request, $transaction, $payType, $request->amount);
            $this->ledgerTransactionObserver->createPaymentFaktur($payments, $type, $subType);

            $transaction->update([
                'total_due_amount'      => $transaction->total_due,
                'status'                => $transaction->total_due < 1 ? 'paid' : 'due'
            ]);

            DB::commit();

            return response()->json([
                'message'       => "Pembayaran berhasil di lakukan",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }

    public function updatePayment(TransactionDueRequest $request, TransactionPayment $payment)
    {
        try {

            if($payment->faktur_detail_id != null) {
                return response()->json([
                    'message'   => 'Pembayaran ini di tambahkan melalui pembayaran transaksi, silahkan ubah di transaksi asal', 
                    'status'    => false
                ], 422);
            }

            DB::beginTransaction();
 
            $payments       = $this->transactionPaymentObserver->updateData($request, $payment, $request->amount);

            if ($payment->due_payment->type == 'hutang' && $request->type == 'supplier') {
                $type       = 'credit';
                $subType    = 'pay_supplier_faktur';
            } else if ($payment->due_payment->type == 'saldo' && $request->type == 'supplier') {
                $type       = 'debit';
                $subType    = 'wd_supplier';
            } else if ($payment->due_payment->type == 'hutang' && $request->type == 'customer') {
                $type       = 'debit';
                $subType    = 'pay_customer_faktur';
            } else if ($payment->due_payment->type == 'saldo' && $request->type == 'customer') {
                $type       = 'credit';
                $subType    = 'wd_customer';
            }

            $this->ledgerTransactionObserver->updatePaymentFaktur($payments, $type, $subType, $request->type);

            $payment->due_payment->update([
                'total_due_amount'      => $payment->due_payment->total_due,
                'status'                => $payment->due_payment->total_due < 1 ? 'paid' : 'due'
            ]);

            DB::commit();

            return response()->json([
                'message'       => "Pembayaran berhasil di lakukan",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }
}
