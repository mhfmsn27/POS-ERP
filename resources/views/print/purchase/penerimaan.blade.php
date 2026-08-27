<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima Penerimaan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: top; 
            background-color: #f0f0f0;
        }

        .container {
            background: #ffffff;
            padding: 10px;
            width: 88mm;
            border: 1px solid #000;
        }

        header {
            text-align: center;
            margin-bottom: 10px;
        }

        header h1 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        .recipient-info p,
        .remarks p,
        .signature p {
            margin: 5px 0;
            font-size: 12px;

        }

        .item-table {
            margin-bottom: 10px;
        }

        .item-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .item-table th,
        .item-table td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 5px;
        }

        .item-table th {
            background-color: #f2f2f2;
        }

        .signature-box {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .signature-box p {
            margin: 0;
            padding: 10px;
            border: 1px solid #000;
            width: 40%;
            text-align: center;
            font-size: 12px;
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
        <header style="border-top: 1px solid black; border-bottom:1px solid black; padding:5px; text-align: center;">
            <h2 style="margin: 0; font-size: 15px; font-weight: bold;"><?= $transaction->store->name ?? (my_store_detail()->name ?? 'POSHUB ACCOUNTING'); ?></h2>
            <h1 style="font-size: 13px; margin: 4px 0 0 0;">BUKTI PENERIMAAN BARANG</h1>
        </header>
        <section class="recipient-info">
            <p><strong>Kepada:</strong> <br /> <?= $transaction->supplier->name ?? ''; ?> </p>
            <p><strong>Alamat:</strong> <br /> <?= $transaction->supplier->address ?? ''; ?> </p>
            <p><strong>No. Form:</strong> <?= $transaction->ref_no; ?></p>
            <p><strong>Tanggal:</strong> <?= tanggal_indo(substr($transaction->transaction_date, 0, 10)); ?></p>

        </section>
        <section class="item-table">
            <table>
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaction->purchase_received as $received)
                    <tr>
                        <td>{{$received->variation->full_name ?? ''}}</td>
                        <td>{{number_format($received->quantity)}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="signature" style="margin-bottom: 100px;">
            <p><strong>Keterangan:</strong></p>
        </section>
    </div>
</body>

</html>