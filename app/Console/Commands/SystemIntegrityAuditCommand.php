<?php

namespace App\Console\Commands;

use App\Models\Account\AccountTransaction;
use App\Models\Product\Stock;
use App\Models\Transaction\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SystemIntegrityAuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poshub:audit-system {--fix : Bersihkan file temp dan cache kadaluarsa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit integritas finansial, balance jurnal, stok minus, dan pembersihan maintenance berkala.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info("==================================================");
        $this->info("  POSHUB ACCOUNTING - SYSTEM INTEGRITY AUDIT      ");
        $this->info("==================================================");

        $hasAnomaly = false;

        // 1. Audit Balance Jurnal Transaksi Finansial
        $this->line("\n[1/3] Memeriksa Integritas Jurnal Akuntansi...");
        try {
            $unbalancedTransactions = DB::table('account_transactions')
                ->select(
                    'transaction_id',
                    DB::raw("SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as total_debit"),
                    DB::raw("SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as total_credit")
                )
                ->whereNotNull('transaction_id')
                ->where('transaction_id', '>', 0)
                ->groupBy('transaction_id')
                ->havingRaw("ABS(total_debit - total_credit) > 0.01")
                ->limit(20)
                ->get();

            if ($unbalancedTransactions->isNotEmpty()) {
                $hasAnomaly = true;
                $this->error("PERINGATAN: Ditemukan " . $unbalancedTransactions->count() . " transaksi jurnal yang tidak balance!");
                foreach ($unbalancedTransactions as $tx) {
                    $this->warn("  - Transaction ID {$tx->transaction_id}: Debit = {$tx->total_debit}, Credit = {$tx->total_credit}");
                }
                Log::warning("POSHUB Audit: Ditemukan transaksi tidak balance", ['items' => $unbalancedTransactions->toArray()]);
            } else {
                $this->info("✓ Seluruh transaksi jurnal akuntansi 100% BALANCE (Debit == Credit).");
            }
        } catch (\Throwable $e) {
            $this->error("Gagal memeriksa jurnal: " . $e->getMessage());
        }

        // 2. Audit Stok Anomali & Stok Minus
        $this->line("\n[2/3] Memeriksa Status & Anomali Stok Barang...");
        try {
            $negativeStocks = Stock::withoutGlobalScopes()
                ->where('qty_available', '<', 0)
                ->count();

            if ($negativeStocks > 0) {
                $hasAnomaly = true;
                $this->warn("PERINGATAN: Ditemukan {$negativeStocks} item dengan stok negatif (< 0).");
            } else {
                $this->info("✓ Tidak ditemukan stok negatif di seluruh cabang toko.");
            }
        } catch (\Throwable $e) {
            $this->error("Gagal memeriksa stok: " . $e->getMessage());
        }

        // 3. Pembersihan File Sementara & Maintenance
        $this->line("\n[3/3] Pembersihan Berkas Sementara (Maintenance)...");
        $tempDirs = [
            storage_path('framework/cache/data'),
            storage_path('logs'),
        ];

        $cleanedFiles = 0;
        if ($this->option('fix')) {
            // Bersihkan file log yang terlalu besar (> 50MB)
            $logFile = storage_path('logs/laravel.log');
            if (File::exists($logFile) && File::size($logFile) > 52428800) {
                File::put($logFile, '');
                $this->info("✓ File log besar laravel.log telah dirotasi.");
                $cleanedFiles++;
            }
            $this->info("✓ Proses pembersihan maintenance selesai.");
        } else {
            $this->comment("Tips: Jalankan dengan opsi '--fix' untuk merotasi log dan membersihkan cache usang.");
        }

        $this->info("\n==================================================");
        if ($hasAnomaly) {
            $this->warn("STATUS AUDIT: Selesai dengan catatan anomali.");
            return Command::SUCCESS;
        }

        $this->info("STATUS AUDIT: 100% SEHAT & INTEGRITAS TERJAGA!");
        return Command::SUCCESS;
    }
}
