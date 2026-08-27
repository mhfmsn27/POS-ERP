<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\PaymentMethodRequest;
use App\Http\Requests\Master\SaldoPaymentMethodRequest;
use App\Http\Resources\Accout\AccountHistoryResource;
use App\Http\Resources\Master\PaymentMethod\PaymentMethodListResource;
use App\Models\Account\AccountTransaction;
use App\Models\Transaction\PaymentMethod;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Master\PaymentMethodObserver;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PaymentMethodController extends Controller
{
    protected $paymentMethodObserver;
    protected $accountObserver;
    protected $uploadImageProcess;
    protected $ledgerTransactionObserver;
    protected $ledgerObserver;

    public function __construct(PaymentMethodObserver $paymentMethodObserver, LedgerObserver $accountObserver, UploadImageProcess $uploadImageProcess, LedgerTransactionObserver $ledgerTransactionObserver, LedgerObserver $ledgerObserver)
    {
        $this->paymentMethodObserver        = $paymentMethodObserver;
        $this->accountObserver              = $accountObserver;
        $this->uploadImageProcess           = $uploadImageProcess;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
        $this->ledgerObserver               = $ledgerObserver;
    }


    public function index(Request $request)
    {

        abort_if(Gate::denies('payment_method_view'), 403);
        $limit  = $request->input('limit', 20);
        $data   = $this->paymentMethodObserver->getData($request);

        $totalRows  = $data->count();
        $methods    = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'methods'       => PaymentMethodListResource::collection($methods),
        ]);
    }


    public function create(PaymentMethodRequest $request)
    {

        abort_if(Gate::denies('payment_method_create'), 403);

        try {

            if ($request->account['id'] != null || $request->account['id'] != '') {
                $account    = $this->accountObserver->checkReadyCode($request->account['id'], 'id');
                if ($account->bank_id != null) {
                    return response()->json([
                        'message'   => 'Akun ini sudah di hubungkan dengan metode pembayaran yang lainnya',
                        'status'    => true
                    ], 422);
                }
            }

            $image = '';

            if ($request->image) {

                $icon = explode(',', $request->image);
                if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                    $imageData = base64_decode($icon[1]);

                    if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                        $image = $this->uploadImageProcess->uploadFile($request->image, $request->name, 'uploads/' . auth()->user()->business_id . '/uploads/payments/');
                    }
                }
            }

            if ($image == '') {
                $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/' . auth()->user()->business_id . '/uploads/payments/');
            }

            $this->paymentMethodObserver->createData($request, $image);

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

    public function update(PaymentMethodRequest $request, PaymentMethod $method)
    {

        abort_if(Gate::denies('payment_method_update'), 403);

        try {

            if ($request->account['id'] != null || $request->account['id'] != '') {
                $account    = $this->accountObserver->checkReadyCode($request->account['id'], 'id');
                if ($account->bank_id != null && $account->bank_id != $method->id) {
                    return response()->json([
                        'message'   => 'Akun ini sudah di hubungkan dengan metode pembayaran yang lainnya',
                        'status'    => true
                    ], 422);
                }
            }

            $image = '';

            if ($request->image) {
                $icon = explode(',', $request->image);
                if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                    $imageData = base64_decode($icon[1]);

                    if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                        $this->uploadImageProcess->unlinkFile($method->logo);
                        $image = $this->uploadImageProcess->uploadFile($request->image, $request->name, 'uploads/payments/');
                    }
                }
            } else {
                $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/payments/');
            }


            $this->paymentMethodObserver->updateData($request, $method, $image);

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

    public function delete(PaymentMethod $method)
    {

        abort_if(Gate::denies('payment_method_delete'), 403);

        try {

            if ($method->payment()->count() > 0) {
                return response()->json([
                    'message'   => 'Maaf, Metode Pembayaran ini sudah memiliki transaksi atau sudah pernah digunakan',
                    'status'    => true
                ], 422);
            }


            if ($method->account) {
                $method->account()->update([
                    'bank_id'       => null
                ]);
            }

            $method->delete();

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

    public function history(Request $request, PaymentMethod $method)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->ledgerTransactionObserver->getData($request)->where("account_id", $method->account_id)->where("sub_type", "deposit");

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => AccountHistoryResource::collection($transactions),
        ], 200);
    }

    public function addSaldo(SaldoPaymentMethodRequest $request, PaymentMethod $method)
    {

        if (!$method->account) {
            return response()->json([
                'message'   => 'Metode pembayaran ini tidak ter-integrasi dengan Account manapun',
                'status'    => false
            ], 422);
        }

        try {

            DB::beginTransaction();

            $account = $method->account;
            $this->ledgerTransactionObserver->depositAccount($request, $account);

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

    public function updateSaldo(SaldoPaymentMethodRequest $request, AccountTransaction $transaction)
    {

        try {

            DB::beginTransaction();


            $transaction->update([
                'created_by'                    => auth()->user()->id,
                'amount'                        => $request->amount
            ]);

            $this->ledgerTransactionObserver->logAccountUpdate($transaction);
            $this->ledgerObserver->updateCashFlowAccount($transaction->account);

            foreach ($transaction->account_transaction as $aTransaction) {

                $aTransaction->update([
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $request->amount
                ]);

                $this->ledgerTransactionObserver->logAccountUpdate($aTransaction);
                $this->ledgerObserver->updateCashFlowAccount($aTransaction->account);
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


    public function deleteSaldo(AccountTransaction $transaction)
    {

        try {

            DB::beginTransaction();

            foreach ($transaction->account_transaction as $account_two) {

                $nextTransaction    = AccountTransaction::where(function ($query) use ($account_two) {
                    $query->where("operation_date", ">", $account_two->operation_date)
                        ->orWhere(function ($subQuery) use ($account_two) {
                            $subQuery->where("operation_date", "=", $account_two->operation_date)
                                ->where("id", "<", $account_two->id);
                        });
                })
                    ->where("account_id", $account_two->account_id)
                    ->orderBy("operation_date", 'asc')
                    ->orderBy("id", 'asc')->first();

                $accountData        = $account_two->account;

                $account_two->delete();


                if ($nextTransaction) {
                    $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
                }

                if ($accountData) {
                    $this->ledgerObserver->updateCashFlowAccount($accountData);
                }
            }

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

            $accountData        = $transaction->account;

            $transaction->delete();

            if ($nextTransaction) {
                $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
            }

            if ($accountData) {
                $this->ledgerObserver->updateCashFlowAccount($accountData);
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
}
