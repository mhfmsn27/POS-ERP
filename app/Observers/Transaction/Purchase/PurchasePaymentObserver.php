<?php

namespace App\Observers\Transaction\Purchase;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Product\Supplier;
use App\Models\Transaction\FakturPaymentDetail;
use App\Models\Transaction\PaymentMethod;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionPayment;
use App\Models\User;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Transaction\TransactionDueObserver;
use App\Observers\Transaction\TransactionPaymentObserver;
use Illuminate\Http\Request;

class PurchasePaymentObserver
{

    protected $transactionDueObserver;
    protected $transactionPaymentObserver;
    protected $ledgerTransactionObserver;
    protected $ledgerObserver;

    public function __construct(TransactionDueObserver $transactionDueObserver, TransactionPaymentObserver $transactionPaymentObserver, LedgerTransactionObserver $ledgerTransactionObserver, LedgerObserver $ledgerObserver)
    {
        $this->transactionDueObserver       = $transactionDueObserver;
        $this->transactionPaymentObserver   = $transactionPaymentObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
        $this->ledgerObserver               = $ledgerObserver;
    }

    public function getData(Request $request)
    {
        $query = Transaction::with('supplier')->where(function ($query) use ($request) {
            return $request->supplier ? $query->whereIn('supplier_id', explode(",", $request->supplier)) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('transaction_date', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : "";
            }
        })->where(function ($query) use ($request) {
            return $request->ref ? $query->where('ref_no', 'like', '%' . $request->ref . '%')->orWhere('additional_notes', 'like', '%' . $request->ref . '%')->orWhere(function ($q) use ($request) {
                $q->whereHas('supplier', function ($q) use ($request) {
                    return $request->ref ? $q->where('name', 'like', '%' . $request->ref . '%') : '';
                });
            }) : '';
        })->where(function ($query) use ($request) {
            return $request->createdby ?  $query->whereIn('created_by', explode(",", $request->createdby)) : '';
        })->where('type', 'purchase_payment');

        if ($request->sort == 'date') {
            $query->orderBy('transaction_date', $request->sortby);
        } else if ($request->sort == 'ref_no') {
            $query->orderBy('ref_no', $request->sortby);
        } else if ($request->sort == 'supplier.name') {
            $query->orderBy(Supplier::select('name')->whereColumn('suppliers.id', 'transactions.supplier_id'), $request->sortby);
        } else if ($request->sort == 'final_total') {
            $query->orderBy('final_total', $request->sortby);
        } else if ($request->sort == 'created.name') {
            $query->orderBy(User::select('name')->whereColumn('users.id', 'transactions.created_by'), $request->sortby);
        }

        return $query;
    }

    public function createUpdateInformation(Request $request, $condition, Transaction $transaction = null)
    {

        $collection     = collect($request->fakturs);
        $minus          = abs((float)$collection->where("type", "saldo")->sum('total_due'));

        if ($condition == 'create') {
            $getTransaction         = Transaction::where("type", "purchase_payment")->whereDate("created_at", date("Y-m-d"))->count() + 1;
            $invoiceNumber          = sprintf("%05d", $getTransaction);
            $refNo                  = Helper::transactionKey('PPO', $invoiceNumber);

            $data                   = new Transaction();
            $data->invoice_no       = $invoiceNumber;
            $data->ref_no           = $refNo;
            $data->transaction_date = Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s');
        } else {
            $data = Transaction::find($transaction->id);
            $data->transaction_date = Helper::setTimeZoneLocal($request->date) . ' ' . $transaction->created_at->format('H:i:s');
        }

        $data->type                 = 'purchase_payment';
        $data->payment_status       = 'due';
        $data->status               = $request->status;
        $data->supplier_id          = $request->supplier['id'];
        $data->final_total          = ($request->total_payment + $minus);
        $data->method_id            = $request->payment_method == 'cash' ? $request->method['id'] : null;
        $data->created_by           = auth()->user()->id;
        $data->save();

        return $data;
    }

    public function createOrUpdateTransaction(Request $request, Transaction $transaction)
    {

        $totalPay       = $request->total_payment;
        $collection     = collect($request->fakturs);
        $minus          = abs((float)$collection->where("type", "saldo")->sum('total_due'));
        $totalMin       = $minus - ($minus * 2);
        $totalUse       = 0;
        $totalCredit    = 0;
        $totalUseSaldo  = 0;

        if (abs($collection->where("type", "saldo")->sum('total_due')) > 0) {
            $totalPay   = $totalPay + $minus;
        }

        foreach ($collection->where("type", "hutang") as $faktur) {
            $dueDetail      = $this->transactionDueObserver->getById($faktur['id']);
            $fakturDetail   = FakturPaymentDetail::where("id", $faktur['item_id'])->first();

            if ($fakturDetail) {
                foreach ($fakturDetail->payment as $fPayment) {
                    $pDueDetail = $fPayment->due_payment;
                    $this->transactionPaymentObserver->deleteData($fPayment);

                    if ($pDueDetail) {
                        $pDueDetail->update([
                            'total_due_amount'  => $pDueDetail->total_due,
                            'status'            => $pDueDetail->total_due < 1 ? 'paid' : 'due'
                        ]);
                    }
                }
            }


            $allocatedQty   = min($totalPay, $dueDetail->total_due);

            if ($allocatedQty > 0) {
                if ($fakturDetail) {
                    $fakturDetail->update([
                        'transaction_id'        => $transaction->id,
                        'transaction_due_id'    => $dueDetail->id,
                        'pay_amount'            => $allocatedQty,
                    ]);
                } else {
                    $fakturDetail =  FakturPaymentDetail::create([
                        'transaction_id'        => $transaction->id,
                        'transaction_due_id'    => $dueDetail->id,
                        'pay_amount'            => $allocatedQty,
                    ]);
                }

                $this->transactionPaymentObserver->createData($request, $dueDetail, 'transaction', $allocatedQty, $fakturDetail->id);
                $totalUse += $allocatedQty;

                $dueDetail->update([
                    'total_due_amount'  => $dueDetail->total_due,
                    'status'            => $dueDetail->total_due < 1 ? 'paid' : 'due'
                ]);


                if ($dueDetail->transaction) {

                    $dueDetail->transaction->update([
                        'payment_status'        => $dueDetail->transaction->due_total_po < 1 ? 'paid' : 'due'
                    ]);
                }

                $totalPay -= $allocatedQty;
            }

            if ($totalPay <= 0) {
                break;
            }
        }

        foreach ($collection->where("type", "saldo") as $faktur) {
            $dueDetail      = $this->transactionDueObserver->getById($faktur['id']);
            $fakturDetail   = FakturPaymentDetail::where("id", $faktur['item_id'])->first();

            if ($fakturDetail) {
                foreach ($fakturDetail->payment as $fPayment) {
                    $pDueDetail = $fPayment->due_payment;
                    $this->transactionPaymentObserver->deleteData($fPayment);

                    if ($pDueDetail) {
                        $pDueDetail->update([
                            'total_due_amount'  => $pDueDetail->total_due,
                            'status'            => $pDueDetail->total_due < 1 ? 'paid' : 'due'
                        ]);
                    }
                }
            }

            $allocatedQty   = min($minus, $dueDetail->total_due) - (min($minus, $dueDetail->total_due) * 2);

            if ($allocatedQty < 0) {

                if ($faktur['item_id'] != null) {
                    $fakturDetail = FakturPaymentDetail::where("id", $faktur['item_id'])->first();
                    $fakturDetail->update([
                        'transaction_id'        => $transaction->id,
                        'transaction_due_id'    => $dueDetail->id,
                        'pay_amount'            => $allocatedQty,
                    ]);
                } else {
                    $fakturDetail =  FakturPaymentDetail::create([
                        'transaction_id'        => $transaction->id,
                        'transaction_due_id'    => $dueDetail->id,
                        'pay_amount'            => $allocatedQty,
                    ]);
                }

                if ($request->payment_method == 'cash') {
                    $payments       = $this->transactionPaymentObserver->createData($request, $dueDetail, 'transaction', abs($allocatedQty), $fakturDetail->id);
                    $totalUseSaldo   += $allocatedQty;

                    $dueDetail->update([
                        'total_due_amount'  => $dueDetail->total_due,
                        'status'            => $dueDetail->total_due < 1  ? 'paid' : 'paid'
                    ]);
                }

                if ($dueDetail->transaction) {
                    if ($dueDetail->transaction->type == 'purchase_return') {
                        $dueDetail->transaction->transaction->update([
                            'payment_status'        => $dueDetail->transaction->due_total_po < 1 ? 'paid' : 'due'
                        ]);
                    }
                }

                $totalMin -= $allocatedQty;
            }

            if ($totalMin >= 0) {
                break;
            }
        }

        if ($totalPay > 0) {
            $totalCredit    = $totalPay;
        }

        return array(
            'total_use'     => $totalUse,
            'use_saldo'     => $totalUseSaldo,
            'total_credit'  => $totalCredit
        );
    }

    public function deleteItem(FakturPaymentDetail $faktur, Transaction $transaction)
    {

        foreach ($faktur->payment as $fPayment) {
            $dueDetail = $fPayment->due_payment;
            $this->transactionPaymentObserver->deleteData($fPayment);

            if ($dueDetail) {
                $dueDetail->update([
                    'total_due_amount'  => $dueDetail->total_due,
                    'status'            => $dueDetail->total_due < 1 ? 'paid' : 'due'
                ]);
            }
        }

        $faktur->transaction_due->update([
            'status'        => $faktur->transaction_due->total_due < 1  ? 'paid' : 'due'
        ]);

        if ($faktur->transaction_due->transaction) {
            $faktur->transaction_due->transaction->update([
                'payment_status'        => abs($faktur->transaction_due->transaction->due_total_po) < 1 ? 'paid' : 'due'
            ]);

            if ($faktur->transaction_due->transaction->type == 'purchase_return') {
                $faktur->transaction_due->transaction->transaction->update([
                    'payment_status'        => abs($faktur->transaction_due->transaction->transaction->due_total_po) < 1 ? 'paid' : 'due'
                ]);
            }
        }

        $faktur->delete();

        $this->subtotalTransactionChange($transaction);
    }

    public function subtotalTransactionChange(Transaction $transaction)
    {
        $subtotal                           = $transaction->faktur_detail()->sum('pay_amount');

        if ($transaction->final_total > $subtotal) {
            $transaction->update([
                'final_total'           => $subtotal,
                'total_before_tax'      => (int)$subtotal
            ]);
        }
    }

    public function deleteTransaction(Transaction $transaction)
    {
        if ($transaction->transaction_due) {
            $transaction->transaction_due()->delete();
        }

        foreach ($transaction->account_transaction as $accountTransaction) {
            $nextTransaction    = AccountTransaction::where(function ($query) use ($accountTransaction) {
                $query->where("operation_date", ">", $accountTransaction->operation_date)
                    ->orWhere(function ($subQuery) use ($accountTransaction) {
                        $subQuery->where("operation_date", "=", $accountTransaction->operation_date)
                            ->where("id", "<", $accountTransaction->id);
                    });
            })
                ->where("account_id", $accountTransaction->account_id)
                ->orderBy("operation_date", 'asc')
                ->orderBy("id", 'asc')->first();

            $account            = $accountTransaction->account;

            $accountTransaction->delete();

            if ($nextTransaction) {
                $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
            }

            if ($account) {
                $this->ledgerObserver->updateCashFlowAccount($account);
            }
        }

        foreach ($transaction->faktur_detail as $faktur) {
            $this->deleteItem($faktur, $transaction);
        }

        $transaction->forceDelete();
    }

    // New Update 26 April 2024
    public function createByTransaction(Request $request, Transaction $transaction)
    {
        $paymentInformation     = $request->payment_information;

        $getTransaction         = Transaction::where("type", "purchase_payment")->whereDate("created_at", date("Y-m-d"))->count() + 1;
        $invoiceNumber          = sprintf("%05d", $getTransaction);
        $refNo                  = Helper::transactionKey('PPO', $invoiceNumber);

        $data                   = new Transaction();
        $data->invoice_no       = $invoiceNumber;
        $data->ref_no           = $refNo;
        $data->transaction_date = Helper::setTimeZoneLocal($paymentInformation['date']) . ' ' . date('H:i:s');

        $data->type                 = 'purchase_payment';
        $data->payment_status       = 'due';
        $data->status               = 'final';
        $data->supplier_id          = $transaction->supplier_id;
        $data->method_id            = $paymentInformation['method']['id'];
        $data->created_by           = auth()->user()->id;
        $data->save();


        $dueDetail      = $this->transactionDueObserver->getByTransaction($transaction->id);

        $totalPay       = $paymentInformation['pay_total'];
        $allocatedQty   = min($totalPay, $dueDetail->total_due);
        $method         = PaymentMethod::select('id', 'name')->where("id", $paymentInformation['method']['id'])->first();

        $data->update([
            'final_total'   => $allocatedQty
        ]);

        if ($allocatedQty > 0) {
            $fakturDetail =  FakturPaymentDetail::create([
                'transaction_id'        => $data->id,
                'transaction_due_id'    => $dueDetail->id,
                'pay_amount'            => $allocatedQty,
            ]);

            $payments       = TransactionPayment::create([
                'method'                => $method->name,
                'transaction_id'        => $transaction->id,
                'payment_method_id'     => $method->id,
                'amount'                => $allocatedQty,
                'created_by'            => auth()->user()->id,
                'transaction_type'      => 'transaction',
                'account_id'            => $method->account->id ?? null,
                'transaction_due_id'    => $dueDetail->id,
                'date'                  => $paymentInformation['date'],
                'faktur_detail_id'      => $fakturDetail->id,
                'note'                  => "",
            ]);

            $type           = 'credit';
            $subType        = 'pay_supplier_faktur';

            $this->ledgerTransactionObserver->createPaymentFaktur($payments, $type, $subType);

            $dueDetail->update([
                'total_due_amount'  => $dueDetail->total_due,
                'status'            => $dueDetail->total_due < 1 ? 'paid' : 'due'
            ]);

            if ($dueDetail->transaction) {
                $dueDetail->transaction->update([
                    'payment_status'        => $dueDetail->transaction->due_total_po < 1 ? 'paid' : 'due'
                ]);
            }
        }
    }
}
