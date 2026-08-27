<?php

namespace App\Observers\Account;

use App\Helper;
use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Stock\StockAdjusmentDetail;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\RekonsiliasiData;
use App\Models\Transaction\Sell;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionDue;
use App\Models\Transaction\TransactionPayment;
use Illuminate\Http\Request;

class LedgerTransactionObserver
{

    protected $ledgerObserver;

    public function __construct(LedgerObserver $ledgerObserver)
    {
        $this->ledgerObserver       = $ledgerObserver;
    }

    public function getData(Request $request, $orderBy = 'desc')
    {
        return AccountTransaction::where(function ($q) use ($request) {
            return $request->account ? $q->where("account_id", $request->account) : '';
        })->where(function ($q) use ($request) {
            if ($request->end && $request->start) {
                return $q->whereBetween('operation_date', [$request->start, $request->end]);
            } else {
                return $request->start ? $q->whereDate("operation_date", $request->start) : "";
            }
        })->where(function ($q) use ($request) {
            return $request->after_rekonsiliasi ? $q->where("after_rekonsiliasi", $request->after_rekonsiliasi) : '';
        })->orderBy("operation_date", $orderBy)->orderBy('id', 'desc');
    }

    public function getRekonsiliasi(Request $request)
    {
        return RekonsiliasiData::where(function($q) use ($request) {
            return $request->name ?  $q->where('note', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->account ? $q->where("account_id", $request->account) : '';
        })->where(function ($q) use ($request) {
            if ($request->end && $request->start) {
                return $q->whereBetween('date', [$request->start, $request->end]);
            } else {
                return $request->start ? $q->whereDate("date", $request->start) : "";
            }
        })->orderBy('date', 'asc');
    }

    public function depositAccount(Request $request, Account $account)
    {
        $date       = Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s');
        $depositTransaction = AccountTransaction::create([
            'account_id'                    => $account->id,
            'created_by'                    => auth()->user()->id,
            'amount'                        => $request->amount,
            'type'                          => 'debit',
            'sub_type'                      => 'deposit',
            'ref_no'                        => $this->generateRefNo('deposit', $date),
            'operation_date'                => $date,
            'note'                          => $request->note,
            'name'                          => $request->name ?? 'Deposit Modal ' . $account->name
        ]);

        $this->ledgerObserver->updateCashFlowAccount($account);
        $this->creditCapital($depositTransaction);
        $this->logAccountTransaction($depositTransaction);
    }

    public function creditCapital(AccountTransaction $accountTransaction, $type = 'deposit_equitas', $typeData = 'credit')
    {
        $accountCapital         = Account::where("default_data", "modal")->first();

        if (!$accountCapital) {
            throw new \Exception('Gagal Deposit, Silahkan buat Akuntansi untuk menampung Credit Equitas Modal');
        }

        $transaction = AccountTransaction::create([
            'account_id'                    => $accountCapital->id,
            'created_by'                    => auth()->user()->id,
            'amount'                        => $accountTransaction->amount,
            'account_transaction_id'        => $accountTransaction->id,
            'sub_type'                      => $type,
            'type'                          => $typeData,
            'ref_no'                        => $this->generateRefNo('deposit_equitas', $accountTransaction->operation_date),
            'operation_date'                => $accountTransaction->operation_date,
            'name'                          => $accountTransaction->name
        ]);

        $this->ledgerObserver->updateCashFlowAccount($accountCapital);
        $this->logAccountTransaction($transaction);
    }

    public function transferSaldo(Request $request, Account $account)
    {

        $date                   = Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s');
        $toAccount              = Account::where("id", $request->account['id'])->first();
        $transferTransaction    = AccountTransaction::create([
            'account_id'                    => $account->id,
            'created_by'                    => auth()->user()->id,
            'amount'                        => $request->amount,
            'type'                          => 'credit',
            'sub_type'                      => 'transfer_dana',
            'ref_no'                        => $this->generateRefNo('transfer_dana', $date),
            'operation_date'                => $date,
            'note'                          => $request->note,
            'name'                          => $request->name ?? 'Transfer Saldo Ke ' . $toAccount->name
        ]);

        $this->ledgerObserver->updateCashFlowAccount($account);
        $this->receivedSaldo($toAccount, $transferTransaction);
        $this->logAccountTransaction($transferTransaction);
    }

    public function receivedSaldo(Account $account, AccountTransaction $transaction)
    {
        $transaction = AccountTransaction::create([
            'account_id'                    => $account->id,
            'created_by'                    => auth()->user()->id,
            'amount'                        => $transaction->amount,
            'type'                          => 'debit',
            'sub_type'                      => 'received_dana',
            'operation_date'                => $transaction->operation_date,
            'note'                          => $transaction->note,
            'ref_no'                        => $this->generateRefNo('received_dana', $transaction->operation_date ?? now()),
            'name'                          => 'Terima Saldo Dari ' . $transaction->account->name,
            'transaction_transfer_id'       => $transaction->id
        ]);

        $this->ledgerObserver->updateCashFlowAccount($account);
        $this->logAccountTransaction($transaction);
    }

    public function generateRefNo($type, $date)
    {
        $getTransaction         = AccountTransaction::where("sub_type", $type)->whereDate("operation_date", substr($date, 0, 10))->count() + 1;
        $invoiceNumber          = sprintf("%05d", $getTransaction);
        $refNo                  = Helper::accountKey($type, $invoiceNumber);

        return $refNo;
    }

    public function logAccountTransaction(AccountTransaction $transaction)
    {
        $transactions   = $this->after_transaction($transaction->operation_date, $transaction->account_id);

        if (count($transactions) > 0) {
            $this->logAccountUpdate($transaction);
        } else {
            $transaction->update([
                'cashflow'          => $transaction->account->cashflow ?? 0
            ]);
        }
    }

    public function logAccountUpdate(AccountTransaction $transaction)
    {

        if ($transaction->type == 'debit') {
            $lastLogs = $transaction->cash_flow_position + $transaction->amount;
        } else {
            $lastLogs = $transaction->cash_flow_position - $transaction->amount;
        }

        $transaction->update([
            'cashflow'                => $lastLogs
        ]);

        $transactions   = $this->after_transaction($transaction->operation_date, $transaction->account_id);



        if (count($transactions) > 0) {
            foreach ($transactions as $flow) {

                // if ($flow->type == 'debit') {
                //     $lastLogs       = ($flow->cashflow - $flow->cashflow) + ($lastLogs + $flow->amount);
                // } else {
                //     $lastLogs       =  ($lastLogs - $flow->amount) - ($flow->cashflow - $flow->cashflow);
                // }

                if ($flow->type == 'debit') {
                    $lastLogs += $flow->amount;
                } else {
                    $lastLogs -= $flow->amount;
                }


                $flow->update([
                    'cashflow'                => $lastLogs
                ]);
            }
        }

        if ($transaction->account) {
            $this->ledgerObserver->updateCashFlowAccount($transaction->account);
        }
    }

    public function after_transaction($date, $account)
    {
        return AccountTransaction::where('account_id', $account)->where('operation_date', ">", $date)->orderBy('operation_date', 'asc')->get(['id', 'cashflow', 'operation_date', 'amount', 'type']);
    }

    public function after_transaction_count($date, $account)
    {
        return AccountTransaction::where('account_id', $account)->where('operation_date', ">", $date)->count();
    }

    public function createDepositProduct(Purchase $purchase, $type = 'first_stock')
    {

        if ($purchase->product->supply_account) {

            $productName    = $purchase->product->name ?? '';
            $varName        = $purchase->variation->name ?? '';
            $name           = $type == 'first_stock' ? 'Saldo Awal Barang ' : 'Penyesuaian Barang ';

            if ($varName == 'no-name') {
                $varName    = '';
            }

            $depositTransaction = AccountTransaction::create([
                'account_id'                    => $purchase->product->supply_account->id,
                'transaction_id'                => $purchase->transaction_id ?? $purchase->transaction_received_id,
                'created_by'                    => auth()->user()->id,
                'item_id'                       => $purchase->id,
                'amount'                        => $purchase->subtotal,
                'type'                          => 'debit',
                'sub_type'                      => $type,
                'ref_no'                        => $this->generateRefNo($type, ($purchase->transaction->transaction_date ?? $purchase->transaction_received->transaction_date)),
                'operation_date'                => ($purchase->transaction->transaction_date ?? $purchase->transaction_received->transaction_date),
                'name'                          => $name . $productName . ' ' . $varName
            ]);

            $this->ledgerObserver->updateCashFlowAccount($purchase->product->supply_account);
            $this->logAccountTransaction($depositTransaction);

            return $depositTransaction;
        }

        return null;
    }

    public function productStockOpname(StockAdjusmentDetail $stock,  $type = 'add_stock')
    {

        if ($type == 'add_stock') {
            $typeCash   = 'debit';
            $title      = 'Penambahan Stok ( Stok Opname )';
        } else if ($type == 'min_stock') {
            $typeCash   = 'credit';
            $title      = 'Pengurangan Stok ( Stok Opname )';
        }

        $productName    = $stock->product->name ?? '';
        $varName        = $stock->variation->name ?? '';

        if ($varName == 'no-name') {
            $varName    = '';
        }

        $depositTransaction = AccountTransaction::create([
            'account_id'                    => $stock->product->supply_account->id,
            'transaction_id'                => $stock->transaction_id,
            'created_by'                    => auth()->user()->id,
            'item_id'                       => $stock->id,
            'amount'                        => $stock->variation->modal_price * $stock->qty_adjustment,
            'type'                          => $typeCash,
            'sub_type'                      => $type,
            'ref_no'                        => $this->generateRefNo($type, $stock->transaction->transaction_date),
            'operation_date'                => $stock->transaction->transaction_date,
            'name'                          => $title . ' ' . $productName . ' ' . $varName
        ]);

        $this->ledgerObserver->updateCashFlowAccount($stock->product->supply_account);
        $this->logAccountTransaction($depositTransaction);
    }

    public function addSupplyAccount(Purchase $purchase)
    {
        if ($purchase->publish == 'not_use') {
            $name = $purchase->transaction_received->supplier->name ?? '';
        } else {
            $name    = $purchase->transaction->supplier->name ?? '';
        }

        if ($purchase->product->supply_account) {

            $depositTransaction = AccountTransaction::create([
                'account_id'                    => $purchase->product->supply_account->id,
                'transaction_id'                => $purchase->transaction_id ?? $purchase->transaction_received_id,
                'item_id'                       => $purchase->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $purchase->modal_price,
                'type'                          => 'debit',
                'sub_type'                      => 'received_product_from_supplier',
                'ref_no'                        => $this->generateRefNo('received_product_from_supplier', $purchase->created_at),
                'operation_date'                => ($purchase->transaction->transaction_date ?? $purchase->transaction_received->transaction_date),
                'name'                          => 'Penerimaan Barang Dari - ' . $name
            ]);

            $this->ledgerObserver->updateCashFlowAccount($purchase->product->supply_account);
            $this->logAccountTransaction($depositTransaction);
        }
    }

    public function updateSupplyAccount(Transaction $transaction, Purchase $purchase)
    {
        $name           = $transaction->supplier->name ?? '';

        if ($purchase->product->supply_account) {

            $supplyTransaction = AccountTransaction::where("transaction_id", $transaction->id)->where("item_id", $purchase->id)->where("account_id", $purchase->product->supply_account->id)->first();

            if ($supplyTransaction) {
                $supplyTransaction->update([
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $purchase->modal_price,
                    'item_id'                       => $purchase->id,
                    'name'                          => 'Penerimaan Barang Dari - ' . $name
                ]);

                $this->logAccountUpdate($supplyTransaction);
                $this->ledgerObserver->updateCashFlowAccount($purchase->product->supply_account);
            } else {
                $this->addSupplyAccount($purchase);
            }
        }
    }

    public function deleteSupplyAccount(Purchase $purchase)
    {

        $supplyTransaction  = AccountTransaction::where("transaction_id", $purchase->transaction->id)->where("item_id", $purchase->id)->where("account_id", $purchase->product->supply_account->id)->first();

        if ($supplyTransaction) {
            $supplyTransaction->forceDelete();
            $this->ledgerObserver->updateCashFlowAccount($purchase->product->supply_account);
        }
    }

    public function addShippingAccount(Sell $sell, $endStock = 0)
    {

        if ($sell->transaction_id != null) {
            $name       = $sell->transaction->customer->name ?? '';
        } else {
            $name       = $sell->transaction_shipping->customer->name ?? '';
        }

        if ($sell->product->supply_account) {
            $depositTransaction = AccountTransaction::create([
                'account_id'                    => $sell->product->supply_account->id,
                'transaction_id'                => $sell->transaction_id ?? $sell->transaction_received_id,
                'item_id'                       => $sell->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => (float)sell_purchase_total($sell->id),
                'type'                          => 'credit',
                'sub_type'                      => 'sent_product_to_customer',
                'ref_no'                        => $this->generateRefNo('sent_product_to_customer', ($sell->transaction->transaction_date ?? $sell->transaction_shipping->transaction_date)),
                'operation_date'                => ($sell->transaction->transaction_date ?? $sell->transaction_shipping->transaction_date),
                'name'                          => 'Pengiriman Ke ' . $name . ' - ( ' . $sell->product->name . ' ' . (float)$sell->qty . ' )'
            ]);

            $this->ledgerObserver->updateCashFlowAccount($sell->product->supply_account);
            $this->logAccountTransaction($depositTransaction);
        }
    }

    public function updateShippingAccount(Transaction $transaction, Sell $sell, $endStock = 0)
    {

        if ($sell->product->supply_account) {

            $supplyTransaction = AccountTransaction::where("type", "credit")->where("sub_type", "sent_product_to_customer")->where("item_id", $sell->id)->where("account_id", $sell->product->supply_account->id)->first();

            if ($supplyTransaction) {
                $supplyTransaction->update([
                    'operation_date'                => $transaction->transaction_date,
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => (float)sell_purchase_total($sell->id),
                    'item_id'                       => $sell->id,
                    'qty_history'                   => $endStock < 0 ?  (abs($endStock) > $sell->qty ? ($sell->qty - ($sell->qty * 2)) : $endStock) : 0,
                ]);

                $this->logAccountUpdate($supplyTransaction);
                $this->ledgerObserver->updateCashFlowAccount($sell->product->supply_account);
            }
        }
    }

    public function deleteShippingAccount(Sell $sell)
    {

        $supplyTransaction  = AccountTransaction::where("transaction_id", $sell->transaction->id)->where("item_id", $sell->id)->where("account_id", $sell->product->supply_account->id ?? null)->first();
        $sentTransaction    = AccountTransaction::where("transaction_id", $sell->transaction->id)->where("item_id", $sell->id)->where("account_id", $sell->product->sent_account->id ?? null)->first();

        if ($supplyTransaction) {
            $supplyTransaction->forceDelete();
            $this->ledgerObserver->updateCashFlowAccount($sell->product->supply_account);
        }

        if ($sentTransaction) {
            $supplyTransaction->forceDelete();
            $this->ledgerObserver->updateCashFlowAccount($sell->product->sent_account);
        }
    }



    public function createDueSupplier(TransactionDue $transaction)
    {

        $name       = $transaction->supplier->name ?? '';
        $title      = $transaction->type == 'hutang' ? 'Utang Supplier - ' : 'Penyimpanan Saldo Supplier -';
        $account    = null;

        if ($transaction->type == 'hutang') {
            $account = $transaction->supplier->debt_account ?? null;
        } else {
            $account = $transaction->supplier->debt_imprest_account ?? null;
        }

        if ($account) {
            $depositTransaction = AccountTransaction::create([
                'account_id'                    => $account->id,
                'transaction_id'                => $transaction->transaction_id,
                'transaction_due_id'            => $transaction->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $transaction->amount,
                'type'                          => $transaction->type == 'hutang' ? 'credit' : 'debit',
                'sub_type'                      => $transaction->type == 'hutang' ? 'due_supplier' : 'saldo_supplier',
                'ref_no'                        => $this->generateRefNo($transaction->type == 'hutang' ? 'due_supplier' : 'saldo_supplier', $transaction->date),
                'operation_date'                => $transaction->date,
                'name'                          => $title . " " . $name
            ]);

            $this->ledgerObserver->updateCashFlowAccount($account);
            $this->logAccountTransaction($depositTransaction);

            // if ($transaction->type != 'hutang') {
            //     $accountTwo = $transaction->supplier->debt_account ?? null;
            //     $doubleInsert = AccountTransaction::create([
            //         'account_id'                    => $accountTwo->id,
            //         'transaction_id'                => $transaction->transaction_id,
            //         'account_transaction_id'        => $depositTransaction->id,
            //         'created_by'                    => auth()->user()->id,
            //         'amount'                        => $transaction->amount,
            //         'type'                          => 'credit',
            //         'sub_type'                      => 'saldo_supplier',
            //         'ref_no'                        => $this->generateRefNo($transaction->type == 'hutang' ? 'due_supplier' : 'saldo_supplier', $transaction->created_at),
            //         'operation_date'                => $transaction->transaction_date,
            //         'name'                          => $title . " " . $name
            //     ]);

            //     $this->ledgerObserver->updateCashFlowAccount($accountTwo);
            //     $this->logAccountTransaction($doubleInsert);
            // }
        }
    }

    public function updateDueSupplier(TransactionDue $transaction)
    {

        $account    = null;
        if ($transaction->type == 'hutang') {
            $account = $transaction->supplier->debt_account ?? null;
        } else {
            $account = $transaction->supplier->debt_imprest_account ?? null;
        }

        if ($account) {
            $accountTransaction = AccountTransaction::where("transaction_due_id", $transaction->id)->where("account_id", $account->id)->first();

            if ($accountTransaction) {
                $accountTransaction->update([
                    'amount'                        => $transaction->amount,
                ]);

                $this->logAccountUpdate($accountTransaction);
                $this->ledgerObserver->updateCashFlowAccount($accountTransaction->account);
            }
        }
    }

    public function updateDueCustomer(TransactionDue $transaction)
    {

        $account    = null;
        if ($transaction->type == 'hutang') {
            $account = $transaction->customer->debt_account ?? null;
        } else {
            $account = $transaction->customer->debt_imprest_account ?? null;
        }

        if ($account) {
            $accountTransaction = AccountTransaction::where("transaction_due_id", $transaction->id)->where("account_id", $account->id)->first();

            if ($accountTransaction) {
                $accountTransaction->update([
                    'operation_date'                => $transaction->date,
                    'amount'                        => $transaction->amount,
                ]);

                $this->logAccountUpdate($accountTransaction);
                $this->ledgerObserver->updateCashFlowAccount($accountTransaction->account);
            }
        }
    }

    public function createDueCustomer(TransactionDue $transaction)
    {

        $name       = $transaction->customer->name ?? '';
        $title      = $transaction->type == 'hutang' ? 'Utang Customer - ' : 'Deposit Saldo Customer -';
        $account    = null;

        if ($transaction->type == 'hutang') {
            $account = $transaction->customer->debt_account ?? null;
        } else {
            $account = $transaction->customer->debt_imprest_account ?? null;
        }

        if ($account) {
            $depositTransaction = AccountTransaction::create([
                'account_id'                    => $account->id,
                'transaction_due_id'            => $transaction->id,
                'transaction_id'                => $transaction->transaction_id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $transaction->amount,
                'type'                          => $transaction->type == 'hutang' ? 'debit' : 'credit',
                'sub_type'                      => $transaction->type == 'hutang' ? 'due_customer' : 'saldo_customer',
                'ref_no'                        => $this->generateRefNo($transaction->type == 'hutang' ? 'due_customer' : 'saldo_customer', $transaction->date),
                'operation_date'                => $transaction->date,
                'name'                          => $title . " " . $name
            ]);

            $this->ledgerObserver->updateCashFlowAccount($account);
            $this->logAccountTransaction($depositTransaction);
        }
    }

    public function createPaymentFaktur(TransactionPayment $payment, String $type, String $subType)
    {


        $account    = $payment->payment_method->account ?? null;

        $title      = '';

        if ($subType == 'pay_supplier_faktur') {
            $title  = 'Pembayaran Faktur - ';
        } else if ($subType == 'wd_supplier') {
            $title  = 'Penggunaan Saldo - ';
        } else if ($subType == 'pay_customer_faktur') {
            $title  = 'Penerimaan Penjualan - ';
        } else if ($subType == 'wd_customer') {
            $title  = 'Penggunaan Saldo - ';
        }

        if ($account) {
            $name    = '';

            $depositTransaction = AccountTransaction::create([
                'account_id'                    => $account->id,
                'transaction_due_id'            => $payment->transaction_due_id,
                'transaction_id'                => $payment->transaction_id,
                'created_by'                    => auth()->user()->id,
                'transaction_payment_id'        => $payment->id,
                'amount'                        => $payment->amount,
                'type'                          => $type,
                'sub_type'                      => $subType,
                'ref_no'                        => $this->generateRefNo($subType, $payment->date),
                'operation_date'                => $payment->date,
                'name'                          => $title . " " . $name
            ]);


            $this->ledgerObserver->updateCashFlowAccount($account);
            $this->logAccountTransaction($depositTransaction);
            $this->doubleEntryUtangPiutang($depositTransaction, $depositTransaction->sub_type);
        }
    }

    public function doubleEntryUtangPiutang(AccountTransaction $transaction, String $subject)
    {

        if ($subject == 'pay_supplier_faktur') {
            $account    = $transaction->transaction_due->supplier->debt_account ?? null;
            $type       = 'debit';
        } else if ($subject == 'wd_supplier') {
            $account    = $transaction->transaction_due->supplier->debt_imprest_account ?? null;
            $type       = 'credit';
        } else if ($subject == 'pay_customer_faktur') {
            $type       = 'credit';
            $account    = $transaction->transaction_due->customer->debt_account ?? null;
        } else if ($subject == 'wd_customer') {
            $type       = 'debit';
            $account    = $transaction->transaction_due->customer->debt_imprest_account ?? null;
        }

        if ($transaction->transaction_due) {
            if ($transaction->transaction_due->type == 'saldo' && $subject == 'pay_customer_faktur') {
                $type          = 'debit';
                $account       = $transaction->transaction_due->customer->debt_imprest_account ?? null;
            }

            if ($transaction->transaction_due->type == 'saldo' && $subject == 'pay_supplier_faktur') {
                $account    = $transaction->transaction_due->supplier->debt_imprest_account ?? null;
                $type       = 'credit';
            }
        }


        if ($account) {
            $transaction = AccountTransaction::create([
                'account_id'                    => $account->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $transaction->amount,
                'account_transaction_id'        => $transaction->id,
                'sub_type'                      => $transaction->sub_type,
                'transaction_payment_id'        => $transaction->transaction_payment_id,
                'type'                          => $type,
                'ref_no'                        => $this->generateRefNo($transaction->sub_type, $transaction->operation_date),
                'operation_date'                => $transaction->operation_date,
                'name'                          => $transaction->name
            ]);

            $this->ledgerObserver->updateCashFlowAccount($account);
            $this->logAccountTransaction($transaction);
        }
    }

    public function updatePaymentFaktur(TransactionPayment $payment, String $type, String $subType, String $subject)
    {

        $account    = $payment->payment_method->account ?? null;
        if ($payment->payment_account()->count() == 0 && $account != null) {
            $this->createPaymentFaktur($payment, $type, $subType);
        } else {
            foreach ($payment->payment_account as $transaction) {

                $transaction->update([
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $payment->amount
                ]);

                $this->logAccountUpdate($transaction);
                $this->ledgerObserver->updateCashFlowAccount($account);

                foreach ($transaction->account_transaction as $aTransaction) {

                    $aTransaction->update([
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $payment->amount
                    ]);

                    $this->logAccountUpdate($aTransaction);
                    $this->ledgerObserver->updateCashFlowAccount($aTransaction->account);
                }
            }
        }
    }

    public function createSupplierDue(Purchase $purchase, Account $account)
    {
        if ($purchase->publish == 'not_use') {
            $name = $purchase->transaction_received->supplier->name ?? '';
        } else {
            $name    = $purchase->transaction->supplier->name ?? '';
        }


        $transaction = AccountTransaction::create([
            'account_id'                    => $account->id,
            'transaction_id'                => $purchase->transaction_id ?? $purchase->transaction_received_id,
            'created_by'                    => auth()->user()->id,
            'amount'                        => $purchase->subtotal,
            'item_id'                       => $purchase->id,
            'type'                          => 'credit',
            'sub_type'                      => 'received_product_from_supplier',
            'ref_no'                        => $this->generateRefNo('received_product_from_supplier', ($purchase->transaction->transaction_date ?? $purchase->transaction_received->transaction_date)),
            'operation_date'                => ($purchase->transaction->transaction_date ?? $purchase->transaction_received->transaction_date),
            'name'                          => 'Penerimaan Barang Dari - ' . $name
        ]);


        $this->ledgerObserver->updateCashFlowAccount($account);
        $this->logAccountTransaction($transaction);

        if ($purchase->product->supply_account) {

            $depositTransaction = AccountTransaction::create([
                'account_id'                    => $purchase->product->supply_account->id,
                'transaction_id'                => $purchase->transaction_id ?? $purchase->transaction_received_id,
                'item_id'                       => $purchase->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $purchase->subtotal,
                'type'                          => 'debit',
                'sub_type'                      => 'received_product_from_supplier',
                'ref_no'                        => $this->generateRefNo('received_product_from_supplier', ($purchase->transaction->transaction_date ?? $purchase->transaction_received->transaction_date)),
                'operation_date'                => ($purchase->transaction->transaction_date ?? $purchase->transaction_received->transaction_date),
                'name'                          => 'Penerimaan Barang Dari - ' . $name
            ]);

            $this->ledgerObserver->updateCashFlowAccount($purchase->product->supply_account);
            $this->logAccountTransaction($depositTransaction);
        }
    }

    public function updateSupplierDue(Transaction $transaction, Purchase $purchase, Account $account)
    {
        $name           = $transaction->supplier->name ?? '';
        $transactions   = AccountTransaction::where("transaction_id", $transaction->id)->where("item_id", $purchase->id)->where("account_id", $account->id)->first();

        if ($transactions) {
            $transactions->update([
                'created_by'                    => auth()->user()->id,
                'amount'                        => $purchase->subtotal,
                'item_id'                       => $purchase->id,
                'name'                          => 'Penerimaan Barang Dari - ' . $name
            ]);


            $this->logAccountUpdate($transactions);
            $this->ledgerObserver->updateCashFlowAccount($account);
        }


        if ($purchase->product->supply_account) {

            $supplyTransaction = AccountTransaction::where("transaction_id", $transaction->id)->where("item_id", $purchase->id)->where("account_id", $purchase->product->supply_account->id)->first();

            if ($supplyTransaction) {
                $supplyTransaction->update([
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $purchase->subtotal,
                    'item_id'                       => $purchase->id,
                    'name'                          => 'Penerimaan Barang Dari - ' . $name
                ]);

                $this->logAccountUpdate($supplyTransaction);
                $this->ledgerObserver->updateCashFlowAccount($purchase->product->supply_account);
            }
        }
    }

    public function deleteSupplierDue(Transaction $transaction, Purchase $purchase, Account $account)
    {
        $transactions       = AccountTransaction::where("transaction_id", $transaction->id)->where("item_id", $purchase->id)->where("account_id", $account->id)->first();
        $supplyTransaction  = AccountTransaction::where("transaction_id", $transaction->id)->where("item_id", $purchase->id)->where("account_id", $purchase->product->supply_account->id)->first();

        if ($transaction) {
            $transactions->delete();

            $this->ledgerObserver->updateCashFlowAccount($account);
        }

        if ($supplyTransaction) {
            $supplyTransaction->delete();
            $this->ledgerObserver->updateCashFlowAccount($purchase->product->supply_account);
        }
    }

    public function createCustomerDue(Sell $sell, Account $account)
    {

        $name   = $sell->transaction->customer->name ?? '';

        $transaction = AccountTransaction::create([
            'account_id'                    => $account->id,
            'transaction_id'                => $sell->transaction_id,
            'created_by'                    => auth()->user()->id,
            'amount'                        => $sell->subtotal,
            'item_id'                       => $sell->id,
            'type'                          => 'debit',
            'sub_type'                      => 'sent_product_to_customer',
            'ref_no'                        => $this->generateRefNo('sent_product_to_customer', ($sell->transaction->transaction_date ?? $sell->transaction_shipping->transaction_date)),
            'operation_date'                => ($sell->transaction->transaction_date ?? $sell->transaction_shipping->transaction_date),
            'name'                          => 'Penjualan Produk Ke - ' . $name
        ]);

        $this->ledgerObserver->updateCashFlowAccount($account);
        $this->logAccountTransaction($transaction);


        if ($sell->product->supply_account) {

            $depositTransaction = AccountTransaction::create([
                'account_id'                    => $sell->product->supply_account->id,
                'transaction_id'                => $sell->transaction_id,
                'item_id'                       => $sell->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $sell->subtotal,
                'type'                          => 'credit',
                'sub_type'                      => 'sent_product_to_customer',
                'ref_no'                        => $this->generateRefNo('sent_product_to_customer', ($sell->transaction->transaction_date ?? $sell->transaction_shipping->transaction_date)),
                'operation_date'                => ($sell->transaction->transaction_date ?? $sell->transaction_shipping->transaction_date),
                'name'                          => 'Penjualan Produk Ke - ' . $name
            ]);

            $this->ledgerObserver->updateCashFlowAccount($sell->product->supply_account);
            $this->logAccountTransaction($depositTransaction);
        }
    }

    public function updateCustomerDue(Transaction $transaction, Sell $sell, Account $account)
    {
        $name           = $transaction->customer->name ?? '';
        $transactions   = AccountTransaction::where("transaction_id", $transaction->id)->where("item_id", $sell->id)->where("account_id", $account->id)->first();

        if ($transaction) {
            $transaction->update([
                'created_by'                    => auth()->user()->id,
                'amount'                        => $sell->subtotal,
                'name'                          => 'Pengiriman Produk Ke - ' . $name
            ]);

            $this->logAccountUpdate($transactions);
            $this->ledgerObserver->updateCashFlowAccount($account);
        }


        if ($sell->product->supply_account) {

            $supplyTransaction = AccountTransaction::where("transaction_id", $transaction->id)->where("item_id", $sell->id)->where("account_id", $sell->product->supply_account->id)->first();

            if ($supplyTransaction) {
                $supplyTransaction->update([
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $sell->subtotal,
                    'name'                          => 'Pengiriman Produk Ke  - ' . $name
                ]);

                $this->logAccountUpdate($supplyTransaction);
                $this->ledgerObserver->updateCashFlowAccount($sell->product->supply_account);
            }
        }
    }

    public function deleteCustomerDue(Transaction $transaction, Sell $sell, Account $account)
    {
        $transactions       = AccountTransaction::where("transaction_id", $transaction->id)->where("item_id", $sell->id)->where("account_id", $account->id)->first();
        $supplyTransaction  = AccountTransaction::where("transaction_id", $transaction->id)->where("item_id", $sell->id)->where("account_id", $sell->product->supply_account->id)->first();

        if ($transaction) {
            $transactions->delete();
            $this->ledgerObserver->updateCashFlowAccount($account);
        }

        if ($supplyTransaction) {
            $supplyTransaction->delete();
            $this->ledgerObserver->updateCashFlowAccount($sell->product->supply_account);
        }
    }

    public function updateJournalCogs(Purchase $purchase, String $type = 'add')
    {

        $accountTransaction = AccountTransaction::whereHas('sell', function ($q) use ($purchase) {
            return $q->where("variation_id", $purchase->variation_id);
        })->where(function ($q) use ($type) {
            return $type == 'add' ? $q->where("qty_history", "<", 0) : '';
        })->orderBy("id", "desc")->get();

        $totalQty   = $purchase->quantity;

        foreach ($accountTransaction as $adjust) {

            $adjustAccount      = $adjust->account;
            $qtySell            = $type == 'add' ? abs($adjust->qty_history) : $adjust->sell->qty;
            $totalReadyQty      = min($totalQty, $qtySell);
            $priceNow           = $purchase->variation->modal_price;

            $priceOld           = $adjust->amount > 0 ? $adjust->amount / $adjust->sell->qty  : 0;
            $forMinus           = $priceOld * ($adjust->sell->qty - abs($adjust->qty_history));
            $newAdd             = abs($adjust->qty_history) * $priceNow;

            $adjust->update([
                'amount'            => $forMinus + $newAdd,
                'qty_history'       => $adjust->qty_history + $totalReadyQty
            ]);


            $this->logAccountUpdate($adjust);
            $this->ledgerObserver->updateCashFlowAccount($adjustAccount);


            $totalQty -= $totalReadyQty;

            if ($type != 'add') {
                if ($totalQty >= 0) {
                    break;
                }
            }
        }
    }
}
