<?php

namespace App\Http\Controllers\Api\Enterprise;

use App\Http\Controllers\Controller;
use App\Services\Accounting\CostCenterProjectService;
use App\Services\Accounting\ExecutiveFinancialAnalyticsService;
use App\Services\CashBank\AutoBankReconciliationService;
use App\Services\Crm\B2bPortalService;
use App\Services\Inventory\MultiTierUomService;
use App\Services\Tax\TaxDjpComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TierOneEnterpriseErpController extends Controller
{
    protected TaxDjpComplianceService $taxService;
    protected ExecutiveFinancialAnalyticsService $analyticsService;
    protected CostCenterProjectService $costCenterService;
    protected MultiTierUomService $uomService;
    protected AutoBankReconciliationService $bankService;
    protected B2bPortalService $b2bService;

    public function __construct(
        TaxDjpComplianceService $taxService,
        ExecutiveFinancialAnalyticsService $analyticsService,
        CostCenterProjectService $costCenterService,
        MultiTierUomService $uomService,
        AutoBankReconciliationService $bankService,
        B2bPortalService $b2bService
    ) {
        $this->taxService        = $taxService;
        $this->analyticsService  = $analyticsService;
        $this->costCenterService = $costCenterService;
        $this->uomService        = $uomService;
        $this->bankService       = $bankService;
        $this->b2bService        = $b2bService;
    }

    private function getStoreId(): int
    {
        return (int)(my_store() ?? Auth::user()->store_id ?? 1);
    }

    // ==========================================
    // 1. PILAR 1: PAJAK & E-FAKTUR DJP
    // ==========================================
    public function exportEfakturCsv(Request $request)
    {
        $request->validate(['transaction_ids' => 'required|array|min:1']);
        $result = $this->taxService->generateEfakturCsv($request->input('transaction_ids'), $this->getStoreId());
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    public function allocateNsfp(Request $request)
    {
        $nsfp = $this->taxService->allocateNsfp($this->getStoreId());
        return response()->json(['status' => true, 'nsfp' => $nsfp]);
    }

    public function calculateTaxWithholding(Request $request)
    {
        $request->validate(['gross_amount' => 'required|numeric|min:0', 'tax_type' => 'required|string']);
        $result = $this->taxService->calculateWithholdingTax(
            (float)$request->input('gross_amount'),
            $request->input('tax_type'),
            (bool)$request->input('has_npwp', true)
        );
        return response()->json($result);
    }

    // ==========================================
    // 2. PILAR 2: ANALITIK KEUANGAN & FORECAST
    // ==========================================
    public function getFinancialHealth(Request $request)
    {
        $result = $this->analyticsService->getFinancialHealthScore(
            $this->getStoreId(),
            $request->query('start_date'),
            $request->query('end_date')
        );
        return response()->json($result);
    }

    public function getAgingSchedule(Request $request)
    {
        $type = $request->query('type', 'ar');
        $result = $this->analyticsService->getAgingSchedule($this->getStoreId(), $type);
        return response()->json($result);
    }

    public function getCashFlowForecast(Request $request)
    {
        $days = (int)$request->query('days', 60);
        $result = $this->analyticsService->getCashFlowForecast($this->getStoreId(), $days);
        return response()->json($result);
    }

    // ==========================================
    // 3. PILAR 3: COST CENTER & PROYEK P&L
    // ==========================================
    public function getProjectPnl(Request $request)
    {
        $result = $this->costCenterService->getProjectDepartmentPnl(
            $this->getStoreId(),
            $request->query('department_id') ? (int)$request->query('department_id') : null,
            $request->query('project_code'),
            $request->query('start_date'),
            $request->query('end_date')
        );
        return response()->json($result);
    }

    public function createAmortization(Request $request)
    {
        $request->validate(['name' => 'required|string', 'total_amount' => 'required|numeric|min:0']);
        $result = $this->costCenterService->createRecurringAmortization($this->getStoreId(), $request->all());
        return response()->json($result);
    }

    // ==========================================
    // 4. PILAR 4: MULTI-SATUAN UOM & MANUFAKTUR
    // ==========================================
    public function convertUom(Request $request)
    {
        $request->validate(['qty' => 'required|numeric|min:0', 'from' => 'required|string', 'to' => 'required|string']);
        $converted = $this->uomService->convertUnits(
            (float)$request->input('qty'),
            $request->input('from'),
            $request->input('to'),
            $request->input('matrix', [])
        );
        return response()->json(['status' => true, 'from' => $request->input('from'), 'to' => $request->input('to'), 'qty_in' => (float)$request->input('qty'), 'converted_qty' => $converted]);
    }

    public function getTieredUomPrices(Request $request)
    {
        $price = (float)$request->query('base_price', 10000);
        $result = $this->uomService->getTieredUomPrices($price, $request->all());
        return response()->json($result);
    }

    public function calculateManufacturingCost(Request $request)
    {
        $request->validate(['materials' => 'required|array|min:1']);
        $result = $this->uomService->calculateManufacturingCosting(
            $request->input('materials'),
            (float)$request->input('direct_labor', 0),
            (float)$request->input('factory_overhead', 0),
            (float)$request->input('output_qty', 1)
        );
        return response()->json($result);
    }

    // ==========================================
    // 5. PILAR 5: REKONSILIASI BANK & PETTY CASH
    // ==========================================
    public function parseBankStatement(Request $request)
    {
        $request->validate(['csv_content' => 'required|string']);
        $result = $this->bankService->parseBankCsv($request->input('csv_content'), $request->input('bank_code', 'bca'));
        return response()->json($result);
    }

    public function autoMatchBank(Request $request)
    {
        $request->validate(['entries' => 'required|array|min:1']);
        $result = $this->bankService->autoMatchTransactions($this->getStoreId(), $request->input('entries'), (int)$request->input('tolerance_days', 3));
        return response()->json($result);
    }

    public function recordPettyCash(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:0', 'type' => 'required|in:in,out']);
        $result = $this->bankService->recordPettyCash($this->getStoreId(), $request->all());
        return response()->json($result);
    }

    // ==========================================
    // 6. PILAR 6: PORTAL B2B CUSTOMER & SUPPLIER
    // ==========================================
    public function getB2bCustomerPortal(Request $request, $id)
    {
        $result = $this->b2bService->getCustomerB2bProfile((int)$id, $this->getStoreId());
        return response()->json($result, $result['status'] ? 200 : 404);
    }

    public function getVendorPortal(Request $request, $id)
    {
        $result = $this->b2bService->getVendorPortalData((int)$id, $this->getStoreId());
        return response()->json($result, $result['status'] ? 200 : 404);
    }

    public function confirmVendorDispatch(Request $request, $id)
    {
        $request->validate(['tracking_no' => 'required|string']);
        $result = $this->b2bService->confirmVendorDispatch((int)$id, $request->input('tracking_no'), $request->input('driver_info'));
        return response()->json($result, $result['status'] ? 200 : 404);
    }
}
