<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Http\Resources\Administrator\Transaction\TransactionDetailResource;
use App\Models\Transaction\TransactionPackage;
use App\Observers\Administrator\PackageObserver;
use App\Observers\Merchant\PackageTransactionObserver;
use App\Observers\Saas\MerchantObserver;
use Illuminate\Http\Request;

class PackageTransactionController extends Controller
{
    protected $packageTransactionObserver;
    protected $packageObserver;
    protected $merchantObserver;

    public function __construct(PackageTransactionObserver $packageTransactionObserver, PackageObserver $packageObserver, MerchantObserver $merchantObserver)
    {
        $this->packageTransactionObserver       = $packageTransactionObserver;
        $this->packageObserver                  = $packageObserver;
        $this->merchantObserver                 = $merchantObserver;
    }

    public function index(Request $request)
    { 
        $data = $this->packageTransactionObserver->getData($request)->orderBy('created_at', 'desc')->paginate(20);

        $pagination       = array(
            'current_page'      => $data->currentPage(),
            'to_page'           => $data->lastPage(),
            'per_page'          => $data->perPage(),
            'first_item'        => $data->firstItem(),
            'last_item'         => $data->lastItem(),
            'links'             => $data->linkCollection()->toArray()
        );

        return view('super.transaction.index', ['page' => 'Daftar Transaksi Paket'], compact('data', 'pagination'));
    }

    public function detail(TransactionPackage $transaction)
    {
        return response()->json([
            'detail'          => TransactionDetailResource::make($transaction),
        ]);
    }

    public function create(Request $request)
    {
        $package    = $this->packageObserver->getData($request)->get();
        $merchant   = $this->merchantObserver->getData($request)->get();
        return view('super.transaction.create', ['page' => 'Buat Transaksi Manual'], compact('package', 'merchant'));
    }

    public function changeStatus(TransactionPackage $transaction)
    {

        $transaction->payment->update([
            'status'        => $transaction->payment->status == 'success' ? 'pending' : 'success',
        ]);

        $transaction->update([
            'status'                => $transaction->status == 'success' ? 'pending' : 'success',
            'payment_status'        => $transaction->payment_status == 'paid' ? 'due' : 'paid'
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'berhasil memperbaharui status'
        ]);
    }
}
