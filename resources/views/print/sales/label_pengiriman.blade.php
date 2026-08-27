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
            text-align: left;
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
            margin: 0;
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
        <header style="border-top: 1px solid black; border-bottom:1px solid black; padding:5px;">
            <h1>FROM</h1>
            <h1><?=$transaction->store->name ?? '';?></h1>
            <h1><?=$transaction->store->phone ?? '';?></h1>
        </header>
        <section class="recipient-info">
            <p><strong>To :</strong> <br /></p>
            <p><strong><?=$transaction->customer->name ?? '';?></strong> <br /> <?=$transaction->address ?? '';?> </p>
            <p><strong><?=$transaction->customer->phone ?? '';?></strong> </p>
           
        </section>
        <section class="item-table" style="margin-top: 10px;">
            <table>
                <thead>
                    <tr>
                        <th style="text-align: center;">Nama Barang</th>
                        <th style="text-align: center;">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaction->sell as $sell)
                    <tr>
                        <td>{{$sell->variation->full_name ?? ''}}</td>
                        <td>{{number_format($sell->qty)}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">Ekspedisi : <?=$transaction->courier->name ?? '';?></td>
                        
                    </tr>
                    <tr>
                        <td colspan="2">No.Faktur : <?=$transaction->ref_no;?></td>
                        
                    </tr>
                </tfoot>
            </table>
        </section>

        <section class="signature" style="margin-bottom: 20px;">
            <p><strong>Keterangan:</strong></p>
        </section>
        <div style="text-align: center;">
            <img style="max-width: 100px;" src="<?= asset('images/fragile.webp'); ?>" alt="Fragile"/>
        </div>
    </div>
</body>

</html>