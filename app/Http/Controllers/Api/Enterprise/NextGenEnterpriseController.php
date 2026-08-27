<?php

namespace App\Http\Controllers\Api\Enterprise;

use App\Http\Controllers\Controller;
use App\Services\Accounting\PeriodClosingService;
use App\Services\Ecommerce\WholesaleB2bService;
use App\Services\Inventory\InventoryReorderAiService;
use App\Services\Logistics\DeliveryDispatchService;
use App\Services\Payment\StoreCreditGiftCardService;
use App\Services\Pos\SplitBillService;
use App\Services\Pos\TableManagementService;
use App\Services\Security\CashierFraudDetectorService;
use App\Services\Webhook\WhatsappInteractiveBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NextGenEnterpriseController extends Controller
{
    protected WholesaleB2bService $wholesaleService;
    protected StoreCreditGiftCardService $giftCardService;
    protected PeriodClosingService $closingService;
    protected CashierFraudDetectorService $fraudService;
    protected InventoryReorderAiService $reorderService;
    protected DeliveryDispatchService $dispatchService;
    protected SplitBillService $splitBillService;
    protected TableManagementService $tableService;
    protected WhatsappInteractiveBotService $botService;

    public function __construct(
        WholesaleB2bService $wholesaleService,
        StoreCreditGiftCardService $giftCardService,
        PeriodClosingService $closingService,
        CashierFraudDetectorService $fraudService,
        InventoryReorderAiService $reorderService,
        DeliveryDispatchService $dispatchService,
        SplitBillService $splitBillService,
        TableManagementService $tableService,
        WhatsappInteractiveBotService $botService
    ) {
        $this->wholesaleService = $wholesaleService;
        $this->giftCardService = $giftCardService;
        $this->closingService = $closingService;
        $this->fraudService = $fraudService;
        $this->reorderService = $reorderService;
        $this->dispatchService = $dispatchService;
        $this->splitBillService = $splitBillService;
        $this->tableService = $tableService;
        $this->botService = $botService;
    }

    // 1. Wholesale & Dynamic Tier Pricing
    public function setTierPrice(Request $request)
    {
        $request->validate(['product_id' => 'required|integer', 'min_quantity' => 'required|numeric|min:1', 'tier_price' => 'required|numeric|min:0']);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->wholesaleService->setTierPrice(
            (int)$storeId,
            (int)$request->input('product_id'),
            $request->input('variation_id') ? (int)$request->input('variation_id') : null,
            (float)$request->input('min_quantity'),
            $request->input('max_quantity') ? (float)$request->input('max_quantity') : null,
            (float)$request->input('tier_price'),
            $request->input('customer_group', 'all')
        );
        return response()->json($result);
    }

    public function getWholesaleTiers(Request $request, $productId)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->wholesaleService->getWholesaleTiers((int)$storeId, (int)$productId, $request->input('variation_id'));
        return response()->json($result);
    }

    // 2. Digital Gift Cards & Store Credit
    public function issueGiftCard(Request $request)
    {
        $request->validate(['initial_balance' => 'required|numeric|min:1000', 'pin' => 'required|string|min:4']);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->giftCardService->issueGiftCard((int)$storeId, (float)$request->input('initial_balance'), $request->input('pin'));
        return response()->json($result);
    }

    public function checkGiftCardBalance(Request $request)
    {
        $request->validate(['card_code' => 'required|string', 'pin' => 'required|string']);
        $result = $this->giftCardService->checkBalance($request->input('card_code'), $request->input('pin'));
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    public function redeemGiftCard(Request $request)
    {
        $request->validate(['card_code' => 'required|string', 'pin' => 'required|string', 'amount' => 'required|numeric|min:1']);
        $result = $this->giftCardService->redeemBalance($request->input('card_code'), $request->input('pin'), (float)$request->input('amount'), $request->input('transaction_id'));
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    // 3. Period Closing & Period Lock
    public function closePeriod(Request $request)
    {
        $request->validate(['period_type' => 'required|in:monthly,yearly', 'period_date' => 'required|date']);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->closingService->closePeriod(
            (int)$storeId,
            $request->input('period_type'),
            $request->input('period_date'),
            Auth::id() ?? 1,
            $request->input('notes')
        );
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    // 4. Cashier Fraud & Anomaly Guard
    public function logFraudAnomaly(Request $request)
    {
        $request->validate(['cashier_name' => 'required|string', 'anomaly_type' => 'required|string']);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->fraudService->logAnomaly(
            (int)$storeId,
            $request->input('user_id'),
            $request->input('cashier_name'),
            $request->input('anomaly_type'),
            $request->input('severity', 'medium'),
            $request->input('details', []),
            $request->input('owner_phone')
        );
        return response()->json($result);
    }

    public function scanFraud(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->fraudService->scanCashierAnomalies((int)$storeId, $request->input('date'));
        return response()->json($result);
    }

    // 5. Inventory Reorder Point AI
    public function generateReorders(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $leadTime = (int) $request->input('lead_time_days', 7);
        $result = $this->reorderService->generateReorderRecommendations((int)$storeId, $leadTime);
        return response()->json($result);
    }

    public function getPendingReorders(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->reorderService->getPendingReorders((int)$storeId);
        return response()->json($result);
    }

    // 6. Logistics & e-POD
    public function dispatchDelivery(Request $request)
    {
        $request->validate(['transaction_id' => 'required|integer', 'driver_name' => 'required|string', 'driver_phone' => 'required|string']);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->dispatchService->assignDelivery((int)$storeId, (int)$request->input('transaction_id'), $request->input('driver_name'), $request->input('driver_phone'));
        return response()->json($result);
    }

    public function submitEpod(Request $request, $id)
    {
        $request->validate(['recipient_name' => 'required|string']);
        $result = $this->dispatchService->submitEpod(
            (int)$id,
            $request->input('recipient_name'),
            $request->input('epod_signature_url'),
            $request->input('epod_photo_url'),
            $request->input('recipient_notes')
        );
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    // 7. Split Bill & Multi-Tender Payment
    public function splitBill(Request $request)
    {
        $request->validate(['transaction_id' => 'required|integer', 'split_count' => 'required|integer|min:2']);
        $result = $this->splitBillService->splitBillEqual((int)$request->input('transaction_id'), (int)$request->input('split_count'));
        return response()->json($result, $result['status'] ? 200 : 404);
    }

    public function settleMultiTender(Request $request)
    {
        $request->validate(['transaction_id' => 'required|integer', 'payments' => 'required|array|min:1']);
        $result = $this->splitBillService->settleMultiTender((int)$request->input('transaction_id'), $request->input('payments'));
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    // 8. Resto Table Management
    public function createTable(Request $request)
    {
        $request->validate(['table_number' => 'required|string']);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->tableService->createTable((int)$storeId, $request->input('table_number'), (int)$request->input('capacity', 4), $request->input('zone', 'Main Hall'));
        return response()->json($result);
    }

    public function getTables(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->tableService->getTables((int)$storeId);
        return response()->json($result);
    }

    public function updateTableStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:available,occupied,billed,reserved']);
        $result = $this->tableService->updateTableStatus((int)$id, $request->input('status'), $request->input('transaction_id'));
        return response()->json($result);
    }

    // 9. Interactive WhatsApp Bot Webhook
    public function handleBotWebhook(Request $request)
    {
        $request->validate(['sender_phone' => 'required|string', 'message_text' => 'required|string']);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->botService->handleIncomingMessage($request->input('sender_phone'), $request->input('message_text'), (int)$storeId);
        return response()->json($result);
    }
}
