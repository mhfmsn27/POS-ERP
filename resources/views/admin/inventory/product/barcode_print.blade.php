<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Barcode - POSHUB</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 15px;
        }
        .no-print-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: #1f57db;
            color: #fff;
            padding: 8px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }
        .btn-print:hover {
            background: #1742a8;
        }

        /* GRID LAYOUTS */
        .barcode-container {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: flex-start;
        }

        /* Thermal Double 40x30mm */
        .layout-thermal_double .barcode-card {
            width: 40mm;
            height: 30mm;
            border: 1px dashed #cbd5e1;
            padding: 3px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            background: #fff;
            page-break-inside: avoid;
        }

        /* Thermal Single 50x30mm */
        .layout-thermal_single .barcode-card {
            width: 50mm;
            height: 30mm;
            border: 1px dashed #cbd5e1;
            padding: 4px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            background: #fff;
            page-break-inside: avoid;
        }

        /* A4 Sheet 3x8 (70x37mm) */
        .layout-sheet_a4_24 .barcode-card {
            width: 68mm;
            height: 35mm;
            border: 1px dashed #cbd5e1;
            padding: 5px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            background: #fff;
            page-break-inside: avoid;
        }

        .label-store {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
            line-height: 1.1;
        }
        .label-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            max-height: 24px;
            overflow: hidden;
            line-height: 1.1;
            margin: 1px 0;
        }
        .label-variation {
            font-size: 9px;
            color: #64748b;
        }
        .label-svg-wrap {
            width: 90%;
            margin: 2px auto;
        }
        .label-code {
            font-size: 10px;
            letter-spacing: 1.5px;
            font-family: monospace;
            font-weight: bold;
        }
        .label-price {
            font-size: 12px;
            font-weight: 800;
            color: #000000;
        }

        @media print {
            body {
                background: transparent;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .barcode-card {
                border: none !important;
            }
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body class="layout-{{ $layout }}">

    <div class="no-print-bar">
        <div>
            <strong style="font-size: 16px; color: #1e293b;">🏷️ Preview Cetak Label Barcode & Rak</strong>
            <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Total Label: {{ count($labels) }} lembar | Format: {{ strtoupper(str_replace('_', ' ', $layout)) }}</p>
        </div>
        <div>
            <button type="button" class="btn-print" onclick="window.print()">🖨️ Cetak Label Sekarang</button>
        </div>
    </div>

    <div class="barcode-container">
        @foreach($labels as $l)
        <div class="barcode-card">
            <div class="label-store">{{ $l['store_name'] }}</div>
            <div class="label-title">{{ $l['product_name'] }}</div>
            @if(!empty($l['variation_name']))
            <div class="label-variation">({{ $l['variation_name'] }})</div>
            @endif
            <div class="label-svg-wrap">
                {!! $l['barcode_svg'] !!}
            </div>
            <div class="label-code">{{ $l['barcode_text'] }}</div>
            <div class="label-price">{{ $l['price_formatted'] }}</div>
        </div>
        @endforeach
    </div>

</body>
</html>
