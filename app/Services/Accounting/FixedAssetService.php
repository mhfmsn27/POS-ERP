<?php

namespace App\Services\Accounting;

use App\Models\Account\AccountTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class FixedAssetService
{
    /**
     * Mendaftarkan Aset Tetap baru ke dalam sistem.
     *
     * @param array $data
     * @param int $storeId
     * @param int $userId
     * @return array
     */
    public function registerAsset(array $data, int $storeId, int $userId): array
    {
        if (!Schema::hasTable('fixed_assets')) {
            return ['status' => false, 'message' => 'Tabel fixed_assets belum tersedia.'];
        }

        $cost = abs((float)($data['acquisition_cost'] ?? 0));
        $salvage = abs((float)($data['salvage_value'] ?? 0));
        $usefulLife = max(1, (int)($data['useful_life_months'] ?? 48));

        $code = $data['code'] ?? ('AST-' . date('Ymd') . '-' . rand(100, 999));

        $id = DB::table('fixed_assets')->insertGetId([
            'store_id'                 => $storeId,
            'name'                     => $data['name'] ?? 'Aset Tetap',
            'code'                     => $code,
            'category'                 => $data['category'] ?? 'equipment',
            'acquisition_date'         => $data['acquisition_date'] ?? now()->format('Y-m-d'),
            'acquisition_cost'         => $cost,
            'salvage_value'            => $salvage,
            'useful_life_months'       => $usefulLife,
            'depreciation_method'      => $data['depreciation_method'] ?? 'straight_line',
            'asset_account_id'         => $data['asset_account_id'] ?? null,
            'depreciation_account_id'  => $data['depreciation_account_id'] ?? null,
            'accumulated_account_id'   => $data['accumulated_account_id'] ?? null,
            'current_book_value'       => $cost,
            'status'                   => 'active',
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);

        return [
            'status'   => true,
            'asset_id' => $id,
            'code'     => $code,
            'message'  => "Aset tetap {$code} berhasil didaftarkan."
        ];
    }

    /**
     * Menjalankan proses depresiasi bulanan otomatis untuk seluruh aset aktif dan auto-posting jurnal ke COA.
     *
     * @param int|null $storeId
     * @param string|null $targetDate Format Y-m-d (default hari ini)
     * @param int|null $userId
     * @return array
     */
    public function processMonthlyDepreciation(?int $storeId = null, ?string $targetDate = null, ?int $userId = null): array
    {
        if (!Schema::hasTable('fixed_assets') || !Schema::hasTable('fixed_asset_depreciations')) {
            return ['status' => false, 'message' => 'Tabel aset tetap belum aktif.'];
        }

        $date = $targetDate ? date('Y-m-d', strtotime($targetDate)) : now()->format('Y-m-d');
        $execUser = $userId ?? auth()->id() ?? 1;

        $query = DB::table('fixed_assets')->where('status', 'active');
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $assets = $query->get();
        $processedCount = 0;
        $totalDepreciated = 0;

        $yearMonth = date('Y-m', strtotime($date));

        foreach ($assets as $asset) {
            // Cek apakah aset sudah disusutkan untuk periode bulan ini (Idempotency)
            $alreadyDepreciated = DB::table('fixed_asset_depreciations')
                ->where('asset_id', $asset->id)
                ->where('depreciation_date', 'like', "{$yearMonth}%")
                ->exists();

            if ($alreadyDepreciated) {
                continue;
            }

            $cost = (float)$asset->acquisition_cost;
            $salvage = (float)$asset->salvage_value;
            $months = max(1, (int)$asset->useful_life_months);
            $bookValue = (float)$asset->current_book_value;

            if ($bookValue <= $salvage) {
                // Aset sudah habis disusutkan
                DB::table('fixed_assets')->where('id', $asset->id)->update([
                    'status'     => 'fully_depreciated',
                    'updated_at' => now(),
                ]);
                continue;
            }

            // Hitung beban depresiasi bulanan (Metode Garis Lurus)
            $monthlyDep = round(($cost - $salvage) / $months, 2);
            if ($bookValue - $monthlyDep < $salvage) {
                $monthlyDep = $bookValue - $salvage;
            }

            if ($monthlyDep <= 0) {
                continue;
            }

            $newBookValue = $bookValue - $monthlyDep;

            DB::transaction(function () use ($asset, $date, $monthlyDep, $newBookValue, $execUser, &$processedCount, &$totalDepreciated) {
                $trxId = null;

                // Auto-Post Jurnal Akuntansi jika akun COA terpasang
                if ($asset->depreciation_account_id && $asset->accumulated_account_id) {
                    $refNo = 'DEP-' . $asset->code . '-' . date('Ym', strtotime($date));

                    // Debit Beban Penyusutan
                    $debit = AccountTransaction::create([
                        'account_id'     => $asset->depreciation_account_id,
                        'created_by'     => $execUser,
                        'amount'         => $monthlyDep,
                        'type'           => 'debit',
                        'sub_type'       => 'fixed_asset_depreciation',
                        'ref_no'         => $refNo,
                        'operation_date' => $date,
                        'name'           => "Beban Penyusutan Aset - {$asset->name} ({$asset->code})",
                    ]);
                    $trxId = $debit->id;

                    // Kredit Akumulasi Penyusutan
                    AccountTransaction::create([
                        'account_id'     => $asset->accumulated_account_id,
                        'created_by'     => $execUser,
                        'amount'         => $monthlyDep,
                        'type'           => 'credit',
                        'sub_type'       => 'fixed_asset_depreciation',
                        'ref_no'         => $refNo,
                        'operation_date' => $date,
                        'name'           => "Akumulasi Penyusutan Aset - {$asset->name} ({$asset->code})",
                    ]);
                }

                // Catat riwayat depresiasi
                DB::table('fixed_asset_depreciations')->insert([
                    'asset_id'               => $asset->id,
                    'depreciation_date'      => $date,
                    'amount'                 => $monthlyDep,
                    'book_value_after'       => $newBookValue,
                    'account_transaction_id' => $trxId,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);

                // Update nilai buku aset
                $newStatus = ($newBookValue <= $asset->salvage_value) ? 'fully_depreciated' : 'active';
                DB::table('fixed_assets')->where('id', $asset->id)->update([
                    'current_book_value' => $newBookValue,
                    'status'             => $newStatus,
                    'updated_at'         => now(),
                ]);

                $processedCount++;
                $totalDepreciated += $monthlyDep;
            });
        }

        return [
            'status'            => true,
            'processed_count'   => $processedCount,
            'total_depreciated' => $totalDepreciated,
            'message'           => "Depresiasi bulanan berhasil dijalankan untuk {$processedCount} aset tetap (Total: Rp " . number_format($totalDepreciated) . ")."
        ];
    }
}
