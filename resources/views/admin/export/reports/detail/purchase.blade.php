<table class="table table-striped">
      <thead>
            <tr>
                  <th colspan="12" style="background-color: yellow; text-align:center; font-size: 30px; height: 50px; font-weight:50;"><b>Laporan Pembelian ( Belanja Stok )</b></th>
            </tr>
            <tr>
                  <th style="width: 100px; text-align:center; height: 40px;">No Ref</th>
                  <th style="width: 170px; text-align:center; ">Tanggal</th>
                  <th style="width: 150px; text-align:center; ">Toko</th>
                  <th style="width: 170px; text-align:center; ">Produk</th>
                  <th style="width: 100px; text-align:center; ">Qty Di Beli</th>
                  <th style="width: 100px; text-align:center; ">Qty Di Return</th>
                  <th style="width: 100px; text-align:center; ">Perapihan Qty</th>
                  <th style="width: 100px; text-align:center; ">Qty Di Transfer</th>
                  <th style="width: 100px; text-align:center; ">Qty Kadaluarsa</th>
                  <th style="width: 100px; text-align:center; ">Qty Terjual</th>
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
                  <th style="text-align: center; background-color:#3c8dbc; color:white">10</th>
                  <th style="text-align: center; background-color:#3c8dbc; color:white">11</th>
                  <th style="text-align: center; background-color:#3c8dbc; color:white">12</th>
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
                        $name = $d->product->name ?? '';
                        $variation = $d->variation->name ?? '';

                        $productName = $name . ' ' . $variation;
                        @endphp

                        {{$productName}}
                  </td>
                  <td style="text-align:left;"> {{ number_format($d->quantity) }} </td>
                  <td style="text-align:right;"> {{ number_format($d->qty_return) }} </td>
                  <td style="text-align:right;"> {{ number_format($d->qty_adjusted) }} </td>
                  <td style="text-align:right;"> {{ number_format($d->qty_transfer) }} </td>
                  <td style="text-align:right;"> {{ number_format($d->qty_expire) }} </td>
                  <td style="text-align:right;"> {{ number_format($d->qty_sold) }} </td>
                  <td style="text-align:right;"> {{ number_format($d->unit_price) }} </td>
                  <td style="text-align:right;">
                        @php
                        $allqty = $d->quantity - $d->qty_return;
                        $subtotal_data = $d->purchase_price * $allqty;
                        @endphp
                        {{number_format($subtotal_data)}}
                  </td>
            </tr>
            @endforeach
      </tbody>
      <tfoot>
            <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                  <th colspan="11" style="height: 30px; font-size:100px; background-color:#5cb85c; text-align:center;">Jumlah Total</th>
                  <th style="text-align:right;"><b>{{ number_format($subtotal) }}</b></th>
            </tr>
      </tfoot>
</table>