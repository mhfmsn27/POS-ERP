<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Admin\Store;
use App\Models\Admin\Setting;
use App\Models\Transaction\Transaction;
use App\Services\Crm\OmnichannelReceiptService;
use App\Services\Inventory\BarcodeLabelGeneratorService;
use App\Services\Pos\PosPrintingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PosReceiptPrintController extends Controller
{
    /**
     * Tampilkan halaman struk kasir termal siap cetak (Web Print / 58mm & 80mm).
     *
     * @param int|string $id
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function printThermal($id, Request $request)
    {
        $data = Transaction::withoutGlobalScopes()
            ->with([
                'customer',
                'store',
                'sell' => function ($q) {
                    $q->with(['product', 'variation', 'unit']);
                },
                'payment',
                'created_user',
                'table',
                'voucher'
            ])
            ->where('id', $id)
            ->orWhere('ref_no', $id)
            ->firstOrFail();

        // Resolusi Store
        $store = $data->store;
        if (!$store) {
            $storeId = Session::get('mystore');
            $store = $storeId ? Store::find($storeId) : Store::first();
        }

        $settings = Setting::first();

        // Resolusi Ukuran Kertas: '58' (58mm) atau '80' (80mm)
        $paper = $request->query('paper');
        if (!$paper) {
            if ($store && !empty($store->printer) && $store->printer->char_per_line < 40) {
                $paper = '58';
            } else {
                $paper = '80';
            }
        }
        $paperWidth = ($paper === '58') ? 58 : 80;

        // Generator Barcode SVG untuk No. Struk (Code-128)
        $barcodeService = app(BarcodeLabelGeneratorService::class);
        $barcodeSvg = $barcodeService->generateCode128Svg($data->ref_no ?? ('TRX-' . $data->id), 30);

        // Perhitungan Loyalitas Poin & VIP Tier
        $earnedPoints = (int)floor($data->final_total / 10000);
        $customerPoints = 0;
        $loyaltyTier = 'Reguler';
        if ($data->customer) {
            $customerPoints = (int)($data->customer->total_points ?? $data->customer->points ?? 0);
            if ($customerPoints >= 500) {
                $loyaltyTier = 'Platinum VIP';
            } elseif ($customerPoints >= 200) {
                $loyaltyTier = 'Gold Member';
            } elseif ($customerPoints >= 50) {
                $loyaltyTier = 'Silver Member';
            }
        }

        // Resolusi Logo Toko
        $logoUrl = null;
        if ($store && !empty($store->logo)) {
            if (file_exists(public_path('storage/' . $store->logo))) {
                $logoUrl = asset('storage/' . $store->logo);
            } elseif (file_exists(public_path($store->logo))) {
                $logoUrl = asset($store->logo);
            }
        }
        if (!$logoUrl && file_exists(public_path('images/logo.png'))) {
            $logoUrl = asset('images/logo.png');
        }

        // Hitung Kembalian & Sisa Tempo
        $cashPaid = (float)($data->payment_cash ?? $data->payment->sum('amount') ?? $data->final_total);
        if ($cashPaid <= 0 && $data->payment_status == 'paid') {
            $cashPaid = (float)$data->final_total;
        }
        $finalTotal = (float)$data->final_total;
        $change = max(0, $cashPaid - $finalTotal);
        $dueAmount = max(0, $finalTotal - $cashPaid);

        return view('pos.print', [
            'page'           => 'Struk Transaksi #' . ($data->ref_no ?? $data->id),
            'data'           => $data,
            'store'          => $store,
            'settings'       => $settings,
            'paperWidth'     => $paperWidth,
            'barcodeSvg'     => $barcodeSvg,
            'earnedPoints'   => $earnedPoints,
            'customerPoints' => $customerPoints,
            'loyaltyTier'    => $loyaltyTier,
            'logoUrl'        => $logoUrl,
            'cashPaid'       => $cashPaid,
            'change'         => $change,
            'dueAmount'      => $dueAmount,
            'autoPrint'      => $request->query('autoprint', 1) == 1
        ]);
    }

    /**
     * Kirimkan Struk Digital ke WhatsApp Pelanggan (1-Click).
     *
     * @param int|string $id
     * @param Request $request
     * @param OmnichannelReceiptService $receiptService
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendWhatsapp($id, Request $request, OmnichannelReceiptService $receiptService)
    {
        $trx = Transaction::withoutGlobalScopes()->where('id', $id)->orWhere('ref_no', $id)->first();
        if (!$trx) {
            return response()->json(['status' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $phone = $request->input('phone', $trx->customer->phone ?? null);
        $result = $receiptService->sendDigitalReceipt($trx->id, $phone);

        return response()->json($result);
    }

    /**
     * Ambil Payload Raw ESC/POS Binary untuk printer thermal fisik (LAN/Bluetooth/USB).
     *
     * @param int|string $id
     * @param Request $request
     * @param PosPrintingService $printingService
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRawEscPos($id, Request $request, PosPrintingService $printingService)
    {
        $trx = Transaction::withoutGlobalScopes()->where('id', $id)->orWhere('ref_no', $id)->first();
        if (!$trx) {
            return response()->json(['status' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $paper = $request->query('paper', '80');
        $cols = ($paper == '58') ? 32 : 48;
        $result = $printingService->generateReceiptEscPos($trx->id, $cols);

        return response()->json($result);
    }
}
