<?php

namespace App\Observers\CashIntOut;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Account\Expense;
use App\Models\Account\ExpenseDetail;
use App\Models\Transaction\PaymentMethod;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use Illuminate\Http\Request;

class CashIntOutObserver
{

    protected $ledgerObserver;
    protected $ledgerTransactionObserver;

    public function __construct(LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
    }


    public function getData(Request $request, $type = '', $year = '', $month = '')
    {
        return Expense::where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereDate("created_at", ">=", $request->start_date)->whereDate("created_at", "<=", $request->end_date);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : '';
            }
        })->where(function ($query) use ($request) {
            return $request->category ? $query->where('category_id', $request->category) : '';
        })->where(function ($query) use ($request) {
            return $request->type_contact ? $query->where('contact_type', $request->type_contact) : '';
        })->where(function ($query) use ($request) {
            return $request->contact ? $query->where('contact_id', $request->contact) : '';
        })->where(function ($query) use ($request) {
            return $request->name ? $query->where('name', 'like', '%' . $request->name . '%')->orWhere('ref_no', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($year) {
            return $year != '' ? $q->whereYear("created_at", $year) : '';
        })->where(function ($q) use ($month) {
            return $month != '' ? $q->whereMonth("created_at", $month) : '';
        })->where(function ($q) use ($request) {
            return $request->type ? $q->where('type', $request->type) : '';
        })->where(function ($q) use ($type) {
            return $type != '' ? $q->where('type', $type) : '';
        })->orderBy("created_at", "desc");
    }

    public function createData(Request $request, String $type)
    {

        $methodDetail   = PaymentMethod::find($request->method['id']);
        $items          = collect($request->items);

        $expense        = Expense::create([
            'method_id'         => $request->method['id'],
            'ref_no'            => $this->createNoRef($request->ref_no),
            'category_id'       => $request->category['id'],
            'name'              => $type == 'expense' ? 'Pembayaran' : 'Penerimaan',
            'amount'            => $request->summary['subtotal'],
            'detail'            => $request->detail,
            'created_at'        => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s'),
            'type'              => $type,
            'detail'            => $request->note,
            'payment_status'    => 'paid',
        ]);

        foreach ($items as $item) {

            $detail = ExpenseDetail::create([
                'expense_id'        => $expense->id,
                'account_id'        => $item['account_id'],
                'name'              => $item['name'],
                'amount'            => $item['amount']
            ]);

            if ($item['account_id'] != null) {
                $expenseDetailAccount = AccountTransaction::create([
                    'account_id'                    => $detail->account_id,
                    'expense_id'                    => $expense->id,
                    'item_id'                       => $detail->id,
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $item['amount'],
                    'type'                          => $type == 'expense' ? 'debit' : 'credit',
                    'sub_type'                      => $type,
                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo($type, $expense->created_at->format("Y-m-d") ?? now()),
                    'operation_date'                => $expense->created_at->format("Y-m-d") ?? now(),
                    'note'                          => $request->note,
                    'name'                          => $item['name']
                ]);


                $this->ledgerObserver->updateCashFlowAccount($detail->account);
                $this->ledgerTransactionObserver->logAccountTransaction($expenseDetailAccount);
            }
        }

        if ($methodDetail->account) {

            $nameHistory    = $type == 'expense' ? 'Pembayaran' : 'Penerimaan';
            $nameHistory    = $nameHistory . ' ' . $expense->created_at->format("Y-m-d");
            $methodAccount = AccountTransaction::create([
                'account_id'                    => $methodDetail->account->id,
                'expense_id'                    => $expense->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $items->sum('amount'),
                'type'                          => $type == 'expense' ? 'credit' : 'debit',
                'sub_type'                      => $type,
                'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo($type, $expense->created_at->format("Y-m-d") ?? now()),
                'operation_date'                => $expense->created_at->format("Y-m-d") ?? now(),
                'note'                          => $request->note,
                'name'                          => $nameHistory
            ]);

            $this->ledgerObserver->updateCashFlowAccount($methodDetail->account);
            $this->ledgerTransactionObserver->logAccountTransaction($methodAccount);
        }

        return $expense;
    }

    public function updateData(Request $request, String $type, Expense $expense)
    {

        

        $methodDetail   = PaymentMethod::find($request->method['id']);
        $items          = collect($request->items);

        $expense->update([
            'method_id'         => $request->method['id'],
            'ref_no'            => $this->createNoRef($request->ref_no),
            'category_id'       => $request->category['id'],
            'name'              => $type == 'expense' ? 'Pembayaran' : 'Penerimaan',
            'amount'            => $request->summary['subtotal'],
            'detail'            => $request->detail,
            'created_at'        => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s'),
            'detail'            => $request->note,
            'type'              => $type,
            'payment_status'    => 'paid',
        ]);


        $this->deleteAccount($expense);

        foreach ($items as $item) {

            $detail = ExpenseDetail::create([
                'expense_id'        => $expense->id,
                'account_id'        => $item['account_id'],
                'name'              => $item['name'],
                'amount'            => $item['amount']
            ]);

            if ($item['account_id']) {
                $expenseDetailAccount = AccountTransaction::create([
                    'account_id'                    => $detail->account_id,
                    'expense_id'                    => $expense->id,
                    'item_id'                       => $detail->id,
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $item['amount'],
                    'type'                          => $type == 'expense' ? 'debit' : 'credit',
                    'sub_type'                      => $type,
                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo($type, $expense->created_at->format("Y-m-d") ?? now()),
                    'operation_date'                => $expense->created_at->format("Y-m-d") ?? now(),
                    'note'                          => $request->note,
                    'name'                          => $item['name']
                ]);

                $this->ledgerTransactionObserver->logAccountUpdate($expenseDetailAccount);
                $this->ledgerObserver->updateCashFlowAccount($detail->account);
            }
        }

        if ($methodDetail->account) {

            $nameHistory    = $type == 'expense' ? 'Pembayaran' : 'Penerimaan';
            $nameHistory    = $nameHistory . ' ' . $expense->created_at->format("Y-m-d");
            $methodAccount = AccountTransaction::create([
                'account_id'                    => $methodDetail->account->id,
                'expense_id'                    => $expense->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $items->sum('amount'),
                'type'                          => $type == 'expense' ? 'credit' : 'debit',
                'sub_type'                      => $type,
                'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo($type, $expense->created_at->format("Y-m-d") ?? now()),
                'operation_date'                => $expense->created_at->format("Y-m-d") ?? now(),
                'note'                          => $request->note,
                'name'                          => $nameHistory
            ]);

            $this->ledgerTransactionObserver->logAccountUpdate($methodAccount);
            $this->ledgerObserver->updateCashFlowAccount($methodDetail->account);
        }

        return $expense;
    }

    public function createNoRef($refNo = null)
    {
        if ($refNo == null || $refNo == '') {
            $getTransaction   = Expense::whereDate("created_at", date("Y-m-d"))->count() + 1;
            $invoiceNumber    = sprintf("%05d", $getTransaction);
            $noref            = Helper::transactionKey('EP', $invoiceNumber);

            return $noref;
        } else {
            return $refNo;
        }
    }

    public function deleteAccount(Expense $expense)
    {
        foreach ($expense->list as $detail) {
            $this->deleteItem($detail);
        }

        foreach ($expense->account_transaction as $transaction) {
            $account            = $transaction->account;
            $nextTransaction    = AccountTransaction::where(function ($query) use ($transaction) {
                $query->where("operation_date", ">", $transaction->operation_date)
                    ->orWhere(function ($subQuery) use ($transaction) {
                        $subQuery->where("operation_date", "=", $transaction->operation_date)
                            ->where("id", "<", $transaction->id);
                    });
            })
                ->where("account_id", $transaction->account_id)
                ->orderBy("operation_date", 'asc')
                ->orderBy("id", 'asc')->first();

            $transaction->delete();

            if ($nextTransaction) {
                $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
            }

            if ($account) {
                $this->ledgerObserver->updateCashFlowAccount($account);
            }
        }
    }

    public function deleteItem(ExpenseDetail $detail)
    {
        $account            = $detail->account;
        $transaction        = AccountTransaction::where("expense_id", $detail->expense_id)->where("item_id", $detail->id)->first();

        if ($transaction) {

            $transaction->delete();
        }

        if ($account) { 

            $nextTransaction    = AccountTransaction::where(function ($query) use ($transaction) {
                $query->where("operation_date", ">", $transaction->operation_date)
                    ->orWhere(function ($subQuery) use ($transaction) {
                        $subQuery->where("operation_date", "=", $transaction->operation_date)
                            ->where("id", "<", $transaction->id);
                    });
            })
                ->where("account_id", $transaction->account_id)
                ->orderBy("operation_date", 'asc')
                ->orderBy("id", 'asc')->first();
                
            if ($nextTransaction) {
                $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
            }

            if ($account) {
                $this->ledgerObserver->updateCashFlowAccount($account);
            }
        }


        

        $detail->delete();
    }

    public function deleteData(Expense $expense)
    {
        $this->deleteAccount($expense);
        $expense->delete();
    }
}
