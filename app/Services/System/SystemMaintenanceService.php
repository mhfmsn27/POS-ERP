<?php

namespace App\Services\System;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SystemMaintenanceService
{
    /**
     * Membersihkan seluruh cache sistem (views, route, config, compiled).
     */
    public function clearAllCache(): array
    {
        $results = [];

        try {
            // 1. Clear Views Cache
            $viewPath = storage_path('framework/views');
            if (File::exists($viewPath)) {
                foreach (File::files($viewPath) as $file) {
                    if ($file->getFilename() !== '.gitignore') {
                        File::delete($file->getRealPath());
                    }
                }
            }
            $results['views'] = 'Cache Blade Views berhasil dibersihkan.';

            // 2. Clear Application Cache
            $cachePath = storage_path('framework/cache/data');
            if (File::exists($cachePath)) {
                File::cleanDirectory($cachePath);
            }
            $results['app_cache'] = 'Cache Data Aplikasi berhasil dibersihkan.';

            // 3. Clear Logs (> 7 days old)
            $logPath = storage_path('logs');
            if (File::exists($logPath)) {
                $cutoff = time() - (7 * 86400);
                foreach (File::files($logPath) as $f) {
                    if ($f->getExtension() === 'log' && $f->getMTime() < $cutoff) {
                        File::delete($f->getRealPath());
                    }
                }
            }
            $results['logs'] = 'File log kadaluarsa berhasil dirampingkan.';

            return [
                'status'  => true,
                'details' => $results,
                'message' => 'Seluruh cache sistem berhasil dibersihkan secara total.'
            ];
        } catch (\Throwable $e) {
            Log::error("Failed to clear cache: " . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Gagal membersihkan cache: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Menjalankan optimasi tabel basis data (OPTIMIZE TABLE).
     */
    public function optimizeTables(): array
    {
        $tablesToOptimize = [
            'transactions', 'sells', 'purchases', 'account_transactions',
            'stocks', 'products', 'product_variations', 'customers', 'suppliers'
        ];

        $optimized = [];
        try {
            foreach ($tablesToOptimize as $table) {
                try {
                    DB::statement("OPTIMIZE TABLE `{$table}`");
                    $optimized[] = $table;
                } catch (\Throwable $tblEx) {
                    // Ignore non-existent tables gracefully
                }
            }

            return [
                'status'           => true,
                'tables_optimized' => $optimized,
                'total'            => count($optimized),
                'message'          => count($optimized) . ' tabel basis data berhasil dioptimasi.'
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => false,
                'message' => 'Gagal mengoptimasi tabel: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mengambil metrik performa & kesehatan server lengkap.
     */
    public function getSystemMetrics(): array
    {
        $storagePath = storage_path();
        $freeSpace = @disk_free_space($storagePath) ?: 0;
        $totalSpace = @disk_total_space($storagePath) ?: 1;
        $usedSpace = $totalSpace - $freeSpace;
        $freePct = round(($freeSpace / $totalSpace) * 100, 1);
        $usedPct = round(($usedSpace / $totalSpace) * 100, 1);

        $dbVersion = 'Unknown';
        try {
            $ver = DB::select('SELECT VERSION() as v');
            $dbVersion = $ver[0]->v ?? 'MySQL';
        } catch (\Throwable $e) {}

        return [
            'app_name'        => 'POSHUB ACCOUNTING Enterprise',
            'app_env'         => config('app.env'),
            'php_version'     => PHP_VERSION,
            'database_engine' => $dbVersion,
            'server_os'       => PHP_OS . ' (' . php_uname('m') . ')',
            'storage'         => [
                'free_gb'       => round($freeSpace / (1024 * 1024 * 1024), 2),
                'used_gb'       => round($usedSpace / (1024 * 1024 * 1024), 2),
                'total_gb'      => round($totalSpace / (1024 * 1024 * 1024), 2),
                'free_percent'  => $freePct,
                'used_percent'  => $usedPct,
            ],
            'memory_usage_mb' => round(memory_get_usage(true) / (1024 * 1024), 2),
            'extensions'      => [
                'pdo_mysql' => extension_loaded('pdo_mysql'),
                'gd'        => extension_loaded('gd'),
                'mbstring'  => extension_loaded('mbstring'),
                'openssl'   => extension_loaded('openssl'),
                'curl'      => extension_loaded('curl'),
                'zip'       => extension_loaded('zip'),
            ],
        ];
    }
}
