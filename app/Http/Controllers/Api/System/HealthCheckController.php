<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HealthCheckController extends Controller
{
    /**
     * Check the health of the application and its critical dependencies.
     *
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        $status = 'healthy';
        $httpCode = 200;
        $checks = [];

        // 1. Database Check
        $dbStart = microtime(true);
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $dbDuration = round((microtime(true) - $dbStart) * 1000, 2);

            $checks['database'] = [
                'status'        => 'up',
                'response_time' => "{$dbDuration}ms",
            ];
        } catch (\Throwable $e) {
            $status = 'unhealthy';
            $httpCode = 503;
            $checks['database'] = [
                'status'  => 'down',
                'message' => $e->getMessage(),
            ];
        }

        // 2. Cache Check
        $cacheStart = microtime(true);
        try {
            $testKey = 'health_check_' . uniqid();
            Cache::put($testKey, 'ok', 10);
            $cacheVal = Cache::get($testKey);
            Cache::forget($testKey);

            $cacheDuration = round((microtime(true) - $cacheStart) * 1000, 2);

            if ($cacheVal === 'ok') {
                $checks['cache'] = [
                    'status'        => 'up',
                    'driver'        => config('cache.default'),
                    'response_time' => "{$cacheDuration}ms",
                ];
            } else {
                throw new \Exception('Cache write/read test failed');
            }
        } catch (\Throwable $e) {
            $status = 'degraded';
            $checks['cache'] = [
                'status'  => 'down',
                'driver'  => config('cache.default'),
                'message' => $e->getMessage(),
            ];
        }

        // 3. Storage Disk Check
        try {
            $storagePath = storage_path();
            $freeSpace = @disk_free_space($storagePath);
            $totalSpace = @disk_total_space($storagePath);

            if ($freeSpace !== false && $totalSpace !== false && $totalSpace > 0) {
                $freePercentage = round(($freeSpace / $totalSpace) * 100, 1);
                $freeGb = round($freeSpace / (1024 * 1024 * 1024), 2);
                $totalGb = round($totalSpace / (1024 * 1024 * 1024), 2);

                $diskStatus = ($freePercentage < 10) ? 'warning' : 'healthy';
                if ($diskStatus === 'warning' && $status === 'healthy') {
                    $status = 'degraded';
                }

                $checks['storage'] = [
                    'status'      => $diskStatus,
                    'free_space'  => "{$freeGb} GB ({$freePercentage}%)",
                    'total_space' => "{$totalGb} GB",
                ];
            } else {
                $checks['storage'] = [
                    'status'  => 'healthy',
                    'message' => 'Disk metrics not restricted by environment',
                ];
            }
        } catch (\Throwable $e) {
            $checks['storage'] = [
                'status'  => 'unknown',
                'message' => $e->getMessage(),
            ];
        }

        // 4. Failed Jobs Check
        try {
            $failedJobsCount = DB::table('failed_jobs')->count();
            $checks['queue'] = [
                'driver'      => config('queue.default'),
                'failed_jobs' => $failedJobsCount,
            ];
        } catch (\Throwable $e) {
            $checks['queue'] = [
                'driver'  => config('queue.default'),
                'status'  => 'table_not_found',
            ];
        }

        return response()->json([
            'status'      => $status,
            'timestamp'   => now()->toIso8601String(),
            'app_name'    => config('app.name', 'POSHUB'),
            'environment' => config('app.env'),
            'php_version' => PHP_VERSION,
            'checks'      => $checks,
        ], $httpCode);
    }
}
