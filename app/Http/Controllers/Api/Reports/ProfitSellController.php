<?php

namespace App\Http\Controllers\Api\Reports;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Admin\AccountSetting; 
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use Illuminate\Http\Request;

class ProfitSellController extends Controller
{
    protected $legderObserver;
    protected $legderTransactionObserver;

    public function __construct(LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->legderObserver               = $ledgerObserver;
        $this->legderTransactionObserver    = $ledgerTransactionObserver;
    }

    public function index(Request $request)
    {
        $settings   = AccountSetting::first(['product_sale', 'product_retur_sale', 'product_discount_sale', 'discount_sale', 'product_cost', 'beban_operasional', 'beban_lainnya', 'pendapatan_lainnya', 'tax_output']);

        $penjualan  = (float)$settings->product_sale_account->balance_date($request->start_date, now()->parse($request->end_date)->addDay(), 'yes');
        $jasa       = (float)$settings->product_sale_account->balance_date($request->start_date, now()->parse($request->end_date)->addDay(), 'no');
        $discount   = (float)$settings->discount_account->balance_date($request->start_date, now()->parse($request->end_date)->addDay(), '', 'sell');
        $return     = (float)$settings->product_retur_sale_account->balance_date($request->start_date, now()->parse($request->end_date)->addDay(), '');
        $cogs       = (float)$settings->product_cost_account->balance_date($request->start_date, now()->parse($request->end_date)->addDay(), '', '', 'debit') - (float)$settings->product_cost_account->balance_date($request->start_date, now()->parse($request->end_date)->addDay(), '', '', 'credit');
        $ppn        = (float)$settings->tax_output_account->balance_date($request->start_date, now()->parse($request->end_date)->addDay(), '');

        $bebanoperasional   = [];
        $jumlahBeban        = 0;

        $bebanlainnya       = [];
        $jumlahbebanlainnya = 0;

        $pendapatanlainnya  = [];
        $jumlahpendapatan   = 0;

        // Beban Operasional
        foreach ($settings->beban_operasional_account->child as $operasional) {
            $i['name']      = $operasional->name;
            $amountData     = (float)$operasional->balance_date($request->start_date, now()->parse($request->end_date)->addDay(), '');
            $i['amount']    = number_format($amountData);
            $jumlahBeban        += $amountData;
            $bebanoperasional[] = $i;
        }

        // Beban Lainnya
        foreach ($settings->beban_lainnya_account->child as $other) {
            $i['name']      = $other->name;
            $amountData     = (float)$other->balance_date($request->start_date, now()->parse($request->end_date)->addDay(), '');
            $i['amount']    = number_format($amountData);
            $jumlahbebanlainnya    += $amountData;
            $bebanlainnya[]         = $i;
        }

        // Pendapatan Lainnya
        foreach ($settings->pendapatan_lainnya_account->child as $pendapatan) {
            $i['name']      = $pendapatan->name;
            $amountData     = (float)$pendapatan->balance_date($request->start_date, now()->parse($request->end_date)->addDay(), '');
            $i['amount']    = number_format($amountData);

            $jumlahpendapatan           += $amountData;
            $pendapatanlainnya[]         = $i;
        }
 

        return response()->json([
            "store" => [
                "name" => "",
                "address" => "",
                "email" => "",
                "phone" => ""
            ],
            "pendapatan" => [
                "pendapatan"            => [
                    "pendapatan"            => number_format($penjualan + $jasa),
                    "penjualan"             => number_format($penjualan),
                    "return_penjualan"      => number_format($return),
                    "jasa"                  => number_format($jasa),
                    "diskon_penjualan"      => number_format($discount)
                ],
                "pendapatan_tetap"      => 0
            ],
            "jumlah_pendapatan"         => number_format(($penjualan + $jasa) - ($return + $discount)),
            "harga_pokok"               => [
                "cogs"                      => number_format((float)$cogs),
                "cogs_tetap"                => 0
            ],
            "jumlah_harga_pokok_penjualan"  => number_format((float)$cogs),
            "laba_kotor"                    => number_format(($penjualan + $jasa) - ($return + $discount + $cogs)),
            "beban_operasional"         => $bebanoperasional,
            "jumlah_beban"              => (float)$jumlahBeban,
            "pendapatan_operasi"        => number_format(($penjualan + $jasa) - ($return + $discount + $cogs + (float)$jumlahBeban)),
            "pendapatan_dan_beban_lainnya" => [
                "pendapatan_lainnya"        => $pendapatanlainnya,
                "jumlah_pendapatan"         => number_format($jumlahpendapatan),
                "beban_lainnya"             => $bebanlainnya,
                "jumlah_beban"              => number_format($jumlahbebanlainnya)
            ],
            "laba_rugi_before_tax"      => number_format(($penjualan + $jasa + $jumlahpendapatan) - ($return + $discount + $cogs + (float)$jumlahBeban + $jumlahbebanlainnya)),
            'ppn'                       => number_format($ppn),
            "laba_rugi_after_tax"       => 0,
        ]);
    }

    public function priode(Request $request)
    {

        $listdata   = [];
        $year       = Helper::setTimeZoneLocal($request->year);
        $year       = substr($year, 0, 4);

        foreach ($request->month as $month) {

            $settings   = AccountSetting::first(['product_sale', 'product_retur_sale', 'product_discount_sale', 'discount_sale', 'product_cost', 'beban_operasional', 'beban_lainnya', 'pendapatan_lainnya', 'tax_output']);

            $penjualan  = (float)$settings->product_sale_account->balance_date('', '', 'yes', '', '', $year, $month['value']);
            $jasa       = (float)$settings->product_sale_account->balance_date('', '', 'no', '', '', $year, $month['value']);
            $discount   = (float)$settings->discount_account->balance_date('', '', '', 'sell', '', $year, $month['value']);
            $return     = (float)$settings->product_retur_sale_account->balance_date('', '', '', '', '', $year, $month['value']);
            $cogs       = (float)$settings->product_cost_account->balance_date('', '', '', '', 'debit', $year, $month['value']) - (float)$settings->product_cost_account->balance_date('', '', '', '', 'credit', $year, $month['value']);
            $ppn        = (float)$settings->tax_output_account->balance_date('', '', '', '', '', $year, $month['value']);

            $bebanoperasional   = [];
            $jumlahBeban        = 0;

            $bebanlainnya       = [];
            $jumlahbebanlainnya = 0;

            $pendapatanlainnya  = [];
            $jumlahpendapatan   = 0;

            // Beban Operasional
            foreach ($settings->beban_operasional_account->child as $operasional) {
                $i['name']      = $operasional->name;
                $i['amount']    = (float)$operasional->balance_date('', '', '', '', '', $year, $month['value']);

                $jumlahBeban        += $i['amount'];
                $bebanoperasional[] = $i;
            }

            // Beban Lainnya
            foreach ($settings->beban_lainnya_account->child as $other) {
                $i['name']      = $other->name;
                $i['amount']    = (float)$other->balance_date('', '', '', '', '', $year, $month['value']);

                $jumlahbebanlainnya    += $i['amount'];
                $bebanlainnya[]         = $i;
            }

            // Pendapatan Lainnya
            foreach ($settings->pendapatan_lainnya_account->child as $pendapatan) {
                $i['name']      = $pendapatan->name;
                $i['amount']    = (float)$pendapatan->balance_date('', '', '', '', '', $year, $month['value']);

                $jumlahpendapatan           += $i['amount'];
                $pendapatanlainnya[]         = $i;
            }

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
                "beban_operasional"         => $bebanoperasional,
                "jumlah_beban"              => (float)$jumlahBeban,
                "pendapatan_operasi"        => ($penjualan + $jasa) - ($return + $discount + $cogs + (float)$jumlahBeban),
                "pendapatan_dan_beban_lainnya" => [
                    "pendapatan_lainnya"        => $pendapatanlainnya,
                    "jumlah_pendapatan"         => $jumlahpendapatan,
                    "beban_lainnya"             => $bebanlainnya,
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
