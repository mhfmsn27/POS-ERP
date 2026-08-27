<?php

namespace App\Http\Controllers\Api\Starter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Starter\PackageTransactionRequest;
use App\Http\Resources\Starter\Transaction\TransactionListResource;
use App\Models\Admin\InternalSetting;
use App\Models\Admin\NotificationSetting;
use App\Models\Admin\Store;
use App\Models\Transaction\TransactionPackage;
use App\Models\Transaction\TransactionPackagePayment;
use App\Observers\Administrator\PackageObserver;
use App\Observers\Merchant\PackageTransactionObserver;
use App\Observers\Notification\NotificationObserver;
use App\Observers\Starter\BusinessTransactionObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Ramsey\Uuid\Uuid;

class BusinessTransactionController extends Controller
{
    protected $businessTransactionObserver;
    protected $packageObserver;
    protected $packageTransactionObserver;
    protected $notificationObserver;

    public function __construct(BusinessTransactionObserver $businessTransactionObserver, PackageObserver $packageObserver, PackageTransactionObserver $packageTransactionObserver, NotificationObserver $notificationObserver)
    {
        $this->businessTransactionObserver      = $businessTransactionObserver;
        $this->packageObserver                  = $packageObserver;
        $this->packageTransactionObserver       = $packageTransactionObserver;
        $this->notificationObserver             = $notificationObserver;
    }

    public function midtransKey()
    {
        $settings               = InternalSetting::first(['midtrans_client']);
        return response()->json([
            'key'     => $settings->midtrans_client,
        ], 200);
    }

    public function index(Request $request)
    {
        $limit          = $request->limit ? $request->limit : 10;
        $transactions   = $this->businessTransactionObserver->getData($request);

        $totalRows      = $transactions->count();
        $transactions   = $transactions->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => TransactionListResource::collection($transactions),
        ], 200);
    }

    public function store(PackageTransactionRequest $request)
    {

        try {

            DB::beginTransaction();

            $package        = $this->packageObserver->packageById($request->package['id']);
            $store          = Store::where('id', $request->store['id'])->first(['id', 'merchant_id']);
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


            return response()->json([
                'status'        => true,
                'message'       => 'Transaksi pembelian paket berhasil di proses, silahkan lakukan pembayaran',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'        => false,
                'message'       => $e->getMessage(),
            ], 422);
        }
    }

    public function addPayment(TransactionPackage $transaction)
    {
        $tokenService           = '';

        if ($transaction->status == 'pending') {

            $payments = $transaction->payment;

          
            if (!$payments) {
                $payments       = $this->packageTransactionObserver->createPayment($transaction);
                $tokenService   = $this->createSnapToken($transaction, $payments);
            } else {
                if ($payments->expire_date != null && $payments->token == null) {
                   
                    if ($payments->expire_date->lt(now())) {
                        $payments->update([
                            'expire_date'           => now()->addHours(24),
                            'order_id'              => Uuid::uuid4()->toString(),
                        ]);

                        $tokenService           = $this->createSnapToken($transaction, $payments);
                    } else {

                        if ($payments->token == null) {
                            $payments->update([
                                'expire_date'           => now()->addHours(24),
                                'order_id'              => Uuid::uuid4()->toString(),
                            ]);

                            $tokenService           = $this->createSnapToken($transaction, $payments);
                        } else {
                            $tokenService           = $payments->token;
                        }
                    }
                } else {
                    $payments->update([
                        'expire_date'           => now()->addHours(24),
                        'order_id'              => Uuid::uuid4()->toString(),
                    ]);

                    $tokenService           = $this->createSnapToken($transaction, $payments);
                }
            }
        } else {
            return response()->json([
                'message'         => 'Transaksi ini sudah lunas',
                'status'          => false
            ]);
        }

        return response()->json([
            'snap'            => $tokenService,
            'status'          => true
        ]);
    }

    public function createSnapToken(TransactionPackage $transaction, TransactionPackagePayment $payments)
    {
        $items                  = array();
        $settings               = InternalSetting::first(['midtrans_client', 'midtrans_server']);

        Config::$serverKey      = $settings->midtrans_server;
        Config::$isProduction   = true;
        Config::$clientKey      = $settings->midtrans_client;


        if ($transaction->tax > 0) {
            $tax['id']                   = 'taxorder';
            $tax['price']                = $transaction->tax / 100 * $transaction->subtotal;
            $tax['quantity']             = 1;
            $tax['name']                 = "Pajak Pembelian";
            $items[]                     = $tax;
        }

        if ($transaction->subtotal > 0) {
            $package['id']                   = 'packageorder';
            $package['price']                = $transaction->subtotal;
            $package['quantity']             = 1;
            $package['name']                 = $transaction->package->name ?? '';
            $items[]                        = $package;
        }

        $params = [
            'transaction_details'   => [
                'order_id'              => $payments->order_id,
                'gross_amount'          => (int)$transaction->grand_total,
            ],
            'item_details'                => $items,
            'customer_details'      => [
                'first_name'            => auth()->user()->name,
                'email'                 => auth()->user()->email,
                'phone'                 => auth()->user()->phone,
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        $payments->update([
            'token'            => $snapToken
        ]);

        return $snapToken;
    }

    public function deleteTransaction(TransactionPackage $transaction)
    {
        if ($transaction->status != 'pending') {
            return response()->json([
                'status'        => true,
                'message'       => 'Transaksi ini sudah tidak dapat di hapus lagi',
            ], 422);
        }

        $transaction->payment()->delete();
        $transaction->delete();

        return response()->json([
            'status'        => true,
            'message'       => 'Data transaksi berhasil di hapus',
        ], 200);
    }
}
