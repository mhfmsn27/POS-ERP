<?php

namespace App\Http\Controllers\Api\Transaction\Return;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\SaleReturn\SaleReturnRequest;
use App\Http\Resources\Transaction\SaleReturn\SaleReturnDetailResource;
use App\Http\Resources\Transaction\SaleReturn\SaleReturnListResource;
use App\Models\Account\AccountTransaction;
use App\Models\Transaction\Transaction;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Transaction\Sales\SaleReturnObserver;
use App\Observers\Transaction\TransactionDueObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReturnSaleController extends Controller
{
    protected $saleReturnObserver;
    protected $transactionDueObserver;
    protected $ledgerObserver;
    protected $ledgerTransactionObserver;

    public function __construct(SaleReturnObserver $saleReturnObserver, TransactionDueObserver $transactionDueObserver, LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->saleReturnObserver           = $saleReturnObserver;
        $this->transactionDueObserver       = $transactionDueObserver;
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('sales_retur_view'), 403);
        $limit          = $request->limit ? $request->limit : 10;
        $data           = $this->saleReturnObserver->getData($request);
        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => SaleReturnListResource::collection($transactions),
        ], 200);
    }

    public function create(SaleReturnRequest $request, Transaction $transaction)
    {

        abort_if(Gate::denies('add_sales_retur'), 403);

        try {

            DB::beginTransaction();

            $transactions    = $this->saleReturnObserver->createUpdateInformation($request, 'create', $transaction);
            $this->saleReturnObserver->createReturns($request, $transactions);
            $this->saleReturnObserver->subtotalTransactionChange($transactions);

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

                if ($transaction->due_total == 0) {
                    $transaction->update([
                        'payment_status'    => 'paid'
                    ]);
                }
            }

            if ($totalReturn > 0) {
                $this->transactionDueObserver->createBySellTransaction($transactions, 'saldo');
            } else {
                $account    = $transactions->customer->debt_account ?? null;
                $name       = $transactions->customer->name ?? '';
                $title      = 'Retur Penjualan - ' . $transactions->ref_no;
                $account    = $transactions->customer->debt_account ?? null;

                if ($account) {
                    $depositTransaction = AccountTransaction::create([
                        'account_id'                    => $account->id,
                        'transaction_id'                => $transactions->id, 
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $dueRevisionTotal,
                        'type'                          => 'credit',
                        'sub_type'                      => 'due_customer',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('due_customer', $transactions->created_at),
                        'operation_date'                => $transactions->transaction_date,
                        'name'                          => $title . " " . $name
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($account);
                    $this->ledgerTransactionObserver->logAccountTransaction($depositTransaction);
                    
                }
            }


            DB::commit();

            return response()->json([
                'message'       => "Informasi Return Penjualan berhasil di simpan",
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

        abort_if(Gate::denies('sales_retur_view'), 403);

        return response()->json([
            'detail'    => SaleReturnDetailResource::make($transaction)
        ], 200);
    }


    public function delete(Transaction $transaction)
    {

        abort_if(Gate::denies('delete_sales_retur'), 403);

        if ($transaction->payment->count() > 0) {
            return response()->json([
                'message'       => "Transaksi sudah tidak dapat di hapus, karena sudah melakukan pembayaran",
                'status'        => false
            ], 422);
        }

        try {


            DB::beginTransaction();

            $saleTransaction = $transaction->transaction;

            $this->saleReturnObserver->deleteTransaction($transaction);
 
            if ($saleTransaction) {
                $saleTransaction->update([
                    'payment_status'    => abs($saleTransaction->due_total) < 1 ? 'paid' : 'due'
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
