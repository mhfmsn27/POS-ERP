<?php

namespace App\Observers\Hrm;

use App\Helper;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Models\Admin\SettingsHrm;
use App\Models\Crm\SalesCommission;
use App\Models\Hrm\Employee;
use App\Models\Hrm\EmployeeKasbon;
use App\Models\Salary\Salary;
use App\Models\Salary\SalaryDetail;
use App\Models\Transaction\PaymentMethod;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryObserver
{

    protected $ledgerObserver;
    protected $ledgerTransactionObserver;
    protected $kasbonObserver;

    public function __construct(LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver, KasbonObserver $kasbonObserver)
    {
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
        $this->kasbonObserver               = $kasbonObserver;
    }

    public function getData(Request $request, $year = '', $month = '')
    {
        return Salary::where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereDate("created_at", ">=", $request->start_date)->whereDate("created_at", "<=", $request->end_date);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : "";
            }
        })->whereHas('user', function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->whereHas('employee.designation', function ($q) use ($request) {
            return $request->department ? $q->where('department_id', $request->department) : '';
        })->where(function ($q) use ($year) {
            return $year != '' ? $q->whereYear("created_at", $year) : '';
        })->where(function ($q) use ($month) {
            return $month != '' ? $q->whereMonth("created_at", $month) : '';
        })->where(function ($q) use ($request) {
            return $request->status ? $q->where("status", $request->status) : '';
        });
    }

    public function createData(Request $request)
    {

        $year           = substr($request->date, 0, 4);
        $month          = substr($request->date, 5, 2);
        $calendar       = CAL_GREGORIAN;
        $day            = cal_days_in_month($calendar, $month, $year);
        $setthrm        = SettingsHrm::first(['salary_tax', 'attendance_to_cutting', 'attendance_to_salary', 'salary_tax']);
        $totalKasbon    = 0;
        $totalSalary    = 0;
        $tunjanganTotal = 0;
        $commissionTotal = 0;

        foreach ($request->detail as $d) {
            $employee       = Employee::find($d['employee_information']['id']);

            if (!$employee) {
                DB::rollBack();
                throw new \Exception(__('merchant/hrm.salary.employee_not_found'));
            }

            $checkSalary    = Salary::where("employee_id", $employee->id)->whereYear('created_at', $year)->whereMonth('created_at', $month)->first();

            if ($checkSalary) {
                DB::rollBack();

                throw new \Exception('Pegawai ' . $employee->user->name . ' ini sudah melakukan gaji sebelumnya');
            }


            $listTunjangan  = [];
            $listPotongan   = [];

            $kasbonTotal    = $d['kasbon'];
            $totalKasbon    += $kasbonTotal;
            $employeeSalary = $d['salary'];
            $totalTunjangan = 0;
            $totalPotongan  = 0;
            $commission     = $d['commission'];

            foreach ($d['tunjangan'] as $a) {


                $day                = $a['hari'];
                $total_allowance    = $a['jumlah'] * $day;
                $totalTunjangan     += $total_allowance;

                if ($total_allowance > 0) {
                    $listTunjangan[] = array(
                        'name'              => $a['name'],
                        'jumlah'            => (float)$a['jumlah'],
                        'hari'              => $day,
                        'total'             => $total_allowance,
                    );
                }
            }

            foreach ($d['potongan'] as $c) {

                $day                = $c['hari'];
                $total_potongan     = $c['jumlah'] * $day;
                $totalPotongan     += $total_potongan;

                if ($total_potongan > 0) {
                    $listPotongan[] = array(
                        'name'              => $c['name'],
                        'jumlah'            => (float)$c['jumlah'],
                        'hari'              => $day,
                        'total'             => $total_potongan,
                    );
                }
            }


            $subtotal       = $employeeSalary - $kasbonTotal - $totalPotongan + $totalTunjangan + $commission;
            $tax            = $setthrm->salary_tax > 0 ? ($setthrm->salary_tax / 100) * $employeeSalary : 0;
            $total          = ($subtotal + (float)$d['bonus']) - $tax;

            $salary = Salary::create([
                'commission'        => $commission,
                'user_id'           => $employee->user_id,
                'employee_id'       => $employee->id,
                'designation_id'    => $employee->designation_id,
                'cutting'           => $totalPotongan,
                'kasbon_total'      => $kasbonTotal,
                'salary'            => $employeeSalary,
                'tax'               => $setthrm->salary_tax,
                'allowance'         => $totalTunjangan,
                'bonus'             => $d['bonus'],
                'date'              => $request->date,
                'attendance_this_month' => $employee->month_total($year, $month),
                'total_work'        => $employee->month_work($year, $month),
                'total'             => $total,
                'note'              => $d['catatan']
            ]);

            $tunjanganTotal     += $salary->allowance;
            $totalSalary        += $salary->salary;
            $commissionTotal    += $salary->commission;


            foreach ($listTunjangan as $tunjangan) {
                SalaryDetail::create([
                    'salary_id'     => $salary->id,
                    'name'          => $tunjangan['name'],
                    'type'          => 'allowance',
                    'amount'        => $tunjangan['jumlah'],
                    'qty'           => $tunjangan['hari'],
                    'subtotal'      => $tunjangan['total']
                ]);
            }

            foreach ($listPotongan as $potongan) {
                SalaryDetail::create([
                    'salary_id'     => $salary->id,
                    'name'          => $potongan['name'],
                    'type'          => 'deduction',
                    'amount'        => $potongan['jumlah'],
                    'qty'           => $potongan['hari'],
                    'subtotal'      => $potongan['total']
                ]);
            }
        }


        if ($request->total > 0) {

            $salaryTotal = $totalSalary + $tunjanganTotal + $commissionTotal;

            $settings = AccountSetting::first(['salaries']);
            if ($settings) {
                if ($settings->salary_account) {

                    $salaryAccount      = AccountTransaction::where("sub_type", "salary")->where("operation_date", now()->format('Y-m-d'))->first();
                    $nameHistory        = 'Gaji Pegawai Tanggal - ' . now()->format('Y-m-d');

                    if ($salaryAccount) {
                        $salaryAccount->update([
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => $salaryAccount->amount + $salaryTotal,
                        ]);
                    } else {
                        $salaryAccount      = AccountTransaction::create([
                            'account_id'                    => $settings->salaries,
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => $salaryTotal,
                            'type'                          => 'debit',
                            'sub_type'                      => 'salary',
                            'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('salary', now()->format('Y-m-d') ?? now()),
                            'operation_date'                => now()->format('Y-m-d H:i:s'),
                            'name'                          => $nameHistory
                        ]);
                    }


                    $this->ledgerObserver->updateCashFlowAccount($settings->salary_account);
                    $this->ledgerTransactionObserver->logAccountTransaction($salaryAccount);
                }
            }
        }
    }

    public function paySalary(Request $request, Salary $salary)
    {

        $methodDetail   = PaymentMethod::find($request->method['id']);
        $settings       = AccountSetting::first(['kasbon', 'commission']);
        $date           = Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s');

        if ($methodDetail->account) {

            $methodAccount = AccountTransaction::create([
                'account_id'                    => $methodDetail->account->id,
                'salary_id'                     => $salary->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $salary->total,
                'type'                          => 'credit',
                'sub_type'                      => 'pay_salary',
                'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('pay_salary', $date ?? now()),
                'operation_date'                => $date,
                'name'                          => 'Pembayaran Gaji - ' . $salary->employee->user->name ?? ''
            ]);

            $this->ledgerObserver->updateCashFlowAccount($methodDetail->account);
            $this->ledgerTransactionObserver->logAccountTransaction($methodAccount);
        }

        if ($salary->kasbon_total > 0) {

            $kasbon         = EmployeeKasbon::create([
                'employee_id'       => $salary->employee_id,
                'method_id'         => $methodDetail->id,
                'salary_id'         => $salary->id,
                'amount'            => $salary->kasbon_total,
                'note'              => '',
                'type'              => 'int',
                'created_at'        => $date
            ]);

            if ($settings) {
                if ($settings->kasbon_account) {
                    $kasbonAccount      = AccountTransaction::create([
                        'account_id'                    => $settings->kasbon,
                        'kasbon_id'                     => $kasbon->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $kasbon->amount,
                        'type'                          => 'credit',
                        'sub_type'                      => 'kasbon',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('kasbon', $date ?? now()),
                        'operation_date'                => $date,
                        'name'                          => 'Pembayaran Kasbon Via Gaji - ' . $date
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($settings->kasbon_account);
                    $this->ledgerTransactionObserver->logAccountTransaction($kasbonAccount);
                }
            }
        }

        // if ($salary->commission > 0) {
        //     SalesCommission::where("commission_contact_id", $salary->user_id)->where("commission_contact_type", "user")->where("status", "due")->where("created_at", "<=", $salary->created_at)->update([
        //         'status'    => 'pay',
        //         'salary_id' => $salary->id
        //     ]);

        //     if ($settings) {
        //         if ($settings->commission_account) {
        //             $commissionAccount      = AccountTransaction::create([
        //                 'account_id'                    => $settings->commission,
        //                 'salary_id'                     => $salary->id,
        //                 'created_by'                    => auth()->user()->id,
        //                 'amount'                        => $salary->commission,
        //                 'type'                          => 'debit',
        //                 'sub_type'                      => 'commission',
        //                 'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('commission', $date ?? now()),
        //                 'operation_date'                => $date,
        //                 'name'                          => 'Pembayaran Komisi Via Gaji - ' . $date
        //             ]);

        //             $this->ledgerObserver->updateCashFlowAccount($settings->commission_account);
        //             $this->ledgerTransactionObserver->logAccountTransaction($commissionAccount);
        //         }
        //     }
        // }


        $salary->update([
            'status'            => 'paid',
            'method_payment'    => $methodDetail->name,
        ]);
    }

    public function delete(Salary $salary)
    {

        $salaryAccount      = AccountTransaction::where("sub_type", "salary")->where("operation_date", now()->format('Y-m-d'))->first();
        $salaryPay          = AccountTransaction::where("sub_type", "pay_salary")->where("salary_id", $salary->id)->first();
        $kasbon             = EmployeeKasbon::where("salary_id", $salary->id)->first();

        SalesCommission::where("salary_id", $salary->id)->update([
            'status'        => 'due',
            'salary_id'     => null,
        ]);

        if ($kasbon) {
            $this->kasbonObserver->deleteData($kasbon);
        }

        if ($salaryAccount) {
            $salaryAccount->update([
                'created_by'                    => auth()->user()->id,
                'amount'                        => $salaryAccount->amount - (($salary->salary + $salary->allowance) - $salary->kasbon_total),
            ]);

            if ($salaryAccount->amount == 0) {
                $account            = $salaryAccount->account;
                $nextTransaction    = AccountTransaction::where("id", ">", $salaryAccount->id)->where("account_id", $salaryAccount->account_id)->first();

                $salaryAccount->delete();

                if ($nextTransaction) {
                    $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
                }

                if ($account) {
                    $this->ledgerObserver->updateCashFlowAccount($account);
                }
            }
        }

        if ($salaryPay) {
            $account            = $salaryPay->account;
            $nextTransaction    = AccountTransaction::where("id", ">", $salaryPay->id)->where("account_id", $salaryPay->account_id)->first();

            $salaryPay->delete();

            if ($nextTransaction) {
                $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
            }

            if ($account) {
                $this->ledgerObserver->updateCashFlowAccount($account);
            }
        }



        SalaryDetail::where("salary_id", $salary->id)->delete();
        $salary->delete();
    }
}
