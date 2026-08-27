<?php

namespace Poshub\Ecommerce\Controllers;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Admin\Bank;
use App\Models\Admin\NotificationSetting;
use App\Models\Admin\Setting;
use App\Models\Transaction\PaymentMethod;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionPayment;
use App\Observers\Notification\NotificationObserver;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\EcommerceApiSetting;
use Poshub\Ecommerce\Repositories\CourierRepository;
use Poshub\Ecommerce\Repositories\OrderRepository;
use Poshub\Ecommerce\Resources\TransactionDetailResource;

class OrderController extends Controller
{

      protected $orderRepository;
      protected $courierRepository;
      protected $notificationObserver;

      public function __construct(NotificationObserver $notificationObserver, OrderRepository $orderRepository, CourierRepository $courierRepository)
      {
            $this->courierRepository      = $courierRepository;
            $this->orderRepository        = $orderRepository;
            $this->notificationObserver   = $notificationObserver;
      }

      public function index()
      {
            $transactions     = $this->orderRepository->getData();
            $settings         = EcommerceApiSetting::where('store_id',session()->get('dfstore'))->first(['client_key', 'payment_method']);
            $ecommercebank    = PaymentMethod::where("store_id", session()->get('dfstore'))->orderBy("name", "asc")->get();
            $banks            = Bank::orderBy("bank_name", "asc")->get(['bank_name', 'id']);
            return view('ecommerce::account.orders', compact('transactions', 'settings', 'banks', 'ecommercebank'));
      }

      public function detail($id)
      {
            $transactions     = $this->orderRepository->getDetail($id);
            $settings         = Setting::first();

            return response()->json([
                  'data'      => TransactionDetailResource::make($transactions),
                  'logo'      => asset($settings->logo),
                  'status'    => true
            ]);
      }

      public function tracking($id)
      {
            $transactions           = $this->orderRepository->getDetail($id);
            $getTrack               = $this->courierRepository->getTracking($transactions);

            if ($getTrack['status'] == false) {
                  return response()->json([
                        'message'   => $getTrack['message'],
                        'status'    => false
                  ]);
            }

            return response()->json($getTrack);
      }

      public function received($id)
      {
            $transactions     = $this->orderRepository->getDetail($id);

            if ($transactions) {
                  $transactions->update([
                        'status'    => 'final'
                  ]);

                  $templates  = $this->notificationObserver->getTemplate('received_template');

                  if ($templates) {
                        $message = str_replace(
                              ['{storename}', '{customer}', '{noref}'],
                              [($transactions->store->name ?? ''), ($transactions->customer->name ?? ''), ($transactions->ref_no ?? '')],
                              $templates->message
                        );

                        $this->notificationObserver->sendMessage($message);
                  }
            }

            return response()->json([
                  'message'   => 'Pesanan berhasil di konfirmasi',
                  'status'    => true
            ]);
      }

      public function addPayment(Request $request, Transaction $transaction)
      {
            $this->validate($request, [
                  'from_bank'       => 'required',
                  'to_bank'         => 'required',
                  'no_rek'          => 'required',
                  'file'            => 'mimes:jpg,jpeg,png',
                  'amount'          => 'required'
            ]);

            if ($transaction->payment_status == 'paid') {
                  return response()->json([
                        'message'   => 'Sepertinya pesanan anda sudah lunas, silahkan refresh ulang kembali halaman',
                        'status'    => false
                  ]);
            }

            $payments = TransactionPayment::where("transaction_id", $transaction->id)->where("payment_status", "pending")->first();

            if ($payments) {
                  return response()->json([
                        'message'   => 'Bukti Pembayaran sudah pernah dikirimkan, silahkan tunggu hasil review terlebih dahulu',
                        'status'    => false
                  ]);
            }

            $banks      = Bank::find($request->from_bank);
            $ebank      = PaymentMethod::find($request->to_bank);

            $payments = TransactionPayment::create([
                  'transaction_id'        => $transaction->id,
                  'amount'                => Helper::fresh_aprice($request->amount),
                  'method'                => 'bank_transfer',
                  'payment_method_id'     => $ebank->id,
                  'account_id'            => $ebank->account->id ?? null,
                  'payment_status'        => 'pending',
                  'no_rek'                => $request->no_rek,
                  'bank_name'             => $banks->bank_name,
                  'to_bank'               => $ebank->name,
                  'file'                  => $request->file ? $this->uploadImage($request, 'file', 'ecommerce/payments/' . $transaction->id . '/') : '',
            ]);

            $notification     = NotificationSetting::withoutGlobalScopes()->where('store_id', $transaction->store_id)->first();
            $templates        = $this->notificationObserver->getTemplate('payment_template', $notification);

            if ($templates && $notification) {
                  $message = str_replace(
                        ['{storename}', '{customer}', '{noref}', '{amount}', '{paymentmethod}'],
                        [($transaction->store->name ?? ''), ($transaction->customer->name ?? ''), ($transaction->ref_no ?? ''), number_format($payments->amount), $ebank->name],
                        $templates->message
                  );

                  $this->notificationObserver->sendMessage($message, $notification->phone);
            }

            return response()->json([
                  'message'   => 'Bukti Pembayaran sudah kami kirimkan',
                  'status'    => true
            ]);
      }
}
