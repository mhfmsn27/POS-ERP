<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Imports\Master\RekonsiliasiImport;
use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Transaction\RekonsiliasiData;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class RekonsiliasiBankController extends Controller
{
    protected $ledgerTransactionObserver;
    protected $ledgerObserver;

    public function __construct(LedgerTransactionObserver $ledgerTransactionObserver, LedgerObserver $ledgerObserver)
    {
        $this->ledgerTransactionObserver        = $ledgerTransactionObserver;
        $this->ledgerObserver                   = $ledgerObserver;
    }


    public function index(Request $request)
    {
        abort_if(Gate::denies('rekonsiliasi_view'), 403);

        $type           = $request->type ? $request->type : 'jurnal';
        $type           = $type != 'jurnal' && $type != 'mutasi' ? 'jurnal' : $type;
        $limit          = $request->limit ? $request->limit : 10;
        $account        = Account::where("id", $request->account)->first();
        $withMutation   = $account != null ? ($account->smartlink ? true : false) : false;
        $totalRows      = 0;
        $mutation       = [];
        if ($type == 'jurnal' || !$withMutation) {
            $data           = $this->ledgerTransactionObserver->getData($request,'asc');
            $totalRows      = $data->count();
            $transactions   = $data->paginate($limit);

            foreach ($transactions as $transaction) {

                $mutasiData     = [];
                $mutasiStatus   = false;

                $dateRange = [
                    Carbon::parse($transaction->operation_date)->subDays(3)->format('Y-m-d'),
                    Carbon::parse($transaction->operation_date)->addDays(2)->format('Y-m-d')
                ];


                if ($withMutation) {

                    $mutasi    = RekonsiliasiData::where('status', 'no')->where('type', $transaction->type)->where('account_id', $account->id)->where('amount', (float)$transaction->amount)->whereBetween('date', $dateRange)->first();

                    if ($mutasi) {
                        $mutasiStatus   = true;
                        $mutasiData[]     = array(
                            'id'            => $mutasi->id,
                            'date'          => substr($mutasi->date, 0, 10),
                            'note'          => $mutasi->note,
                            'amount'        => (float)$mutasi->amount,
                            'type'          => $mutasi->type
                        );
                    }
                }

                $mutation[] = array(
                    'id'        => $transaction->id,
                    'date'      => date_format_indo($transaction->operation_date),
                    'tanggal'   => substr($transaction->operation_date, 0, 10),
                    'ref_no'    => $transaction->ref_no,
                    'name'      => $transaction->name,
                    'sub_type'  => $transaction->sub_type,
                    'type'      => $transaction->type,
                    'amount'    => (float)$transaction->amount,
                    'daterange' => $dateRange,
                    'transaction'   => array(
                        'id'            => $transaction->transaction->id ?? null,
                        'ref'           => $transaction->transaction->ref_no ?? null,
                        'type'          => $transaction->transaction->type ?? '',
                        'route'         => $transaction->route_name
                    ),
                    'customer'      => array(
                        'name'          => $transaction->transaction->customer->name ?? '',
                    ),
                    'supplier'      => array(
                        'name'          => $transaction->transaction->supplier->name ?? ''
                    ),
                    'transaction_due'   => array(
                        'id'            => $transaction->transaction_due->id ?? null,
                        'ref'           => $transaction->transaction_due->no_ref ?? null,
                    ),
                    'saldo'     => number_format($transaction->cashflow),
                    'note'      => $transaction->note,
                    'mutation'  => $mutasiData,
                    'status'    => $mutasiStatus
                );
            }
        } else {
            $data           = $this->ledgerTransactionObserver->getRekonsiliasi($request)->where('status', 'no');
            $totalRows      = $data->count();
            $transactions   = $data->paginate($limit);

            foreach ($transactions as $transaction) {

                $accountData    = [];
                $accountStatus  = false;

                $dateRange = [
                    Carbon::parse($transaction->date)->subDays(3)->format('Y-m-d'),
                    Carbon::parse($transaction->date)->addDays(2)->format('Y-m-d')
                ];

                if ($withMutation) {


                    $accountTransaction    = AccountTransaction::where('after_rekonsiliasi', 'no')->where('account_id', $account->id)->where('amount', (float)$transaction->amount)->where('type', $transaction->type)->whereBetween('operation_date', $dateRange)->first();

                    if ($accountTransaction) {
                        $accountStatus  = true;
                        $accountData[]    = array(
                            'id'        => $accountTransaction->id,
                            'date'      => date_format_indo($accountTransaction->operation_date),
                            'tanggal'   => substr($accountTransaction->operation_date, 0, 10),
                            'ref_no'    => $accountTransaction->ref_no,
                            'name'      => $accountTransaction->name,
                            'sub_type'  => $accountTransaction->sub_type,
                            'type'      => $accountTransaction->type,
                            'amount'    => (float)$accountTransaction->amount,
                            'transaction'   => array(
                                'id'            => $accountTransaction->transaction->id ?? null,
                                'ref'           => $accountTransaction->transaction->ref_no ?? null,
                                'type'          => $accountTransaction->transaction->type ?? '',
                                'route'         => $accountTransaction->route_name ?? ""
                            ),
                            'customer'      => array(
                                'name'          => $accountTransaction->transaction->customer->name ?? "",
                            ),
                            'supplier'      => array(
                                'name'          => $accountTransaction->transaction->supplier->name ?? ""
                            ),
                            'transaction_due'   => array(
                                'id'            => $accountTransaction->transaction_due->id ?? null,
                                'ref'           => $accountTransaction->transaction_due->no_ref ?? null,
                            ),
                            'saldo'     => number_format($accountTransaction->cashflow),
                            'note'      => $accountTransaction->note,
                        );
                    }
                }

                $mutation[] = array(
                    'id'            => $transaction->id,
                    'date'          => substr($transaction->date, 0, 10),
                    'note'          => $transaction->note,
                    'amount'        => (float)$transaction->amount,
                    'type'          => $transaction->type,
                    'account'       => $accountData,
                    'daterange'     => $dateRange,
                    'status'        => $accountStatus
                );
            }
        }

        return response()->json([
            'totalRows'     => $totalRows,
            'account'       => array(
                'name'          => $account != null ? $account->name : '',
                'code'          => $account != null ? $account->coa : '',
                'saldo'         => $account != null ? number_format($account->cashflow) : 0,
                'end_balance'   => $account != null ? number_format($account->end_balance) : 0,
                'import'        => $withMutation,
            ),
            'transactions'  => $mutation,
        ], 200);
    }

    public function getNota(Request $request, RekonsiliasiData $mutasi)
    {
        $data           = $this->ledgerTransactionObserver->getData($request);
        $limit          = $request->limit ? $request->limit : 10;
        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);
        $list           = [];

        foreach ($transactions as $transaction) {

            $mutasiData     = [];
            $mutasiStatus   = false;

            $list[] = array(
                'id'        => $transaction->id,
                'date'      => date_format_indo($transaction->operation_date),
                'tanggal'   => substr($transaction->operation_date, 0, 10),
                'ref_no'    => $transaction->ref_no,
                'name'      => $transaction->name,
                'sub_type'  => $transaction->sub_type,
                'type'      => $transaction->type,
                'amount'    => (float)$transaction->amount,
                'transaction'   => array(
                    'id'            => $transaction->transaction->id ?? null,
                    'ref'           => $transaction->transaction->ref_no ?? null,
                    'type'          => $transaction->transaction->type ?? '',
                    'route'         => $transaction->route_name
                ),
                'customer'      => array(
                    'name'          => $transaction->transaction->customer->name ?? '',
                ),
                'supplier'      => array(
                    'name'          => $transaction->transaction->supplier->name ?? ''
                ),
                'transaction_due'   => array(
                    'id'            => $transaction->transaction_due->id ?? null,
                    'ref'           => $transaction->transaction_due->no_ref ?? null,
                ),
                'saldo'     => number_format($transaction->cashflow),
                'note'      => $transaction->note,
                'mutation'  => $mutasiData,
                'status'    => $mutasiStatus
            );
        }

        return response()->json([
            'totalRows'     => $totalRows,
            'items'         => $list,
        ], 200);
    }

    public function getMutasi(Request $request, AccountTransaction $transaction)
    {
        $data           = $this->ledgerTransactionObserver->getRekonsiliasi($request)->where('status', 'no');
        $limit          = $request->limit ? $request->limit : 10;
        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);
        $list           = [];
        $dateRange = [
            Carbon::parse($transaction->date)->subDays(3)->format('Y-m-d'),
            Carbon::parse($transaction->date)->addDays(2)->format('Y-m-d')
        ];

        foreach ($transactions as $transaction) {

            $list[] = array(
                'id'            => $transaction->id,
                'date'          => substr($transaction->date, 0, 10),
                'note'          => $transaction->note,
                'amount'        => (float)$transaction->amount,
                'type'          => $transaction->type,
                'daterange'     => $dateRange,
            );
        }

        return response()->json([
            'totalRows'     => $totalRows,
            'items'         => $list,
        ], 200);
    }

    public function action(AccountTransaction $transaction)
    {

        abort_if(Gate::denies('rekonsiliasi_action'), 403);

        try {

            $transaction->update([
                'after_rekonsiliasi'        => 'yes'
            ]);


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

    public function actionForNota(Request $request, AccountTransaction $transaction)
    {
        try {

            DB::beginTransaction();

            foreach ($request->mutation as $mutation) {
                $data = RekonsiliasiData::findOrFail($mutation['id']);
                $data->update([
                    'transaction_account_id'    => $transaction->id,
                    'status'                    => 'yes'
                ]);
            }

            $transaction->update([
                'after_rekonsiliasi'        => 'yes'
            ]);

            DB::commit();

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function actionForMutasi(Request $request, RekonsiliasiData $mutasi)
    {
        try {

            DB::beginTransaction();

            $transaction        = [];

            foreach ($request->account as $account) {
                $transaction[]  = array(
                    'id'            => $account['id']
                );

                $data = AccountTransaction::findOrFail($account['id']);

                $data->update([
                    'after_rekonsiliasi'        => 'yes'
                ]);
            }

            $mutasi->update([
                'transaction_account_id'    => implode(',', array_column($transaction, 'id')),
                'status'                    => 'yes'
            ]);


            DB::commit();

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function rejected(AccountTransaction $transaction)
    {

        abort_if(Gate::denies('rekonsiliasi_action'), 403);

        try {

            DB::beginTransaction();

            $rekonsiliasiData = RekonsiliasiData::where('status', 'yes')->whereRaw("find_in_set('" .  $transaction->id . "',transaction_account_id)")->get();

            foreach ($rekonsiliasiData as $rekon) {
                $IdTransaction      = explode(",", $rekon->transaction_account_id);

                if (count($IdTransaction) > 0) {
                    foreach ($IdTransaction as $id) {
                        $t = AccountTransaction::where('id', $id)
                            ->where('id', '!=', $transaction->id) // Urutan benar
                            ->first();

                        if ($t) {
                            $t->update([
                                'after_rekonsiliasi'        => 'no'
                            ]);
                        }
                    }
                }

                $rekon->update([
                    'transaction_account_id'    => null,
                    'status'                    => 'no'
                ]);
            }

            $transaction->update([
                'after_rekonsiliasi'        => 'no'
            ]);

            DB::commit();

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function import(Request $request, Account $account)
    {

        try {
            $import = new RekonsiliasiImport($account);
            Excel::import($import, $request->file('file'));

            if (count($import->getErrors()) > 0) {
                return response()->json([
                    'status'    => false,
                    'message'   => $import->getErrors(),
                ], 422);
            }

            return response()->json([
                'status'    => true,
                'message'   => 'Successfully imported ' . $import->getTotalRows() . ' records',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage()
            ], 422);
        }
    }

    public function createTransaction(Request $request, Account $account, RekonsiliasiData $mutasi)
    {
        try {

            DB::beginTransaction();

            $paymentAccount = AccountTransaction::create([
                'account_id'                    => $account->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $mutasi->amount,
                'type'                          => $mutasi->type,
                'sub_type'                      => $request->type,
                'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo($request->type, $request->date ?? now()),
                'operation_date'                => $request->date,
                'note'                          => $request->note,
                'name'                          => $mutasi->note,
                'after_rekonsiliasi'            => 'yes'
            ]);

            $this->ledgerObserver->updateCashFlowAccount($account);
            $this->ledgerTransactionObserver->logAccountTransaction($paymentAccount);

            $lawanTransaksiAccount = Account::findOrFail($request->account['id']);

            if ($lawanTransaksiAccount) {
                $lawanTransaksi = AccountTransaction::create([
                    'account_id'                    => $lawanTransaksiAccount->id,
                    'account_transaction_id'        => $paymentAccount->id,
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $mutasi->amount,
                    'type'                          => $mutasi->type == 'credit' ? 'debit' : 'credit',
                    'sub_type'                      => $request->type,
                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo($request->type, $request->date ?? now()),
                    'operation_date'                => $request->date ?? now(),
                    'note'                          => $request->note,
                    'name'                          => $request->note
                ]);


                $this->ledgerObserver->updateCashFlowAccount($lawanTransaksiAccount);
                $this->ledgerTransactionObserver->logAccountTransaction($lawanTransaksi);
            }

            $mutasi->update([
                'status'                    => 'yes',
                'transaction_account_id'    => $paymentAccount->id
            ]);

            DB::commit();

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function rekonsiliasiRemove(RekonsiliasiData $mutasi)
    {
        if($mutasi->status == 'no') {
            $mutasi->delete();
        }

        return response()->json([
            'message'   => 'Data berhasil di perbaharui',
            'status'    => true
        ], 200);
    }

    public function autoMatch(Request $request, \App\Services\Accounting\BankReconciliationService $service)
    {
        abort_if(Gate::denies('rekonsiliasi_action'), 403);

        $request->validate([
            'account_id' => 'required|integer',
        ]);

        $toleranceDays = (int)($request->input('tolerance_days', 3));
        $minConfidence = (int)($request->input('min_confidence', 90));

        $result = $service->batchAutoReconcile(
            (int)$request->account_id,
            $minConfidence,
            $toleranceDays
        );

        return response()->json([
            'status'  => true,
            'message' => "Proses Auto-Rekonsiliasi selesai: {$result['matched_count']} transaksi berhasil dicocokkan otomatis.",
            'data'    => $result,
        ], 200);
    }
}

