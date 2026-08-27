<?php

namespace App\Http\Controllers\Api\Crm;

use App\Exports\Crm\CustoemrSptExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\AddDueRequest;
use App\Http\Requests\Crm\CustomerRequest;
use App\Http\Requests\ImportRequest;
use App\Http\Resources\Crm\CustomerResource;
use App\Http\Resources\Crm\SimpleCustomerResource;
use App\Imports\Crm\Customer\CustomerDueImport;
use App\Imports\Crm\Customer\CustomerImport;
use App\Imports\Crm\Customer\CustomerSaldoImport;
use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Models\Admin\Customer;
use App\Models\Admin\TermPayment;
use App\Models\Transaction\TransactionDue;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Crm\CustomerObserver;
use App\Observers\Transaction\TransactionDueObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    protected $customerObserver;
    protected $transactionDueObserver;
    protected $ledgerObserver;
    protected $ledgerTransactionObserver;

    public function __construct(CustomerObserver $customerObserver, TransactionDueObserver $transactionDueObserver, LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->customerObserver             = $customerObserver;
        $this->transactionDueObserver       = $transactionDueObserver;
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
    }

    public function index(Request $request)
    {

        abort_if(Gate::denies('customer_view'), 403);

        $limit = $request->input('limit', 10);
        $data   = $this->customerObserver->getData($request);

        $totalRows  = $data->count();
        $customers  = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'customers'     => CustomerResource::collection($customers),
        ]);
    }

    public function detail(Customer $customer)
    {
        abort_if(Gate::denies('customer_view'), 403);
        return response()->json(CustomerResource::make($customer));
    }

    public function simple(Request $request)
    {
        $data       = $this->customerObserver->getData($request)->limit(20)->get(['id', 'name', 'tax_default', 'tax_option', 'type', 'term_payment', 'address', 'npwp', 'phone']);

        return response()->json([
            'customers'      => SimpleCustomerResource::collection($data),
        ]);
    }


    public function create(CustomerRequest $request)
    {

        abort_if(Gate::denies('add_customer'), 403);

        try {

            $this->customerObserver->createData($request);

            return response()->json([
                'message'   => 'Data berhasil di tambahkan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function update(CustomerRequest $request, Customer $customer)
    {

        abort_if(Gate::denies('update_customer'), 403);

        try {

            if ($customer->due_history()->count() > 0) {
                if ($request->is_account == true && ($customer->debt != $request->debt['id'])) {
                    return response()->json([
                        'message'   => 'Pengaturan Akuntansi sudah tidak dapat di ubah',
                        'status'    => true
                    ], 422);
                }
            }

            $this->customerObserver->updateData($request, $customer);

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function delete(Customer $customer)
    {

        abort_if(Gate::denies('delete_customer'), 403);

        if ($customer->transaction_history->count() > 0) {
            return response()->json([
                'message'   => 'Data Pelanggan Sudah tidak dapat di hapus',
                'status'    => false
            ], 422);
        }

        try {

            $customer->delete();

            return response()->json([
                'message'   => 'Data berhasil di hapus',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function addDue(AddDueRequest $request, Customer $customer)
    {

        abort_if(Gate::denies('due_customer'), 403);

        try {

            DB::beginTransaction();

            $dueTransaction = $this->transactionDueObserver->createByCustomer($request, $customer, $request->type);

            if ($customer->store->accountant_use == 'yes') {
                if ($request->type != 'hutang') {
                    $account = Account::find($request->account['id']);

                    if ($account) {

                        $saleAccount    = AccountTransaction::create([
                            'account_id'                    => $account->id,
                            'transaction_due_id'            => $dueTransaction->id,
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => $request->amount,
                            'type'                          => 'debit',
                            'sub_type'                      => 'due_customer',
                            'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('due_customer', $dueTransaction->created_at),
                            'operation_date'                => $dueTransaction->date,
                            'name'                          => 'Penyimpanan Saldo Customer ' . $customer->name ?? ''
                        ]);


                        $this->ledgerObserver->updateCashFlowAccount($account);
                        $this->ledgerTransactionObserver->logAccountTransaction($saleAccount);
                    }
                } else {
                    $accountCapital         = Account::where("default_data", "modal")->first();

                    if (!$accountCapital) {
                        throw new \Exception('Gagal Deposit, Silahkan buat Akuntansi untuk menampung Credit Equitas Modal');
                    } else {

                        // Trigger To Equitas Saldo
                        $equitasSaldo   = AccountTransaction::create([
                            'account_id'                    => $accountCapital->id,
                            'transaction_due_id'            => $dueTransaction->id,
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => $request->amount,
                            'type'                          => 'credit',
                            'sub_type'                      => 'due_customer',
                            'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('due_customer', $dueTransaction->created_at),
                            'operation_date'                => $dueTransaction->date,
                            'name'                          => 'Penyimpanan Saldo Pelanggan ' . $customer->name ?? ''
                        ]);


                        $this->ledgerObserver->updateCashFlowAccount($accountCapital);
                        $this->ledgerTransactionObserver->logAccountTransaction($equitasSaldo);
                    }
                }
            }


            DB::commit();

            return response()->json([
                'message'   => 'Data berhasil di tambahkan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }


    public function deleteDue(TransactionDue $transaction)
    {

        abort_if(Gate::denies('delete_due_customer'), 403);

        try {

            if ($transaction->transaction_id != null || $transaction->payment()->count() > 0) {
                return response()->json([
                    'message'   => 'Faktur ini sudah tidak dapat di hapus',
                    'status'    => true
                ], 422);
            }

            DB::beginTransaction();

            $this->transactionDueObserver->deleteData($transaction);

            DB::commit();

            return response()->json([
                'message'   => 'Data berhasil di tambahkan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function import(ImportRequest $request)
    {

        abort_if(Gate::denies('add_customer'), 403);

        $import = Excel::toArray(new CustomerImport(), request()->file('file'));

        if (count($import[0]) > 0) {


            try {

                $settingAccount = AccountSetting::first(['customer_debt', 'customer_debt_imprest']);
                $defaultTerm    = TermPayment::where("default", "yes")->first();

                DB::beginTransaction();

                foreach ($import[0] as $d) {

                    if ($d['nama'] != null) {
                        Customer::firstOrNew(
                            ['name'     =>  $d['nama']],
                            [
                                'email'             => $d['email'],
                                'phone'             => $d['ponsel'],
                                'address'           => $d['alamat'],
                                'is_account'        => $d['gunakan_akuntansi'],
                                'term_payment'      => $defaultTerm ? $defaultTerm->id : null,
                                'debt'              => $d['gunakan_akuntansi'] == 'yes' && $settingAccount ? $settingAccount->customer_debt : null,
                                'debt_imprest'      => $d['gunakan_akuntansi'] == 'yes' && $settingAccount ? $settingAccount->customer_debt_imprest : null,
                                'tax_default'       => $d['default_harga_penjualan'],
                                'npwp'              => $d['npwp'],
                                'type'              => $d['tipe'],
                                'tax_option'        => $d['pajak'],
                            ]
                        )->save();
                    }
                }

                DB::commit();

                return response()->json([
                    'status'    => true,
                    'message'   => 'Import Data berhasil di lakukan'
                ], 200);
            } catch (\Exception $e) {

                DB::rollBack();
                return response()->json(
                    [
                        'status'    => false,
                        'message'   => $e->getMessage(),
                    ],
                    409
                );
            }
        } else {

            return response()->jsown([
                'message'   => 'Data Exce kosong',
                'status'    => false
            ], 409);
        }

        return response()->json([
            'message'   => 'Kami tidak dapat mendeteksi file xlsx anda',
            'status'    => false
        ], 409);
    }

    public function importSaldo(ImportRequest $request, Customer $customer)
    {

        abort_if(Gate::denies('saldo_customer_view'), 403);

        $import = Excel::toArray(new CustomerSaldoImport(), request()->file('file'));

        if (count($import[0]) > 0) {


            try {

                DB::beginTransaction();

                foreach ($import[0] as $d) {

                    if ($d['referensi'] != null) {
                        $transaction = TransactionDue::create([
                            'no_ref'                => $d['referensi'],
                            'customer_id'           => $customer->id,
                            'amount'                => $d['nominal'],
                            'note'                  => $d['catatan'],
                            'date'                  => excelDateToDateTime($d['tanggal'])->format('Y-m-d H:i:s'),
                            'total_due_amount'      => $d['nominal'],
                            'type'                  => 'saldo'
                        ]);

                        $this->ledgerTransactionObserver->createDueCustomer($transaction);

                        // $account = Account::where("coa", $d['coa'])->first();


                        // if ($account) {

                        //     $saleAccount    = AccountTransaction::create([
                        //         'account_id'                    => $account->id,
                        //         'transaction_due_id'            => $transaction->id,
                        //         'created_by'                    => auth()->user()->id,
                        //         'amount'                        => $transaction->amount,
                        //         'type'                          => 'debit',
                        //         'sub_type'                      => 'due_customer',
                        //         'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('due_customer', $transaction->created_at),
                        //         'operation_date'                => $transaction->transaction_date,
                        //         'name'                          => 'Penyimpanan Saldo Customer ' . $customer->name ?? ''
                        //     ]);

                        //     $this->ledgerTransactionObserver->logAccountTransaction($saleAccount);
                        //     $this->ledgerObserver->updateCashFlowAccount($account);
                        // }
                    }
                }

                DB::commit();

                return response()->json([
                    'status'    => true,
                    'message'   => 'Import Data berhasil di lakukan'
                ], 200);
            } catch (\Exception $e) {

                DB::rollBack();
                return response()->json(
                    [
                        'status'    => false,
                        'message'   => $e->getMessage(),
                    ],
                    409
                );
            }
        } else {

            return response()->jsown([
                'message'   => 'Data Exce kosong',
                'status'    => false
            ], 409);
        }

        return response()->json([
            'message'   => 'Kami tidak dapat mendeteksi file xlsx anda',
            'status'    => false
        ], 409);
    }

    public function importDue(ImportRequest $request, Customer $customer)
    {

        abort_if(Gate::denies('due_customer'), 403);

        $import = Excel::toArray(new CustomerDueImport(), request()->file('file'));

        if (count($import[0]) > 0) {


            try {

                DB::beginTransaction();

                foreach ($import[0] as $d) {

                    if ($d['referensi'] != null) {
                        $transaction = TransactionDue::create([
                            'no_ref'                => $d['referensi'],
                            'customer_id'           => $customer->id,
                            'amount'                => $d['nominal'],
                            'note'                  => $d['catatan'],
                            'date'                  => excelDateToDateTime($d['tanggal'])->format('Y-m-d H:i:s'),
                            'total_due_amount'      => $d['nominal'],
                            'type'                  => 'hutang'
                        ]);

                        $this->ledgerTransactionObserver->createDueCustomer($transaction);

                        if ($customer->store->accountant_use == 'yes') {
                            $accountCapital         = Account::where("default_data", "modal")->first();

                            if (!$accountCapital) {
                                throw new \Exception('Gagal Deposit, Silahkan buat Akuntansi untuk menampung Credit Equitas Modal');
                            } else {

                                // Trigger To Equitas Saldo
                                $equitasSaldo   = AccountTransaction::create([
                                    'account_id'                    => $accountCapital->id,
                                    'transaction_due_id'            => $transaction->id,
                                    'created_by'                    => auth()->user()->id,
                                    'amount'                        => $transaction->amount,
                                    'type'                          => 'credit',
                                    'sub_type'                      => 'due_customer',
                                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('due_customer', $transaction->created_at),
                                    'operation_date'                => $transaction->date,
                                    'name'                          => 'Penyimpanan Saldo Pelanggan ' . $customer->name ?? ''
                                ]);


                                $this->ledgerObserver->updateCashFlowAccount($accountCapital);
                                $this->ledgerTransactionObserver->logAccountTransaction($equitasSaldo);
                            }
                        }
                    }
                }

                DB::commit();

                return response()->json([
                    'status'    => true,
                    'message'   => 'Import Data berhasil di lakukan'
                ], 200);
            } catch (\Exception $e) {

                DB::rollBack();
                return response()->json(
                    [
                        'status'    => false,
                        'message'   => $e->getMessage(),
                    ],
                    409
                );
            }
        } else {

            return response()->jsown([
                'message'   => 'Data Exce kosong',
                'status'    => false
            ], 409);
        }

        return response()->json([
            'message'   => 'Kami tidak dapat mendeteksi file xlsx anda',
            'status'    => false
        ], 409);
    }

    public function downloadSample()
    {

        $file = public_path('berkas/customer_export.xlsx');
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return Response::download($file, 'customer_export_sample.xlsx', $headers);
    }

    public function setDefault(Customer $customer)
    {

        abort_if(Gate::denies('update_customer'), 403);

        if ($customer->default == 'no') {
            Customer::where('default', 'yes')->where('id', '!=', $customer->id)->update([
                'default'       => 'no'
            ]);
        }

        $customer->update([
            'default'       => $customer->default == 'no' ? 'yes' : 'no'
        ]);

        return response()->json([
            'message'   => 'Berhasil memperbaharui data default pelanggan',
            'status'    => true
        ], 200);
    }

    public function downloadSaldo()
    {

        $file = public_path('berkas/customer_saldo_export.xlsx');
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return Response::download($file, 'customer_export_saldo_sample.xlsx', $headers);
    }

    public function downloadDue()
    {

        $file = public_path('berkas/customer_due_export.xlsx');
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return Response::download($file, 'customer_export_due_sample.xlsx', $headers);
    }

    public function downloadSpt(Request $request)
    {

        return (new CustoemrSptExport($request, $this->customerObserver))->download('customer_spt_format.xlsx');
    }
}
