<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Services\Inventory\AutoPurchaseOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockAlertPoController extends Controller
{
    protected AutoPurchaseOrderService $autoPoService;

    public function __construct(AutoPurchaseOrderService $autoPoService)
    {
        $this->autoPoService = $autoPoService;
    }

    /**
     * Trigger pembuatan Draf PO otomatis dari Stock Alert.
     */
    public function generate(Request $request): JsonResponse
    {
        $storeId = my_store() ?? (auth()->user()->store_id ?? 1);
        $userId  = auth()->id() ?? 1;

        $result = $this->autoPoService->generateDraftPoFromStockAlert((int)$storeId, (int)$userId);
        $statusCode = $result['status'] ? 201 : 422;

        return response()->json($result, $statusCode);
    }
}
