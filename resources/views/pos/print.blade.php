<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page ?? 'Struk Transaksi' }} - {{ $store->name ?? 'POSHUB' }}</title>

    <style>
        /* ================= THERMAL RECEIPT STYLESHEET ================= */
        @page {
            margin: 0;
            size: auto;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, 'Lucida Console', monospace, sans-serif;
            color: #000;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px 0;
            font-size: 11px;
            line-height: 1.35;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Paper Size Variants */
        .receipt-container {
            background: #ffffff;
            margin: 0 auto;
            padding: 12px 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
        }

        .paper-58 {
            width: 215px;
            max-width: 58mm;
            font-size: 10px;
            padding: 8px 10px;
        }

        .paper-80 {
            width: 310px;
            max-width: 80mm;
            font-size: 11.5px;
            padding: 14px 16px;
        }

        /* Typography & Layout Utilities */
        .text-center { text-align: center; }
        .text-start  { text-align: left; }
        .text-end    { text-align: right; }
        .fw-bold     { font-weight: bold; }
        .fw-semibold { font-weight: 600; }
        
        .store-logo {
            max-height: 42px;
            max-width: 140px;
            margin: 0 auto 6px auto;
            display: block;
            filter: grayscale(100%) contrast(150%);
        }

        .store-title {
            margin: 0;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: -0.2px;
            text-transform: uppercase;
        }
        .paper-80 .store-title {
            font-size: 16px;
        }

        .store-subtitle {
            margin: 2px 0;
            font-size: 9.5px;
            color: #222;
        }

        .dashed-line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .double-line {
            border-top: 2px dashed #000;
            margin: 6px 0;
        }

        /* Tabular Breakdown */
        .table-receipt {
            width: 100%;
            border-collapse: collapse;
            font-size: inherit;
        }

        .table-receipt td, .table-receipt th {
            padding: 2px 0;
            vertical-align: top;
        }

        .item-row td {
            padding-top: 3px;
        }

        .item-subline {
            font-size: 9px;
            color: #333;
            padding-left: 6px;
        }

        .total-row {
            font-size: 13px;
            font-weight: 900;
        }
        .paper-80 .total-row {
            font-size: 14.5px;
        }

        .barcode-wrapper {
            margin: 8px auto 4px auto;
            text-align: center;
        }
        .barcode-wrapper svg {
            max-width: 85%;
            height: 28px;
        }

        .footer-note {
            font-size: 9.5px;
            margin: 3px 0;
            line-height: 1.3;
        }

        .brand-watermark {
            font-size: 8.5px;
            color: #555;
            margin-top: 6px;
            letter-spacing: 0.5px;
        }

        /* Non-Printable Floating Action Toolbar */
        .no-print-toolbar {
            position: fixed;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            background: #0f172a;
            color: #fff;
            padding: 8px 16px;
            border-radius: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 9999;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 13px;
        }

        .toolbar-btn {
            background: #1e293b;
            color: #fff;
            border: 1px solid #334155;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }
        .toolbar-btn:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }
        .toolbar-btn.active {
            background: #2563eb;
            border-color: #2563eb;
        }
        .toolbar-btn.btn-wa {
            background: #10b981;
            border-color: #10b981;
        }
        .toolbar-btn.btn-wa:hover {
            background: #059669;
        }

        /* PRINT MEDIA QUERY */
        @media print {
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }
            .no-print-toolbar {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 2mm !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
        }
    </style>
</head>

<body>

    <!-- FLOATING ACTION TOOLBAR -->
    <div class="no-print-toolbar">
        <button onclick="window.print();" class="toolbar-btn active">🖨️ Cetak Struk</button>
        <span style="color: #64748b;">|</span>
        <a href="?paper=58" class="toolbar-btn {{ ($paperWidth ?? 80) == 58 ? 'active' : '' }}">58mm (2")</a>
        <a href="?paper=80" class="toolbar-btn {{ ($paperWidth ?? 80) == 80 ? 'active' : '' }}">80mm (3")</a>
        <span style="color: #64748b;">|</span>
        <button onclick="sendWhatsappReceipt('{{ $data->id }}');" class="toolbar-btn btn-wa" id="btnWaReceipt">📱 Kirim WA</button>
        <button onclick="window.close();" class="toolbar-btn" style="background: #334155;">✖ Tutup</button>
    </div>

    <!-- THERMAL RECEIPT BODY -->
    <div class="receipt-container {{ ($paperWidth ?? 80) == 58 ? 'paper-58' : 'paper-80' }}">

        <!-- 1. DYNAMIC STORE HEADER -->
        <div class="text-center">
            @if(!empty($logoUrl))
                <img src="{{ $logoUrl }}" alt="Logo" class="store-logo" />
            @endif

            <h1 class="store-title">{{ $store->name ?? 'POSHUB OFFICIAL STORE' }}</h1>
            
            @if(!empty($store->slogan ?? ($settings->header_text ?? null)))
                <p class="store-subtitle fw-semibold">{{ $store->slogan ?? $settings->header_text }}</p>
            @endif

            <p class="store-subtitle">{{ $store->address ?? 'Indonesia' }}</p>
            
            @if(!empty($store->phone))
                <p class="store-subtitle">Telp/WA: {{ $store->phone }}</p>
            @endif

            @if(!empty($store->tax) || !empty($store->gst))
                <p class="store-subtitle">NPWP: {{ $store->tax ?? $store->gst }}</p>
            @endif
        </div>

        <div class="dashed-line"></div>

        <!-- 2. TRANSACTION METADATA -->
        <table class="table-receipt">
            <tr>
                <td>No.Nota:</td>
                <td class="text-end fw-bold">{{ $data->ref_no ?? ('TRX-' . $data->id) }}</td>
            </tr>
            <tr>
                <td>Waktu:</td>
                <td class="text-end">{{ date('d/m/Y H:i', strtotime($data->transaction_date ?? $data->created_at)) }}</td>
            </tr>
            <tr>
                <td>Kasir:</td>
                <td class="text-end">{{ $data->created_user->name ?? 'Kasir' }}</td>
            </tr>
            @if(!empty($data->customer) && $data->customer->name != 'Walk-In Customer')
            <tr>
                <td>Pelanggan:</td>
                <td class="text-end">{{ $data->customer->name }}</td>
            </tr>
            @if(!empty($loyaltyTier) && $loyaltyTier != 'Reguler')
            <tr>
                <td>Member VIP:</td>
                <td class="text-end fw-bold">[{{ $loyaltyTier }}]</td>
            </tr>
            @endif
            @endif
            @if(!empty($data->order_type) || !empty($data->table))
            <tr>
                <td>Pesanan:</td>
                <td class="text-end">
                    {{ strtoupper(str_replace('_', ' ', $data->order_type ?? 'Take Away')) }}
                    @if(!empty($data->table)) (Meja: {{ $data->table->name ?? $data->table_id }}) @endif
                </td>
            </tr>
            @endif
        </table>

        <div class="dashed-line"></div>

        <!-- 3. ITEMS BREAKDOWN -->
        <table class="table-receipt">
            <thead>
                <tr class="fw-bold" style="border-bottom: 1px dashed #000;">
                    <th class="text-start" style="width: 50%;">Item</th>
                    <th class="text-center" style="width: 15%;">Qty</th>
                    <th class="text-end" style="width: 35%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->sell as $sell)
                @php
                    $productName = $sell->product->name ?? ($sell->item_name ?? 'Produk');
                    $variationName = (!empty($sell->variation) && $sell->variation->name != 'no-name') ? ' (' . $sell->variation->name . ')' : '';
                    $disc = (float)($sell->disc_amount ?? 0);
                    $price = (float)$sell->unit_price;
                    $qty = (float)$sell->qty;
                    $lineTotal = ($price - $disc) * $qty;
                @endphp
                <tr class="item-row">
                    <td colspan="3" class="fw-semibold">{{ $productName }}{{ $variationName }}</td>
                </tr>
                <tr>
                    <td class="item-subline">
                        @ {{ number_format($price) }}
                        @if($disc > 0) <span style="color: #555;">(Disc -{{ number_format($disc) }})</span> @endif
                    </td>
                    <td class="text-center" style="font-size: 10px;">{{ $qty }} {{ $sell->unit->name ?? '' }}</td>
                    <td class="text-end fw-semibold">{{ number_format($lineTotal) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="dashed-line"></div>

        <!-- 4. TOTALS & PAYMENT BREAKDOWN -->
        <table class="table-receipt">
            <tr>
                <td>Subtotal</td>
                <td class="text-end">{{ number_format($data->total_before_tax ?? $data->sell->sum(function($s){ return $s->unit_price * $s->qty; })) }}</td>
            </tr>
            
            @if(!empty($data->discount_amount) && $data->discount_amount > 0)
            <tr>
                <td>Diskon Transaksi @if($data->discount_type != 'fixed') ({{ $data->discount_amount }}%) @endif</td>
                <td class="text-end">-{{ number_format($data->discount_final ?? $data->discount_amount) }}</td>
            </tr>
            @endif

            @if(!empty($data->voucher))
            <tr>
                <td>Voucher ({{ $data->voucher->name ?? 'Promo' }})</td>
                <td class="text-end">-{{ number_format($data->voucher->amount ?? 0) }}</td>
            </tr>
            @endif

            @if(!empty($data->tax_amount) && $data->tax_amount > 0)
            <tr>
                <td>PPN / Pajak</td>
                <td class="text-end">+{{ number_format($data->tax_final ?? $data->tax_amount) }}</td>
            </tr>
            @endif

            @if(!empty($data->service_charge) && $data->service_charge > 0)
            <tr>
                <td>Biaya Layanan</td>
                <td class="text-end">+{{ number_format($data->service_charge) }}</td>
            </tr>
            @endif

            @if(!empty($data->shipping_charges) && $data->shipping_charges > 0)
            <tr>
                <td>Ongkos Kirim</td>
                <td class="text-end">+{{ number_format($data->shipping_charges) }}</td>
            </tr>
            @endif

            @if(!empty($data->other_charges) && $data->other_charges > 0)
            <tr>
                <td>Biaya Lain</td>
                <td class="text-end">+{{ number_format($data->other_charges) }}</td>
            </tr>
            @endif

            <tr class="double-line"><td colspan="2"></td></tr>

            <tr class="total-row">
                <td>GRAND TOTAL</td>
                <td class="text-end">Rp {{ number_format($data->final_total) }}</td>
            </tr>
            
            <tr class="double-line"><td colspan="2"></td></tr>

            <tr>
                <td>Metode Bayar</td>
                <td class="text-end fw-semibold">{{ ucfirst(str_replace('_', ' ', $data->payment_method ?? ($data->method->name ?? 'Tunai'))) }}</td>
            </tr>
            <tr>
                <td>Bayar / Diterima</td>
                <td class="text-end">{{ number_format($cashPaid) }}</td>
            </tr>
            
            @if($change > 0 || $cashPaid >= $finalTotal)
            <tr class="fw-bold">
                <td>Kembalian</td>
                <td class="text-end">Rp {{ number_format($change) }}</td>
            </tr>
            @endif

            @if($dueAmount > 0 && ($data->status == 'due' || $data->status == 'partial'))
            <tr class="fw-bold" style="color: #900;">
                <td>Sisa Tagihan (Tempo)</td>
                <td class="text-end">Rp {{ number_format($dueAmount) }}</td>
            </tr>
            @endif
        </table>

        <!-- 5. LOYALTY POINTS REWARD (CRM) -->
        @if(!empty($earnedPoints) && $earnedPoints > 0)
        <div class="dashed-line"></div>
        <table class="table-receipt" style="font-size: 9.5px;">
            <tr>
                <td>Poin Transaksi Ini:</td>
                <td class="text-end fw-bold">+{{ number_format($earnedPoints) }} Poin</td>
            </tr>
            @if($customerPoints > 0)
            <tr>
                <td>Saldo Poin Anda:</td>
                <td class="text-end">{{ number_format($customerPoints) }} Poin</td>
            </tr>
            @endif
        </table>
        @endif

        <div class="dashed-line"></div>

        <!-- 6. DYNAMIC FOOTER & QR/BARCODE -->
        <div class="text-center">
            <p class="footer-note fw-bold">{{ $store->footer_text ?? 'Terima Kasih Atas Kunjungan Anda!' }}</p>
            <p class="footer-note" style="font-size: 8.5px;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan tanpa struk resmi.</p>
            
            @if(!empty($barcodeSvg))
            <div class="barcode-wrapper">
                {!! $barcodeSvg !!}
                <div style="font-size: 8.5px; letter-spacing: 1px; margin-top: 2px;">{{ $data->ref_no ?? $data->id }}</div>
            </div>
            @endif

            <p class="brand-watermark">Powered by <strong>POSHUB ACCOUNTING</strong></p>
        </div>

    </div>

    <!-- AUTO-PRINT SCRIPT & AJAX WHATSAPP DISPATCH -->
    <script>
        @if($autoPrint ?? true)
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 300);
        });
        @endif

        function sendWhatsappReceipt(transactionId) {
            const btn = document.getElementById('btnWaReceipt');
            btn.innerHTML = '⏳ Mengirim...';
            btn.disabled = true;

            fetch(`/api/omnichannel/send-receipt/${transactionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    btn.innerHTML = '✅ Terkirim!';
                    alert('Struk digital berhasil dikirimkan ke WhatsApp pelanggan.');
                } else {
                    btn.innerHTML = '❌ Gagal';
                    alert('Gagal mengirim WhatsApp: ' + (data.message || 'Periksa nomor pelanggan.'));
                }
                setTimeout(() => {
                    btn.innerHTML = '📱 Kirim WA';
                    btn.disabled = false;
                }, 3000);
            })
            .catch(err => {
                btn.innerHTML = '❌ Error';
                alert('Gagal menghubungi gateway WhatsApp.');
                setTimeout(() => {
                    btn.innerHTML = '📱 Kirim WA';
                    btn.disabled = false;
                }, 3000);
            });
        }
    </script>
</body>

</html>