<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page ?? 'Struk Pembayaran' }} - {{ $store->name ?? 'POSHUB' }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <style>
        @page {
            margin: 0;
            size: auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace, 'Arial', sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .print-area {
            width: 300px;
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
        }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .table-receipt {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .table-receipt td, .table-receipt th {
            padding: 2px 0;
            vertical-align: top;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        @media print {
            .print-area {
                width: 100%;
                padding: 0;
                margin: 0;
            }
        }
    </style>
    <script>
        window.onafterprint = window.close;
        window.print();
    </script>
</head>

<body>
    <div class="print-area">
        <!-- HEADER TOKO -->
        <div class="text-center">
            <h4 style="margin: 0; font-size: 16px; font-weight: bold;">{{ $store->name ?? 'POSHUB STORE' }}</h4>
            <p style="margin: 2px 0; font-size: 11px;">{{ $store->address ?? '' }}</p>
            @if(!empty($store->phone))
            <p style="margin: 0; font-size: 11px;">Telp/WA: {{ $store->phone }}</p>
            @endif
        </div>

        <div class="dashed-line"></div>

        <!-- INFO TRANSAKSI -->
        <table style="width: 100%; font-size: 11px;">
            <tr>
                <td>No: <strong>{{ $data->ref_no }}</strong></td>
                <td class="text-end">{{ transaction_date($data->transaction_date ?? $data->created_at) }}</td>
            </tr>
            <tr>
                <td>Kasir: {{ $data->created_user->name ?? 'Kasir' }}</td>
                <td class="text-end">
                    @if(!empty($data->order_type))
                        [{{ strtoupper(str_replace('_', ' ', $data->order_type)) }}]
                    @endif
                    @if(!empty($data->table))
                        Meja: {{ $data->table->name ?? $data->table_id }}
                    @endif
                </td>
            </tr>
            @if(!empty($data->customer) && $data->customer->name != 'Walk-In Customer')
            <tr>
                <td colspan="2">Pelanggan: {{ $data->customer->name }}</td>
            </tr>
            @endif
        </table>

        <div class="dashed-line"></div>

        <!-- DAFTAR ITEM BARANG -->
        <table class="table-receipt">
            <thead>
                <tr>
                    <th style="text-align: left;">Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->sell as $sell)
                @php
                    $varName = (!empty($sell->variation) && $sell->variation->name != 'no-name') ? ' (' . $sell->variation->name . ')' : '';
                    $disc = $sell->disc_amount ?? 0;
                    $lineTotal = ($sell->unit_price - $disc) * $sell->qty;
                @endphp
                <tr>
                    <td colspan="4" style="font-weight: 600;">
                        {{ $sell->product->name ?? ($sell->variation->name ?? 'Produk') }}{{ $varName }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-left: 8px;">
                        @if($disc > 0)
                            <small>Disc: -{{ number_format($disc) }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ (float)$sell->qty }} {{ $sell->unit->name ?? '' }}</td>
                    <td class="text-end">{{ number_format($sell->unit_price) }}</td>
                    <td class="text-end">{{ number_format($lineTotal) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="dashed-line"></div>

        <!-- SUMMARY PEMBAYARAN -->
        <table style="width: 100%; font-size: 11px;">
            <tr>
                <td>Subtotal</td>
                <td class="text-end">{{ number_format($data->total_before_tax ?? $data->sell->sum(function($s){ return $s->unit_price * $s->qty; })) }}</td>
            </tr>
            @if(!empty($data->discount_amount) && $data->discount_amount > 0)
            <tr>
                <td>Diskon @if($data->discount_type != 'fixed') ({{ $data->discount_amount }}%) @endif</td>
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
                <td>Pajak (PPN)</td>
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
                <td>Biaya Lain-lain</td>
                <td class="text-end">+{{ number_format($data->other_charges) }}</td>
            </tr>
            @endif

            <tr style="font-weight: bold; font-size: 13px;">
                <td>TOTAL</td>
                <td class="text-end">{{ number_format($data->final_total) }}</td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td class="text-end">{{ ucfirst(str_replace('_', ' ', $data->payment_method ?? ($data->method->name ?? 'Cash'))) }}</td>
            </tr>
            <tr>
                <td>Bayar / Diterima</td>
                <td class="text-end">{{ number_format($data->payment_cash ?? $data->final_total) }}</td>
            </tr>
            @php
                $cashPaid = (float)($data->payment_cash ?? $data->final_total);
                $finalTotal = (float)$data->final_total;
                $change = max(0, $cashPaid - $finalTotal);
                $remaining = max(0, $finalTotal - $cashPaid);
            @endphp
            @if($change > 0 || $cashPaid >= $finalTotal)
            <tr style="font-weight: bold;">
                <td>Kembalian</td>
                <td class="text-end">{{ number_format($change) }}</td>
            </tr>
            @endif
            @if($remaining > 0 && ($data->status == 'due' || $data->status == 'partial'))
            <tr style="color: #c00; font-weight: bold;">
                <td>Sisa Tagihan (Tempo)</td>
                <td class="text-end">{{ number_format($remaining) }}</td>
            </tr>
            @endif
        </table>

        <div class="dashed-line"></div>

        <!-- FOOTER & THANK YOU -->
        <div class="text-center" style="font-size: 11px; margin-top: 5px;">
            <p style="margin: 2px 0;">{{ $store->footer_text ?? 'Terima Kasih Atas Kunjungan Anda' }}</p>
            <p style="margin: 0; font-size: 9px; color: #555;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan tanpa struk resmi.</p>
            <p style="margin: 4px 0 0 0; font-size: 9px; color: #888;">Powered by POSHUB ACCOUNTING</p>
        </div>
    </div>
</body>

</html>