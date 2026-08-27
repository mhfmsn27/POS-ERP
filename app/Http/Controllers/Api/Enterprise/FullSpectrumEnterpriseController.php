<?php

namespace App\Http\Controllers\Api\Enterprise;

use App\Http\Controllers\Controller;
use App\Services\Accounting\BudgetingService;
use App\Services\Accounting\FixedAssetService;
use App\Services\Crm\CustomerRetentionService;
use App\Services\Crm\RmaServiceTrackerService;
use App\Services\Hrm\PayrollComplianceService;
use App\Services\Inventory\SerialImeiTrackingService;
use App\Services\Inventory\WarehouseBinService;
use App\Services\Manufacturing\ManufacturingService;
use App\Services\Pos\OfflinePosSyncService;
use App\Services\Pos\PosPrintingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FullSpectrumEnterpriseController extends Controller
{
    protected FixedAssetService $fixedAssetService;
    protected BudgetingService $budgetingService;
    protected ManufacturingService $manufacturingService;
    protected PayrollComplianceService $payrollService;
    protected SerialImeiTrackingService $serialService;
    protected WarehouseBinService $binService;
    protected RmaServiceTrackerService $rmaService;
    protected CustomerRetentionService $retentionService;
    protected OfflinePosSyncService $offlineSyncService;
    protected PosPrintingService $printingService;

    public function __construct(
        FixedAssetService $fixedAssetService,
        BudgetingService $budgetingService,
        ManufacturingService $manufacturingService,
        PayrollComplianceService $payrollService,
        SerialImeiTrackingService $serialService,
        WarehouseBinService $binService,
        RmaServiceTrackerService $rmaService,
        CustomerRetentionService $retentionService,
        OfflinePosSyncService $offlineSyncService,
        PosPrintingService $printingService
    ) {
        $this->fixedAssetService = $fixedAssetService;
        $this->budgetingService = $budgetingService;
        $this->manufacturingService = $manufacturingService;
        $this->payrollService = $payrollService;
        $this->serialService = $serialService;
        $this->binService = $binService;
        $this->rmaService = $rmaService;
        $this->retentionService = $retentionService;
        $this->offlineSyncService = $offlineSyncService;
        $this->printingService = $printingService;
    }

    // 1. Aset Tetap & Depresiasi
    public function registerAsset(Request $request)
    {
        $request->validate(['name' => 'required|string', 'acquisition_cost' => 'required|numeric|min:0']);
        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->fixedAssetService->registerAsset($request->all(), (int)$storeId, Auth::id() ?? 1);
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    public function processDepreciation(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id;
        $result = $this->fixedAssetService->processMonthlyDepreciation($storeId ? (int)$storeId : null, $request->input('date'), Auth::id() ?? 1);
        return response()->json($result);
    }

    // 2. Budgeting vs Realisasi
    public function setBudget(Request $request)
    {
        $request->validate(['year' => 'required|integer', 'account_id' => 'required|integer', 'amount' => 'required|numeric|min:0']);
        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->budgetingService->setBudget(
            (int)$storeId,
            $request->input('department_id') ? (int)$request->input('department_id') : null,
            (int)$request->input('year'),
            $request->input('month') ? (int)$request->input('month') : null,
            (int)$request->input('account_id'),
            (float)$request->input('amount')
        );
        return response()->json($result);
    }

    public function getBudgetVariance(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $year = (int) $request->query('year', date('Y'));
        $month = $request->query('month') ? (int)$request->query('month') : null;
        $result = $this->budgetingService->getBudgetVarianceReport((int)$storeId, $year, $month);
        return response()->json($result);
    }

    // 3. Manufaktur, BOM & Work Orders
    public function createBom(Request $request)
    {
        $request->validate([
            'finished_product_id' => 'required|integer',
            'name'                => 'required|string',
            'raw_materials'       => 'required|array|min:1',
        ]);
        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->manufacturingService->createBom(
            (int)$storeId,
            (int)$request->input('finished_product_id'),
            $request->input('finished_variation_id') ? (int)$request->input('finished_variation_id') : null,
            $request->input('name'),
            (float)$request->input('yield_quantity', 1),
            $request->input('raw_materials'),
            $request->input('notes')
        );
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    public function createWorkOrder(Request $request)
    {
        $request->validate(['bom_id' => 'required|integer', 'target_quantity' => 'required|numeric|min:1']);
        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->manufacturingService->createWorkOrder((int)$storeId, (int)$request->input('bom_id'), (float)$request->input('target_quantity'));
        return response()->json($result);
    }

    public function executeWorkOrder(Request $request, $id)
    {
        $qty = $request->input('actual_quantity') ? (float)$request->input('actual_quantity') : null;
        try {
            $result = $this->manufacturingService->executeWorkOrder((int)$id, $qty);
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // 4. Payroll PPh 21 TER & BPJS
    public function calculatePayroll(Request $request)
    {
        $request->validate(['gross_salary' => 'required|numeric|min:0']);
        $result = $this->payrollService->calculatePayroll(
            (int)$request->input('employee_id', 1),
            (float)$request->input('gross_salary'),
            $request->input('ptkp_status', 'TK/0')
        );
        return response()->json(['status' => true, 'calculation' => $result]);
    }

    // 5. Serial Number / IMEI Tracking
    public function registerSerialNumbers(Request $request)
    {
        $request->validate(['product_id' => 'required|integer', 'serial_numbers' => 'required|array|min:1']);
        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->serialService->registerSerialNumbers(
            (int)$storeId,
            (int)$request->input('product_id'),
            $request->input('variation_id') ? (int)$request->input('variation_id') : null,
            $request->input('serial_numbers'),
            (int)$request->input('warranty_months', 12)
        );
        return response()->json($result);
    }

    public function lookupSerial(Request $request, $sn)
    {
        $result = $this->serialService->lookupSerial((string)$sn);
        return response()->json($result, $result['status'] ? 200 : 404);
    }

    // 6. Gudang Bin Locations
    public function createBin(Request $request)
    {
        $request->validate(['zone' => 'required|string', 'aisle' => 'required|string', 'rack' => 'required|string', 'shelf' => 'required|string']);
        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->binService->createBin(
            (int)$storeId,
            $request->input('warehouse_id') ? (int)$request->input('warehouse_id') : null,
            $request->input('zone'),
            $request->input('aisle'),
            $request->input('rack'),
            $request->input('shelf'),
            $request->input('description')
        );
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    public function getBins(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id;
        $result = $this->binService->getBins($storeId ? (int)$storeId : null);
        return response()->json($result);
    }

    // 7. RMA & Servis
    public function createRmaTicket(Request $request)
    {
        $request->validate(['customer_name' => 'required|string', 'customer_phone' => 'required|string', 'device_name' => 'required|string']);
        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->rmaService->createTicket(
            (int)$storeId,
            $request->input('customer_name'),
            $request->input('customer_phone'),
            $request->input('device_name'),
            $request->input('serial_number'),
            $request->input('issue_description', 'Kendala unit'),
            (float)$request->input('estimated_cost', 0),
            $request->input('customer_id') ? (int)$request->input('customer_id') : null
        );
        return response()->json($result);
    }

    public function updateRmaStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:diagnosing,waiting_parts,repairing,ready_for_pickup,completed,cancelled']);
        $result = $this->rmaService->updateTicketStatus(
            (int)$id,
            $request->input('status'),
            $request->input('technician_notes'),
            $request->input('actual_cost') !== null ? (float)$request->input('actual_cost') : null
        );
        return response()->json($result);
    }

    // 8. CRMHUB Omnichannel Retensi & Broadcast
    public function sendBirthdayGreetings(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id;
        $discount = (float)$request->input('discount_percent', 15);
        $result = $this->retentionService->processBirthdayGreetings($storeId ? (int)$storeId : null, $discount);
        return response()->json($result);
    }

    public function sendSafeBroadcast(Request $request)
    {
        $request->validate(['message' => 'required|string', 'phones' => 'required|array|min:1']);
        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->retentionService->sendSafeBroadcast((int)$storeId, $request->input('message'), $request->input('phones'));
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    // 9. Offline POS Sync & ESC/POS
    public function syncOfflineTransactions(Request $request)
    {
        $request->validate(['transactions' => 'required|array|min:1']);
        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->offlineSyncService->syncTransactions($request->input('transactions'), (int)$storeId, Auth::id() ?? 1);
        return response()->json($result);
    }

    public function getEscPosReceipt(Request $request, $id)
    {
        $width = (int)$request->query('width', 32);
        $result = $this->printingService->generateReceiptEscPos((int)$id, $width);
        return response()->json($result, $result['status'] ? 200 : 404);
    }
}
