<?php

namespace App\Http\Controllers\Api\Reports\NonAccountant;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Observers\CashIntOut\CashIntOutObserver;
use App\Observers\Hrm\KasbonObserver;
use App\Observers\Hrm\SalaryObserver;
use App\Observers\Transaction\Sales\SaleReturnObserver;
use App\Observers\Transaction\Sales\SalesObserver;
use Illuminate\Http\Request;

class ProfitSellController extends Controller
{

    protected $salesObserver;
    protected $returSaleObserver;
    protected $kasbonObserver;
    protected $salaryObserver;
    protected $cashIntOutObserver;

    public function __construct(SalesObserver $salesObserver, SaleReturnObserver $returSaleObserver, KasbonObserver $kasbonObserver, SalaryObserver $salaryObserver, CashIntOutObserver $cashIntOutObserver)
    {
        $this->salesObserver        = $salesObserver;
        $this->returSaleObserver    = $returSaleObserver;
        $this->kasbonObserver       = $kasbonObserver;
        $this->salaryObserver       = $salaryObserver;
        $this->cashIntOutObserver   = $cashIntOutObserver;
    }


    public function index(Request $request)
    {


        $penjualan  = (float)$this->salesObserver->getData($request)->sum('total_before_tax');
        $jasa       = $this->salesObserver->getSalesProducts($request, 'no')->selectRaw('sum(unit_price_before_disc * (qty - qty_return)) as jumlah')->first();
        $discount   = (float)$this->salesObserver->getData($request)->sum('discount_final');
        $return     = (float)$this->returSaleObserver->getData($request)->sum('final_total');
        $cogs       = $this->salesObserver->getSellPurchase($request)->selectRaw('sum(purchase_price * qty) as jumlah')->first();
        $ppn        = (float)$this->salesObserver->getData($request)->sum('tax_final') - (float)$this->returSaleObserver->getData($request)->sum('tax_final');
        $kasbon     = (float)$this->kasbonObserver->getData($request, 'out')->sum('amount') - (float)$this->kasbonObserver->getData($request, 'int')->sum('amount');
        $salary     = (float)$this->salaryObserver->getData($request)->sum('total');
        $cashInt    = (float)$this->cashIntOutObserver->getData($request, 'cash_int')->sum('amount');
        $cashOut    = (float)$this->cashIntOutObserver->getData($request, 'expense')->sum('amount');


        $jumlahBeban        = $kasbon + $salary;
        $jumlahbebanlainnya = $cashOut;
        $jumlahpendapatan   = $cashInt;
        $jasa               = $jasa != null ? (float)$jasa->jumlah : 0;
        $cogs               = $cogs != null ? (float)$cogs->jumlah : 0;

        return response()->json([
            "store" => [
                "name" => "",
                "address" => "",
                "email" => "",
                "phone" => ""
            ],
            "pendapatan" => [
                "pendapatan"            => [
                    "pendapatan"            => $penjualan + $jasa,
                    "penjualan"             => $penjualan,
                    "return_penjualan"      => $return,
                    "jasa"                  => $jasa,
                    "diskon_penjualan"      => $discount
                ],
                "pendapatan_tetap"      => 0
            ],
            "jumlah_pendapatan"         => ($penjualan + $jasa) - ($return + $discount),
            "harga_pokok"               => [
                "cogs"                      => (float)$cogs,
                "cogs_tetap"                => 0
            ],
            "jumlah_harga_pokok_penjualan"  => (float)$cogs,
            "laba_kotor"                    => ($penjualan + $jasa) - ($return + $discount + $cogs),
            "beban_operasional"         => [],
            'salary'                    => $salary,
            'kasbon'                    => $kasbon,
            "jumlah_beban"              => (float)$jumlahBeban,
            "pendapatan_operasi"        => ($penjualan + $jasa) - ($return + $discount + $cogs + (float)$jumlahBeban),
            "pendapatan_dan_beban_lainnya" => [
                'pendapatan_lainnya'        => [],
                "cash_int"                  => $cashInt,
                "jumlah_pendapatan"         => $jumlahpendapatan,
                "cash_out"                  => $cashOut,
                "jumlah_beban"              => $jumlahbebanlainnya
            ],
            "laba_rugi_before_tax"      => ($penjualan + $jasa + $jumlahpendapatan) - ($return + $discount + $cogs + (float)$jumlahBeban + $jumlahbebanlainnya),
            'ppn'                       => $ppn,
            "laba_rugi_after_tax"       => ($penjualan + $jasa + $jumlahpendapatan) - ($return + $discount + $cogs + (float)$jumlahBeban + $jumlahbebanlainnya + $ppn),
        ]);
    }

    public function priode(Request $request)
    {

        $listdata   = [];
        $year       = Helper::setTimeZoneLocal($request->year);
        $year       = substr($year, 0, 4);

        foreach ($request->month as $month) {


            $penjualan  = (float)$this->salesObserver->getData($request, null, $year, $month['value'])->sum('total_before_tax');
            $jasa       = $this->salesObserver->getSalesProducts($request, 'no', $year, $month['value'])->selectRaw('sum(unit_price_before_disc * (qty - qty_return)) as jumlah')->first();
            $discount   = (float)$this->salesObserver->getData($request, null, $year, $month['value'])->sum('discount_final');
            $return     = (float)$this->returSaleObserver->getData($request, $year, $month['value'])->sum('final_total');
            $cogs       = $this->salesObserver->getSellPurchase($request, $year, $month['value'])->selectRaw('sum(purchase_price * qty) as jumlah')->first();
            $ppn        = (float)$this->salesObserver->getData($request, null, $year, $month['value'])->sum('tax_final') - (float)$this->returSaleObserver->getData($request, null, $year, $month['value'])->sum('tax_final');
            $kasbon     = (float)$this->kasbonObserver->getData($request, 'out', $year, $month['value'])->sum('amount') - (float)$this->kasbonObserver->getData($request, 'int', $year, $month['value'])->sum('amount');
            $salary     = (float)$this->salaryObserver->getData($request, $year, $month['value'])->sum('total');
            $cashInt    = (float)$this->cashIntOutObserver->getData($request, 'cash_int', $year, $month['value'])->sum('amount');
            $cashOut    = (float)$this->cashIntOutObserver->getData($request, 'expense', $year, $month['value'])->sum('amount');


            $jumlahBeban        = $kasbon + $salary;
            $jumlahbebanlainnya = $cashOut;
            $jumlahpendapatan   = $cashInt;
            $jasa               = $jasa != null ? (float)$jasa->jumlah : 0;
            $cogs               = $cogs != null ? (float)$cogs->jumlah : 0;


            $listdata[] = array(
                'year'          => $year,
                'month'         => $month['name'],
                "pendapatan" => [
                    "pendapatan"            => [
                        "pendapatan"            => $penjualan + $jasa,
                        "penjualan"             => $penjualan,
                        "return_penjualan"      => $return,
                        "jasa"                  => $jasa,
                        "diskon_penjualan"      => $discount
                    ],
                    "pendapatan_tetap"      => 0
                ],
                "jumlah_pendapatan"         => ($penjualan + $jasa) - ($return + $discount),
                "harga_pokok"               => [
                    "cogs"                      => (float)$cogs,
                    "cogs_tetap"                => 0
                ],
                "jumlah_harga_pokok_penjualan"  => (float)$cogs,
                "laba_kotor"                    => ($penjualan + $jasa) - ($return + $discount + $cogs),
                "beban_operasional"         => [],
                'salary'                    => $salary,
                'kasbon'                    => $kasbon,
                "jumlah_beban"              => (float)$jumlahBeban,
                "pendapatan_operasi"        => ($penjualan + $jasa) - ($return + $discount + $cogs + (float)$jumlahBeban),
                "pendapatan_dan_beban_lainnya" => [
                    "cash_int"                  => $cashInt,
                    "jumlah_pendapatan"         => $jumlahpendapatan,
                    "cash_out"                  => $cashOut,
                    "jumlah_beban"              => $jumlahbebanlainnya
                ],
                "laba_rugi_before_tax"      => ($penjualan + $jasa + $jumlahpendapatan) - ($return + $discount + $cogs + (float)$jumlahBeban + $jumlahbebanlainnya),
                'ppn'                       => $ppn,
                "laba_rugi_after_tax"       => ($penjualan + $jasa + $jumlahpendapatan) - ($return + $discount + $cogs + (float)$jumlahBeban + $jumlahbebanlainnya + $ppn),
            );
        }

        return response()->json([
            'list'  => $listdata
        ]);
    }
}
