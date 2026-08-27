<table class="table table-striped">
      <thead>
            <tr>
                  <th style="width: 100px; text-align:center; height: 40px;"><b>Nama Produk</b></th>
                  <th style="width: 200px; text-align:center"><b>Nama Variant</b></th>
                  <th style="width: 200px; text-align:center"><b>SKU</b></th>
                  <th style="width: 200px; text-align:center"><b>Kategori</b></th>
                  <th style="width: 200px; text-align:center"><b>Brand</b></th>
                  <th style="width: 100px; text-align:center"><b>Tipe Barcode</b></th>
                  <th style="width: 150px; text-align:center"><b>Peringatan Qty </b></th>
                  <th style="width: 200px; text-align:center"><b>Harga Modal </b></th>
                  <th style="width: 200px; text-align:center"><b>Harga Jual </b></th>
                  <th style="width: 200px; text-align:center"><b>Harga Grosir </b></th>
                  <th style="width: 200px; text-align:center"><b>Harga Reseller </b></th>
                  <th style="width: 200px; text-align:center"><b>Pajak </b></th>
                  <th style="width: 200px; text-align:center"><b>Unit Dasar</b></th>
                  <th style="width: 200px; text-align:center"><b>Unit Pembelian </b></th>
                  <th style="width: 200px; text-align:center"><b>Unit Penjualan </b></th>
                  <th style="width: 200px; text-align:center"><b>Barcode Produk </b></th>
            </tr>
      </thead>
      <tbody>
            @foreach ($data as $d)
            <tr>
                  <td> {{ $d->name }}</td>
                  <td style="text-align:left;">
                        @if($d->type == 'variant')
                        @foreach($d->variant as $v)
                        {{$v->name}},
                        @endforeach
                        @else
                        @endif
                  </td>
                  <td style="text-align:left;"> {{ $d->sku }} </td>
                  <td style="text-align:left;"> {{ $d->category->name ?? '' }} </td>
                  <td style="text-align:left;"> {{ $d->brand->name ?? '' }} </td>
                  <td style="text-align:left;"> {{ $d->barcode_type }} </td>
                  <td style="text-align:left;"> {{ (int)$d->alert_quantity }} </td>
                  <td style="text-align:left;">
                        @foreach($d->variant as $v)
                        {{(int)$v->purchase_price}},
                        @endforeach
                  </td>
                  <td style="text-align:left;">
                        @foreach($d->variant as $v)
                        {{(int)$v->selling_price}},
                        @endforeach
                  </td>
                  <td style="text-align:left;">
                        @foreach($d->variant as $v)
                        {{(int)$v->grocery}},
                        @endforeach
                  </td>
                  <td style="text-align:left;">
                        @foreach($d->variant as $v)
                        {{(int)$v->reseller}},
                        @endforeach
                  </td>
                  <td style="text-align:left;">
                        @foreach($d->variant as $v)
                        {{(int)$v->taxrate}},
                        @endforeach
                  </td>
                  <td style="text-align:left;">

                  </td>
                  <td style="text-align:left;">

                  </td>
                  <td style="text-align:left;">

                  </td>
                  <td style="text-align:left;">
                        @foreach($d->variant as $v)
                        {{$v->sku}},
                        @endforeach
                  </td>
            </tr>
            @endforeach
      </tbody>
</table>