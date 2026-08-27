<?php

namespace App\Http\Controllers\Api\Enterprise;

use App\Http\Controllers\Controller;
use App\Services\Analytics\ExecutiveBriefingService;
use App\Services\Crm\ServiceAppointmentService;
use App\Services\Inventory\ConsignmentSettlementService;
use App\Services\Pos\SmartPromotionEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontierEnterpriseController extends Controller
{
    protected ServiceAppointmentService $appointmentService;
    protected ConsignmentSettlementService $consignmentService;
    protected SmartPromotionEngineService $promoService;
    protected ExecutiveBriefingService $briefingService;

    public function __construct(
        ServiceAppointmentService $appointmentService,
        ConsignmentSettlementService $consignmentService,
        SmartPromotionEngineService $promoService,
        ExecutiveBriefingService $briefingService
    ) {
        $this->appointmentService = $appointmentService;
        $this->consignmentService = $consignmentService;
        $this->promoService = $promoService;
        $this->briefingService = $briefingService;
    }

    // 1. Service Appointments
    public function bookAppointment(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string',
            'customer_phone'   => 'required|string',
            'service_name'     => 'required|string',
            'appointment_date' => 'required|date',
            'start_time'       => 'required|string',
        ]);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->appointmentService->bookAppointment(
            (int)$storeId,
            $request->input('customer_name'),
            $request->input('customer_phone'),
            $request->input('service_name'),
            $request->input('appointment_date'),
            $request->input('start_time'),
            $request->input('end_time'),
            $request->input('staff_id') ? (int)$request->input('staff_id') : null,
            $request->input('staff_name'),
            (float)$request->input('estimated_fee', 0),
            $request->input('customer_id') ? (int)$request->input('customer_id') : null
        );
        return response()->json($result);
    }

    public function sendAppointmentReminders(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->appointmentService->sendReminders((int)$storeId, $request->input('date'));
        return response()->json($result);
    }

    public function updateAppointmentStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:booked,confirmed,in_progress,completed,cancelled,no_show']);
        $result = $this->appointmentService->updateStatus((int)$id, $request->input('status'), $request->input('notes'));
        return response()->json($result);
    }

    public function getAppointments(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->appointmentService->getAppointments((int)$storeId, $request->query('date'), $request->query('status'));
        return response()->json($result);
    }

    // 2. Consignment & Revenue Sharing
    public function registerConsignmentProduct(Request $request)
    {
        $request->validate([
            'product_id'    => 'required|integer',
            'supplier_id'   => 'required|integer',
            'supplier_name' => 'required|string',
        ]);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->consignmentService->registerConsignmentProduct(
            (int)$storeId,
            (int)$request->input('product_id'),
            $request->input('variation_id') ? (int)$request->input('variation_id') : null,
            (int)$request->input('supplier_id'),
            $request->input('supplier_name'),
            (float)$request->input('consignor_share_percent', 80.00),
            (float)$request->input('store_margin_percent', 20.00),
            (float)$request->input('unit_consignor_cost', 0)
        );
        return response()->json($result);
    }

    public function generateConsignmentSettlement(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|integer',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date',
        ]);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->consignmentService->generateSettlement(
            (int)$storeId,
            (int)$request->input('supplier_id'),
            $request->input('start_date'),
            $request->input('end_date')
        );
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    public function getConsignmentSettlements(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->consignmentService->getSettlementHistory((int)$storeId, $request->query('supplier_id'));
        return response()->json($result);
    }

    public function updateConsignmentSettlementStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:draft,approved,paid']);
        $result = $this->consignmentService->updateSettlementStatus((int)$id, $request->input('status'));
        return response()->json($result);
    }

    // 3. Smart Promotion Engine
    public function createPromotion(Request $request)
    {
        $request->validate([
            'name'       => 'required|string',
            'promo_type' => 'required|in:combo_bundle,bogo,threshold_discount',
        ]);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->promoService->createPromotion(
            (int)$storeId,
            $request->input('name'),
            $request->input('promo_type'),
            $request->input('conditions', []),
            $request->input('rewards', []),
            $request->input('start_date'),
            $request->input('end_date')
        );
        return response()->json($result);
    }

    public function evaluateCartPromotions(Request $request)
    {
        $request->validate([
            'cart_items' => 'required|array|min:1',
            'subtotal'   => 'required|numeric|min:0',
        ]);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->promoService->evaluateCart((int)$storeId, $request->input('cart_items'), (float)$request->input('subtotal'));
        return response()->json($result);
    }

    public function getPromotions(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->promoService->getPromotions((int)$storeId);
        return response()->json($result);
    }

    public function togglePromotion(Request $request, $id)
    {
        $request->validate(['is_active' => 'required|boolean']);
        $result = $this->promoService->togglePromotionStatus((int)$id, (bool)$request->input('is_active'));
        return response()->json($result);
    }

    // 4. Executive Briefing
    public function getExecutiveSnapshot(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->briefingService->compileDailyBriefing((int)$storeId, $request->query('date'));
        return response()->json(['status' => true, 'briefing' => $result]);
    }

    public function sendExecutiveBriefing(Request $request)
    {
        $request->validate(['owner_phone' => 'required|string']);
        $storeId = my_store() ?? Auth::user()->store_id ?? $request->input('store_id', 1);
        $result = $this->briefingService->sendMorningBriefing((int)$storeId, $request->input('owner_phone'), $request->input('date'));
        return response()->json($result, $result['status'] ? 200 : 422);
    }
}
