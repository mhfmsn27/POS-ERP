<?php

namespace App\Http\Controllers\Api\Enterprise;

use App\Http\Controllers\Controller;
use App\Services\Accounting\BankReconciliationService;
use App\Services\Accounting\CashFlowPredictorService;
use App\Services\Payment\DynamicQrisService;
use App\Services\Pos\KitchenOrderService;
use App\Services\Tax\TaxComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StrategicEnterpriseController extends Controller
{
    protected BankReconciliationService $bankReconService;
    protected TaxComplianceService $taxComplianceService;
    protected DynamicQrisService $qrisService;
    protected KitchenOrderService $kitchenOrderService;
    protected CashFlowPredictorService $cashFlowPredictorService;

    public function __construct(
        BankReconciliationService $bankReconService,
        TaxComplianceService $taxComplianceService,
        DynamicQrisService $qrisService,
        KitchenOrderService $kitchenOrderService,
        CashFlowPredictorService $cashFlowPredictorService
    ) {
        $this->bankReconService = $bankReconService;
        $this->taxComplianceService = $taxComplianceService;
        $this->qrisService = $qrisService;
        $this->kitchenOrderService = $kitchenOrderService;
        $this->cashFlowPredictorService = $cashFlowPredictorService;
    }

    /**
     * 1. Impor Baris Rekening Koran Bank.
     */
    public function importBankStatement(Request $request)
    {
        $request->validate([
            'rows'      => 'required|array|min:1',
            'bank_name' => 'required|string|max:50',
        ]);

        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->bankReconService->importStatement(
            $request->input('rows'),
            (int)$storeId,
            $request->input('account_id') ? (int)$request->input('account_id') : null,
            $request->input('bank_name'),
            Auth::id() ?? 1
        );

        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * 2. Auto-Match Mutasi Bank dengan Faktur Penjualan Belum Lunas.
     */
    public function autoMatchBankStatement(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id;
        $accountId = $request->input('account_id');

        $result = $this->bankReconService->autoMatchTransactions(
            $storeId ? (int)$storeId : null,
            $accountId ? (int)$accountId : null,
            Auth::id() ?? 1
        );

        return response()->json($result);
    }

    /**
     * 3. Export CSV DJP e-Faktur Pajak.
     */
    public function exportEfaktur(Request $request)
    {
        $request->validate([
            'tax_period' => 'required|string|regex:/^\d{4}-\d{2}$/', // YYYY-MM
        ]);

        $storeId = my_store() ?? Auth::user()->store_id;
        $result = $this->taxComplianceService->generateEfakturCsv(
            $request->input('tax_period'),
            $storeId ? (int)$storeId : null,
            Auth::id() ?? 1
        );

        return response()->json($result);
    }

    /**
     * 4. Validasi Format NPWP / NIK 16-Digit.
     */
    public function validateTaxNumber(Request $request)
    {
        $request->validate([
            'tax_number' => 'required|string',
        ]);

        $result = $this->taxComplianceService->validateTaxId($request->input('tax_number'));
        return response()->json($result);
    }

    /**
     * 5. Generate Tagihan QRIS Dinamis untuk Kasir POS.
     */
    public function generateQris(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:100',
            'transaction_id' => 'nullable|integer',
        ]);

        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->qrisService->generateDynamicQris(
            (int)$storeId,
            (float)$request->input('amount'),
            $request->input('transaction_id') ? (int)$request->input('transaction_id') : null
        );

        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * 6. Cek Status Pelunasan QRIS Dinamis.
     */
    public function checkQrisStatus(Request $request, $invoice)
    {
        $result = $this->qrisService->checkStatus((string)$invoice);
        return response()->json($result);
    }

    /**
     * 7. Webhook Callback Pembayaran QRIS (Public).
     */
    public function qrisCallback(Request $request)
    {
        $result = $this->qrisService->handleQrisCallback($request->all());
        return response()->json($result, $result['status'] ? 200 : 400);
    }

    /**
     * 8. Buat Tiket Layar Dapur (KDS Ticket).
     */
    public function createKdsTicket(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer',
            'items'          => 'required|array|min:1',
        ]);

        $storeId = my_store() ?? Auth::user()->store_id ?? 1;
        $result = $this->kitchenOrderService->createTickets(
            (int)$storeId,
            (int)$request->input('transaction_id'),
            $request->input('items'),
            $request->input('table_number'),
            $request->input('customer_name'),
            $request->input('notes')
        );

        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * 9. Ambil Daftar Tiket Dapur Aktif (KDS Screen).
     */
    public function getActiveKdsTickets(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id;
        $station = $request->query('station', 'all');

        $result = $this->kitchenOrderService->getActiveTickets(
            $storeId ? (int)$storeId : null,
            $station
        );

        return response()->json($result);
    }

    /**
     * 10. Update Status Pengerjaan Tiket Dapur (pending -> cooking -> ready -> served).
     */
    public function updateKdsStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:cooking,ready,served,cancelled',
        ]);

        $result = $this->kitchenOrderService->updateTicketStatus((int)$id, $request->input('status'));
        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * 11. Proyeksi Arus Kas 30/60/90 Hari & Likuiditas Bisnis.
     */
    public function getCashFlowForecast(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id;
        $days = (int) $request->query('days', 90);

        $result = $this->cashFlowPredictorService->predictCashFlow(
            $storeId ? (int)$storeId : null,
            $days
        );

        return response()->json($result);
    }

    /**
     * 12. Kirim Ringkasan Laporan Z-Report Tutup Shift ke WhatsApp Manajer/Owner.
     */
    public function sendShiftZReportWa(Request $request, $shiftId)
    {
        $overridePhone = $request->input('phone');
        $result = app(\App\Services\Crm\OmnichannelReceiptService::class)->sendShiftZReportToManager((int)$shiftId, $overridePhone);
        return response()->json($result, $result['status'] ? 200 : 422);
    }
}
