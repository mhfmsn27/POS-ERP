<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pembelian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .container {
            margin: auto;
            padding: 15px;  
        }

        .header {
            display: flex;
            justify-content: space-between;
        }

        .title {
            font-size: 24px;
            margin: 0;
        }

        .kepada {
            text-align: right;
        }

        .kepada p {
            margin: 0;
            font-size: 16px;
        }


        .nomor p,
        .pembayaran p,
        .admin p {
            margin: 0;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .keterangan {
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }
    </style>
    <script>
        function autoPrint() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        }
        window.onload = autoPrint;
    </script>
</head>

<body>
    <div class="container">
        <div class="header">
            <div>
                <h1 style="margin-top: 0px; margin-bottom:0px;"><?= $transaction->store->name ?? (my_store_detail()->name ?? 'POSHUB ACCOUNTING'); ?></h1>
                <div>
                    <b>Kepada :</b> <br />
                    <?= $transaction->supplier->name ?? ''; ?> <br />
                    <?= $transaction->supplier->address ?? ''; ?>
                </div>
            </div>
            <div>
                <h1 style="margin-top: 0px;">Pembayaran Pembelian</h1>
                <div>
                    <table>
                        <tr>
                            <th>Nomor</th>
                            <th>Tanggal</th>
                            <th>Pembayaran</th>
                            <th>Admin</th>
                        </tr>
                        <tr>
                            <th><?= $transaction->ref_no; ?></th>
                            <th><?= tanggal_indo(substr($transaction->transaction_date, 0, 10)); ?> </th>
                            <th><?= $transaction->method->name ?? ''; ?> </th>
                            <th><?= auth()->user()->name; ?> </th>
                        </tr>

                    </table>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No. Faktur</th>
                    <th>Tgl. Faktur</th>
                    <th>Total Faktur</th>
                    <th>Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->faktur_detail as $faktur)
                <tr>
                    <td><?= $faktur->transaction_due->no_ref ?? ''; ?></td>
                    <td><?= tanggal_indo($faktur->transaction_due->date ?? ''); ?></td>
                    <td><?= number_format($faktur->transaction_due->amount ?? 0); ?></td>
                    <td><?= number_format($faktur->pay_amount); ?></td>
                </tr>
                @endforeach


                <tr>
                    <td colspan="3">
                        <b>Total</b>
                    </td>
                    <td>
                        <b><?= number_format($transaction->final_total); ?></b>
                    </td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="2" rowspan="3" style="vertical-align: top; text-align:left; height:100px;">
                        Keterangan :
                    </td>
                    <td rowspan="3" style="vertical-align: top;">Pemberi</td>
                    <td rowspan="3" style="vertical-align: top;">Penerima</td>
                </tr>


            </tfoot>
        </table>

        <div class="footer">
            <p>Halaman 1 dari 1</p>
        </div>
    </div>
</body>

</html>