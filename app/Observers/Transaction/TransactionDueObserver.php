<?php

namespace App\Observers\Transaction;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\Customer;
use App\Models\Product\Supplier;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionDue;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionDueObserver
{

    protected $ledgerTransactionObserver;
    protected $ledgerObserver;

    public function __construct(LedgerTransactionObserver $ledgerTransactionObserver, LedgerObserver $ledgerObserver)
    {
        $this->ledgerTransactionObserver        = $ledgerTransactionObserver;
        $this->ledgerObserver                   = $ledgerObserver;
    }

    public function getHutang(Request $request)
    {

        return TransactionDue::select(
            'customer_id',
            DB::raw('MIN(date) as date'),
            DB::raw('MIN(due_end) as due_date'),
            DB::raw('DATEDIFF(MIN(due_end), NOW()) as days_left'),
            DB::raw('SUM(total_due_amount) as total_due_amount')
        )->whereHas('customer', function ($q) {
            return $q->where('store_id', my_store());
        })
            ->where('due_end', '<=', now())
            ->where('customer_id', '!=', null)
            ->where('type', 'hutang')
            ->where('due_limit', '>', 1)
            ->where('status', 'due')
            ->groupBy('customer_id')
            ->orderBy('days_left', 'asc')
            ->limit(10)
            ->get();
    }

    public function getData(Request $request)
    {
        $q = TransactionDue::where(function ($q) use ($request) {
            return $request->supplier ? $q->where("supplier_id", $request->supplier) : '';
        })->where(function ($query) use ($request) {
            return $request->customer ? $query->where('customer_id', $request->customer) : '';
        })->where(function ($query) use ($request) {
            return $request->transaction ?  $query->where('transaction_id', $request->transaction) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('date', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("date", $request->start_date) : "";
            }
        })->where(function ($query) use ($request) {
            return $request->ref ? $query->whereHas('transaction', function ($q) use ($request) {
                return $q->where('ref_no', 'like', '%' . $request->ref . '%');
            })->orWhere('transaction_id', null) : '';
        })->where(function ($query) use ($request) {
            return $request->ref ? $query->where('no_ref', 'like', '%' . $request->ref . '%') : '';
        })->where(function ($query) use ($request) {
            return $request->status ? $query->where('status', $request->status) : '';
        })->where(function ($query) use ($request) {
            return $request->type ? $query->where('type', $request->type) : '';
        });

        if ($request->order) {
            $q->orderBy("created_at", $request->order);
        } else {
            $q->orderBy("created_at", "desc");
        }

        return $q;
    }

    public function getById($idData)
    {
        return TransactionDue::findOrFail($idData);
    }

    public function getByTransaction($idTransaction)
    {
        return TransactionDue::where("transaction_id", $idTransaction)->first();
    }

    public function createBySupplier(Request $request, Supplier $supplier, $type = 'hutang')
    {

        $date                   = Helper::setTimeZoneLocal($request->date) . date('H:i:s');
        $getTransaction         = TransactionDue::where("supplier_id", $supplier->id)->whereDate("date", substr($date, 0, 10))->count() + 1;
        $invoiceNumber          = sprintf("%05d", $getTransaction);
        $refNo                  = 'TD' . date("Ymd") . '/' . $invoiceNumber;

        $transaction = TransactionDue::create([
            'no_ref'                => $refNo,
            'supplier_id'           => $supplier->id,
            'amount'                => $request->amount,
            'note'                  => $request->note,
            'date'                  => $date,
            'type'                  => $type,
            'total_due_amount'      => $request->amount
        ]);

        $this->ledgerTransactionObserver->createDueSupplier($transaction);

        return $transaction;
    }

    public function createByCustomer(Request $request, Customer $customer, $type = 'hutang')
    {

        $date                   = Helper::setTimeZoneLocal($request->date) . date('H:i:s');;
        $getTransaction         = TransactionDue::where("customer_id", $customer->id)->whereDate("date", substr($date, 0, 10))->count() + 1;
        $invoiceNumber          = sprintf("%05d", $getTransaction);
        $refNo                  = 'TD' . date("Ymd") . '/' . $invoiceNumber;


        $transaction = TransactionDue::create([
            'no_ref'                => $refNo,
            'customer_id'           => $customer->id,
            'amount'                => $request->amount,
            'note'                  => $request->note,
            'date'                  => $date,
            'total_due_amount'      => $request->amount,
            'type'                  => $type
        ]);

        $this->ledgerTransactionObserver->createDueCustomer($transaction);

        return $transaction;
    }

    public function createBySellTransaction(Transaction $transaction, $type = 'hutang')
    {

        $getTransaction         = TransactionDue::where("customer_id", $transaction->customer->id)->whereDate("date", substr($transaction->transaction_date, 0, 10))->count() + 1;
        $invoiceNumber          = sprintf("%05d", $getTransaction);
        $typeRef                = $type == 'hutang' ? 'PC' : 'SC';
        $refNo                  = $typeRef . date("Ymd") . '/' . $invoiceNumber;


        $transaction = TransactionDue::create([
            'no_ref'                => $refNo,
            'transaction_id'        => $transaction->id,
            'customer_id'           => $transaction->customer->id,
            'amount'                => $transaction->final_total,
            'note'                  => $transaction->additional_notes,
            'date'                  => $transaction->transaction_date,
            'type'                  => $type,
            'due_limit'             => $transaction->due_limit,
            'due_end'               => $transaction->due_end,
            'total_due_amount'      => $transaction->final_total
        ]);

        $this->ledgerTransactionObserver->createDueCustomer($transaction);
    }

    public function updateBySellTransaction(Transaction $transaction)
    {

        if ($transaction->transaction_due) {
            $transaction->transaction_due->update([
                'customer_id'           => $transaction->customer->id,
                'amount'                => $transaction->final_total,
                'note'                  => $transaction->additional_notes,
                'date'                  => $transaction->transaction_date,
            ]);

            $this->ledgerTransactionObserver->updateDueCustomer($transaction->transaction_due);
        }
    }

    public function createByTransaction(Transaction $transaction, $type = 'hutang')
    {
        $getTransaction         = TransactionDue::where("supplier_id", $transaction->supplier->id)->whereDate("date", substr($transaction->transaction_date, 0, 10))->count() + 1;
        $invoiceNumber          = sprintf("%05d", $getTransaction);
        $typeRef                = $type == 'hutang' ? 'TD' : 'SS';
        $refNo                  = $typeRef . date("Ymd") . '/' . $invoiceNumber;


        $transaction = TransactionDue::create([
            'no_ref'                => $refNo,
            'transaction_id'        => $transaction->id,
            'supplier_id'           => $transaction->supplier->id,
            'amount'                => $transaction->final_total,
            'note'                  => $transaction->additional_notes,
            'type'                  => $type,
            'date'                  => $transaction->transaction_date,
            'total_due_amount'      => $transaction->final_total
        ]);

        $this->ledgerTransactionObserver->createDueSupplier($transaction);
    }

    public function updateByTransaction(Transaction $transaction)
    {
        if ($transaction->transaction_due) {
            $transaction->transaction_due->update([
                'supplier_id'           => $transaction->supplier->id,
                'amount'                => $transaction->final_total,
                'note'                  => $transaction->additional_notes,
                'date'                  => $transaction->transaction_date,
            ]);

            $this->ledgerTransactionObserver->updateDueSupplier($transaction->transaction_due);
        }
    }

    public function deleteData(TransactionDue $transaction)
    {
        foreach ($transaction->account_transaction as $account) {
            foreach ($account->account_transaction as $account_two) {

                $nextTransaction    = AccountTransaction::where(function ($query) use ($account_two) {
                    $query->where("operation_date", ">", $account_two->operation_date)
                        ->orWhere(function ($subQuery) use ($account_two) {
                            $subQuery->where("operation_date", "=", $account_two->operation_date)
                                ->where("id", "<", $account_two->id);
                        });
                })
                    ->where("account_id", $account_two->account_id)
                    ->orderBy("operation_date", 'asc')
                    ->orderBy("id", 'asc')->first();
                $accountData        = $account_two->account;

                $account_two->delete();


                if ($nextTransaction) {
                    $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
                }

                if ($accountData) {
                    $this->ledgerObserver->updateCashFlowAccount($accountData);
                }
            }

            $nextTransaction    = AccountTransaction::where(function ($query) use ($account) {
                $query->where("operation_date", ">", $account->operation_date)
                    ->orWhere(function ($subQuery) use ($account) {
                        $subQuery->where("operation_date", "=", $account->operation_date)
                            ->where("id", "<", $account->id);
                    });
            })
                ->where("account_id", $account->account_id)
                ->orderBy("operation_date", 'asc')
                ->orderBy("id", 'asc')->first();
            $accountData        = $account->account;
            $account->delete();

            if ($nextTransaction) {
                $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
            }

            if ($accountData) {
                $this->ledgerObserver->updateCashFlowAccount($accountData);
            }
        }

        $transaction->delete();
    }
}
