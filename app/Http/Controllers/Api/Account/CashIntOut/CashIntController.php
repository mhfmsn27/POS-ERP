<?php

namespace App\Http\Controllers\Api\Account\CashIntOut;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashIntOut\CashIntOutRequest;
use App\Http\Resources\CashIntOut\CashIntOutDetailResource;
use App\Http\Resources\CashIntOut\CashIntOutListResource;
use App\Models\Account\AccountTransaction;
use App\Models\Account\Expense;
use App\Observers\CashIntOut\CashIntOutObserver;
use App\Observers\Transaction\TransactionPaymentObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashIntController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Cash Int Controller
    |--------------------------------------------------------------------------
    */

    protected $cashIntOutObserver;
    protected $paymentObserver;

    public function __construct(CashIntOutObserver $cashIntOutObserver, TransactionPaymentObserver $paymentObserver)
    {
        $this->cashIntOutObserver       = $cashIntOutObserver;
        $this->paymentObserver          = $paymentObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Cash Int List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        //  permission_check(Gate::denies('cash_int_view'), 403);

        $limit  = $request->input('limit', 10);
        $data   = $this->cashIntOutObserver->getData($request);

        $totalRows  = $data->count();
        $cashint    = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => CashIntOutListResource::collection($cashint),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Cash Int
    |--------------------------------------------------------------------------
    */

    public function create(CashIntOutRequest $request)
    {



        // permission_check(Gate::denies('cash_int_store'), 403);

        try {

            DB::beginTransaction();

            $this->cashIntOutObserver->createData($request, $request->type);

            DB::commit();

            return response()->json([
                'message'   => 'Berhasil menambahkan data',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 409);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Detail Cash Int
    |--------------------------------------------------------------------------
    */

    public function detail(Expense $expense)
    {
        //   permission_check(Gate::denies('cash_int_update'), 403);


        return response()->json([
            'detail'      => CashIntOutDetailResource::make($expense),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Update Cash Int
    |--------------------------------------------------------------------------
    */

    public function update(CashIntOutRequest $request, Expense $expense)
    {

        $getAccount     = AccountTransaction::where('expense_id', $expense->id)->where(function ($q) use ($expense) {
            return $expense->method->account ? $q->where('account_id', $expense->method->account->id) : '';
        })->first(['id', 'after_rekonsiliasi']);

        if ($getAccount) {
            if ($getAccount->after_rekonsiliasi == 'yes') {
                return response()->json([
                    'message'   => 'Transaksi ini sudah di lakukan rekonsiliasi dan tidak dapat di edit kembali', 
                    'status'    => false
                ], 422);
            }
        }

        try {

            DB::beginTransaction();

            $expense    = $this->cashIntOutObserver->updateData($request, $request->type, $expense);

            DB::commit();

            return response()->json([
                'message'   => 'Berhasil memperbaharui data',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Delete Expense
    |--------------------------------------------------------------------------
    */

    public function delete(Expense $expense)
    {
        // permission_check(Gate::denies('cash_int_deleet'), 403);

        $getAccount     = AccountTransaction::where('expense_id', $expense->id)->where(function ($q) use ($expense) {
            return $expense->method->account ? $q->where('account_id', $expense->method->account->id) : '';
        })->first(['id', 'after_rekonsiliasi']);

        if ($getAccount) {
            if ($getAccount->after_rekonsiliasi == 'yes') {
                return response()->json([
                    'message'   => 'Transaksi ini sudah di lakukan rekonsiliasi dan tidak dapat di hapus kembali', 
                    'status'    => false
                ], 422);
            }
        }

        try {

            DB::beginTransaction();

            $this->cashIntOutObserver->deleteData($expense);

            DB::commit();

            return response()->json([
                'message'   => 'Berhasil menghapus data',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }
}
