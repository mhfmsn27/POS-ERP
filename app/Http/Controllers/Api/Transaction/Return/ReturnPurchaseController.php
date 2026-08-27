<?php

namespace App\Http\Controllers\Api\Transaction\Return;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\PurchaseReturn\CreateReturnPurchaseRequest;
use App\Http\Resources\Transaction\PurchaseReturn\PurchaseReturnDetailResource;
use App\Http\Resources\Transaction\PurchaseReturn\PurchaseReturnListResource;
use App\Models\Account\AccountTransaction;
use App\Models\Transaction\Transaction;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Transaction\Purchase\PurchaseReturnObserver;
use App\Observers\Transaction\TransactionDueObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReturnPurchaseController extends Controller
{
    protected $purchaseReturnObserver;
    protected $transactionDueObserver;
    protected $ledgerObserver;
    protected $ledgerTransactionObserver;

    public function __construct(PurchaseReturnObserver $purchaseReturnObserver, TransactionDueObserver $transactionDueObserver, LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->purchaseReturnObserver       = $purchaseReturnObserver;
        $this->transactionDueObserver       = $transactionDueObserver;
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('purchase_retur_view'), 403);
        $limit          = $request->limit ? $request->limit : 10;
        $data           = $this->purchaseReturnObserver->getData($request);
        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => PurchaseReturnListResource::collection($transactions),
        ], 200);
    }

    public function create(CreateReturnPurchaseRequest $request, Transaction $transaction)
    {

        abort_if(Gate::denies('add_purchase_retur'), 403);

        try {

            DB::beginTransaction();

            $transactions       = $this->purchaseReturnObserver->createUpdateInformation($request, 'create', $transaction);
            $this->purchaseReturnObserver->createReturns($request, $transactions);
            $this->purchaseReturnObserver->subtotalTransactionChange($transactions);

            $totalReturn        = $transactions->final_total;
            $dueRevisionTotal   = 0;

            if ($transaction->transaction_due) {
                $dueAmount          = $transaction->transaction_due->total_due_amount;
                if ($transaction->transaction_due->total_due_amount > 0) {
                    if ($totalReturn > $dueAmount) {
                        $dueRevisionTotal   = $dueAmount;
                    } else {
                        $dueRevisionTotal   = $totalReturn;
                    }
                }

                $transaction->transaction_due->update([
                    'amount'                => $transaction->transaction_due->amount - $dueRevisionTotal,
                    'total_due_amount'      => $transaction->transaction_due->total_due_amount - $dueRevisionTotal,
                ]);

                $totalReturn    = $totalReturn - $dueRevisionTotal;

                if ($transaction->due_total_po == 0) {
                    $transaction->update([
                        'payment_status'    => 'paid'
                    ]);
                }
            }

            if ($totalReturn > 0) {
                $this->transactionDueObserver->createByTransaction($transactions, 'saldo');
            } else {
                $account    = $transactions->supplier->debt_account ?? null;
                $name       = $transactions->supplier->name ?? '';
                $title      = 'Retur Pembelian - ' . $transactions->ref_no;
                $account    = $transactions->supplier->debt_account ?? null;

                if ($account) {
                    $depositTransaction = AccountTransaction::create([
                        'account_id'                    => $account->id,
                        'transaction_id'                => $transactions->id, 
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $dueRevisionTotal,
                        'type'                          => 'debit',
                        'sub_type'                      => 'due_supplier',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('due_supplier', $transactions->created_at),
                        'operation_date'                => $transactions->transaction_date,
                        'name'                          => $title . " " . $name
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($account); 
                    $this->ledgerTransactionObserver->logAccountTransaction($depositTransaction);
                }
            } 

            DB::commit();

            return response()->json([
                'message'       => "Informasi Return Pembelian berhasil di simpan",
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

    public function detail(Transaction $transaction)
    {

        abort_if(Gate::denies('purchase_retur_view'), 403);

        return response()->json([
            'detail'    => PurchaseReturnDetailResource::make($transaction)
        ], 200);
    }

    public function delete(Transaction $transaction)
    {

        abort_if(Gate::denies('delete_purchase_retur'), 403);

        if ($transaction->payment->count() > 0) {
            return response()->json([
                'message'       => "Transaksi sudah tidak dapat di hapus, karena sudah melakukan pembayaran",
                'status'        => false
            ], 422);
        }

        try {


            DB::beginTransaction();

            $purchaseTransaction = $transaction->transaction;

            $this->purchaseReturnObserver->deleteTransaction($transaction);

            if ($purchaseTransaction) {
                $purchaseTransaction->update([
                    'payment_status'    => abs($purchaseTransaction->due_total_po) < 1 ? 'paid' : 'due'
                ]);
            }

            DB::commit();

            return response()->json([
                'message'   => "Transaksi berhasil di hapus",
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine()
            ], 409);
        }
    }
}
