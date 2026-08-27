<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Sales\SalesPaymentRequest;
use App\Http\Resources\Transaction\Sales\Faktur\FakturDetailResource;
use App\Http\Resources\Transaction\Sales\Faktur\FakturListResource;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\Customer;
use App\Models\Transaction\FakturPaymentDetail;
use App\Models\Transaction\PaymentMethod;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionDue;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Transaction\Sales\SalesPaymentObserver;
use App\Observers\Transaction\TransactionDueObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PaymentSalesController extends Controller
{
    protected $salesPaymentObserver;
    protected $transactionDueObserver;
    protected $ledgerTransactionObserver;
    protected $ledgderObserver;

    public function __construct(SalesPaymentObserver $salesPaymentObserver, TransactionDueObserver $transactionDueObserver, LedgerTransactionObserver $ledgerTransactionObserver, LedgerObserver $ledgderObserver)
    {
        $this->salesPaymentObserver         = $salesPaymentObserver;
        $this->transactionDueObserver       = $transactionDueObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
        $this->ledgderObserver              = $ledgderObserver;
    }

    public function index(Request $request)
    {

        abort_if(Gate::denies('sales_payment_view'), 403);

        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->salesPaymentObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => FakturListResource::collection($transactions),
        ], 200);
    }

    public function store(SalesPaymentRequest $request)
    {

        abort_if(Gate::denies('add_sales_payment'), 403);

        try {

            DB::beginTransaction();

            $customer           = Customer::find($request->customer['id']);
            $transaction        = $this->salesPaymentObserver->createUpdateInformation($request, 'create');
            $details            = $this->salesPaymentObserver->createOrUpdateTransaction($request, $transaction);
            $account            = PaymentMethod::where("id", $request->method['id'])->first(['id', 'account_id']);
            $customerAccount    = $transaction->customer->debt_imprest_account ?? null;

            // Bank Account Debit
 
            if ($details['total_use'] > 0 && $request->total_payment > 0) {

                if ($account->account) {

                    $depositTransaction = AccountTransaction::create([
                        'account_id'                    => $account->account->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $request->total_payment,
                        'type'                          => 'debit',
                        'sub_type'                      => 'pay_customer_faktur',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('pay_customer_faktur', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Penerimaan Penjualan .' . $transaction->ref_no
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($account->account);
                    $this->ledgerTransactionObserver->logAccountTransaction($depositTransaction);

                    if ($customerAccount) {
                        $piutangCustomer = AccountTransaction::create([
                            'transaction_id'                => $transaction->id,
                            'account_id'                    => $customerAccount->id,
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => $depositTransaction->amount,
                            'account_transaction_id'        => $depositTransaction->id,
                            'sub_type'                      => 'pay_customer_faktur',
                            'type'                          => 'credit',
                            'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo($depositTransaction->sub_type, $depositTransaction->operation_date),
                            'operation_date'                => $depositTransaction->operation_date,
                            'name'                          => 'Penerimaan Penjualan'
                        ]);


                        $this->ledgderObserver->updateCashFlowAccount($customerAccount);
                        $this->ledgerTransactionObserver->logAccountTransaction($piutangCustomer);
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
                        'type'                          => 'credit',
                        'sub_type'                      => 'pay_customer_faktur',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('pay_customer_faktur', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Penerimaan Penjualan .' . $transaction->ref_no
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($account->account);
                    $this->ledgerTransactionObserver->logAccountTransaction($depositTransaction);
                }

                if ($customerAccount) {
                    $wdCustomer = AccountTransaction::create([
                        'transaction_id'                => $transaction->id,
                        'account_id'                    => $customerAccount->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => abs($details['use_saldo']),
                        'account_transaction_id'        => $transaction->id,
                        'sub_type'                      => 'wd_customer',
                        'type'                          => 'debit',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('wd_customer', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Penggunaan Saldo'
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($customerAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($wdCustomer);
                }
            }

            // For Credit Account
            if ($details['total_credit'] > 0) {

                $getTransaction         = TransactionDue::where("customer_id", $customer->id)->whereDate("date", substr($request->date, 0, 10))->count() + 1;
                $invoiceNumber          = sprintf("%05d", $getTransaction);
                $refNo                  = 'TD' . date("Ymd") . '/' . $invoiceNumber;

                TransactionDue::create([
                    'transaction_id'        => $transaction->id,
                    'no_ref'                => $refNo,
                    'customer_id'           => $customer->id,
                    'amount'                => $details['total_credit'],
                    'date'                  => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s'),
                    'total_due_amount'      => $details['total_credit'],
                    'type'                  => 'saldo'
                ]);
            }

            DB::commit();

            return response()->json([
                'message'       => "Informasi Pembayaran Penjualan berhasil di simpan",
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

    public function update(SalesPaymentRequest $request, Transaction $transaction)
    {

        abort_if(Gate::denies('update_sales_payment'), 403);

        try {


            DB::beginTransaction();

            $transaction    = $this->salesPaymentObserver->createUpdateInformation($request, 'update', $transaction);
            $details        = $this->salesPaymentObserver->createOrUpdateTransaction($request, $transaction);


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
            $customerAccount    = $transaction->customer->debt_imprest_account ?? null;

            // Bank Account Debit
            if ($details['total_use'] > 0 && $request->total_payment > 0) {
                if ($account->account) {
                    $depositTransaction = AccountTransaction::create([
                        'account_id' => $account->account->id,
                        'transaction_id' => $transaction->id,
                        'created_by' => auth()->user()->id,
                        'amount' => $request->total_payment,
                        'type' => 'debit',
                        'sub_type' => 'pay_customer_faktur',
                        'ref_no' => $this->ledgerTransactionObserver->generateRefNo('pay_customer_faktur', $transaction->created_at),
                        'operation_date' => $transaction->transaction_date,
                        'name' => 'Penerimaan Penjualan .' . $transaction->ref_no
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($account->account);
                    $this->ledgerTransactionObserver->logAccountTransaction($depositTransaction);

                    if ($customerAccount) {
                        $piutangCustomer = AccountTransaction::create([
                            'transaction_id' => $transaction->id,
                            'account_id' => $customerAccount->id,
                            'created_by' => auth()->user()->id,
                            'amount' => $depositTransaction->amount,
                            'account_transaction_id' => $depositTransaction->id,
                            'sub_type' => 'pay_customer_faktur',
                            'type' => 'credit',
                            'ref_no' => $this->ledgerTransactionObserver->generateRefNo($depositTransaction->sub_type, $depositTransaction->operation_date),
                            'operation_date' => $depositTransaction->operation_date,
                            'name' => 'Penerimaan Penjualan'
                        ]);

                        $this->ledgderObserver->updateCashFlowAccount($customerAccount);
                        $this->ledgerTransactionObserver->logAccountTransaction($piutangCustomer);
                    }
                }
            }

            // For Payout
            if ($request->total_payment < 0) {
                if ($account->account) {
                    $depositTransaction = AccountTransaction::create([
                        'account_id' => $account->account->id,
                        'transaction_id' => $transaction->id,
                        'created_by' => auth()->user()->id,
                        'amount' => abs($request->total_payment),
                        'type' => 'credit',
                        'sub_type' => 'pay_customer_faktur',
                        'ref_no' => $this->ledgerTransactionObserver->generateRefNo('pay_customer_faktur', $transaction->created_at),
                        'operation_date' => $transaction->transaction_date,
                        'name' => 'Penerimaan Penjualan .' . $transaction->ref_no
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($account->account);
                    $this->ledgerTransactionObserver->logAccountTransaction($depositTransaction);
                }

                if ($customerAccount) {
                    $wdCustomer = AccountTransaction::create([
                        'transaction_id' => $transaction->id,
                        'account_id' => $customerAccount->id,
                        'created_by' => auth()->user()->id,
                        'amount' => abs($details['use_saldo']),
                        'account_transaction_id' => $transaction->id,
                        'sub_type' => 'wd_customer',
                        'type' => 'debit',
                        'ref_no' => $this->ledgerTransactionObserver->generateRefNo('wd_customer', $transaction->created_at),
                        'operation_date' => $transaction->transaction_date,
                        'name' => 'Penggunaan Saldo'
                    ]);

                    $this->ledgderObserver->updateCashFlowAccount($customerAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($wdCustomer);
                }
            }

            // For Credit Account
            if ($details['total_credit'] > 0) {
                $getTransaction = TransactionDue::where("customer_id", $transaction->customer->id)->whereDate("date", substr($request->date, 0, 10))->count() + 1;
                $invoiceNumber = sprintf("%05d", $getTransaction);
                $refNo = 'TD' . date("Ymd") . '/' . $invoiceNumber;

                TransactionDue::updateOrCreate(
                    ['transaction_id' => $transaction->id],
                    [
                        'no_ref'            => $refNo,
                        'customer_id'       => $transaction->customer->id,
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
                'message'       => "Informasi Penjualan berhasil di perbaharui",
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

    public function deleteItem(FakturPaymentDetail $faktur)
    {

        abort_if(Gate::denies('update_sales_payment'), 403);

        try {

            DB::beginTransaction();
            $this->salesPaymentObserver->deleteItem($faktur, $faktur->transaction);
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
        return response()->json([
            'transaction'   => FakturDetailResource::make($transaction),
        ], 200);
    }

    public function deleteDraft(Transaction $transaction)
    {

        abort_if(Gate::denies('delete_sales_payment'), 403);

        try {


            DB::beginTransaction();

            $this->salesPaymentObserver->deleteTransaction($transaction);

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
}
