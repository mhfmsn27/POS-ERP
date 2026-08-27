<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Purchase\ProcessPurchaseRequest;
use App\Http\Resources\Transaction\PaymentTransactionResource;
use App\Http\Resources\Transaction\Purchase\PurchaseInformationResource;
use App\Http\Resources\Transaction\Purchase\PurchaseItemResource;
use App\Http\Resources\Transaction\Purchase\PurchaseListResource;
use App\Http\Resources\Transaction\Purchase\PurchaseOtherInformationResource;
use App\Http\Resources\Transaction\PurchaseReturn\PurchaseReturnListResource;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\Transaction;
use App\Observers\Transaction\Purchase\PurchaseObserver;
use App\Observers\Transaction\Purchase\PurchasePaymentObserver;
use App\Observers\Transaction\TransactionDueObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PurchaseController extends Controller
{
    protected $purchaseObserver;
    protected $transactionDueObserver;
    protected $purchasePaymentObserver;

    public function __construct(PurchaseObserver $purchaseObserver, TransactionDueObserver $transactionDueObserver, PurchasePaymentObserver $purchasePaymentObserver)
    {
        $this->purchaseObserver         = $purchaseObserver;
        $this->transactionDueObserver   = $transactionDueObserver;
        $this->purchasePaymentObserver  = $purchasePaymentObserver;
    }

    public function index(Request $request)
    {

        abort_if(Gate::denies('purchase_faktur_view'), 403);

        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->purchaseObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => PurchaseListResource::collection($transactions),
        ], 200);
    }

    public function store(ProcessPurchaseRequest $request)
    {

        abort_if(Gate::denies('add_purchase_faktur'), 403);

        try {

            DB::beginTransaction();

            $transaction    = $this->purchaseObserver->createUpdateInformation($request, 'create');
            $this->purchaseObserver->createOrUpdateTransaction($request, $transaction);
            $this->purchaseObserver->updateOtherInformation($request, $transaction);
            $this->transactionDueObserver->createByTransaction($transaction);

            if ($request->with_pay == true && $request->payment_information['pay_total'] > 0) {
                $this->purchasePaymentObserver->createByTransaction($request, $transaction);
            }

            DB::commit();

            return response()->json([
                'message'       => "Informasi Pembelian berhasil di simpan",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }

    public function update(ProcessPurchaseRequest $request, Transaction $transaction)
    {

        abort_if(Gate::denies('update_purchase_faktur'), 403);

        try {


            if ($transaction->payment->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di edit, karena sudah melakukan pembayaran",
                    'status'        => false
                ], 422);
            }


            if ($transaction->purchase_return->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di edit, karena memiliki retur pembelian",
                    'status'        => false
                ], 422);
            }


            if ($transaction->purchase()->where("qty_return", ">", 0)->count() > 0) {
                return response()->json([
                    'message'       => "Tidak dapat edit data, sebagian transaksi telah di lakukan return",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            $transaction    = $this->purchaseObserver->createUpdateInformation($request, 'update', $transaction);
            $this->purchaseObserver->createOrUpdateTransaction($request, $transaction);
            $this->purchaseObserver->updateOtherInformation($request, $transaction);


            if ($transaction->transaction_due != null) {
                $this->transactionDueObserver->updateByTransaction($transaction);

                $transaction->transaction_due->update([
                    'total_due_amount'      => $transaction->transaction_due->total_due,
                    'status'                => $transaction->transaction_due->total_due < 1  ? 'paid' : 'due',
                    'due_limit'             => $transaction->due_limit,
                    'due_end'               => $transaction->due_end
                ]);
            } else {
                $this->transactionDueObserver->createByTransaction($transaction);
            }

            if ($request->with_pay == true && $request->payment_information['pay_total'] > 0 && $transaction->due_total_po > 0) {
                $this->purchasePaymentObserver->createByTransaction($request, $transaction);
            }


            DB::commit();

            return response()->json([
                'message'       => "Informasi Pembelian berhasil di perbaharui",
                'transaction'   => $transaction->id,
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }

    public function deleteItem(Purchase $purchase)
    {

        abort_if(Gate::denies('update_purchase_faktur'), 403);

        try {

            if ($purchase->transaction->payment->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di hapus, karena sudah melakukan pembayaran",
                    'status'        => false
                ], 422);
            }


            if ($purchase->transaction->purchase_return->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di hapus, karena memiliki retur pembelian",
                    'status'        => false
                ], 422);
            }


            if ($purchase->transaction->purchase()->where("qty_return", ">", 0)->count() > 0) {
                return response()->json([
                    'message'       => "Tidak dapat menghapus data, sebagian transaksi telah di lakukan return",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            $this->purchaseObserver->deleteItem($purchase, $purchase->transaction);

            if ($purchase->transaction->transaction_due != null) {
                $this->transactionDueObserver->updateByTransaction($purchase->transaction);

                $purchase->transaction->transaction_due->update([
                    'total_due_amount'      => $purchase->transaction->transaction_due->total_due,
                    'status'                => $purchase->transaction->transaction_due->total_due < 1  ? 'paid' : 'due'
                ]);
            }

            DB::commit();
            return response()->json([
                'message'   => "Item pembelian berhasil di hapus",
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine()
            ], 409);
        }
    }

    public function detail(Transaction $transaction)
    {

        abort_if(Gate::denies('purchase_faktur_view'), 403);


        return response()->json([
            'general_information'   => PurchaseInformationResource::make($transaction),
            'product_information'   => array(
                'discount_product_total'    => 0,
                'tax_product_total'         => 0,
                'subtotal'                  => 0,
                'items'                     => PurchaseItemResource::collection($transaction->purchase),
            ),
            'returns'               => PurchaseReturnListResource::collection($transaction->purchase_return),
            'payment_information'   => PurchaseOtherInformationResource::make($transaction),
            'payments'              => PaymentTransactionResource::collection($transaction->payment)
        ], 200);
    }

    public function deleteDraft(Transaction $transaction)
    {

        abort_if(Gate::denies('delete_purchase_faktur'), 403);

        try {


            if ($transaction->payment->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di hapus, karena sudah melakukan pembayaran",
                    'status'        => false
                ], 422);
            }


            if ($transaction->purchase_return->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di hapus, karena memiliki retur pembelian",
                    'status'        => false
                ], 422);
            }


            if ($transaction->purchase()->where("qty_return", ">", 0)->count() > 0) {
                return response()->json([
                    'message'       => "Tidak dapat menghapus data, sebagian transaksi telah di lakukan return",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            $this->purchaseObserver->deleteTransaction($transaction);

            DB::commit();

            return response()->json([
                'message'   => "Transaksi berhasil di hapus",
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'all'       => $e,
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine()
            ], 409);
        }
    }

    public function editDraft(Transaction $transaction)
    {

        abort_if(Gate::denies('purchase_faktur_view'), 403);

        return response()->json([
            'message'               => "Pengambilan data draft transaksi pembelian",
            'status'                => true,
            'general_information'   => PurchaseInformationResource::make($transaction),
            'items'                 => PurchaseItemResource::collection($transaction->purchase),
            'payment_information'   => PurchaseOtherInformationResource::make($transaction),
        ], 200);
    }

    public function penerimaanprint(Transaction $transaction)
    {
        return view('print.purchase.penerimaan', compact('transaction'));
    }
}
