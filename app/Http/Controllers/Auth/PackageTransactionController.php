<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin\InternalSetting;
use App\Models\Admin\NotificationSetting;
use App\Models\Admin\Store;
use App\Models\Transaction\TransactionPackage;
use App\Models\Transaction\TransactionPackagePayment;
use App\Observers\Administrator\PackageObserver;
use App\Observers\Merchant\PackageTransactionObserver;
use App\Observers\Notification\NotificationObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Ramsey\Uuid\Uuid;

class PackageTransactionController extends Controller
{
    protected $packageObserver;
    protected $packageTransactionObserver;
    protected $notificationObserver;

    public function __construct(NotificationObserver $notificationObserver, PackageObserver $packageObserver, PackageTransactionObserver $packageTransactionObserver)
    {
        $this->packageObserver              = $packageObserver;
        $this->packageTransactionObserver   = $packageTransactionObserver;
        $this->notificationObserver         = $notificationObserver;
    }

    public function index(Request $request)
    {
        return redirect()->route('store.choose')->with([
            'sukses' => 'POSHUB Enterprise Edition: Seluruh fitur dan cabang telah aktif permanen (Unlimited Lifetime).'
        ]);
    }

    public function order(Request $request, Store $store)
    {
        return redirect()->route('store.choose')->with([
            'sukses' => 'Cabang ' . $store->name . ' telah aktif dengan lisensi POSHUB Enterprise Lifetime.'
        ]);
    }

    public function transaction(Request $request, Store $store)
    {
        $this->validate($request, [
            'package'          => 'required',
        ]);

        if ($store->transaction_package_pending == false) {
            return redirect()->back()->with(['gagal' => 'Silahkan batalkan atau selesaikan transaksi sebelumnya terlebih dahulu']);
        }

        try {

            DB::beginTransaction();

            $package        = $this->packageObserver->packageById($request->package);
            $transaction    = $this->packageTransactionObserver->createData($store, $package);
            $this->packageTransactionObserver->createPayment($transaction);

            DB::commit();

            $settData   = NotificationSetting::withoutGlobalScopes()->where('store_id', null)->first();
            $templates  = $this->notificationObserver->getTemplate('package_template', $settData);

            if ($templates) {
                $message = str_replace(
                    ['{storename}', '{package}', '{packageprice}', '{packagetax}', '{name}'],
                    [$store->name, $package->name, number_format($package->price), number_format($transaction->tax), ($store->merchant->owner->name ?? '')],
                    $templates->message
                );

                $this->notificationObserver->sendMessage($message, ($settData->phone ?? '-'));
            }


            return redirect()->route('package.order')->with(['flash' => 'Berhasil melakukan transaksi']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }

   
    public function callBackMidtrans(Request $request)
    {

        $serverKey          = env('MIDTRANS_SERVER_KEY');
        $orderId            = $request->input('order_id');
        $statusCode         = $request->input('transaction_status');
        $grossAmount        = $request->input('gross_amount');
        $signature          = $request->header('X-Midtrans-Signature');
        $expectedSignature  = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $paymentType        = $request->input('payment_type');

        if ($signature !== $expectedSignature) {
            return response('Invalid', 400);
        }

        $transactionPayment = TransactionPackagePayment::where("snap", $orderId)->first();

        if ($transactionPayment) {
            // Proses status pembayaran
            if ($statusCode === 'capture') {
                $this->changeStatusPayment($transactionPayment, $paymentType);
            } elseif ($statusCode === 'settlement') {
                $this->changeStatusPayment($transactionPayment, $paymentType);
            } elseif ($statusCode === 'cancel' || $statusCode === 'deny' || $statusCode === 'expire') {
            } elseif ($statusCode === 'pending') {
            } else {
            }

            // Proses notifikasi WhatsApp
            $settData   = NotificationSetting::withoutGlobalScopes()->where('store_id', null)->first();
            $templates  = $this->notificationObserver->getTemplate('payment_package_template', $settData);

            if ($templates) {
                $message = str_replace(
                    ['{storename}', '{package}', '{packageprice}', '{packagetax}', '{amount}'],
                    [($transactionPayment->store->name ?? ''), ($transactionPayment->transaction->package->name ?? ''), number_format($transactionPayment->transaction->subtotal ?? 0), number_format($transactionPayment->transaction->tax ?? 0), number_format($transactionPayment->amount)],
                    $templates->message
                );

                $this->notificationObserver->sendMessage($message, ($settData->phone ?? '-'));
            }

            // Kirim respons sukses ke Midtrans
            return response('OK', 200);
        }

        return response('Invalid', 400);
    }

    public function changeStatusPayment(TransactionPackagePayment $transactionPayment, $methode)
    {

        if ($transactionPayment) {
            if ($transactionPayment->status == 'pending') {
                $transactionPayment->update([
                    'status'        => 'success',
                ]);

                $transactionPayment->transaction->update([
                    'status'                => 'success',
                    'payment_status'        => 'paid'
                ]);
            }
        }
    }
}
