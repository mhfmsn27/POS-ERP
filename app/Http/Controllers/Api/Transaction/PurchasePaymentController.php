<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Purchase\PurchasePaymentRequest;
use App\Http\Resources\Transaction\Purchase\Faktur\FakturDetailResource;
use App\Http\Resources\Transaction\Purchase\Faktur\FakturListResource;
use App\Models\Account\AccountTransaction;
use App\Models\Transaction\FakturPaymentDetail;
use App\Models\Transaction\PaymentMethod;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionDue;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Transaction\Purchase\PurchasePaymentObserver;
use App\Observers\Transaction\TransactionDueObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PurchasePaymentController extends Controller
{
    protected $purchasePaymentObserver;
    protected $transactionDueObserver;
    protected $ledgderObserver;
    protected $ledgerTransactionObserver;

    public function __construct(PurchasePaymentObserver $purchasePaymentObserver, TransactionDueObserver $transactionDueObserver, LedgerObserver $ledgderObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->purchasePaymentObserver      = $purchasePaymentObserver;
        $this->transactionDueObserver       = $transactionDueObserver;
        $this->ledgderObserver              = $ledgderObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
    }

    public function index(Request $request)
    {

        abort_if(Gate::denies('purchase_payment_view'), 403);

        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->purchasePaymentObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => FakturListResource::collection($transactions),
        ], 200);
    }

    public function store(PurchasePaymentRequest $request)
    {

        abort_if(Gate::denies('add_purchase_payment'), 403);

        try {

            DB::beginTransaction();

            $transaction    = $this->purchasePaymentObserver->createUpdateInformation($request, 'create');
            $details        = $this->purchasePaymentObserver->createOrUpdateTransaction($request, $transaction);
            $account            = PaymentMethod::where("id", $request->method['id'])->first(['id', 'account_id']);
            $supplierAccount    = $transaction->supplier->debt_imprest_account ?? null;
            // Bank Account Debit
            if ($details['total_use'] > 0 && $request->total_payment > 0) {

                if ($account->account) {

                    $depositTransaction = AccountTransaction::create([
                        'account_id'                    => $account->account->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $request->total_payment,
                        'type'                          => 'credit',
                        'sub_type'                      => 'pay_supplier_faktur',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('pay_supplier_faktur', $transaction->operation_date),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Pembayaran Pembelian - ' . $transaction->ref_no
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($account->account);
                    $this->ledgerTransactionObserver->logAccountTransaction($depositTransaction);

                    if ($supplierAccount) {
                        $hutangSupplier = AccountTransaction::create([
                            'transaction_id'                => $transaction->id,
                            'account_id'                    => $supplierAccount->id,
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => $depositTransaction->amount,
                            'account_transaction_id'        => $depositTransaction->id,
                            'sub_type'                      => 'pay_supplier_faktur',
                            'type'                          => 'debit',
                            'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo($depositTransaction->sub_type, $depositTransaction->operation_date),
                            'operation_date'                => $depositTransaction->operation_date,
                            'name'                          => 'Pembayaran Faktur'
                        ]);

                        $this->ledgderObserver->updateCashFlowAccount($supplierAccount);
                        $this->ledgerTransactionObserver->logAccountTransaction($hutangSupplier);
                    }
                }
            }

            // For Payout
            if ($request->total_payment < 0) {

                if ($account->account) {
                    $depositTransaction = AccountTransaction::create([
                        'account_id'                    => $account->account->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => abs($request->total_payment),
                        'type'                          => 'debit',
                        'sub_type'                      => 'pay_supplier_faktur',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('pay_supplier_faktur', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Pembayaran Pembelian .' . $transaction->ref_no
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($account->account);
                    $this->ledgerTransactionObserver->logAccountTransaction($depositTransaction);
                }

                if ($supplierAccount) {
                    $wdSupplier = AccountTransaction::create([
                        'transaction_id'                => $transaction->id,
                        'account_id'                    => $supplierAccount->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => abs($details['use_saldo']),
                        'account_transaction_id'        => $transaction->id,
                        'sub_type'                      => 'wd_supplier',
                        'type'                          => 'credit',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('wd_supplier', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Penggunaan Saldo'
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($supplierAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($wdSupplier);
                }
            }

            // For Credit Account
            if ($details['total_credit'] > 0) {

                $getTransaction         = TransactionDue::where("supplier_id", $transaction->supplier->id)->whereDate("date", substr($request->date, 0, 10))->count() + 1;
                $invoiceNumber          = sprintf("%05d", $getTransaction);
                $refNo                  = 'TD' . date("Ymd") . '/' . $invoiceNumber;

                TransactionDue::create([
                    'transaction_id'        => $transaction->id,
                    'no_ref'                => $refNo,
                    'supplier_id'           => $transaction->supplier->id,
                    'amount'                => $details['total_credit'],
                    'date'                  => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s'),
                    'total_due_amount'      => $details['total_credit'],
                    'type'                  => 'saldo'
                ]);
            }

            DB::commit();

            return response()->json([
                'transaction'   => $transaction->id,
                'message'       => "Informasi Pembayaran Pembelian berhasil di simpan",
                'details'       => FakturDetailResource::make($transaction),
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

    public function update(PurchasePaymentRequest $request, Transaction $transaction)
    {

        abort_if(Gate::denies('update_purchase_payment'), 403);

        try {

            DB::beginTransaction();

            $transaction    = $this->purchasePaymentObserver->createUpdateInformation($request, 'update', $transaction);
            $details        = $this->purchasePaymentObserver->createOrUpdateTransaction($request, $transaction);

            $oldAccountTransactions = AccountTransaction::where('transaction_id', $transaction->id)->get();
            foreach ($oldAccountTransactions as $accountTransaction) {
                $nextTransaction = AccountTransaction::where(function ($query) use ($accountTransaction) {
                    $query->where("operation_date", ">", $accountTransaction->operation_date)
                        ->orWhere(function ($subQuery) use ($accountTransaction) {
                            $subQuery->where("operation_date", "=", $accountTransaction->operation_date)
                                ->where("id", "<", $accountTransaction->id);
                        });
                })
                    ->where("account_id", $accountTransaction->account_id)
                    ->orderBy("operation_date", 'asc')
                    ->orderBy("id", 'asc')->first();
                    
                $account = $accountTransaction->account;

                $accountTransaction->delete();

                if ($nextTransaction) {
                    $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
                }

                if ($account) {
                    $this->ledgderObserver->updateCashFlowAccount($account);
                }
            }

            $account            = PaymentMethod::where("id", $request->method['id'])->first(['id', 'account_id']);
            $supplierAccount    = $transaction->supplier->debt_imprest_account ?? null;
            // Bank Account Debit
            if ($details['total_use'] > 0 && $request->total_payment > 0) {

                if ($account->account) {

                    $depositTransaction = AccountTransaction::create([
                        'account_id'                    => $account->account->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $request->total_payment,
                        'type'                          => 'credit',
                        'sub_type'                      => 'pay_supplier_faktur',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('pay_supplier_faktur', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Pembayaran Pembelian - ' . $transaction->ref_no
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($account->account);
                    $this->ledgerTransactionObserver->logAccountTransaction($depositTransaction);

                    if ($supplierAccount) {
                        $hutangSupplier = AccountTransaction::create([
                            'transaction_id'                => $transaction->id,
                            'account_id'                    => $supplierAccount->id,
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => $depositTransaction->amount,
                            'account_transaction_id'        => $depositTransaction->id,
                            'sub_type'                      => 'pay_supplier_faktur',
                            'type'                          => 'debit',
                            'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo($depositTransaction->sub_type, $depositTransaction->operation_date),
                            'operation_date'                => $depositTransaction->operation_date,
                            'name'                          => 'Pembayaran Faktur'
                        ]);

                        $this->ledgderObserver->updateCashFlowAccount($supplierAccount);
                        $this->ledgerTransactionObserver->logAccountTransaction($hutangSupplier);
                    }
                }
            }

            // For Payout
            if ($request->total_payment < 0) {

                if ($account->account) {
                    $depositTransaction = AccountTransaction::create([
                        'account_id'                    => $account->account->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => abs($request->total_payment),
                        'type'                          => 'debit',
                        'sub_type'                      => 'pay_supplier_faktur',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('pay_supplier_faktur', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Pembayaran Pembelian - ' . $transaction->ref_no
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($account->account);
                    $this->ledgerTransactionObserver->logAccountTransaction($depositTransaction);
                }

                if ($supplierAccount) {
                    $wdSupplier = AccountTransaction::create([
                        'transaction_id'                => $transaction->id,
                        'account_id'                    => $supplierAccount->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => abs($details['use_saldo']),
                        'account_transaction_id'        => $transaction->id,
                        'sub_type'                      => 'wd_supplier',
                        'type'                          => 'credit',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('wd_supplier', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Penggunaan Saldo'
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($supplierAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($wdSupplier);
                }
            }

            // For Credit Account
            if ($details['total_credit'] > 0) {
                $getTransaction = TransactionDue::where("supplier_id", $transaction->supplier->id)->whereDate("date", substr($request->date, 0, 10))->count() + 1;
                $invoiceNumber = sprintf("%05d", $getTransaction);
                $refNo = 'TD' . date("Ymd") . '/' . $invoiceNumber;

                TransactionDue::updateOrCreate(
                    ['transaction_id' => $transaction->id],
                    [
                        'no_ref'            => $refNo,
                        'supplier_id'       => $transaction->supplier->id,
                        'amount'            => $details['total_credit'],
                        'date'              => $request->date,
                        'total_due_amount'  => $details['total_credit'],
                        'type'              => 'saldo'
                    ]
                );
            } else {
                TransactionDue::where('transaction_id', $transaction->id)->delete();
            }


            DB::commit();

            return response()->json([
                'message'       => "Informasi Pembelian berhasil di perbaharui",
                'details'       => FakturDetailResource::make($transaction),
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

    public function deleteItem(FakturPaymentDetail $faktur)
    {

        abort_if(Gate::denies('update_purchase_payment'), 403);

        try {

            DB::beginTransaction();
            $this->purchasePaymentObserver->deleteItem($faktur, $faktur->transaction);
            DB::commit();
            return response()->json([
                'message'   => "Item Faktur berhasil di hapus",
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage(),
                'line'      => $e->getLine()
            ], 409);
        }
    }


    public function detail(Transaction $transaction)
    {

        abort_if(Gate::denies('purchase_payment_view'), 403);

        return response()->json([
            'transaction'   => FakturDetailResource::make($transaction),
        ], 200);
    }

    public function deleteDraft(Transaction $transaction)
    {

        abort_if(Gate::denies('delete_purchase_payment'), 403);

        try {

            DB::beginTransaction();

            $this->purchasePaymentObserver->deleteTransaction($transaction);

            DB::commit();

            return response()->json([
                'message'   => "Transaksi berhasil di hapus",
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage(),
                'line'      => $e->getLine()
            ], 409);
        }
    }

    public function print(Transaction $transaction)
    {
        return view('print.purchase.pembayaran_pembelian', compact('transaction'));
    }
}
