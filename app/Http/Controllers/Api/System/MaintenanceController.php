<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Services\System\SystemMaintenanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    protected SystemMaintenanceService $maintenanceService;

    public function __construct(SystemMaintenanceService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    /**
     * Tampilkan GUI Toolkit Pemeliharaan Sistem di Admin Panel.
     */
    public function viewIndex()
    {
        $metrics = $this->maintenanceService->getSystemMetrics();
        return view('admin.settings.maintenance.index', [
            'page'    => 'Pemeliharaan & Kesehatan Sistem (System Maintenance)',
            'metrics' => $metrics,
        ]);
    }

    /**
     * API JSON: Bersihkan seluruh cache aplikasi & views.
     */
    public function clearCache(): JsonResponse
    {
        $result = $this->maintenanceService->clearAllCache();
        $code = $result['status'] ? 200 : 500;
        return response()->json($result, $code);
    }

    /**
     * API JSON: Optimasi tabel database.
     */
    public function optimizeDb(): JsonResponse
    {
        $result = $this->maintenanceService->optimizeTables();
        $code = $result['status'] ? 200 : 500;
        return response()->json($result, $code);
    }

    /**
     * API JSON: Dapatkan metrik performa server real-time.
     */
    public function getMetrics(): JsonResponse
    {
        $metrics = $this->maintenanceService->getSystemMetrics();
        return response()->json([
            'status'  => true,
            'metrics' => $metrics,
        ]);
    }
}
