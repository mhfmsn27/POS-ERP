<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{$page}} </title>
    <style>
        @font-face {
            font-family: SourceSansPro;
            src: url(SourceSansPro-Regular.ttf);
        }

        #logo {
           text-align: center;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table,
        .table th,
        .table td {
            border: 1px solid black;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-family: SourceSansPro;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
            margin-top: 8px;
        }

        #logo img {
            height: 70px;
        }

        #company {
            float: right;
            text-align: right;
        }


        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {
            float: right;
            text-align: right;
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 2.4em;
            line-height: 1em;
            font-weight: normal;
            margin: 0 0 10px 0;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 20px;
            background: #EEEEEE;
        }

        table td h3 {
            color: #57B223;
            font-size: 1.2em;
            font-weight: normal;
            margin: 0 0 0.2em 0;
        }

        table .no {
            color: #FFFFFF;
            font-size: 1.6em;
            background: #57B223;
        }


        table .total {
            background: #57B223;
            color: #FFFFFF;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }


        table tfoot td {
            padding: 10px 20px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-top: 1px solid #AAAAAA;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr:last-child td {
            color: #57B223;
            font-size: 1.4em;
            border-top: 1px solid #57B223;

        }

        table tfoot tr td:first-child {
            border: none;
        }

        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }
    </style>
</head>

<body onload="window.print();" inmaintabuse="1">
    <!--  -->
    <header class="clearfix">
        <div id="logo">
            {!! $qr !!} <br />
            Scan kode berikut untuk melihat update
        </div>
        <div id="company">
            <br>
            <h2 class="name">{{$transaction->store->name ?? ''}}</h2>
            <div>+{{$transaction->store->phone ?? ''}}</div>
            <div>{{$transaction->store->address ?? ''}}</div>
        </div>

    </header>
    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <div class="to">No.Ref : {{$transaction->ref_no}}</div>
                <div class="name">Nama Pemilik : {{$transaction->customer->name ?? ''}}</div>
                <div class="address">Alamat : {{$transaction->customer->address ?? ''}} </div>
                <div class="email">No HP : {{$transaction->customer->phone ?? ''}} </div>
            </div>
            <div id="invoice">
                <div class="date">Tanggal : {{$transaction->created_at->format('Y-m-d')}}</div>
                <div class="date">Estimasi Selesai : {{substr($transaction->estimate_date,0,10)}}</div>
                <div class="date">Estimasi Biaya : Rp. {{number_format($transaction->estimate_price)}} </div>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Keluhan </th>
                    <th>Kelengkapan </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->details as $detail)
                <tr>
                    <td>{{$detail->product_name}} </td>
                    <td>{{$detail->complaint}} </td>
                    <td>{{$detail->completeness}} </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table border="1" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td>
                        <p><strong>Syarat dan Ketentuan RMA :</strong></p>
                        <p>1. Produk sudah di cek dan sesuai tanda terima<br>2. Produk yang Tidak di ambil 2 Minggu setelah di konfirmasi Selesai akan dikenakan biaya penitipan sebesar Rp10.000 / Hari&nbsp;<br>3. Produk yang tidak di ambil 1 Bulan setelah di konfirmasi Selesai di anggap TIDAK DIINGINKAN lagi oleh pemilik nya. Toko akan memusnahkan produk nya.<br>4. Produk yang sudah di musnahkan tidak di Ganti atau di minta kembali.<br>5. Pembeli / Customer dianggap telah Seteju dan Mengerti syarat dan ketentuan</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table border="0" cellspacing="0" cellpadding="0">
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">
                        <p align="center">Pelanggan <br> <br> <br> <br> </p>
                        <hr>
                        <p></p>
                    </td>
                    <td colspan="2">
                        <p align="center">Petugas <br> <br> <br> <br> </p>
                        <hr>
                        <p></p>
                    </td>
                </tr>
                <tr>
                </tr>
                <tr>
                </tr>
            </tfoot>
        </table>
    </main>
</body>

</html>