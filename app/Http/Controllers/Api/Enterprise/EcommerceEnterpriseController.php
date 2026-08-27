<?php

namespace App\Http\Controllers\Api\Enterprise;

use App\Http\Controllers\Controller;
use App\Services\Ecommerce\AbandonedCartRecoveryService;
use App\Services\Ecommerce\EcommerceStockReservationService;
use App\Services\Ecommerce\FlashSaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EcommerceEnterpriseController extends Controller
{
    protected FlashSaleService $flashSaleService;
    protected AbandonedCartRecoveryService $abandonedCartService;
    protected EcommerceStockReservationService $stockReservationService;

    public function __construct(
        FlashSaleService $flashSaleService,
        AbandonedCartRecoveryService $abandonedCartService,
        EcommerceStockReservationService $stockReservationService
    ) {
        $this->flashSaleService = $flashSaleService;
        $this->abandonedCartService = $abandonedCartService;
        $this->stockReservationService = $stockReservationService;
    }

    /**
     * 1. Buat Kampanye Flash Sale Baru.
     */
    public function createFlashSale(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|integer',
            'name'           => 'required|string|max:100',
            'original_price' => 'required|numeric|min:0',
            'flash_price'    => 'required|numeric|min:0',
            'quota_total'    => 'required|integer|min:1',
            'start_time'     => 'required|date',
            'end_time'       => 'required|date|after:start_time',
        ]);

        $storeId = my_store() ?? Auth::user()->store_id ?? 1;

        $result = $this->flashSaleService->createCampaign(
            (int)$storeId,
            (int)$request->input('product_id'),
            $request->input('variation_id') ? (int)$request->input('variation_id') : null,
            $request->input('name'),
            (float)$request->input('original_price'),
            (float)$request->input('flash_price'),
            (int)$request->input('quota_total'),
            $request->input('start_time'),
            $request->input('end_time')
        );

        return response()->json($result, $result['status'] ? 200 : 422);
    }

    /**
     * 2. Ambil Daftar Flash Sale Aktif (Lengkap dengan Countdown Timer).
     */
    public function getActiveFlashSales(Request $request)
    {
        $storeId = my_store() ?? Auth::user()->store_id;
        $result = $this->flashSaleService->getActiveFlashSales($storeId ? (int)$storeId : null);
        return response()->json($result);
    }

    /**
     * 3. Catat Keranjang Belanja Aktif Pelanggan (Untuk Abandoned Cart Tracking).
     */
    public function trackCart(Request $request)
    {
        $request->validate([
            'cart_items'     => 'required|array|min:1',
            'customer_phone' => 'nullable|string',
        ]);

        $storeId = my_store() ?? Auth::user()->store_id ?? 1;

        $id = $this->abandonedCartService->trackCart(
            (int)$storeId,
            $request->input('cart_items'),
            $request->input('customer_id') ? (int)$request->input('customer_id') : null,
            $request->input('customer_phone'),
            $request->input('customer_name')
        );

        return response()->json([
            'status'  => true,
            'cart_id' => $id,
            'message' => 'Keranjang belanja berhasil dipantau.'
        ]);
    }

    /**
     * 4. Eksekusi Pengiriman Pesan WhatsApp Pengingat Keranjang Menggantung.
     */
    public function processAbandonedCarts(Request $request)
    {
        $hours = (int)$request->input('threshold_hours', 2);
        $result = $this->abandonedCartService->processAbandonedCarts($hours);
        return response()->json($result);
    }

    /**
     * 5. Bersihkan dan Kembalikan Stok Reservasi yang Kadaluarsa.
     */
    public function releaseExpiredReservations(Request $request)
    {
        $released = $this->stockReservationService->releaseExpiredReservations();
        return response()->json([
            'status'         => true,
            'released_count' => $released,
            'message'        => "Berhasil melepaskan {$released} reservasi stok yang kadaluarsa."
        ]);
    }
}
