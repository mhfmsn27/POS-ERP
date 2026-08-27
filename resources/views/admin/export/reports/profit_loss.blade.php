<table class="table table-striped">
      <thead>
            <tr>
                  <th colspan="3" style="background-color: yellow; text-align:center; font-size: 30px; height: 50px; font-weight:50;"><b>Laporan Laba Rugi</b></th>
            </tr>
            <tr>
                  <th style="width: 300px; text-align:center; height: 60px;">Keterangan</th>
                  <th style="width: 300px; text-align:center; ">(+ / - )</th>
                  <th style="width: 300px; text-align:center; ">Subtotal</th>
            </tr>
            <tr>
                  <th style="text-align: center; background-color:#3c8dbc; color:white">1</th>
                  <th style="text-align: center; background-color:#3c8dbc; color:white">2</th>
                  <th style="text-align: center; background-color:#3c8dbc; color:white">3</th>
            </tr>
      </thead>
      <tbody>
            <tr>
                  <td style="text-align:left; height: 40px;">
                        Return Penjualan ( Setelah Di Potong Harga Modal )
                  </td>
                  <td style="text-align:left;"> - </td>
                  <td style="text-align:left;"> {{ my_currency($profitsell->dikembalikan) }} </td>
            </tr>
            <tr>
                  <td style="text-align:left; height: 40px;">
                        Total Pengurangan Stok Adjustment
                  </td>
                  <td style="text-align:left;"> - </td>
                  <td style="text-align:left;"> {{ my_currency($data['stock_adjustment']->total ?? 0) }} </td>
            </tr>
            <tr>
                  <td style="text-align:left; height: 40px;">
                        Total Pengeluaran
                  </td>
                  <td style="text-align:left;"> - </td>
                  <td style="text-align:left;"> {{ my_currency($data['total_expense']) }} </td>
            </tr>
            <tr>
                  <td style="text-align:left; height: 40px;">
                        Biaya Ongkos Kirim Pembelian
                  </td>
                  <td style="text-align:left;"> - </td>
                  <td style="text-align:left;"> {{ my_currency($data['purchase_shipping']) }} </td>
            </tr>
            <tr>
                  <td style="text-align:left; height: 40px;">
                        Biaya Ongkos Kirim Transfer Stok
                  </td>
                  <td style="text-align:left;"> - </td>
                  <td style="text-align:left;"> {{ my_currency($data['transfer_shipping']) }} </td>
            </tr>
            <tr>
                  <td style="text-align:left; height: 40px;">
                        Diskon Penjualan
                  </td>
                  <td style="text-align:left;"> - </td>
                  <td style="text-align:left;"> {{ my_currency($data['sell_discount']) }} </td>
            </tr>

            <tr>
                  <td style="text-align:left; height: 40px;">
                        Biaya Ongkos Kirim Penjualan
                  </td>
                  <td style="text-align:left;"> + </td>
                  <td style="text-align:left;"> {{ my_currency($data['sell_shipping']) }} </td>
            </tr>
            <tr>
                  <td style="text-align:left; height: 40px;">
                        Biaya Pemulihan Stok Opname Adjustment
                  </td>
                  <td style="text-align:left;"> + </td>
                  <td style="text-align:left;"> {{ my_currency($data['amount_recovered']) }} </td>
            </tr>
            <tr>
                  <td style="text-align:left; height: 40px;">
                        Diskon Pembelian
                  </td>
                  <td style="text-align:left;"> + </td>
                  <td style="text-align:left;"> {{ my_currency($data['purchase_discount']) }} </td>
            </tr>

      </tbody>
      <tfoot>
            @php
            $adjustment = $data['stock_adjustment']->total ?? 0;
            $jumlah = $adjustment + $data['total_expense'] + $data['purchase_shipping'] + $data['transfer_shipping'] + $data['sell_discount'];
            $jml = $data['sell_shipping'] + $data['amount_recovered'] + $data['purchase_discount'];
            $profiitbersih = ($profitsell->terjual - $profitsell->dikembalikan) - $jumlah + $jml;
            @endphp
            <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                  <th colspan="2" style="height: 30px; font-size:20px; background-color:#5cb85c; text-align:center;">Profit Bersih</th>
                  <th style="text-align:right;"><b>{{ my_currency($profiitbersih) }}</b></th>
            </tr>
      </tfoot>
</table>