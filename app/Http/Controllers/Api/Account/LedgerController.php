<?php

namespace App\Http\Controllers\Api\Account;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\AccountRequest;
use App\Http\Requests\Account\AccountUpdateRequest;
use App\Http\Requests\Account\DepositRequest;
use App\Http\Requests\Account\TransferRequest;
use App\Http\Resources\Account\AccountListResource;
use App\Http\Resources\Account\AccountSimpleResource;
use App\Http\Resources\Accout\AccountHistoryResource;
use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Master\PaymentMethodObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class LedgerController extends Controller
{
    protected $ledgerObserver;
    protected $ledgerTransactionObserver;
    protected $paymentMethodObserver;

    public function __construct(LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver, PaymentMethodObserver $paymentMethodObserver)
    {
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
        $this->paymentMethodObserver        = $paymentMethodObserver;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('account_view'), 403);
        $limit = $request->input('limit', 20);
        $data   = $this->ledgerObserver->getData($request);

        $totalRows  = $data->count();
        $accounts   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'accounts'      => AccountListResource::collection($accounts),
        ]);
    }

    public function simple(Request $request)
    {
        $data       = $this->ledgerObserver->getData($request)->limit(20)->get(['id', 'name', 'coa']);

        return response()->json([
            'accounts'      => AccountSimpleResource::collection($data),
        ]);
    }


    public function create(AccountRequest $request)
    {

        abort_if(Gate::denies('account_create'), 403);

        try {

            if ($request->subtype != true || $request->autocode != true) {
                $checkReadyCode = $this->ledgerObserver->checkReadyCode($request->coa);


                if ($checkReadyCode) {
                    return response()->json([
                        'message'   => 'Maaf, Kode ini sudah digunakan sebelumnya',
                        'status'    => false
                    ], 422);
                }
            }

            $account = $this->ledgerObserver->createData($request);

            if ($account->type->type == 'bank_cash') {
                $this->paymentMethodObserver->createAutomatic($account);
            }

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

    public function update(AccountUpdateRequest $request, Account $account)
    {

        abort_if(Gate::denies('account_update'), 403);

        try {

            $this->ledgerObserver->updateData($request, $account);

            if ($account->type->type == 'bank_cash') {
                $this->paymentMethodObserver->updateAutomatic($account);
            }

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

    public function delete(Account $account)
    {

        abort_if(Gate::denies('account_delete'), 403);

        try {

            if ($account->child()->count() > 0) {
                return response()->json([
                    'message'   => 'Tidak dapat menghapus data induk',
                    'status'    => true
                ], 422);
            }

            if (
                $account->supply()->count() > 0 ||
                $account->sale()->count() > 0 ||
                $account->return_sale()->count() > 0 ||
                $account->discount()->count() > 0 ||
                $account->cost()->count() > 0 ||
                $account->retur_purchase()->count() > 0 ||
                $account->supplier_debt()->count() > 0 ||
                $account->sent()->count() > 0
            ) {
            }

            if ($account->default != null) {
                return response()->json([
                    'message'   => 'Tidak dapat menghapus data akun ini, karena telah di gunakan oleh data lainnya',
                    'status'    => true
                ], 422);
            }


            $account->sheet()->delete();
            $this->paymentMethodObserver->deleteAutomatic($account);
            $account->delete();

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

    // Ledger Transaction
    public function deposit(DepositRequest $request, Account $account)
    {

        abort_if(Gate::denies('account_deposit'), 403);

        try {

            DB::beginTransaction();

            if ($account->type_account != 'tax') {
                $this->ledgerTransactionObserver->depositAccount($request, $account);
            } else {
                $settingsTax    = AccountSetting::first(['tax_input', 'tax_output', 'tax_minus', 'tax_over', 'pph_two_two']);
                $status         = false;

                if ($account->id == $settingsTax->tax_input) {
                    $status         = true;
                    $dataAccount    = $settingsTax->tax_input_account;

                    $ppnMasukan = AccountTransaction::create([
                        'account_id'                    => $dataAccount->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $request->amount,
                        'type'                          => 'debit',
                        'tax_type'                      => '1',
                        'sub_type'                      => 'tax_input',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('tax_input', $request->date),
                        'operation_date'                => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s'),
                        'name'                          => $request->name
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($ppnMasukan);
                }

                if ($account->id == $settingsTax->tax_output) {
                    $status         = true;
                    $dataAccount    = $settingsTax->tax_output_account;

                    $ppnKeluaran = AccountTransaction::create([
                        'account_id'                    => $dataAccount->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $request->amount,
                        'type'                          => 'credit',
                        'sub_type'                      => 'tax_output',
                        'tax_type'                      => '1',
                        'tax_gunggung'                  => 'yes',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('tax_output', $request->date),
                        'operation_date'                => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s'), 
                        'name'                          => $request->name,
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($settingsTax->tax_output_account);
                    $this->ledgerTransactionObserver->logAccountTransaction($ppnKeluaran);
                }

                if ($account->id == $settingsTax->tax_minus) {
                    $status         = true;

                    $taxMinus    = AccountTransaction::create([
                        'account_id'                    => $settingsTax->tax_minus_account->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $request->amount,
                        'type'                          => 'debit',
                        'sub_type'                      => 'spt_tax',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('spt_tax', $request->date),
                        'operation_date'                => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s'),
                        'name'                          => $request->name
                    ]);


                    $this->ledgerObserver->updateCashFlowAccount($settingsTax->tax_minus_account);
                    $this->ledgerTransactionObserver->logAccountTransaction($taxMinus);
                }

                if ($account->id == $settingsTax->tax_over) {
                    $status         = true;

                    $taxOver    = AccountTransaction::create([
                        'account_id'                    => $settingsTax->tax_over_account->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $request->amount,
                        'type'                          => 'debit',
                        'sub_type'                      => 'spt_tax',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('spt_tax', $request->date),
                        'operation_date'                => Helper::setTimeZoneLocal($request->date) . ' ' . date('H:i:s'),
                        'name'                          => $request->name
                    ]);


                    $this->ledgerObserver->updateCashFlowAccount($settingsTax->tax_over_account);
                    $this->ledgerTransactionObserver->logAccountTransaction($taxOver);
                }

                if ($account->id == $settingsTax->pph_two_two) {
                    $status         = true;
                }

                if ($status == false) {
                    return response()->json([
                        'message'   => 'Maaf, Kami tidak dapat menemukan pengaturan pajak untuk akun ini',
                        'status'    => true
                    ], 422);
                }
            }


            DB::commit();
            return response()->json([
                'message'   => 'Berhasil melakukan deposit akun',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function transfer(TransferRequest $request, Account $account)
    {

        abort_if(Gate::denies('account_transfer'), 403);

        if ($account->cashflow < $request->amount) {
            return response()->json([
                'message'   => 'Saldo anda tidak mencukupi',
                'status'    => true
            ], 422);
        }

        try {

            DB::beginTransaction();

            $this->ledgerTransactionObserver->transferSaldo($request, $account);

            DB::commit();
            return response()->json([
                'message'   => 'Data melakukan transfer bank',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function history(Request $request)
    {

        abort_if(Gate::denies('account_history'), 403);

        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->ledgerTransactionObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => AccountHistoryResource::collection($transactions),
        ], 200);
    }

    public function ledgerDefault()
    {
        $settingLedger  = AccountSetting::first();

        return response()->json([
            'customer_debt'                 => array(
                'id'                            => $settingLedger->customer_debt_account->id ?? '',
                'name'                          => $settingLedger->customer_debt_account->name ?? ''
            ),
            'customer_debt_imprest'         => array(
                'id'                            => $settingLedger->customer_debt_imprest_account->id ?? '',
                'name'                          => $settingLedger->customer_debt_imprest_account->name ?? ''
            ),
            'supplier_debt'                 => array(
                'id'                            => $settingLedger->supplier_debt_account->id ?? '',
                'name'                          => $settingLedger->supplier_debt_account->name ?? ''
            ),
            'supplier_debt_imprest'         => array(
                'id'                            => $settingLedger->supplier_debt_imprest_account->id ?? '',
                'name'                          => $settingLedger->supplier_debt_imprest_account->name ?? ''
            ),
            'product_supply'                => array(
                'id'                            => $settingLedger->product_supply_account->id ?? '',
                'name'                          => $settingLedger->product_supply_account->name ?? ''
            ),
            'product_sale'                  => array(
                'id'                            => $settingLedger->product_sale_account->id ?? '',
                'name'                          => $settingLedger->product_sale_account->name ?? ''
            ),
            'product_retur_sale'            => array(
                'id'                            => $settingLedger->product_retur_sale_account->id ?? '',
                'name'                          => $settingLedger->product_retur_sale_account->name ?? ''
            ),
            'product_discount_sale'         => array(
                'id'                            => $settingLedger->product_discount_sale_account->id ?? '',
                'name'                          => $settingLedger->product_discount_sale_account->name ?? ''
            ),
            'product_sent'                  => array(
                'id'                            => $settingLedger->product_sent_account->id ?? '',
                'name'                          => $settingLedger->product_sent_account->name ?? ''
            ),
            'product_cost'                  => array(
                'id'                            => $settingLedger->product_cost_account->id ?? '',
                'name'                          => $settingLedger->product_cost_account->name ?? ''
            ),
            'product_retur_purchase'        => array(
                'id'                            => $settingLedger->product_retur_purchase_account->id ?? '',
                'name'                          => $settingLedger->product_retur_purchase_account->name ?? ''
            ),
            'product_supplier_debt'         => array(
                'id'                            => $settingLedger->product_supplier_debt_account->id ?? '',
                'name'                          => $settingLedger->product_supplier_debt_account->name ?? ''
            ),
        ], 200);
    }
}
