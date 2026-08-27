<?php

namespace App\Http\Controllers\Api\Enterprise;

use App\Http\Controllers\Controller;
use App\Services\Accounting\FiscalPeriodService;
use App\Services\Crm\CustomerLoyaltyService;
use App\Services\Inventory\StockTransferService;
use App\Services\Pos\ShiftRegisterService;
use App\Services\Security\PosSecurityAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnterpriseFeaturesController extends Controller
{
    protected FiscalPeriodService $fiscalPeriodService;
    protected ShiftRegisterService $shiftRegisterService;
    protected StockTransferService $stockTransferService;
    protected CustomerLoyaltyService $customerLoyaltyService;
    protected PosSecurityAuditService $securityAuditService;

    public function __construct(
        FiscalPeriodService $fiscalPeriodService,
        ShiftRegisterService $shiftRegisterService,
        StockTransferService $stockTransferService,
        CustomerLoyaltyService $customerLoyaltyService,
        PosSecurityAuditService $securityAuditService
    ) {
        $this->fiscalPeriodService = $fiscalPeriodService;
        $this->shiftRegisterService = $shiftRegisterService;
        $this->stockTransferService = $stockTransferService;
        $this->customerLoyaltyService = $customerLoyaltyService;
        $this->securityAuditService = $securityAuditService;
    }

    /**
     * 1. Tutup Buku Tahunan (Year-End Closing).
     */
    public function closeFiscalYear(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2099',
        ]);

        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->fiscalPeriodService->closeFiscalYear((int)$storeId, (int)$request->year, Auth::id() ?? 1);

        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * 2. Kunci / Buka Status Periode Fiskal Bulanan.
     */
    public function updatePeriodStatus(Request $request)
    {
        $request->validate([
            'period_id' => 'required|integer',
            'status'    => 'required|in:open,locked,closed',
        ]);

        $result = $this->fiscalPeriodService->updatePeriodStatus(
            (int)$request->period_id,
            $request->status,
            Auth::id(),
            $request->input('notes')
        );

        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * 3. Buat Dokumen Mutasi Antar Cabang (Inter-Store Transfer).
     */
    public function createTransfer(Request $request)
    {
        $request->validate([
            'from_store_id' => 'required|integer',
            'to_store_id'   => 'required|integer|different:from_store_id',
            'items'         => 'required|array|min:1',
            'items.*.product_id'   => 'required|integer',
            'items.*.variation_id' => 'required|integer',
            'items.*.qty'          => 'required|numeric|min:0.01',
        ]);

        $result = $this->stockTransferService->createTransfer($request->all(), Auth::id() ?? 1);
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * 4. Kirim Transfer Barang (Dispatch to In-Transit).
     */
    public function dispatchTransfer(Request $request, $id)
    {
        $result = $this->stockTransferService->dispatchTransfer((int)$id, Auth::id() ?? 1);
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * 5. Terima Transfer Barang di Cabang Tujuan (Receive & Discrepancy Tracking).
     */
    public function receiveTransfer(Request $request, $id)
    {
        $receivedItems = $request->input('received_items', []);
        $notes = $request->input('discrepancy_notes');

        $result = $this->stockTransferService->receiveTransfer((int)$id, $receivedItems, $notes, Auth::id() ?? 1);
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * 6. Informasi Poin Loyalitas & VIP Membership Tier Pelanggan.
     */
    public function getCustomerLoyalty(Request $request, $customerId)
    {
        $tier = $this->customerLoyaltyService->getCustomerTier((int)$customerId);
        $balance = $this->customerLoyaltyService->getBalance((int)$customerId);

        return response()->json([
            'customer_id'   => (int)$customerId,
            'point_balance' => $balance,
            'membership'    => $tier,
        ]);
    }

    /**
     * 7. Redeem Poin Loyalitas Pelanggan di Kasir POS.
     */
    public function redeemLoyaltyPoints(Request $request)
    {
        $request->validate([
            'customer_id'       => 'required|integer',
            'points_to_redeem'  => 'required|integer|min:1',
            'transaction_id'    => 'nullable|integer',
        ]);

        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->customerLoyaltyService->redeemPoints(
            (int)$request->customer_id,
            (int)$storeId,
            (int)$request->points_to_redeem,
            $request->transaction_id ? (int)$request->transaction_id : null
        );

        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * 8. Rekapitulasi Anomali Keamanan & Anti-Fraud Kasir.
     */
    public function getSecurityAnomalies(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id;
        $data = $this->securityAuditService->getSecurityAnomalies(
            $storeId ? (int)$storeId : null,
            $request->query('start_date'),
            $request->query('end_date')
        );

        return response()->json($data);
    }

    /**
     * 9. Log No-Sale Drawer Kick dari Kasir POS.
     */
    public function logDrawerKick(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $this->securityAuditService->logDrawerKick(
            (int)$storeId,
            Auth::id() ?? 1,
            $request->input('reason', 'Buka laci tanpa transaksi')
        );

        return response()->json(['status' => true, 'message' => 'Drawer kick security audit event logged.']);
    }
}
