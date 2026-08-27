<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Sales\ProcessSalesRequest;
use App\Http\Resources\Transaction\PaymentTransactionResource;
use App\Http\Resources\Transaction\SaleReturn\SaleReturnListResource;
use App\Http\Resources\Transaction\Sales\SaleItemsResource;
use App\Http\Resources\Transaction\Sales\SaleOtherInformationResource;
use App\Http\Resources\Transaction\Sales\SalesInformationResource;
use App\Http\Resources\Transaction\Sales\SalesListResource;
use App\Http\Resources\Transaction\Sales\SellHistoryPriceResource;
use App\Models\Account\AccountTransaction;
use App\Models\Transaction\Sell;
use App\Models\Transaction\Transaction;
use App\Observers\Tax\TaxNoRefObserver;
use App\Observers\Transaction\Sales\SalesObserver;
use App\Observers\Transaction\TransactionDueObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SalesController extends Controller
{
    protected $salesObserver;
    protected $transactionDueObserver;
    protected $taxNoRefObserver;

    public function __construct(SalesObserver $salesObserver, TransactionDueObserver $transactionDueObserver, TaxNoRefObserver $taxNoRefObserver)
    {
        $this->salesObserver            = $salesObserver;
        $this->transactionDueObserver   = $transactionDueObserver;
        $this->taxNoRefObserver         = $taxNoRefObserver;
    }

    public function index(Request $request)
    {

        abort_if(Gate::denies('sales_faktur_view'), 403);

        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->salesObserver->getData($request)->where(function ($q) {
            return $q->where("status", "received")->orWhere("status", "transit");
        });

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => SalesListResource::collection($transactions),
        ], 200);
    }

    public function store(ProcessSalesRequest $request)
    {

        abort_if(Gate::denies('add_sales_faktur'), 403);

        try {

            DB::beginTransaction();

            $transaction    = $this->salesObserver->createUpdateInformation($request, 'create');

            // if ($request->general_information['no_tax'] != null) {
            //     $taxRef     = $this->taxNoRefObserver->checkData($request->general_information['no_tax'])->where("transaction_id", null)->first();

            //     if (!$taxRef) {
            //         return response()->json([
            //             'message'   => 'Nomor Pajak Sudah digunakan atau tidak ada',
            //             'status'    => false
            //         ], 422);
            //     } else {
            //         $fromPrefix = explode(".", $taxRef->number);
            //         if ($fromPrefix[1] != substr(date('Y'), 2, 2)) {
            //             return response()->json([
            //                 'message'   => 'Nomor Pajak ini tidak dapat digunakan karena berbeda tahun',
            //                 'status'    => false
            //             ], 422);
            //         }

            //         $taxRef->update([
            //             'transaction_id'        => $transaction->id
            //         ]);
            //     }
            // } else {
            //     if (my_store_detail()->tax_option == 'active' && $transaction->customer->npwp != null) {
            //         return response()->json([
            //             'message'   => 'Pelanggan ini memiliki npwp, dan anda tidak memiliki nomor pajak aktif',
            //             'status'    => false
            //         ], 422);
            //     }
            // }

            $this->salesObserver->createOrUpdateTransaction($request, $transaction);
            $this->salesObserver->updateOtherInformation($request, $transaction);
            $this->transactionDueObserver->createBySellTransaction($transaction);

            if ($request->with_pay == true && $request->payment_information['pay_total'] > 0) {
                $this->salesObserver->createByTransaction($request, $transaction);
            }


            DB::commit();

            return response()->json([
                'message'       => "Informasi Penjualan berhasil di simpan",
                'transaction'   => $transaction->id,
                'detail'        => array(
                    'general_information'   => SalesInformationResource::make($transaction),
                    'items'                 => SaleItemsResource::collection($transaction->sell),
                    'payment_information'   => SaleOtherInformationResource::make($transaction),
                    'qty_sell'              => (int)$transaction->qty_sell,
                ),
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }

    public function update(ProcessSalesRequest $request, Transaction $transaction)
    {
        abort_if(Gate::denies('update_sales_faktur'), 403);

        try {

            if ($transaction->payment->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di edit, karena sudah melakukan pembayaran",
                    'status'        => false
                ], 422);
            }


            if ($transaction->purchase_return->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di edit, karena memiliki retur penjualan",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            // if ($request->general_information['no_tax'] != null) {

            //     if ($transaction->no_tax_ref) {
            //         if ($transaction->no_tax_ref->number != $request->general_information['no_tax']) {

            //             $taxRef     = $this->taxNoRefObserver->checkData($request->general_information['no_tax'])->where("transaction_id", null)->first();

            //             if (!$taxRef) {
            //                 return response()->json([
            //                     'message'   => 'Nomor Pajak Sudah digunakan sebelumnya',
            //                     'status'    => false
            //                 ], 422);
            //             }

            //             $transaction->no_tax_ref->update([
            //                 'transaction_id'    => null
            //             ]);

            //             $taxRef->update([
            //                 'transaction_id'    => $transaction->id
            //             ]);
            //         }
            //     }
            // } else {
            //     if (my_store_detail()->tax_option == 'active' && $transaction->customer->npwp != null) {
            //         return response()->json([
            //             'message'   => 'Pelanggan ini memiliki npwp, dan anda tidak memiliki nomor pajak aktif',
            //             'status'    => false
            //         ], 422);
            //     }
            // }

            $transaction    = $this->salesObserver->createUpdateInformation($request, 'update', $transaction);
            $this->salesObserver->createOrUpdateTransaction($request, $transaction);
            $this->salesObserver->updateOtherInformation($request, $transaction);

            if ($transaction->transaction_due != null) {
                $this->transactionDueObserver->updateBySellTransaction($transaction);

                $transaction->transaction_due->update([
                    'operation_date'        => $transaction->transaction_date,
                    'total_due_amount'      => $transaction->transaction_due->total_due,
                    'status'                => $transaction->transaction_due->total_due < 1  ? 'paid' : 'due',
                    'due_limit'             => $transaction->due_limit,
                    'due_end'               => $transaction->due_end
                ]);
            } else {
                $this->transactionDueObserver->createBySellTransaction($transaction);
            }

            if ($request->with_pay == true && $request->payment_information['pay_total'] > 0 && $transaction->due_total > 0) {
                $this->salesObserver->createByTransaction($request, $transaction);
            }

            DB::commit();

            return response()->json([
                'message'       => "Informasi Penjualan berhasil di perbaharui",
                'detail'        => array(
                    'general_information'   => SalesInformationResource::make($transaction),
                    'items'                 => SaleItemsResource::collection($transaction->sell),
                    'payment_information'   => SaleOtherInformationResource::make($transaction),
                    'qty_sell'              => (int)$transaction->qty_sell,
                ),
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

    public function deleteItem(Sell $sell)
    {

        abort_if(Gate::denies('update_sales_faktur'), 403);

        try {

            if ($sell->transaction->payment->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di hapus, karena sudah melakukan pembayaran",
                    'status'        => false
                ], 422);
            }


            if ($sell->transaction->purchase_return->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di hapus, karena memiliki retur penjualan",
                    'status'        => false
                ], 422);
            }


            DB::beginTransaction();

            $this->salesObserver->deleteItem($sell, $sell->transaction);

            if ($sell->transaction->transaction_due != null) {
                $this->transactionDueObserver->updateBySellTransaction($sell->transaction);

                $sell->transaction->transaction_due->update([
                    'total_due_amount'      => $sell->transaction->transaction_due->total_due,
                    'status'                => $sell->transaction->transaction_due->total_due < 1  ? 'paid' : 'due'
                ]);
            }

            DB::commit();

            return response()->json([
                'message'   => "Item Penjualan berhasil di hapus",
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

        abort_if(Gate::denies('sales_faktur_view'), 403);

        return response()->json([
            'general_information'   => SalesInformationResource::make($transaction),
            'qty_sell'              => (int)$transaction->qty_sell,
            'product_information'   => array(
                'discount_product_total'    => 0,
                'tax_product_total'         => 0,
                'subtotal'                  => 0,
                'items'                     => SaleItemsResource::collection($transaction->sell),
            ),
            'returns'               => SaleReturnListResource::collection($transaction->sales_return),
            'payment_information'   => SaleOtherInformationResource::make($transaction),
            'payments'              => PaymentTransactionResource::collection($transaction->payment)
        ], 200);
    }

    public function deleteDraft(Transaction $transaction)
    {

        abort_if(Gate::denies('delete_sales_faktur'), 403);

        try {


            if ($transaction->payment->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di hapus, karena sudah melakukan pembayaran",
                    'status'        => false
                ], 422);
            }


            if ($transaction->purchase_return->count() > 0) {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di hapus, karena memiliki retur penjualan",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            if ($transaction->no_tax_ref) {
                $transaction->no_tax_ref->update([
                    'transaction_id'        => null
                ]);
            }

            $this->salesObserver->deleteTransaction($transaction);

            DB::commit();

            return response()->json([
                'message'   => "Transaksi berhasil di hapus",
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

    public function editDraft(Transaction $transaction)
    {

        abort_if(Gate::denies('delete_sales_faktur'), 403);

        return response()->json([
            'message'               => "Pengambilan data draft transaksi Penjualan",
            'status'                => true,
            'qty_sell'              => (int)$transaction->qty_sell,
            'general_information'   => SalesInformationResource::make($transaction),
            'items'                 => SaleItemsResource::collection($transaction->sell),
            'payment_information'   => SaleOtherInformationResource::make($transaction),
        ], 200);
    }

    public function historysells(Request $request)
    {
        $data           = $this->salesObserver->getSalesProducts($request)->limit(20)->get();

        return response()->json([
            'totalRows'     => count($data),
            'sells'         => SellHistoryPriceResource::collection($data),
        ], 200);
    }

    public function print(Transaction $transaction)
    {
        return view('print.sales.faktur_penjualan', compact('transaction'));
    }

    public function printPengiriman(Transaction $transaction)
    {
        return view('print.sales.pengiriman', compact('transaction'));
    }

    public function label(Transaction $transaction)
    {
        return view('print.sales.label_pengiriman', compact('transaction'));
    }
}
