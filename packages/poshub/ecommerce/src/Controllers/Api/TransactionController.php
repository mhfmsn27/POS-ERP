<?php

namespace Poshub\Ecommerce\Controllers\Api;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Transaction\PaymentTransactionResource;
use App\Http\Resources\Transaction\SaleReturn\SaleReturnListResource;
use App\Http\Resources\Transaction\Sales\SaleItemsResource;
use App\Http\Resources\Transaction\Sales\SaleOtherInformationResource;
use App\Http\Resources\Transaction\Sales\SalesInformationResource;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting; 
use App\Models\Transaction\FakturPaymentDetail;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionDue;
use App\Models\Transaction\TransactionPayment;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Inventory\StockObserver;
use App\Observers\Notification\NotificationObserver;
use App\Observers\Transaction\Sales\SalesObserver;
use App\Observers\Transaction\Sales\ShippingProductObserver;
use App\Observers\Transaction\TransactionDueObserver;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Poshub\Ecommerce\Repositories\OrderRepository;
use Poshub\Ecommerce\Resources\Admin\SalesListResource; 

class TransactionController extends Controller
{

      protected $orderRepository;
      protected $transactionDueObserver;
      protected $ledgerTransactionObserver;
      protected $shippingProductObserver;
      protected $stockObserver;
      protected $ledgerObserver;
      protected $notificationObserver;
      protected $salesObserver;

      public function __construct(NotificationObserver $notificationObserver, OrderRepository $orderRepository, TransactionDueObserver $transactionDueObserver, LedgerTransactionObserver $ledgerTransactionObserver, ShippingProductObserver $shippingProductObserver, StockObserver $stockObserver, LedgerObserver $ledgerObserver, SalesObserver $salesObserver)
      {
            $this->orderRepository        = $orderRepository;
            $this->transactionDueObserver = $transactionDueObserver;
            $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
            $this->shippingProductObserver      = $shippingProductObserver;
            $this->stockObserver                = $stockObserver;
            $this->ledgerObserver               = $ledgerObserver;
            $this->notificationObserver         = $notificationObserver;
            $this->salesObserver                = $salesObserver;
      }

      public function index(Request $request)
      {
            $limit  = $request->limit ? $request->limit : 10;
            $data   = $this->salesObserver->getData($request);

            $totalRows      = $data->count();
            $transactions   = $data->paginate($limit);

            return response()->json([
                  'totalRows'     => $totalRows,
                  'transactions'  => SalesListResource::collection($transactions),
            ], 200);
      }

      public function detail($id)
      {

            $transaction      = Transaction::find($id);
            return response()->json([
                  'general_information'   => SalesInformationResource::make($transaction),
                  'resi_no'               => $transaction->shipping_detail->resi_no ?? '',
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

      public function confirmationPayment($id)
      {

            $payment          = TransactionPayment::find($id);
            if ($payment->payment_status == 'pending' && $payment->transaction->payment_status == 'due') {

                  try {

                        DB::beginTransaction();

                        $amount           = $payment->amount;
                        $transaction      = $payment->transaction;
                        if ($amount >= $payment->transaction->final_total) {

                              $amount     = $payment->transaction->final_total;
                              $payment->update([
                                    'amount'                => $amount,
                                    'payment_status'        => 'success'
                              ]);

                              $payment->transaction->update([
                                    'payment_status'        => 'paid',
                                    'status'                => 'ordered'
                              ]);
                        } else {
                              $payment->update([
                                    'amount'                => $amount,
                                    'payment_status'        => 'success'
                              ]);
                        }

                        $getTransaction         = Transaction::where("type", "sales_payment")->whereDate("created_at", date("Y-m-d"))->count() + 1;
                        $invoiceNumber          = sprintf("%05d", $getTransaction);
                        $refNo                  = Helper::transactionKey('PSL', $invoiceNumber);

                        $data                   = new Transaction();
                        $data->invoice_no       = $invoiceNumber;
                        $data->ref_no           = $refNo;
                        $data->transaction_date = $payment->created_at;

                        $data->type                 = 'sales_payment';
                        $data->payment_status       = 'due';
                        $data->status               = 'final';
                        $data->customer_id          = $transaction->customer_id;
                        $data->method_id            = $payment->payment_method_id;
                        $data->created_by           = auth()->user()->id;
                        $data->save();

                        $getTransaction         = TransactionDue::where("customer_id", $transaction->customer_id)->whereDate("date", substr($payment->created_at, 0, 10))->count() + 1;
                        $invoiceNumber          = sprintf("%05d", $getTransaction);
                        $refNo                  = 'TD' . date("Ymd") . '/' . $invoiceNumber;


                        $dueDetail = TransactionDue::create([
                              'no_ref'                => $refNo,
                              'customer_id'           => $transaction->customer_id,
                              'amount'                => $transaction->final_total,
                              'date'                  => $transaction->created_at,
                              'total_due_amount'      => $transaction->final_total,
                              'type'                  => 'hutang'
                        ]);

                        $this->ledgerTransactionObserver->createDueCustomer($dueDetail);

                        $totalPay       = $payment->amount;
                        $allocatedQty   = min($totalPay, $dueDetail->total_due);

                        $data->update([
                              'final_total'       => $allocatedQty
                        ]);

                        if ($allocatedQty > 0) {
                              $fakturDetail =  FakturPaymentDetail::create([
                                    'transaction_id'        => $data->id,
                                    'transaction_due_id'    => $dueDetail->id,
                                    'pay_amount'            => $allocatedQty,
                              ]);

                              $payment->update([
                                    'transaction_due_id'    => $dueDetail->id,
                                    'faktur_detail_id'      => $fakturDetail->id,
                                    'date'                  => $payment->created_at
                              ]);


                              $type           = 'debit';
                              $subType        = 'pay_customer_faktur';

                              $this->ledgerTransactionObserver->createPaymentFaktur($payment, $type, $subType);

                              $dueDetail->update([
                                    'total_due_amount'  => $dueDetail->total_due,
                                    'status'            => $dueDetail->total_due < 1 ? 'paid' : 'due'
                              ]);
                        }

                        // Commit stock reservation & award loyalty points
                        try {
                              app(\App\Services\Ecommerce\EcommerceStockReservationService::class)->commitReservation((int)$transaction->id);
                        } catch (\Throwable $e) {}

                        try {
                              if (!empty($transaction->customer_id)) {
                                    app(\App\Services\Crm\CustomerLoyaltyService::class)->addPointsForSale(
                                          (int)$transaction->customer_id,
                                          (int)$transaction->store_id,
                                          (int)$transaction->id,
                                          (float)$transaction->final_total
                                    );
                              }
                        } catch (\Throwable $e) {}

                        DB::commit();

                        return response()->json([
                              'status'          => true,
                              'message'         => 'Pembayaran sudah berhasil di setujui'
                        ], 200);
                  } catch (\Exception $e) {
                        DB::rollBack();

                        return response()->json([
                              'status'          => true,
                              'message'         => $e->getMessage()
                        ], 419);
                  }
            } else {
                  return response()->json([
                        'status'          => true,
                        'message'         => 'Pembayaran sudah tidak dapat di konfirmasi, transaksi sudah lunas'
                  ], 419);
            }
      }

      public function rejectedPayment($id)
      {
            $payment          = TransactionPayment::find($id);

            if ($payment->payment_status == 'success') {
                  return response()->json([
                        'status'          => false,
                        'message'         => 'Pembayaran sudah berstatus di terima'
                  ], 419);
            }

            $payment->delete();

            return response()->json([
                  'status'          => true,
                  'message'         => 'Pembayaran sudah berhasil di reject'
            ], 200);
      }

      public function sendOrder($id, Request $request)
      {

            $transaction      = Transaction::find($id);
            DB::beginTransaction();

            try {

                  foreach ($transaction->sell as $sell) {

                        $purchasesReady     = qty_having($sell->product_id, $sell->variation_id);
                        $this->shippingProductObserver->salesPurchaseCreate($purchasesReady, ($sell->qty - $sell->qty_return), $sell);

                        $stocks     = $this->stockObserver->createData($sell->variation, $transaction->warehouse_id);
                        $firstStock = $sell->variation->all_stock->sum('qty_available');
                        $endStock   = $sell->variation->all_stock->sum('qty_available') - $sell->qty;

                        if ($stocks) {
                              $stocks->update([
                                    'qty_available'     => $stocks->qty_available - $sell->qty
                              ]);
                        }

                        $this->ledgerTransactionObserver->addShippingAccount($sell, $endStock);
                        $this->stockObserver->createHistoryStock('sell', $sell, $transaction->id, $sell->qty, $firstStock, $endStock);

                        // Sale Account
                        if ($sell->product->sale_account) {
                              $accountSale = $sell->product->sale_account;

                              $saleAccount    = AccountTransaction::create([
                                    'account_id'                    => $accountSale->id,
                                    'transaction_id'                => $sell->transaction_id,
                                    'created_by'                    => auth()->user()->id,
                                    'amount'                        => ($sell->unit_price - ($sell->transaction->customer->tax_default == 'yes' ? $sell->tax_total : 0)) * $sell->qty,
                                    'item_id'                       => $sell->id,
                                    'type'                          => 'credit',
                                    'sub_type'                      => 'sale_faktur',
                                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('sale_faktur', $sell->created_at),
                                    'operation_date'                => $transaction->transaction_date,
                                    'name'                          => 'Faktur Penjualan - (' . $sell->product->name . ' ' . (float)$sell->qty . ' )'
                              ]);

                              $this->ledgerObserver->updateCashFlowAccount($accountSale);
                              $this->ledgerTransactionObserver->logAccountTransaction($saleAccount);
                        }

                        // Seamless FEFO Batch Stock Deduction for Ecommerce Fulfillment
                        try {
                              app(\App\Services\Inventory\BatchExpiryService::class)->deductStockFEFO(
                                    (int)$sell->variation_id,
                                    (int)$transaction->store_id,
                                    (float)$sell->qty
                              );
                        } catch (\Throwable $batchEx) {
                              \Illuminate\Support\Facades\Log::warning("Ecommerce FEFO deduction warning: " . $batchEx->getMessage());
                        }

                        // Cost Account
                        if ($sell->product->cost_account) {
                              $accountCost = $sell->product->cost_account;

                              $costAccount    = AccountTransaction::create([
                                    'account_id'                    => $accountCost->id,
                                    'transaction_id'                => $sell->transaction_id,
                                    'created_by'                    => auth()->user()->id,
                                    'amount'                        => (float)sell_purchase_total($sell->id),
                                    'item_id'                       => $sell->id,
                                    'type'                          => 'debit',
                                    'sub_type'                      => 'sale_faktur',
                                    'qty_history'                   => $endStock < 0 ?  (abs($endStock) > $sell->qty ? ($sell->qty - ($sell->qty * 2)) : $endStock) : 0,
                                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('sale_faktur', $sell->created_at),
                                    'operation_date'                => $transaction->transaction_date,
                                    'name'                          => 'Faktur Penjualan - (' . $sell->product->name . ' ' . (float)$sell->qty . ' )'
                              ]);

                              $this->ledgerObserver->updateCashFlowAccount($accountCost);
                              $this->ledgerTransactionObserver->logAccountTransaction($costAccount);
                        }

                        $this->stockObserver->updatePricing($sell->variation);
                  }

                  $transaction->update([
                        'status'    => 'transit'
                  ]);

                  $transaction->shipping_detail->update([
                        'resi_no'         => $request->resi_no
                  ]);

                  $taxFinal   = $transaction->sell()->selectRaw('sum(tax_total * qty) as jumlah')->first();
                  $settings   = AccountSetting::withoutGlobalScopes()->where('store_id', $transaction->store_id)->first(['cost_shipping_transaction', 'discount_sale', 'tax_output', 'pph_two_two', 'pph_two_tree'])
                        ?? AccountSetting::withoutGlobalScopes()->first(['cost_shipping_transaction', 'discount_sale', 'tax_output', 'pph_two_two', 'pph_two_tree']);

                  if ($settings) {
                        if ($settings->transaction_shipping_account) {

                              $dataAccount = $settings->transaction_shipping_account;

                              $accountShippingTransaction = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "sell_shipping")->first();

                              if ($accountShippingTransaction && $transaction->shipping_charges == 0) {
                                    $accountShippingTransactionNext = AccountTransaction::where(function ($query) use ($accountShippingTransaction) {
                                          $query->where("operation_date", ">", $accountShippingTransaction->operation_date)
                                                ->orWhere(function ($subQuery) use ($accountShippingTransaction) {
                                                      $subQuery->where("operation_date", "=", $accountShippingTransaction->operation_date)
                                                            ->where("id", "<", $accountShippingTransaction->id);
                                                });
                                    })
                                          ->where("account_id", $accountShippingTransaction->account_id)
                                          ->orderBy("operation_date", 'asc')
                                          ->orderBy("id", 'asc')->first();

                                    $accountShippingTransaction->forceDelete();

                                    if ($accountShippingTransactionNext) {
                                          $this->ledgerTransactionObserver->logAccountUpdate($accountShippingTransactionNext);
                                    } else {
                                          if ($dataAccount) {
                                                $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                                          }
                                    }
                              }

                              if ($accountShippingTransaction && $transaction->shipping_charges > 0) {

                                    $accountShippingTransaction->update([
                                          'created_by'                    => auth()->user()->id,
                                          'amount'                        => $transaction->shipping_charges,
                                    ]);

                                    $this->ledgerTransactionObserver->logAccountUpdate($accountShippingTransaction);
                              }

                              if (!$accountShippingTransaction && $transaction->shipping_charges > 0) {

                                    $accountShippingTransaction = AccountTransaction::create([
                                          'account_id'                    => $dataAccount->id,
                                          'transaction_id'                => $transaction->id,
                                          'created_by'                    => auth()->user()->id,
                                          'amount'                        => $transaction->shipping_charges,
                                          'type'                          => 'credit',
                                          'sub_type'                      => 'sell_shipping',
                                          'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('sale_faktur', $transaction->created_at),
                                          'operation_date'                => $transaction->transaction_date,
                                          'name'                          => 'Faktur Penjualan - ' . $transaction->ref_no
                                    ]);

                                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                                    $this->ledgerTransactionObserver->logAccountTransaction($accountShippingTransaction);
                              }
                        }

                        if ($settings->tax_output_account) {

                              // If Delete Account Tax
                              $dataAccount = $settings->tax_output_account;

                              $ppnKeluaran = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "tax_output")->where("tax_type", "1")->first();
                              if ($ppnKeluaran && $taxFinal->jumlah == 0) {

                                    $ppnKeluaranNext = AccountTransaction::where(function ($query) use ($ppnKeluaran) {
                                          $query->where("operation_date", ">", $ppnKeluaran->operation_date)
                                                ->orWhere(function ($subQuery) use ($ppnKeluaran) {
                                                      $subQuery->where("operation_date", "=", $ppnKeluaran->operation_date)
                                                            ->where("id", "<", $ppnKeluaran->id);
                                                });
                                    })
                                          ->where("account_id", $ppnKeluaran->account_id)
                                          ->orderBy("operation_date", 'asc')
                                          ->orderBy("id", 'asc')->first();

                                    $ppnKeluaran->forceDelete();

                                    if ($ppnKeluaranNext) {
                                          $this->ledgerTransactionObserver->logAccountUpdate($ppnKeluaranNext);
                                    } else {
                                          if ($dataAccount) {
                                                $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                                          }
                                    }
                              }

                              // If Update Account
                              if ($ppnKeluaran && $taxFinal->jumlah > 0) {

                                    $ppnKeluaran->update([
                                          'created_by'                    => auth()->user()->id,
                                          'amount'                        => $taxFinal->jumlah,
                                          'tax_gunggung'                  => $transaction->customer->npwp == null || $transaction->customer->npwp == '' ? 'yes' : 'no'
                                    ]);

                                    $this->ledgerTransactionObserver->logAccountUpdate($ppnKeluaran);
                              }

                              // If Create New
                              if (!$ppnKeluaran && $taxFinal->jumlah > 0 && my_store_detail()->tax_option == 'active') {

                                    $ppnKeluaran = AccountTransaction::create([
                                          'account_id'                    => $dataAccount->id,
                                          'transaction_id'                => $transaction->id,
                                          'created_by'                    => auth()->user()->id,
                                          'amount'                        => $taxFinal->jumlah,
                                          'tax_type'                      => '1',
                                          'type'                          => 'credit',
                                          'sub_type'                      => 'tax_output',
                                          'tax_gunggung'                  => $transaction->customer->npwp == null || $transaction->customer->npwp == '' ? 'yes' : 'no',
                                          'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('tax_output', $transaction->created_at),
                                          'operation_date'                => $transaction->transaction_date,
                                          'name'                          => 'PPN Keluaran - ' . $transaction->ref_no
                                    ]);

                                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                                    $this->ledgerTransactionObserver->logAccountTransaction($ppnKeluaran);
                              }
                        }
                  }

                  DB::commit();

                  // CRMHUB Omnichannel WhatsApp Shipping Notification
                  try {
                        $custPhone = $transaction->customer->phone ?? null;
                        if (!empty($custPhone)) {
                              $curirName = $transaction->shipping_detail->curir_name ?? 'Kurir';
                              $curirSvc  = $transaction->shipping_detail->curir_service ?? '';
                              $shipMsg = "Halo Kak *{$transaction->customer->name}*,\n\n"
                                    . "Pesanan *#{$transaction->ref_no}* telah *DIKIRIMKAN*!\n\n"
                                    . "🚚 Ekspedisi: *{$curirName} {$curirSvc}*\n"
                                    . "📦 No. Resi: *{$request->resi_no}*\n\n"
                                    . "Anda dapat memantau status pengiriman melalui menu riwayat pesanan.\n\n"
                                    . "Terima kasih telah berbelanja!";
                              \App\Jobs\SendWhatsappDigitalReceiptJob::dispatch($custPhone, $shipMsg);
                        }
                  } catch (\Throwable $waEx) {}

                  $templates  = $this->notificationObserver->getTemplate('shipping_template');

                  if ($templates) {
                        $message = str_replace(
                              ['{name}', '{noref}', '{ekspedisi}', '{noresi}'],
                              [($transaction->customer->name ?? ''), $transaction->ref_no, ($transaction->shipping_detail->curir_name ?? ''), ($transaction->shipping_detail->resi_no ?? '')],
                              $templates->message
                        );

                        $this->notificationObserver->sendMessage($message, ($transaction->customer->phone ?? '-'));
                  }

                  return response()->json([
                        'message'   => 'Berhasil memperbaharui status dan mengirimkan resi',
                        'status'    => true
                  ]);
            } catch (\Exception $e) {
                  DB::rollBack();
                  return response()->json([
                        'status'  => false,
                        'message' => $e->getMessage()
                  ], 500);
            }
      }

      public function showPayment(Transaction $transaction)
      {

            $list = array();
            foreach ($transaction->payment as $d) {
                  $item['id']     = $d->id;
                  $item['date']   = substr($d->created_at, 0, 10);
                  $item['user']   = $d->user->name ?? '';
                  $item['amount'] = number_format($d->amount);
                  $item['method'] = $d->payment_methode;
                  $item['account'] = $d->account->account->name ?? null;
                  $item['payment_status'] = $d->payment_status;
                  $item['bank_name'] = $d->bank_name;
                  $item['no_rek'] = $d->no_rek;
                  $item['an']     = $d->an;
                  $item['to_bank']    = $d->to_bank;
                  $item['file']       = asset($d->file);
                  $item['method'] = $d->method;
                  $list[]     = $item;
            }

            return response()->json([
                  'payment' => $list,
                  'message' => 'success'
            ]);
      }
}
