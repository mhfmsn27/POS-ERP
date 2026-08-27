<table class="table table-striped">
      <thead>
            <tr>
                  <th colspan="9" style="background-color: yellow; text-align:center; font-size: 30px; height: 50px; font-weight:50;"><b>Laporan Return Penjualan</b></th>
            </tr>
            <tr>
                  <th style="width: 100px; text-align:center; height: 40px;">No Ref</th>
                  <th style="width: 170px; text-align:center; ">Tanggal</th>
                  <th style="width: 150px; text-align:center; ">Toko</th>
                  <th style="width: 180px; text-align:center; ">Produk</th>
                  <th style="width: 150px; text-align:center; ">Pelanggan</th>
                  <th style="width: 150px; text-align:center; ">Kondisi Pengembalian</th>
                  <th style="width: 100px; text-align:center; ">Qty Di Return</th>
                  <th style="width: 100px; text-align:center; ">Harga Satuan</th>
                  <th style="width: 100px; text-align:center; ">Subtotal </th>
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
                  <th style="text-align: center; background-color:#3c8dbc; color:white">9</th>
            </tr>
      </thead>
      <tbody>

            @foreach ($data as $d)
            <tr>

                  <td style="text-align:left;"> {{ $d->transaction->ref_no ?? '' }}</td>
                  <td style="text-align:left;"> {{ my_date($d->created_at) }} </td>
                  <td style="text-align:left;"> {{ $d->transaction->store->name ?? '' }} </td>
                  <td style="text-align:left;">
                        @php
                        $name = $d->sell->product->name ?? '';
                        $variation = $d->sell->variation->name ?? '';

                        $productName = $name . ' ' . $variation;
                        @endphp

                        {{$productName}}
                  </td>
                  <td style="text-align:left;"> {{ $d->transaction->customer->name ?? '' }} </td>
                  <td style="text-align:left;">
                        @php
                        if ($d->condition == 'good') {
                        echo "Baik / Masih Bagus";
                        } else {
                        echo "Sudah Rusak";
                        }
                        @endphp
                  </td>
                  <td style="text-align:right;"> {{ number_format($d->return_qty) }} </td>
                  <td style="text-align:right;"> {{ number_format($d->sell->unit_price ?? 0) }} </td>
                  <td style="text-align:right;">
                        @php
                        $allqty = $d->return_qty;
                        $subtotal_data = $d->sell->unit_price * $allqty;
                        @endphp
                        {{number_format($subtotal_data)}}
                  </td>
            </tr>
            @endforeach
      </tbody>
      <tfoot>
            <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                  <th colspan="8" style="height: 30px; font-size:100px; background-color:#5cb85c; text-align:center;">Jumlah Total</th>
                  <th style="text-align:right;"><b>{{ number_format($subtotal) }}</b></th>
            </tr>
      </tfoot>
</table>