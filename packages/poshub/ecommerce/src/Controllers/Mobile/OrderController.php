<?php

namespace Poshub\Ecommerce\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Admin\Bank;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\Auth;
use Poshub\Ecommerce\Models\EcommerceApiSetting;
use Poshub\Ecommerce\Models\EcommerceBankTransansfer;
use Poshub\Ecommerce\Repositories\CourierRepository;
use Poshub\Ecommerce\Repositories\OrderRepository;

class OrderController extends Controller
{

    protected $orderRepository;
    protected $courierRepository;

    public function __construct(OrderRepository $orderRepository, CourierRepository $courierRepository)
    {
        $this->courierRepository      = $courierRepository;
        $this->orderRepository        = $orderRepository;
    }

    public function index(String $status)
    {
        $transactions     = Transaction::where("customer_id", Auth::guard('customers')->user()->id)->where("status", $status)->orderBy("id", "desc")->get();
        return view('ecommerce::mobile.account.order.index', compact('transactions', 'status'), ['page' => 'Pesanan Saya']);
    }

    public function detail(Transaction $transaction)
    {
        $settings         = EcommerceApiSetting::where('store_id',session()->get('dfstore'))->first(['client_key', 'payment_method']);
        $ecommercebank    = EcommerceBankTransansfer::orderBy("bank_name", "asc")->get();
        $banks            = Bank::orderBy("bank_name", "asc")->get(['bank_name', 'id']);
        return view('ecommerce::mobile.account.order.detail', compact('transaction', 'settings', 'ecommercebank', 'banks'), ['page' => 'Detail Transaksi - ' . $transaction->ref_no]);
    }
}
