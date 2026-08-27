<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Product\Product;
use App\Models\Inventory\Product\ProductVariation;

class BarcodeLabelGeneratorService
{
    /**
     * Menghasilkan barcode SVG Code-128 murni tanpa dependensi eksternal.
     *
     * @param string $code
     * @param int $height
     * @return string SVG code
     */
    public function generateCode128Svg(string $code, int $height = 40): string
    {
        $code = trim($code);
        if (empty($code)) {
            $code = '1000000000';
        }

        // Code 128 Character Set B encoding table
        $patterns = [
            ' ' => '212222', '!' => '222122', '"' => '222221', '#' => '121223',
            '$' => '121322', '%' => '131222', '&' => '122213', '\'' => '122312',
            '(' => '132212', ')' => '221213', '*' => '221312', '+' => '231212',
            ',' => '112232', '-' => '122132', '.' => '122231', '/' => '113222',
            '0' => '123122', '1' => '123221', '2' => '223211', '3' => '221132',
            '4' => '221231', '5' => '213212', '6' => '223112', '7' => '312131',
            '8' => '311222', '9' => '321122', ':' => '321221', ';' => '312212',
            '<' => '322112', '=' => '322211', '>' => '212123', '?' => '212321',
            '@' => '232121', 'A' => '111323', 'B' => '131123', 'C' => '131321',
            'D' => '112313', 'E' => '132113', 'F' => '132311', 'G' => '211313',
            'H' => '231113', 'I' => '231311', 'J' => '112133', 'K' => '112331',
            'L' => '132131', 'M' => '113123', 'N' => '113321', 'O' => '133121',
            'P' => '313121', 'Q' => '211331', 'R' => '231131', 'S' => '213113',
            'T' => '213311', 'U' => '213131', 'V' => '311123', 'W' => '311321',
            'X' => '331121', 'Y' => '312113', 'Z' => '312311', '[' => '332111',
            '\\' => '314111', ']' => '221411', '^' => '431111', '_' => '111224',
            '`' => '111422', 'a' => '121124', 'b' => '121421', 'c' => '141122',
            'd' => '141221', 'e' => '112214', 'f' => '112412', 'g' => '122114',
            'h' => '122411', 'i' => '142112', 'j' => '142211', 'k' => '241211',
            'l' => '221114', 'm' => '413111', 'n' => '241112', 'o' => '134111',
            'p' => '111242', 'q' => '121142', 'r' => '121241', 's' => '114212',
            't' => '124112', 'u' => '124211', 'v' => '411212', 'w' => '421112',
            'x' => '421211', 'y' => '212141', 'z' => '214121', '{' => '412121',
            '|' => '111143', '}' => '111341', '~' => '131141',
        ];

        // Start Code B
        $startPattern = '211214';
        $stopPattern  = '2331112';

        $seq = $startPattern;
        for ($i = 0; $i < strlen($code); $i++) {
            $char = $code[$i];
            $seq .= $patterns[$char] ?? '121212';
        }
        $seq .= $stopPattern;

        // Render Bars
        $x = 0;
        $barWidth = 1.5;
        $rects = '';
        $isBar = true;

        for ($i = 0; $i < strlen($seq); $i++) {
            $w = (int)$seq[$i] * $barWidth;
            if ($isBar) {
                $rects .= "<rect x='{$x}' y='0' width='{$w}' height='{$height}' fill='#000000' />";
            }
            $x += $w;
            $isBar = !$isBar;
        }

        $totalWidth = $x;
        return "<svg viewBox='0 0 {$totalWidth} {$height}' preserveAspectRatio='xMidYMid meet' style='width: 100%; height: {$height}px;'>{$rects}</svg>";
    }

    /**
     * Mempersiapkan dataset label barcode untuk rendering view cetak.
     *
     * @param array $items Array of ['id' => variation_id, 'qty' => print_qty]
     * @param string $layout Paper format profile
     * @return array
     */
    public function prepareLabelData(array $items, string $layout = 'thermal_double'): array
    {
        $labelList = [];

        foreach ($items as $item) {
            $varId = $item['id'] ?? null;
            $qty   = max(1, (int)($item['qty'] ?? 1));

            if (!$varId) continue;

            $variation = ProductVariation::with(['product', 'product.store'])->find($varId);
            if (!$variation) continue;

            $product = $variation->product;
            $barcodeText = $variation->sub_sku ?: ($product->sku ?: str_pad($variation->id, 8, '0', STR_PAD_LEFT));
            $productName = $product->name ?? 'Produk';
            $variationName = ($variation->name && $variation->name !== 'DUMMY') ? $variation->name : '';
            $price = (float)($variation->default_sell_price ?: $product->sell_price ?: 0);
            $storeName = $product->store->name ?? 'POSHUB';

            $svg = $this->generateCode128Svg($barcodeText, 35);

            for ($i = 0; $i < $qty; $i++) {
                $labelList[] = [
                    'store_name'     => $storeName,
                    'product_name'   => $productName,
                    'variation_name' => $variationName,
                    'barcode_text'   => $barcodeText,
                    'barcode_svg'    => $svg,
                    'price'          => $price,
                    'price_formatted'=> 'Rp ' . number_format($price, 0, ',', '.'),
                ];
            }
        }

        return [
            'layout' => $layout,
            'total_labels' => count($labelList),
            'labels' => $labelList,
        ];
    }
}
