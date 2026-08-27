<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        .invoice {
            max-width: 800px;
            margin: 10px auto;
            padding: 10px;
            background: #fff;
            
        }

        header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .company-info h2 {
            margin: 0;
        }

        .customer-info h3 {
            margin: 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px; 
            font-weight: 500;
        }

        
        .items-table th,
        .items-table td {
            border: 1px solid black;
            padding: 2px;
            text-align: left; 
        }
 

        .items-table th {
            background: #f0f0f0;
        }

        tfoot td {
            font-weight: 500;
        }

        footer {
            margin-top: 20px;
        }

        .footer-info {
            margin-top: 10px;
            font-size: 0.9em;
        }

        p {
            margin-top: 3px;
            margin-bottom: 3px;
            font-size: 12px;
        }

        h2 {
            font-size: 12px;
        }
    </style> 
</head>

<body>
    <div class="invoice">
        <header>
            <div class="company-info" style="max-width:400px;">
                <h2>
                    <?= $transaction->store->name ?? ''; ?>
                </h2>
                <p><?= $transaction->store->address ?? ''; ?></p>
                <p>Telp <?= $transaction->store->phone ?? ''; ?></p>
                <div style="border-top:1px solid black; border-bottom: 1px solid black; margin-top:10px; margin-right:10px;">
                    <p> Kepada :</p>
                </div>
                <p><b><?= $transaction->customer->name ?? ''; ?> </b><br /><?= $transaction->customer->address ?? ''; ?></p>
            </div>
            <div class="invoice-info" style="min-width: 250px;">
                <h2 style="text-align:center; font-size:14px !important;">Faktur Penjualan</h2>
                <table style="border:1px solid black; width:100%">
                    <tr>
                        <td style="font-size:10px; min-width:100px; border-bottom: 1px solid black; border-right:1px solid black;">
                            <p>Tanggal</p>
                            <p><b><?= short_date(substr($transaction->transaction_date, 0, 10)); ?></b> </p>
                        </td>
                        <td style="font-size:10px; min-width:100px; border-bottom: 1px solid black; ">
                            <p>Nomor</p>
                            <p><b> <?= $transaction->ref_no; ?> </b></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:10px; min-width:100px;  border-right:1px solid black;">
                            <p>Syarat Pembayaran</p>
                            <p><b><?= $transaction->due_limit; ?> Hari</b> </p>
                        </td>
                        <td style="font-size:10px; min-width:100px; vertical-align:top;  ">
                            <p>Ekspedisi</p>
                            <p><b> <?= $transaction->courier->name ?? ''; ?> </b></p>
                        </td>
                    </tr>
                </table>
            </div>
        </header>

        <table class="items-table">
            <thead>
                <tr>
                    <td colspan="5" style="text-align: center; width:60%;">Nama Barang</td>
                    <td style="text-align: center; ">Qty</td>
                    <td style="text-align: center;">@Harga</td>
                    <td style="text-align: center;">Diskon</td>
                    <td style="text-align: center;">Total Harga</td>
                </tr>
            </thead>
            <tbody class="itemsdata">
                @foreach ($transaction->sell as $sell)
                <tr>
                    <td colspan="5">{{$sell->variation->full_name ?? ''}}</td>
                    <td style="text-align: right;"><?= (int)$sell->unit_qty; ?> <?= $sell->unit->name ?? ''; ?> </td>
                    <td style="text-align: right;">
                        <?= number_format($sell->unit_price); ?>
                    </td>
                    <td style="text-align: right;"><?= number_format($sell->disc_amount); ?> </td>
                    <td style="text-align: right;">
                        <?= number_format(($sell->unit_price - $sell->disc_amount) * $sell->qty); ?>
                    </td>
                </tr>
                @endforeach

            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" rowspan="2" style="vertical-align: top;">
                        Keterangan : <?= $transaction->additional_notes; ?>
                    </td>
                    <td colspan="2"><b>Subtotal DPP</b></td>
                    <td style="text-align: right;"><?= number_format($transaction->total_before_tax); ?> </td>
                </tr>
                <tr>
                    <td colspan="2"><b>Diskon</b></td>
                    <td style="text-align: right;"><?= number_format($transaction->discount_final); ?> </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:center;"><?= $transaction->store->footer_text ?? ''; ?></td>
                    <td colspan="2" style="text-align:right;">Total Qty : <?= number_format($transaction->qty_sell); ?></td>
                    <td colspan="2"><b>PPN</b></td>
                    <td style="text-align: right;"><?= number_format($transaction->tax_final); ?> </td>
                </tr>
                <!-- <tr>
                   
                    <td colspan="2"><b>Phh23</b></td>
                    <td style="text-align: right;">
                        <?= number_format($transaction->service_tax); ?>
                    </td>
                </tr> -->
                <tr>
                <td style="text-align:center; border: none;" >Marketing</td>
                    <td style="text-align:center; border: none;">Admin</td>
                    <td style="text-align:center; border: none;">Gudang</td>
                    <td style="text-align:center; border: none;" >Checker</td>
                    <td colspan="2" style="text-align:center; border: none;" >Diterima</td> 
                    <td colspan="2"><b>Biaya Kirim</b></td>
                    <td style="text-align: right;">
                        <?= number_format($transaction->shipping_charges); ?>
                    </td>
                </tr>

                <tr>
                    <td colspan="6" style="text-align:center; border: none;"></td>
                    <td colspan="2"><b>Total</b></td>
                    <td style="text-align: right;">
                        <?= number_format($transaction->final_total); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align:center; border: none;"></td>
                    <td colspan="2" style="text-align:center; border: none; font-size:10px;"><?= date('d M, Y H:i:s'); ?> </td>
                    <td style="text-align:center; border: none; font-size:10px;">Hal 1 dari 1 <?= !empty($transaction->print_count) ? 'Cetak ' . $transaction->print_count : 'Original'; ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>