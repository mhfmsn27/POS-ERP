<?php

namespace App\Observers\Hrm;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Models\Hrm\Employee;
use App\Models\Hrm\EmployeeKasbon;
use App\Models\Transaction\PaymentMethod;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use Illuminate\Http\Request;

class KasbonObserver
{

    protected $ledgerObserver;
    protected $ledgerTransactionObserver;

    public function __construct(LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
    }

    public function getData(Request $request, String $type = '', $year = '', $month = '')
    {
        return EmployeeKasbon::with('employee')->where(function ($query) use ($request) {
            return $request->employee ? $query->where('employee_id', $request->employee) : '';
        })->where(function ($query) use ($request) {
            return $request->method ?  $query->where('method_id', $request->method) : '';
        })->whereHas('employee.user', function ($query) use ($request) {
            return $request->name ?  $query->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : "";
            }
        })->where(function ($q) use ($type) {
            return $type != '' ? $q->where("type", $type) : '';
        })->where(function ($q) use ($year) {
            return $year != '' ? $q->whereYear("created_at", $year) : '';
        })->where(function ($q) use ($month) {
            return $month != '' ? $q->whereMonth("created_at", $month) : '';
        })->orderBy("created_at", "desc");
    }

    public function createData(Request $request, Employee $employee)
    {

        $methodDetail   = PaymentMethod::find($request->method['id']);
        $settings       = AccountSetting::first(['kasbon']);

        $kasbon         = EmployeeKasbon::create([
            'employee_id'       => $employee->id,
            'method_id'         => $request->method['id'],
            'amount'            => $request->amount,
            'note'              => $request->note,
            'type'              => $request->type,
            'created_at'        => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s')
        ]);


        if ($methodDetail->account) {

            $nameHistory    = $request->type == 'int' ? 'Pembayaran Kasbon - ' : 'Kasbon Pegawai -';
            $nameHistory    = $nameHistory . $employee->user->name ?? '';

            $methodAccount = AccountTransaction::create([
                'account_id'                    => $methodDetail->account->id,
                'kasbon_id'                     => $kasbon->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $kasbon->amount,
                'type'                          => $kasbon->type == 'int' ? 'debit' : 'credit',
                'sub_type'                      => 'kasbon',
                'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('kasbon', $kasbon->created_at->format("Y-m-d") ?? now()),
                'operation_date'                => $kasbon->created_at->format("Y-m-d") ?? now(),
                'name'                          => $nameHistory
            ]);

            $this->ledgerObserver->updateCashFlowAccount($methodDetail->account);
            $this->ledgerTransactionObserver->logAccountTransaction($methodAccount);
        }

        if ($settings) {
            if ($settings->kasbon_account) {
                $kasbonAccount      = AccountTransaction::create([
                    'account_id'                    => $settings->kasbon,
                    'kasbon_id'                     => $kasbon->id,
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $kasbon->amount,
                    'type'                          => $kasbon->type == 'int' ? 'credit' : 'debit',
                    'sub_type'                      => 'kasbon',
                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('kasbon', $kasbon->created_at->format("Y-m-d") ?? now()),
                    'operation_date'                => $kasbon->created_at->format("Y-m-d") ?? now(),
                    'name'                          => $nameHistory
                ]);

                $this->ledgerObserver->updateCashFlowAccount($settings->kasbon_account);
                $this->ledgerTransactionObserver->logAccountTransaction($kasbonAccount);
            }
        }
    }

    public function updateData(Request $request, EmployeeKasbon $kasbon, Employee $employee)
    {

        $methodDetail   = PaymentMethod::find($request->method['id']);
        $settings       = AccountSetting::first(['kasbon']);

        $kasbon->update([
            'employee_id'       => $employee->id,
            'method_id'         => $request->method['id'],
            'amount'            => $request->amount,
            'note'              => $request->note,
            'created_at'        => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s')
        ]);

        // $this->deleteAccount($kasbon);

        foreach ($kasbon->transaction as $transaction) {

            if ($transaction->type == 'credit') {

                $transaction->update([
                    'account_id'            => $methodDetail->account_id,
                    'amount'                => $kasbon->amount,
                    'type'                  => $kasbon->type == 'int' ? 'debit' : 'credit',
                ]);
            } else {
                $transaction->update([
                    'account_id'            => $settings->kasbon,
                    'amount'                => $kasbon->amount,
                    'type'                  => $kasbon->type == 'int' ? 'credit' : 'debit',
                ]);
            }


            $this->ledgerTransactionObserver->logAccountUpdate($transaction);
            $this->ledgerObserver->updateCashFlowAccount($transaction->account);
        }
    }

    public function deleteAccount(EmployeeKasbon $kasbon)
    {

        foreach ($kasbon->transaction as $transaction) {
            $account            = $transaction->account;
            $nextTransaction    = AccountTransaction::where("id", ">", $transaction->id)->where("account_id", $transaction->account_id)->first();
            
            $transaction->delete();

            if ($nextTransaction) {
                $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
            }

            if ($account) {
                $this->ledgerObserver->updateCashFlowAccount($account);
            }
        }
    }

    public function deleteData(EmployeeKasbon $kasbon)
    {
        $this->deleteAccount($kasbon);
        $kasbon->forceDelete();
    }
}
