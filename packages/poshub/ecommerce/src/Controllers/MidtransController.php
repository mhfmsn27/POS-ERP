<?php

namespace Poshub\Ecommerce\Controllers;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Admin\NotificationSetting;
use App\Models\Admin\Store;
use App\Models\Transaction\Sell;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionPayment;
use App\Observers\Notification\NotificationObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Poshub\Ecommerce\Models\CustomerAddress;
use Poshub\Ecommerce\Models\EcommerceApiSetting;
use Poshub\Ecommerce\Models\TransactionShippingDetail;
use Poshub\Ecommerce\Repositories\AddressRepository;
use Poshub\Ecommerce\Repositories\CartRepository;
use Poshub\Ecommerce\Repositories\CourierRepository;
use Poshub\Ecommerce\Repositories\OrderRepository;
use Poshub\Ecommerce\Requests\TransactionRequest;
use Midtrans\Config;
use Midtrans\Snap;
use Ramsey\Uuid\Uuid;

class MidtransController extends Controller
{

      protected $cartRepository;
      protected $addressRepository;
      protected $couerierRepository;
      protected $orderRepository;
      protected $notificationObserver;

      public function __construct(NotificationObserver $notificationObserver, OrderRepository $orderRepository, CartRepository $cartRepository, AddressRepository $addressRepository, CourierRepository $couerierRepository)
      {
            $this->addressRepository      = $addressRepository;
            $this->cartRepository         = $cartRepository;
            $this->couerierRepository     = $couerierRepository;
            $this->orderRepository        = $orderRepository;
            $this->notificationObserver   = $notificationObserver;
      }


      public function create(TransactionRequest $request)
      {
            $cartFilter       = array_values(array_column($request->details, 'cart'));
            $carts            = $this->cartRepository->getCartByFilter($cartFilter);
            $address          = CustomerAddress::findOrFail($request->ongkir['from']);
            $stores           = Store::where("id", Session::get('dfstore'))->first(['sub_district_id', 'id', 'tax', 'tax_option', 'tax_one']);

            $componentForCost = array(
                  'weight'          => $carts['total_weight'],
                  'code'            => $request->ongkir['code'] == 'J&T' ? 'jnt' : $request->ongkir['code'],
                  'district'        => $stores->sub_district_id
            );

            if ($request->ongkir['code'] == 'kurir' && $request->ongkir['service'] == 'kurir') {
                  $getCost = array(
                        'price'     => $request->ongkir['price'],
                        'name'      => 'Instant Kurir',
                        'code'      => '',
                        'service'   => ''
                  );
            } else {
                  $getCost          = $this->couerierRepository->getCostByCode($address, $componentForCost);

                  $getCost = $getCost->filter(function ($item) use ($request) {
                        return false !== stristr($item['service'], $request->ongkir['service']);
                  })->first();

                  if (!$getCost) {
                        return response()->json([
                              'message'               => 'Kami mengalami masalah dengan layanan ongkir ini, silahkan ganti layanan ongkir dengan yang lain',
                              'status'                => true
                        ]);
                  }
            }

            DB::beginTransaction();

            try {


                  $getTransaction   = Transaction::where("type", "sell")->whereDate("created_at", date("Y-m-d"))->count() + 1;
                  $invoiceNumber    = sprintf("%05d", $getTransaction);
                  $refNo            = Helper::transactionKey('SL', $invoiceNumber);
                  $taxAmount        = $stores->tax > 0 && $carts['subtotal'] > 0 ? $stores->tax / 100 * $carts['subtotal'] : 0;
 
                  $transaction = Transaction::withoutGlobalScopes()->create([
                        'store_id'        => $stores->id,
                        'type'            => 'sell',
                        'status'          => 'hold',
                        'payment_status'  => 'due',
                        'customer_id'     => Auth::guard('customers')->user()->id,
                        'invoice_no'      => $invoiceNumber,
                        'ref_no'          => $refNo,
                        'courier_id'      => $request->ongkir['id'],
                        'transaction_date' => now(),
                        'total_before_tax' => $carts['subtotal'],
                        'tax_final'       => $taxAmount,
                        'tax_amount'      => $stores->tax,
                        'shipping_charges' => $getCost['price'],
                        'final_total'     => ($carts['subtotal'] + $taxAmount + $getCost['price']),
                        'created_by'      => null,
                        'type_sell'       => 'ecommerce',
                        'midtrans_ref'    => $refNo . '' . Auth::guard('customers')->user()->id . '' . rand()
                  ]);

                  $itemsToReserve = [];
                  foreach ($carts['carts'] as $item) {

                        $unitPrice        = $item->variation->selling_price ?? 0;
                        $tax              = $stores->tax_option == 'active' ? $stores->tax_one : 0;
                        $totalTax         = $unitPrice > 0 && $tax > 0 ? ($tax / 100) * ($unitPrice / (1 + $tax / 100)) : 0;
                        Sell::create([
                              'transaction_id'  => $transaction->id,
                              'store_id'        => $stores->id,
                              'product_id'      => $item->variation->product_id,
                              'variation_id'    => $item->variation_id,
                              'qty'             => $item->quantity,
                              'unit_qty'        => $item->quantity,
                              'unit_id'         => $item->variation->unit_id ?? null,
                              'unit_price'      => $unitPrice,
                              'unit_price_before_disc' => $unitPrice,
                              'item_tax'        => $tax,
                              'tax_total'                         => $totalTax,
                        ]);

                        $itemsToReserve[] = [
                              'product_id'   => $item->variation->product_id,
                              'variation_id' => $item->variation_id,
                              'qty'          => $item->quantity,
                        ];

                        $item->delete();
                  }

                  // Cadangkan stok secara atomik
                  try {
                        app(\App\Services\Ecommerce\EcommerceStockReservationService::class)->reserveStock(
                              (int)$stores->id,
                              (int)$transaction->id,
                              $itemsToReserve,
                              60
                        );
                  } catch (\Throwable $resEx) {
                        throw new \Exception($resEx->getMessage());
                  }

                  $totalTax   = $transaction->sell()->selectRaw('sum(tax_total * qty) as jumlah')->first();
                  $transaction->update([
                        'tax_final' => $totalTax->jumlah
                  ]);

                  $expedisi = TransactionShippingDetail::create([
                        'transaction_id'        => $transaction->id,
                        'curir_name'            => $getCost['name'],
                        'curir_code'            => $getCost['code'] == 'J&T' ? 'jnt' : $getCost['code'],
                        'curir_service'         => $getCost['service'],
                        'to_subdistrict_id'     => $address->sub_district_id,
                        'postal_code'           => $address->postal_code,
                        'phone'                 => $address->phone,
                        'address_detail'        => $address->address,
                        'name'                  => $address->name,

                  ]);

                  DB::commit();

                  $notification     = NotificationSetting::withoutGlobalScopes()->where('store_id', $transaction->store_id)->first();
                  $templates        = $this->notificationObserver->getTemplate('order_template', $notification);

                  if ($templates && $notification) {
                        $message = str_replace(
                              ['{storename}', '{customer}', '{noref}', '{price}', '{ekspedisi}'],
                              [($transaction->store->name ?? ''), ($transaction->customer->name ?? ''), $transaction->ref_no, number_format($transaction->final_total), $expedisi->curir_name . ' ' . $expedisi->curir_service],
                              $templates->message
                        );

                        $this->notificationObserver->sendMessage($message, $notification->phone);
                  }


                  return response()->json([
                        'message'         => 'Transaksi Berhasil di buat, silahkan selesaikan pembayaran',
                        'status'          => true
                  ]);
            } catch (\Exception $e) {

                  DB::rollBack();
                  return response()->json([
                        'status' => false,
                        'message' => $e->getMessage(),
                  ], 302);
            }
      }

      public function createSnapToken($transaction, TransactionPayment $payments)
      {
            $setting                = EcommerceApiSetting::where('store_id',session()->get('dfstore'))->first(['server_key', 'client_key', 'merchant_id']);
            $items                  = array();

            Config::$serverKey      = $setting->server_key;
            Config::$isProduction   = true;
            Config::$clientKey      = $setting->client_key;

            $jasakirim['id']                   = 'layanankirim';
            $jasakirim['price']                = $transaction->shipping_charges;
            $jasakirim['quantity']             = 1;
            $jasakirim['name']                 = "Biaya Jasa Kirim";
            $items[]                           = $jasakirim;

            foreach ($transaction->sell as $item) {
                  $array['id']                 = $item->product_id;
                  $array['price']              = $item->unit_price;
                  $array['quantity']           = $item->qty;
                  $array['name']               = $item->product->name ?? 'Produk';
                  $items[]                     = $array;
            }

            $params = array(
                  'transaction_details' => array(
                        'order_id' => $payments->id,
                        'gross_amount' => $payments->amount,
                  ),
                  'item_details'        => $items,
                  'customer_details' => array(
                        'first_name' => $transaction->customer->name,
                        'last_name' => '',
                        'email' => $transaction->customer->email,
                        'phone' => $transaction->customer->phone,
                  ),
            );

            try {
                  $snapToken = Snap::getSnapToken($params);
                  $payments->update([
                        'snap_token'      => $snapToken
                  ]);
                  return $snapToken;
            } catch (\Exception $e) {
                  return $e->getMessage();
            }
      }

      public function getSnapToken(Request $request, $id)
      {
            $transaction = Transaction::where("customer_id", Auth::guard('customers')->user()->id)->where("id", $id)->first();
            $tokenService = '';
            if ($transaction->payment_status == 'due') {
                  $payments = TransactionPayment::where("transaction_id", $id)->where("method", "midtrans")->first();
                  if ($payments) {
                        if ($payments->snap_token == null) {
                              $tokenService           = $this->createSnapToken($transaction, $payments);
                        } else {
                              $tokenService           = $payments->snap_token;
                        }
                  } else {
                        $payments = TransactionPayment::create([
                              'transaction_id'        => $transaction->id,
                              'amount'                => $transaction->final_total,
                              'method'                => 'midtrans',
                              'payment_status'        => 'due',
                        ]);

                        if ($payments->snap_token == null) {
                              $tokenService           = $this->createSnapToken($transaction, $payments);
                        } else {
                              $tokenService           = $payments->snap_token;
                        }
                  }
            }
            return response()->json([
                  'snap'            => $tokenService,
                  'status'          => true
            ]);
      }

      public function callBackMidtrans(Request $request)
      {
            $orderId            = $request->input('order_id');
            $statusCode         = $request->input('transaction_status');
            $midtransStatusCode = $request->input('status_code') ?: $statusCode;
            $grossAmount        = $request->input('gross_amount');
            $signature          = $request->input('signature_key') ?: $request->header('X-Midtrans-Signature');
            $paymentType        = $request->input('payment_type');

            $transactionPayment = TransactionPayment::where("snap_token", $orderId)
                  ->orWhere('id', $orderId)
                  ->first();

            $storeId = $transactionPayment->transaction->store_id ?? session()->get('dfstore') ?? 1;
            $setting = EcommerceApiSetting::where('store_id', $storeId)->first(['server_key', 'client_key', 'merchant_id'])
                  ?? EcommerceApiSetting::first();

            $serverKey = $setting ? $setting->server_key : '';
            $expectedSignature = hash('sha512', $orderId . $midtransStatusCode . $grossAmount . $serverKey);

            if ($signature && $signature !== $expectedSignature) {
                  return response('Invalid Signature', 400);
            }

            if ($transactionPayment) {
                  // Proses status pembayaran
                  if ($statusCode === 'capture' || $statusCode === 'settlement') {
                        $this->changeStatusPayment($transactionPayment, $paymentType);
                  } elseif (in_array($statusCode, ['cancel', 'deny', 'expire'])) {
                        // Release stock if expired or cancelled
                        try {
                              app(\App\Services\Ecommerce\EcommerceStockReservationService::class)->releaseExpiredReservations();
                        } catch (\Throwable $e) {}
                  }

                  $storeTargetId = $transactionPayment->transaction->store_id ?? $storeId;
                  $notification  = NotificationSetting::withoutGlobalScopes()->where('store_id', $storeTargetId)->first();
                  $templates     = $this->notificationObserver->getTemplate('payment_template', $notification);

                  if ($templates && $notification) {
                        $message = str_replace(
                              ['{storename}', '{customer}', '{noref}', '{amount}', '{paymentmethod}'],
                              [($transactionPayment->transaction->store->name ?? ''), ($transactionPayment->transaction->customer->name ?? ''), ($transactionPayment->transaction->ref_no ?? ''), number_format($transactionPayment->amount), $paymentType],
                              $templates->message
                        );

                        $this->notificationObserver->sendMessage($message, $notification->phone);
                  }

                  // Kirim respons sukses ke Midtrans
                  return response('OK');
            }

            return response('Transaction Not Found', 404);
      }



      public function changeStatusPayment(TransactionPayment $transactionPayment, $methode)
      {
            if ($transactionPayment) {
                  DB::transaction(function () use ($transactionPayment, $methode) {
                        $payment = TransactionPayment::where('id', $transactionPayment->id)->lockForUpdate()->first();
                        if ($payment && $payment->payment_status !== 'paid') {
                              $payment->update([
                                    'payment_status' => 'success',
                                    'method'         => $methode
                              ]);

                              if ($payment->transaction) {
                                    $payment->transaction->update([
                                          'status'         => 'ordered',
                                          'payment_status' => 'paid'
                                    ]);

                                    // Commit stock reservation
                                    try {
                                          app(\App\Services\Ecommerce\EcommerceStockReservationService::class)->commitReservation($payment->transaction->id);
                                    } catch (\Throwable $e) {}

                                    // Award Loyalty Points for online purchase
                                    try {
                                          if (!empty($payment->transaction->customer_id)) {
                                                app(\App\Services\Crm\CustomerLoyaltyService::class)->addPointsForSale(
                                                      (int)$payment->transaction->customer_id,
                                                      (int)$payment->transaction->store_id,
                                                      (int)$payment->transaction->id,
                                                      (float)$payment->transaction->final_total
                                                );
                                          }
                                    } catch (\Throwable $loyaltyEx) {}

                                    // Dispatch WhatsApp Order Payment Confirmation to customer
                                    try {
                                          if (!empty($payment->transaction->customer->phone)) {
                                                $custPhone = $payment->transaction->customer->phone;
                                                $orderMsg = "Halo Kak *{$payment->transaction->customer->name}*,\n\n"
                                                      . "Pembayaran untuk pesanan *#{$payment->transaction->ref_no}* sebesar *Rp " . number_format($payment->transaction->final_total) . "* telah *BERHASIL TERKONFIRMASI*.\n\n"
                                                      . "Pesanan Anda sedang disiapkan dan akan segera diproses pengiriman.\n\n"
                                                      . "Terima kasih telah berbelanja!";
                                                \App\Jobs\SendWhatsappDigitalReceiptJob::dispatch($custPhone, $orderMsg);
                                          }
                                    } catch (\Throwable $waEx) {}
                              }
                        }
                  });
            }
      }
}
