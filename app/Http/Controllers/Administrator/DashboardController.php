<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Admin\Merchant;
use App\Models\Admin\Store;
use App\Models\Transaction\TransactionPackage; 
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $summary    = array(
            'merchant_monthly'      => Merchant::whereYear("created_at", date("Y"))->whereMonth("created_at", date("m"))->count(),
            'merchant_daily'        => Merchant::whereDate("created_at", date("Y-m-d"))->count(),
            'monthly_payment'       => TransactionPackage::where("status", "success")->whereYear("created_at", date("Y"))->whereMonth("created_at", date("m"))->sum("grand_total"),
            'total_payment'         => TransactionPackage::where("status", "success")->sum("grand_total")
        );

        $mustFollow     = TransactionPackage::select('merchant_id', 'store_id', DB::raw('MAX(end_date) as last_expire_date'))
            ->where("store_id", "!=", null)
            ->where("status", "success")
            ->groupBy('merchant_id', 'store_id')
            ->havingRaw('MAX(end_date) <= DATE_ADD(CURDATE(), INTERVAL 4 DAY)')
            ->havingRaw('MAX(end_date) >= DATE_ADD(CURDATE(), INTERVAL 0 DAY)')
            ->limit(10)
            ->get();

        $merchantNotPackage = Merchant::where('created_at', '>', now()->subDays(30)->endOfDay())->where(function ($q) {
            return $q->whereHas('transaction', function ($query) {
                return  $query->selectRaw("count(id) as total")->havingRaw('count(id) = ?', [0]);
            });
        })->limit(10)->get();

        $notPayment = TransactionPackage::where("status", "pending")->where('created_at', '>', now()->subDays(7)->endOfDay())->orderBy("created_at", "desc")->limit(10)->get();
        $business   = Store::where('created_at', '>', now()->subDays(7)->endOfDay())->limit(10)->get();

        return view('super.index', ['page' => 'Administrator'], compact('summary', 'mustFollow', 'merchantNotPackage', 'notPayment', 'business'));
    }

    public function analisis()
    {
        $penjualanData  = array();
        $penjualanDate  = array();

        $transactionDate = TransactionPackage::selectRaw('LEFT(created_at,10) as date, sum(grand_total) as total')->where("status", "success")->groupBy('date')->get();

        foreach ($transactionDate as $sell) {
            $penjualanDate[]    = Carbon::parse($sell->date, "UTC")->setTimezone(auth()->user()->timezone)->format("d, M Y");
            $penjualanData[]    = (int)$sell->total;
        }

        return response()->json([
            'analisis_penjualan'    => array(
                'penjualan'         => $penjualanData,
                'tanggal'           => $penjualanDate,
            ),
        ]);
    }
}
