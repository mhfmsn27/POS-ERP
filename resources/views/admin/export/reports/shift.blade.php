<table class="table table-striped">
    <thead>
        <tr>
            <th colspan="8"
                style="background-color: yellow; text-align:center; font-size: 30px; height: 50px; font-weight:50;">
                <b>Laporan Shift Register</b>
            </th>
        </tr>
        <tr>
            <th style="width: 100px; text-align:center; height: 40px;">Tanggal</th>
            <th style="width: 150px; text-align:center; ">Toko</th>
            <th style="width: 100px; text-align:center; ">Jam Buka</th>
            <th style="width: 100px; text-align:center; ">Jam Tutup </th>
            <th style="width: 100px; text-align:center; ">Open Amount</th>
            <th style="width: 100px; text-align:center; ">Close Amount</th>
            <th style="width: 100px; text-align:center; ">Other Amount</th>
            <th style="width: 100px; text-align:center; ">Jumlah Transaksi</th>
        </tr>
        <tr>
            <th style="text-align: center; background-color:#3c8dbc; color:white">1</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">2</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">3</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">4</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">5</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">6</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">7</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">8</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($data as $d)
            <tr>
                <td style="text-align:left;"> {{ dt($d->created_at) }} </td>
                <td style="text-align:left;"> {{ $d->store->name ?? '' }} </td>
                <td style="text-align:left;"> {{ shiftTime($d->created_at) }}</td>
                <td style="text-align:left;">
                    @if ($d->closed_at != null)
                        {{ substr($d->closed_at, 11, 5) }}
                    @endif
                </td>
                <td style="text-align:left;"> {{ number_format($d->open_amount) }} </td>
                <td style="text-align:left;"> {{ number_format($d->close_amount) }} </td>
                <td style="text-align:left;"> {{ number_format($d->other_amount) }} </td>
                <td style="text-align:left;"> {{ count($d->transactionshift) }} </td>
            </tr>
        @endforeach
    </tbody>

</table>
