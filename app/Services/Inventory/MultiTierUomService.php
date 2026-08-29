<?php

namespace App\Services\Inventory;

use App\Models\Product\Product;
use App\Models\Product\Stock;
use App\Models\Product\Unit;
use App\Models\Product\Variation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MultiTierUomService
{
    /**
     * Konversi Multi-Satuan Berjenjang (Contoh: 1 Dus = 12 Pak = 144 Pcs).
     *
     * @param float $qty
     * @param string $fromUnit ('dus', 'pak', 'pcs', 'lusin', 'karton', 'box')
     * @param string $toUnit   ('pcs', 'pak', 'dus')
     * @param array $customMultiplier ['dus_to_pak' => 12, 'pak_to_pcs' => 12]
     * @return float
     */
    public function convertUnits(
        float $qty,
        string $fromUnit,
        string $toUnit,
        array $customMultiplier = []
    ): float {
        $from = strtolower(trim($fromUnit));
        $to   = strtolower(trim($toUnit));

        if ($from === $to) return $qty;

        $pakToPcs = (float)($customMultiplier['pak_to_pcs'] ?? 12);
        $dusToPak = (float)($customMultiplier['dus_to_pak'] ?? 12);
        $dusToPcs = (float)($customMultiplier['dus_to_pcs'] ?? ($dusToPak * $pakToPcs));

        // 1. Konversi dari asal ke unit dasar (Pcs)
        $basePcs = $qty;
        if ($from === 'dus' || $from === 'karton' || $from === 'box') {
            $basePcs = $qty * $dusToPcs;
        } elseif ($from === 'pak' || $from === 'lusin') {
            $basePcs = $qty * $pakToPcs;
        }

        // 2. Konversi dari Pcs ke unit tujuan
        if ($to === 'pcs' || $to === 'satuan' || $to === 'unit') {
            return $basePcs;
        } elseif ($to === 'pak' || $to === 'lusin') {
            return $pakToPcs > 0 ? round($basePcs / $pakToPcs, 4) : $basePcs;
        } elseif ($to === 'dus' || $to === 'karton' || $to === 'box') {
            return $dusToPcs > 0 ? round($basePcs / $dusToPcs, 4) : $basePcs;
        }

        return $basePcs;
    }

    /**
     * Menghitung Matriks Harga & Diskon Grosir Berdasarkan Satuan Bertingkat.
     *
     * @param float $basePcsPrice Harga eceran per 1 Pcs
     * @param array $config ['dus_to_pak' => 12, 'pak_to_pcs' => 12, 'pak_discount_pct' => 5, 'dus_discount_pct' => 12]
     * @return array
     */
    public function getTieredUomPrices(float $basePcsPrice, array $config = []): array
    {
        $pakToPcs = (int)($config['pak_to_pcs'] ?? 12);
        $dusToPak = (int)($config['dus_to_pak'] ?? 12);
        $dusToPcs = $pakToPcs * $dusToPak;

        $pakDiscPct = (float)($config['pak_discount_pct'] ?? 5);
        $dusDiscPct = (float)($config['dus_discount_pct'] ?? 12);

        $pricePcs = $basePcsPrice;
        $pricePak = round(($basePcsPrice * $pakToPcs) * (1 - ($pakDiscPct / 100)));
        $priceDus = round(($basePcsPrice * $dusToPcs) * (1 - ($dusDiscPct / 100)));

        return [
            'base_unit'   => 'Pcs',
            'tiers'       => [
                [
                    'unit_name'       => 'Pcs',
                    'multiplier_pcs'  => 1,
                    'price'           => $pricePcs,
                    'discount_percent'=> 0,
                    'savings'         => 0
                ],
                [
                    'unit_name'       => 'Pak (Isi ' . $pakToPcs . ' Pcs)',
                    'multiplier_pcs'  => $pakToPcs,
                    'price'           => $pricePak,
                    'discount_percent'=> $pakDiscPct,
                    'savings'         => ($basePcsPrice * $pakToPcs) - $pricePak
                ],
                [
                    'unit_name'       => 'Dus (Isi ' . $dusToPcs . ' Pcs)',
                    'multiplier_pcs'  => $dusToPcs,
                    'price'           => $priceDus,
                    'discount_percent'=> $dusDiscPct,
                    'savings'         => ($basePcsPrice * $dusToPcs) - $priceDus
                ]
            ]
        ];
    }

    /**
     * Kalkulasi HPP Produksi Manufaktur (Bahan Baku + Tenaga Kerja + Biaya Overhead Pabrik).
     *
     * @param array $materials [['name' => 'Tepung', 'qty' => 10, 'unit_cost' => 12000], ...]
     * @param float $directLaborCost Biaya Tenaga Kerja Langsung
     * @param float $factoryOverheadCost Biaya Overhead (Listrik, Mesin, Gas, Sewa Pabrik)
     * @param float $outputQty Jumlah Hasil Produksi Barang Jadi
     * @return array
     */
    public function calculateManufacturingCosting(
        array $materials,
        float $directLaborCost,
        float $factoryOverheadCost,
        float $outputQty = 1
    ): array {
        $rawMaterialTotal = 0;
        $materialsBreakdown = [];

        foreach ($materials as $m) {
            $qty  = (float)($m['qty'] ?? 1);
            $cost = (float)($m['unit_cost'] ?? 0);
            $sub  = $qty * $cost;
            $rawMaterialTotal += $sub;

            $materialsBreakdown[] = [
                'name'      => $m['name'] ?? 'Bahan Baku',
                'qty'       => $qty,
                'unit_cost' => $cost,
                'subtotal'  => $sub
            ];
        }

        $totalProductionCost = $rawMaterialTotal + $directLaborCost + $factoryOverheadCost;
        $outputQty = max(1, $outputQty);
        $costPerUnit = round($totalProductionCost / $outputQty, 2);

        return [
            'raw_material_cost'      => $rawMaterialTotal,
            'direct_labor_cost'      => $directLaborCost,
            'factory_overhead_cost'  => $factoryOverheadCost,
            'total_production_cost'  => $totalProductionCost,
            'output_quantity'        => $outputQty,
            'cost_per_unit_hpp'      => $costPerUnit,
            'materials_breakdown'    => $materialsBreakdown,
            'cost_composition'       => [
                'raw_materials_pct'  => round(($rawMaterialTotal / $totalProductionCost) * 100, 1),
                'direct_labor_pct'   => round(($directLaborCost / $totalProductionCost) * 100, 1),
                'overhead_pct'       => round(($factoryOverheadCost / $totalProductionCost) * 100, 1)
            ]
        ];
    }
}
