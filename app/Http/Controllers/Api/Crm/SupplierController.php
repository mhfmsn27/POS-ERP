<?php

namespace App\Http\Controllers\Api\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\AddDueRequest;
use App\Http\Requests\Crm\SupplierRequest;
use App\Http\Requests\ImportRequest;
use App\Http\Resources\Crm\SimpleSupplierResource;
use App\Http\Resources\Crm\SupplierResource;
use App\Imports\Crm\Supplier\SupplierDueImport;
use App\Imports\Crm\Supplier\SupplierImport;
use App\Imports\Crm\Supplier\SupplierSaldoImport;
use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Models\Admin\TermPayment;
use App\Models\Product\Supplier;
use App\Models\Transaction\TransactionDue;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Crm\SupplierObserver;
use App\Observers\Transaction\TransactionDueObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    protected $supplierObserver;
    protected $transactionDueObserver;
    protected $ledgerObserver;
    protected $ledgerTransactionObserver;

    public function __construct(SupplierObserver $supplierObserver, TransactionDueObserver $transactionDueObserver, LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->supplierObserver             = $supplierObserver;
        $this->transactionDueObserver       = $transactionDueObserver;
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
    }

    public function index(Request $request)
    {

        abort_if(Gate::denies('supplier_view'), 403);

        $limit = $request->input('limit', 10);
        $data   = $this->supplierObserver->getData($request);

        $totalRows  = $data->count();
        $suppliers  = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'suppliers'     => SupplierResource::collection($suppliers),
        ]);
    }

    public function detail(Supplier $supplier)
    {
        return response()->json(SupplierResource::make($supplier));
    }

    public function simple(Request $request)
    {
        $data       = $this->supplierObserver->getData($request)->limit(20)->get(['id', 'name', 'tax_option', 'tax_default', 'term_payment', 'address']);

        return response()->json([
            'suppliers'      => SimpleSupplierResource::collection($data),
        ]);
    }


    public function create(SupplierRequest $request)
    {

        abort_if(Gate::denies('add_supplier'), 403);

        try {

            $this->supplierObserver->createData($request);

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

    public function update(SupplierRequest $request, Supplier $supplier)
    {

        abort_if(Gate::denies('update_supplier'), 403);

        try {

            if ($supplier->due_history()->count() > 0) {
                if ($request->is_account == true && ($supplier->debt != $request->debt['id'])) {
                    return response()->json([
                        'message'   => 'Pengaturan Akuntansi sudah tidak dapat di ubah',
                        'status'    => true
                    ], 422);
                }
            }

            $this->supplierObserver->updateData($request, $supplier);

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

    public function delete(Supplier $supplier)
    {

        abort_if(Gate::denies('delete_supplier'), 403);

        if ($supplier->transaction_history->count() > 0) {
            return response()->json([
                'message'   => 'Data Supplier Sudah tidak dapat di hapus',
                'status'    => false
            ], 422);
        }

        try {

            $supplier->delete();

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

    public function addDue(AddDueRequest $request, Supplier $supplier)
    {

        abort_if(Gate::denies('due_supplier'), 403);

        try {

            DB::beginTransaction();

            $dueTransaction =  $this->transactionDueObserver->createBySupplier($request, $supplier, $request->type);

            if ($supplier->store->accountant_use == 'yes') {
                if ($request->type != 'hutang') {
                    $account = Account::find($request->account['id']);
                    if ($account) {

                        $dueAccount    = AccountTransaction::create([
                            'account_id'                    => $account->id,
                            'transaction_due_id'            => $dueTransaction->id,
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => $request->amount,
                            'type'                          => 'credit',
                            'sub_type'                      => 'due_supplier',
                            'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('due_supplier', $dueTransaction->created_at),
                            'operation_date'                => $dueTransaction->date,
                            'name'                          => 'Penyimpanan Saldo Supplier ' . $dueTransaction->supplier->name ?? ''
                        ]);


                        $this->ledgerObserver->updateCashFlowAccount($account);
                        $this->ledgerTransactionObserver->logAccountTransaction($dueAccount);
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
                            'type'                          => 'debit',
                            'sub_type'                      => 'due_supplier',
                            'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('due_supplier', $dueTransaction->created_at),
                            'operation_date'                => $dueTransaction->date,
                            'name'                          => 'Penyimpanan Saldo Supplier ' . $dueTransaction->supplier->name ?? ''
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

        abort_if(Gate::denies('delete_due_supplier'), 403);

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

        // permission_check(Gate::denies('category_import'), 403);

        $import = Excel::toArray(new SupplierImport(), request()->file('file'));

        if (count($import[0]) > 0) {


            try {

                $settingAccount = AccountSetting::first(['supplier_debt', 'supplier_debt_imprest']);
                $defaultTerm    = TermPayment::where("default", "yes")->first();

                DB::beginTransaction();

                foreach ($import[0] as $d) {

                    if ($d['nama'] != null) {
                        Supplier::firstOrNew(
                            ['name'     =>  $d['nama']],
                            [
                                'email'             => $d['email'],
                                'phone'             => $d['ponsel'],
                                'address'           => $d['alamat'],
                                'is_account'        => $d['gunakan_akuntansi'],
                                'term_payment'      => $defaultTerm ? $defaultTerm->id : null,
                                'debt'              => $d['gunakan_akuntansi'] == 'yes' && $settingAccount ? $settingAccount->supplier_debt : null,
                                'debt_imprest'      => $d['gunakan_akuntansi'] == 'yes' && $settingAccount ? $settingAccount->supplier_debt_imprest : null,
                                'tax_default'       => $d['default_harga_penjualan'],
                                'npwp'              => $d['npwp'],
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

    public function importSaldo(ImportRequest $request, Supplier $supplier)
    {

        // permission_check(Gate::denies('category_import'), 403);

        $import = Excel::toArray(new SupplierSaldoImport(), request()->file('file'));

        if (count($import[0]) > 0) {


            try {

                DB::beginTransaction();

                foreach ($import[0] as $d) {

                    if ($d['referensi']) {
                        $transaction = TransactionDue::create([
                            'no_ref'                => $d['referensi'],
                            'supplier_id'           => $supplier->id,
                            'amount'                => $d['nominal'],
                            'note'                  => $d['catatan'],
                            'date'                  => excelDateToDateTime($d['tanggal'])->format('Y-m-d H:i:s'),
                            'total_due_amount'      => $d['nominal'],
                            'type'                  => 'saldo'
                        ]);

                        $this->ledgerTransactionObserver->createDueSupplier($transaction);

                        // $account = Account::where("coa", $d['coa'])->first();


                        // if ($account) {

                        //     $dueAccount    = AccountTransaction::create([
                        //         'account_id'                    => $account->id,
                        //         'transaction_due_id'            => $transaction->id,
                        //         'created_by'                    => auth()->user()->id,
                        //         'amount'                        => $transaction->amount,
                        //         'type'                          => 'credit',
                        //         'sub_type'                      => 'due_supplier',
                        //         'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('due_supplier', $transaction->created_at),
                        //         'operation_date'                => $transaction->transaction_date,
                        //         'name'                          => 'Penyimpanan Saldo Supplier ' . $transaction->supplier->name ?? ''
                        //     ]);

                        //     $this->ledgerTransactionObserver->logAccountTransaction($dueAccount);
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

    public function importDue(ImportRequest $request, Supplier $supplier)
    {

        // permission_check(Gate::denies('category_import'), 403);

        $import = Excel::toArray(new SupplierDueImport(), request()->file('file'));

        if (count($import[0]) > 0) {


            try {

                DB::beginTransaction();

                foreach ($import[0] as $d) {

                    if ($d['referensi'] != null) {
                        $transaction = TransactionDue::create([
                            'no_ref'                => $d['referensi'],
                            'supplier_id'           => $supplier->id,
                            'amount'                => $d['nominal'],
                            'note'                  => $d['catatan'],
                            'date'                  => excelDateToDateTime($d['tanggal'])->format('Y-m-d H:i:s'),
                            'total_due_amount'      => $d['nominal'],
                            'type'                  => 'hutang'
                        ]);

                        $this->ledgerTransactionObserver->createDueSupplier($transaction);

                        if ($supplier->store->accountant_use == 'yes') {
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
                                    'type'                          => 'debit',
                                    'sub_type'                      => 'due_supplier',
                                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('due_supplier', $transaction->created_at),
                                    'operation_date'                => $transaction->date,
                                    'name'                          => 'Penyimpanan Saldo Supplier ' . $transaction->supplier->name ?? ''
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

        $file = public_path('berkas/supplier_export.xlsx');
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return Response::download($file, 'supplier_export_sample.xlsx', $headers);
    }

    public function downloadSaldo()
    {

        $file = public_path('berkas/supplier_saldo_export.xlsx');
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return Response::download($file, 'supplier_export_saldo_sample.xlsx', $headers);
    }

    public function downloadDue()
    {

        $file = public_path('berkas/supplier_due_export.xlsx');
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return Response::download($file, 'supplier_export_due_sample.xlsx', $headers);
    }
}
