<?php

namespace App\Http\Controllers\Api\Crm;

use App\Http\Controllers\Controller;
use App\Services\Crm\CustomerLoyaltyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerLoyaltyController extends Controller
{
    protected CustomerLoyaltyService $loyaltyService;

    public function __construct(CustomerLoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Mengambil informasi kartu loyalitas pelanggan (Saldo poin & VIP Tier).
     */
    public function getLoyaltyCard(int $id): JsonResponse
    {
        $tier = $this->loyaltyService->getCustomerTier($id);
        $balance = $this->loyaltyService->getBalance($id);

        return response()->json([
            'status'         => true,
            'customer_id'    => $id,
            'points_balance' => $balance,
            'point_value_rp' => $balance * CustomerLoyaltyService::VALUE_PER_POINT,
            'tier'           => $tier,
        ]);
    }

    /**
     * Menukarkan poin menjadi potongan harga saat checkout POS.
     */
    public function redeem(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'points' => 'required|integer|min:1',
        ]);

        $storeId = my_store() ?? (auth()->user()->store_id ?? 1);
        $points = (int) $request->input('points');
        $transactionId = $request->input('transaction_id') ? (int)$request->input('transaction_id') : null;

        $result = $this->loyaltyService->redeemPoints($id, (int)$storeId, $points, $transactionId);
        $statusCode = $result['status'] ? 200 : 422;

        return response()->json($result, $statusCode);
    }
}
