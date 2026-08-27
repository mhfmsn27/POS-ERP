<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\JurnalUmumRequest;
use App\Http\Resources\Account\Jurnal\JurnalListDetailResource;
use App\Http\Resources\Account\Jurnal\JurnalListResource;
use App\Models\Transaction\Transaction;
use App\Observers\Account\JurnalUmumObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalUmumController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Jurnal Umum Controller
    |--------------------------------------------------------------------------
    */

    protected $jurnalObserver;
    protected $paymentObserver;

    public function __construct(JurnalUmumObserver $jurnalObserver)
    {
        $this->jurnalObserver       = $jurnalObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Jurnal List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {


        $limit  = $request->input('limit', 10);
        $data   = $this->jurnalObserver->getData($request);

        $totalRows  = $data->count();
        $cashint    = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => JurnalListResource::collection($cashint),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Jurnal
    |--------------------------------------------------------------------------
    */

    public function create(JurnalUmumRequest $request)
    {


        $items       = collect($request->items);
        $debitTotal  = round((float)$items->where('type', 'debit')->sum('amount'), 2);
        $creditTotal = round((float)$items->where('type', 'credit')->sum('amount_credit'), 2);

        if (abs($debitTotal - $creditTotal) > 0.001) {
            return response()->json([
                'message' => 'Kredit dan Debit anda tidak balance (Debit: ' . number_format($debitTotal, 2) . ', Kredit: ' . number_format($creditTotal, 2) . ')',
                'status' => false
            ], 422);
        }

        try {

            DB::beginTransaction();

            $this->jurnalObserver->createData($request);

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
    | 3. Detail Jurnal
    |--------------------------------------------------------------------------
    */

    public function detail(Transaction $transaction)
    {
        //   permission_check(Gate::denies('cash_int_update'), 403);


        return response()->json([
            'detail'      => JurnalListDetailResource::make($transaction),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Update Jurnal
    |--------------------------------------------------------------------------
    */

    public function update(JurnalUmumRequest $request, Transaction $transaction)
    {

        $items       = collect($request->items);
        $debitTotal  = round((float)$items->where('type', 'debit')->sum('amount'), 2);
        $creditTotal = round((float)$items->where('type', 'credit')->sum('amount_credit'), 2);

        if (abs($debitTotal - $creditTotal) > 0.001) {
            return response()->json([
                'message' => 'Kredit dan Debit anda tidak balance (Debit: ' . number_format($debitTotal, 2) . ', Kredit: ' . number_format($creditTotal, 2) . ')',
                'status' => false
            ], 422);
        }

        try {

            DB::beginTransaction();

            $this->jurnalObserver->updateData($request, $transaction);

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
    | 5. Delete Jurnal
    |--------------------------------------------------------------------------
    */

    public function delete(Transaction $transaction)
    {
        // permission_check(Gate::denies('cash_int_deleet'), 403);

        try {

            DB::beginTransaction();

            $this->jurnalObserver->deleteData($transaction);

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
