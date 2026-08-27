<?php

namespace App\Observers\Account;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Account\JurnalUmum;
use App\Models\Transaction\Transaction;
use Illuminate\Http\Request;

class JurnalUmumObserver
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
        return Transaction::where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereDate("transaction_date", ">=", $request->start_date)->whereDate("transaction_date", "<=", $request->end_date);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : '';
            }
        })->where("type", 'jurnal')->where(function ($query) use ($request) {
            return $request->ref ? $query->where('ref_no', 'like', '%' . $request->ref . '%')->orWhere('additional_notes', 'like', '%' . $request->ref . '%') : '';
        })->orderBy("transaction_date", "desc");
    }

    public function createData(Request $request)
    {

        $items          = collect($request->items);

        $transaction        = Transaction::create([
            'ref_no'            => $this->createNoRef($request->ref_no),
            'additional_notes'  => $request->name,
            'transaction_date'  => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s'),
            'payment_status'    => 'paid',
            'type'              => 'jurnal'
        ]);

        foreach ($items as $item) {

            $detail = JurnalUmum::create([
                'transaction_id'    => $transaction->id,
                'account_id'        => $item['account_id'],
                'name'              => $item['name'],
                'amount'            => $item['type'] == 'debit' ? $item['amount'] : $item['amount_credit'],
                'type'              => $item['type']
            ]);

            if ($item['account_id'] != null) {
                $jurnalUmumDetail = AccountTransaction::create([
                    'account_id'                    => $detail->account_id,
                    'transaction_id'                => $transaction->id,
                    'item_id'                       => $detail->id,
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $detail->amount,
                    'type'                          => $detail->type,
                    'sub_type'                      => 'jurnal',
                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('jurnal', $transaction->transaction_date),
                    'operation_date'                => $transaction->transaction_date,
                    'note'                          => $request->note,
                    'name'                          => $item['name']
                ]);


                $this->ledgerObserver->updateCashFlowAccount($detail->account);
                $this->ledgerTransactionObserver->logAccountTransaction($jurnalUmumDetail);
            }
        }

        return $transaction;
    }

    public function updateData(Request $request, Transaction $transaction)
    {

        $items          = collect($request->items);

        $transaction->update([
            'ref_no'            => $this->createNoRef($request->ref_no),
            'additional_notes'  => $request->name,
            'transaction_date'  => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s'),
        ]);



        $this->deleteAccount($transaction);

        foreach ($items as $item) {

            $detail = JurnalUmum::create([
                'transaction_id'    => $transaction->id,
                'account_id'        => $item['account_id'],
                'name'              => $item['name'],
                'amount'            => $item['type'] == 'debit' ? $item['amount'] : $item['amount_credit'],
                'type'              => $item['type']
            ]);

            if ($item['account_id']) {
                $jurnalUmumDetail = AccountTransaction::create([
                    'account_id'                    => $detail->account_id,
                    'transaction_id'                => $transaction->id,
                    'item_id'                       => $detail->id,
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $detail->amount,
                    'type'                          => $detail->type,
                    'sub_type'                      => 'jurnal',
                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('jurnal', $transaction->transaction_date),
                    'operation_date'                => $transaction->transaction_date,
                    'note'                          => $request->note,
                    'name'                          => $item['name']
                ]);


                $this->ledgerTransactionObserver->logAccountUpdate($jurnalUmumDetail);
                $this->ledgerObserver->updateCashFlowAccount($detail->account);
            }
        }

        return $transaction;
    }

    public function createNoRef($refNo = null)
    {
        if ($refNo == null || $refNo == '') {
            $getTransaction   = Transaction::where("type", "jurnal")->whereDate("created_at", date("Y-m-d"))->count() + 1;
            $invoiceNumber    = sprintf("%05d", $getTransaction);
            $noref            = Helper::transactionKey('JR', $invoiceNumber);

            return $noref;
        } else {
            return $refNo;
        }
    }

    public function deleteAccount(Transaction $transaction)
    {
        foreach ($transaction->jurnal as $detail) {
            $this->deleteItem($detail);
        }

        foreach ($transaction->account_transaction as $transaction) {
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

    public function deleteItem(JurnalUmum $detail)
    {
        $account            = $detail->account;
        $transaction        = AccountTransaction::where("transaction_id", $detail->transaction_id)->where("item_id", $detail->id)->first();

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

    public function deleteData(Transaction $transaction)
    {
        $this->deleteAccount($transaction);
        $transaction->delete();
    }
}
