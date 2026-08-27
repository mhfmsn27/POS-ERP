<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Product\ProductVariation;
use App\Services\Inventory\BarcodeLabelGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    protected BarcodeLabelGeneratorService $barcodeService;

    public function __construct(BarcodeLabelGeneratorService $barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }

    /**
     * Tampilkan halaman cetak label barcode produk / rak.
     */
    public function printView(Request $request)
    {
        $items = $request->input('items', []);
        
        // Jika tidak ada item dari query/post, ambil contoh produk pertama
        if (empty($items)) {
            $firstVars = ProductVariation::take(1)->get();
            foreach ($firstVars as $v) {
                $items[] = ['id' => $v->id, 'qty' => 1];
            }
        }

        $layout = $request->input('layout', 'thermal_double');
        $data = $this->barcodeService->prepareLabelData($items, $layout);

        return view('admin.inventory.product.barcode_print', [
            'page'   => 'Cetak Label Barcode & Rak Produk',
            'layout' => $layout,
            'labels' => $data['labels'],
            'total'  => $data['total_labels'],
        ]);
    }

    /**
     * API JSON untuk menghasilkan barcode SVG instan berdasarkan teks/SKU.
     */
    public function generateSingleSvg(Request $request): JsonResponse
    {
        $code = $request->query('code', 'POS123456');
        $height = (int)$request->query('height', 40);
        $svg = $this->barcodeService->generateCode128Svg($code, $height);

        return response()->json([
            'status'       => true,
            'code'         => $code,
            'barcode_svg'  => $svg,
        ]);
    }
}
