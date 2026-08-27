<table class="table table-striped">
    <thead>
        <tr>
            <th colspan="9" style="background-color: #0084ff; color:#ffffff; text-align:center; font-size: 30px; height: 50px; font-weight:50;"><b>Master Data Produk</b></th>
        </tr>
        <tr>
            <th style="width: 150px; text-align:center; height: 60px;">Nama Produk</th>
            <th style="width: 200px; text-align:center; ">Nama Variant</th>
            <th style="width: 150px; text-align:center; ">Kategori</th>
            <th style="width: 150px; text-align:center; ">Brand</th>
            <th style="width: 200px; text-align:center; ">Satuan</th>
            <th style="width: 200px; text-align:center; ">Stok</th>
            <th style="width: 200px; text-align:center; ">Harga Modal</th>
            <th style="width: 200px; text-align:center; ">Harga Jual</th>
            <th style="width: 200px; text-align:center; ">Harga Grosir</th>
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

        @foreach($d->variant as $v)
        <tr>
            <td style="height: 40px;"> {{$d->name}} </td>
            <td>
                @if($v->name != 'no-name')
                {{$v->name}}
                @endif
            </td>
            <td> {{$d->category->name ?? ''}} </td>
            <td> {{$d->brand->name ?? ''}} </td>
            <td> {{$v->unit->name ?? ''}} </td>
            <td>
                @if($d->is_stock == 'yes')
                {{(int)$v->stock->sum("qty_available")}}
                @else
                -
                @endif
            </td>
            <td> {{averaging_price($v,null)}} </td>
            <td> {{(int)$v->selling_price}} </td>
            <td> {{(int)$v->grocery}} </td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>