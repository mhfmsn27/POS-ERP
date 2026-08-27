<?php

namespace App\Observers\Transaction;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Transaction\PaymentMethod;
use App\Models\Transaction\TransactionDue;
use App\Models\Transaction\TransactionPayment;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use Illuminate\Http\Request;

class TransactionPaymentObserver
{

    protected $ledgerObserver;
    protected $ledgerTransactionObserver;

    public function __construct(LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
    }

    public function getData(Request $request)
    {
        return TransactionPayment::where(function ($q) use ($request) {
            return $request->due ? $q->where("transaction_due_id", $request->due) : '';
        })->where(function ($query) use ($request) {
            return $request->transaction ? $query->where('transaction_id', $request->transaction) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('date', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("date", $request->start_date) : "";
            }
        })->orderBy("created_at", "desc");
    }


    public function createData(Request $request, TransactionDue $transactionDue, String $type, Float $amount, $fakturId = null)
    {

        $method     = PaymentMethod::select('id', 'name')->where("id", $request->method['id'])->first();

        return TransactionPayment::create([
            'method'                => $method->name,
            'transaction_id'        => $transactionDue->transaction_id,
            'payment_method_id'     => $method->id,
            'amount'                => $amount,
            'created_by'            => auth()->user()->id,
            'transaction_type'      => $type,
            'account_id'            => $method->account->id ?? null,
            'transaction_due_id'    => $transactionDue->id,
            'date'                  => Helper::setTimeZoneLocal($request->date) . date('H:i:s'),
            'faktur_detail_id'      => $fakturId,
            'note'                  => $request->note,
        ]);
    }

    public function createDataUseSaldo(Request $request, TransactionDue $transactionDue, TransactionDue $saldo, String $type, Float $amount, $fakturId = null)
    {

        if ($type == 'wd_supplier' || $type == 'wd_customer') {
            return TransactionPayment::create([
                'method'                => ($type == 'wd_supplier' ? 'Pembayaran Faktur' : 'Penerimaan Penjualan') . ' - ' . $transactionDue->transaction->ref_no ?? '',
                'transaction_id'        => null,
                'amount'                => $amount,
                'created_by'            => auth()->user()->id,
                'transaction_type'      => 'transaction',
                'transaction_due_id'    => $saldo->id,
                'date'                  => Helper::setTimeZoneLocal($request->date) . date('H:i:s'),
                'faktur_detail_id'      => $fakturId,
                'note'                  => $request->note,
            ]);
        } else {
            return TransactionPayment::create([
                'method'                => 'Penggunaan Saldo - ' . $saldo->no_ref,
                'transaction_id'        => $transactionDue->transaction_id,
                'amount'                => $amount,
                'created_by'            => auth()->user()->id,
                'transaction_type'      => 'transaction',
                'transaction_due_id'    => $transactionDue->id,
                'date'                  => Helper::setTimeZoneLocal($request->date) . date('H:i:s'),
                'faktur_detail_id'      => $fakturId,
                'note'                  => $request->note,
            ]);
        }
    }

    public function updateData(Request $request, TransactionPayment $payment, Float $amount)
    {
        $method     = PaymentMethod::select('id', 'name')->where("id", $request->method['id'])->first();

        $payment->update([
            'method'                => $method->name,
            'payment_method_id'     => $method->id,
            'amount'                => $amount,
            'created_by'            => auth()->user()->id,
            'account_id'            => $method->account->id ?? null,
            'date'                  => Helper::setTimeZoneLocal($request->date) . date('H:i:s'),
            'note'                  => $request->note
        ]);

        if ($payment->due_payment) {
            $payment->due_payment->update([
                'total_due_amount'      => $payment->due_payment->total_due
            ]);
        }

        return $payment;
    }

    public function deleteData(TransactionPayment $payment)
    {
        foreach ($payment->payment_account as $transaction) {
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
            $account            = $transaction->account;

            $transaction->delete();

            if ($nextTransaction) {
                $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
            }

            if ($account) {
                $this->ledgerObserver->updateCashFlowAccount($account);
            }
        }

        $payment->delete();
    }
}
