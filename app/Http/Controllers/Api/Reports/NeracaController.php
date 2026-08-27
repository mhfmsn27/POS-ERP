<?php

namespace App\Http\Controllers\Api\Reports;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Admin\AccountSetting;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Account\TypeObserver;
use Illuminate\Http\Request;

class NeracaController extends Controller
{
    protected $legderObserver;
    protected $legderTransactionObserver;
    protected $typeObserver;

    public function __construct(LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver, TypeObserver $typeObserver)
    {
        $this->legderObserver               = $ledgerObserver;
        $this->legderTransactionObserver    = $ledgerTransactionObserver;
        $this->typeObserver                 = $typeObserver;
    }

    public function index(Request $request)
    {

        $date       = Helper::setTimeZoneLocal($request->start_date);

        // Cash and Bank
        $typeCash   = $this->typeObserver->getByType('1101');
        $kasBank    = [];
        $totalKas   = 0;


        if ($typeCash) {
            foreach ($typeCash->account as $account) {
                $i['name']      = $account->name;
                $amountData     = !empty($this->legderObserver->getByType($account->id, $date)) ? (float)$this->legderObserver->getByType($account->id, $date)->cashflow : 0;
                $i['amount']    = number_format($amountData);
                $totalKas       += $amountData;
                $kasBank[]      = $i;
            }
        }

        // Setara Cash
        $typeSetara = $this->typeObserver->getByType('1102');
        $setara     = [];
        $totalSetara = 0;

        if ($typeSetara) {
            foreach ($typeSetara->account as $account) {
                $i['name']      = $account->name;
                $amountData     = !empty($this->legderObserver->getByType($account->id, $date)) ? (float)$this->legderObserver->getByType($account->id, $date)->cashflow : 0;
                $i['amount']    = number_format($amountData);
                $totalSetara    += $amountData;
                $setara[]       = $i;
            }
        }

        // Piutang Usaha
        $typePiutang = $this->typeObserver->getByType('1103');
        $piutang     = [];
        $totalPiutang = 0;

        if ($typePiutang) {
            foreach ($typePiutang->account as $account) {
                $i['name']      = $account->name;
                $amountData     = !empty($this->legderObserver->getByType($account->id, $date)) ? (float)$this->legderObserver->getByType($account->id, $date)->cashflow : 0;
                $i['amount']    = number_format($amountData);
                $totalPiutang    += $amountData;
                $piutang[]       = $i;
            }
        }


        // Persediaan
        $typePersediaan     = $this->typeObserver->getByType('1104');
        $persediaan         = [];
        $totalPerseidaan    = 0;

        if ($typePersediaan) {
            foreach ($typePersediaan->account as $account) {
                $i['name']      = $account->name;
                $amountData     = !empty($this->legderObserver->getByType($account->id, $date)) ? (float)$this->legderObserver->getByType($account->id, $date)->cashflow : 0;
                $i['amount']    = number_format($amountData);
                $totalPerseidaan    += $amountData;
                $persediaan[]       = $i;
            }
        }

        // Asset Lancar Lainnya
        $typeOtherAsset     = $this->typeObserver->getByType('1105');
        $otherAsset         = [];
        $totalOther         = 0;

        if ($typeOtherAsset) {
            foreach ($typeOtherAsset->account as $account) {
                $i['name']      = $account->name;
                $amountData     = !empty($this->legderObserver->getByType($account->id, $date)) ? (float)$this->legderObserver->getByType($account->id, $date)->cashflow : 0;
                $i['amount']    = number_format($amountData);
                $totalOther         += $amountData;
                $otherAsset[]       = $i;
            }
        }

        // Asset Tetap
        $assetTetap         = $this->typeObserver->getByType('1106');
        $tetap              = [];
        $totalTetap         = 0;

        if ($assetTetap) {
            foreach ($assetTetap->account as $account) {
                $i['name']      = $account->name;
                $amountData     = !empty($this->legderObserver->getByType($account->id, $date)) ? (float)$this->legderObserver->getByType($account->id, $date)->cashflow : 0;
                $i['amount']    = number_format($amountData);
                $totalTetap     += $amountData;
                $tetap[]        = $i;
            }
        }

        // Penyusutan Asset
        $penyusutan         = $this->typeObserver->getByType('1106');
        $penyusutanItems              = [];
        $totalPenyusutan         = 0;

        if ($penyusutan) {
            foreach ($penyusutan->account as $account) {
                $i['name']      = $account->name;
                $amountData     = !empty($this->legderObserver->getByType($account->id, $date)) ? (float)$this->legderObserver->getByType($account->id, $date)->cashflow : 0;
                $i['amount']    = number_format($amountData);
                $totalPenyusutan     += $amountData;
                $penyusutanItems[]        = $i;
            }
        }

        // Hutang Usaha
        $hutang         = $this->typeObserver->getByType('2101');
        $hutangItems              = [];
        $totalHutang         = 0;

        if ($hutang) {
            foreach ($hutang->account as $account) {
                $i['name']      = $account->name;
                $amountData     = !empty($this->legderObserver->getByType($account->id, $date)) ? abs((float)$this->legderObserver->getByType($account->id, $date)->cashflow) : 0;
                $i['amount']    = number_format($amountData);
                $totalHutang     += $amountData;
                $hutangItems[]        = $i;
            }
        }

        // Kewajiban jangka pendek
        $liabilitas         = $this->typeObserver->getByType('2102');
        $liabilitasItems              = [];
        $totalLiabilitas         = 0;

        if ($liabilitas) {
            foreach ($liabilitas->account as $account) {
                $i['name']          = $account->name;
                $amountData         = !empty($this->legderObserver->getByType($account->id, $date)) ? (float)$this->legderObserver->getByType($account->id, $date)->cashflow : 0;
                $amountData         = $amountData > 0 ? $this->opposite($amountData) : abs($amountData);
                $i['amount']        = number_format($amountData);
                $totalLiabilitas    += $amountData;
                $liabilitasItems[]  = $i;
            }
        }

        $totalLiabilitas       = abs($totalLiabilitas);

        // Jangka Panjang
        $jangkaPanjang         = $this->typeObserver->getByType('2103');
        $panjangitems              = [];
        $totalPanjang         = 0;

        if ($jangkaPanjang) {
            foreach ($jangkaPanjang->account as $account) {
                $i['name']      = $account->name;
                $amountData     = !empty($this->legderObserver->getByType($account->id, $date)) ? abs((float)$this->legderObserver->getByType($account->id, $date)->cashflow) : 0;
                $i['amount']    = number_format($amountData);
                $totalPanjang     += $amountData;
                $panjangitems[]        = $i;
            }
        }

        // Modal
        $modal              = $this->typeObserver->getByType('3000');
        $modalItems         = [];
        $totalModal         = 0;

        if ($modal) {
            foreach ($modal->account as $account) {
                $i['name']      = $account->name;
                $amountData     = !empty($this->legderObserver->getByType($account->id, $date)) ? abs((float)$this->legderObserver->getByType($account->id, $date)->cashflow) : 0;
                $i['amount']    = number_format($amountData);
                $totalModal       += $amountData;
                $modalItems[]     = $i;
            }
        }

        $totalModal        = $totalModal;

        // Perhitungan
        $settings   = AccountSetting::first(['product_sale', 'product_retur_sale', 'product_discount_sale', 'discount_sale', 'product_cost', 'beban_operasional', 'beban_lainnya', 'pendapatan_lainnya', 'tax_output']);

        $penjualan  = (float)$settings->product_sale_account->balance_date('', $date, 'yes');
        $jasa       = (float)$settings->product_sale_account->balance_date('', $date, 'no');
        $discount   = (float)$settings->discount_account->balance_date('', $date, '', 'sell');
        $return     = (float)$settings->product_retur_sale_account->balance_date('', $date, '');
        $cogs       = (float)$settings->product_cost_account->balance_date('', $date, '', '', 'debit') - (float)$settings->product_cost_account->balance_date('', $date, '', '', 'credit');
        $ppn        = (float)$settings->tax_output_account->balance_date('', $date, '');

        // Pendapatan Lainnya
        $pendapatanLainnya  = 0;
        foreach ($settings->pendapatan_lainnya_account->child as $pendapatan) {
            $pendapatanLainnya           += (float)$pendapatan->balance_date('', $date, '');
        }

        // Beban Operasional
        $bebanOperasional = 0;
        foreach ($settings->beban_operasional_account->child as $operasional) {
            $bebanOperasional        += (float)$operasional->balance_date('', $date, '');
        }

        // Beban Lainnya
        $bebanLainnya   = 0;
        foreach ($settings->beban_lainnya_account->child as $other) {
            $bebanLainnya    += (float)$other->balance_date('', $date, '');
        }

        $totalPendapatan    = $penjualan + $pendapatanLainnya + $jasa - ($return + $discount);
        $beban              = $bebanOperasional + $bebanLainnya;
        $labaKotor          = $totalPendapatan - $cogs;
        $labaOperasional    = $labaKotor - $beban;
        $activa             = ($totalKas + $totalSetara + $totalPiutang + $totalPerseidaan + $totalOther) + ($totalPenyusutan + $totalTetap);
        $liability          = ($totalHutang + $totalLiabilitas) + $totalPanjang;
        $equity             = ($activa - $liability);


        return response()->json([
            "asset" => [
                "asset_lancar"      => [
                    'bank'              => [
                        "total"             => number_format((float)$totalKas),
                        "items"             => $kasBank,
                    ],
                    "setara_kas"        => [
                        "total"             => number_format((float)$totalSetara),
                        "items"             => $setara,
                    ],
                    "jumlah_kas"        => number_format((float)($totalKas + $totalSetara)),
                    "piutang"           => [
                        "total"             => number_format((float)$totalPiutang),
                        "items"             => $piutang,
                    ],
                    "persediaan"        => [
                        'total'             => number_format((float)$totalPerseidaan),
                        "items"             => $persediaan,
                    ],
                    "asset_lainnya"     => [
                        "total"             => number_format((float)$totalOther),
                        "items"             => $otherAsset,
                    ],
                    "jumlah"        => number_format((float)($totalKas + $totalSetara + $totalPiutang + $totalPerseidaan + $totalOther)),
                ],
                "tidak_lancar"      => [
                    "tetap"             => [
                        "items"             => $tetap,
                        "total"             => number_format((float)$totalTetap),
                    ],
                    "penyusutan"        => [
                        "items"             => $penyusutanItems,
                        "total"             => number_format((float)$totalPenyusutan),
                    ],
                    "jumlah"        => number_format((float)($totalPenyusutan + $totalTetap)),
                ],
                "jumlah"            => number_format((float)($totalKas + $totalSetara + $totalPiutang + $totalPerseidaan + $totalOther) + ($totalPenyusutan + $totalTetap)),
                "liabilitas"        => [
                    "hutang"            => [
                        "items"             => $hutangItems,
                        "total"             => number_format((float)$totalHutang),
                    ],
                    "lainnya"           => [
                        "items"             => $liabilitasItems,
                        "total"             => number_format((float)$totalLiabilitas),
                    ],
                    "jumlah"        => number_format((float)($totalHutang + $totalLiabilitas)),
                ],
                "liabilitas_panjang" => [
                    "total"            => number_format((float)$totalPanjang),
                    "items"            => $panjangitems,
                ],
                "jumlah_liabilitas" => number_format((float)$liability),
                "ekuitas" => [
                    "modal"         => [
                        "items"         => $modalItems,
                        "total"         => number_format($totalModal),
                    ],
                    "laba_tahunan"  => number_format((float)$labaOperasional),
                    'jumlah'        => number_format((float)($equity))
                ],
                "liabilitas_dan_ekuitas" => number_format((float)($liability + $equity)),
            ]
        ]);
    }

    public function priode(Request $request)
    {

        $listdata   = [];
        $year       = Helper::setTimeZoneLocal($request->year);
        $year       = substr($year, 0, 4);


        return response()->json([
            'list'  => $listdata
        ]);
    }

    function opposite($number)
    {
        return -$number;
    }
}
