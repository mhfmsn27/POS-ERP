<table class="table table-striped">
    <thead> 
        <tr>
            <th style="width: 100px; text-align:center; height: 40px;"><b>Variation ID</b></th>
            <th style="width: 200px; text-align:center"><b>Product ID</b></th>
            <th style="width: 200px; text-align:center"><b>Harga Modal</b></th>
            <th style="width: 200px; text-align:center"><b>Nama Produk</b></th>
            <th style="width: 200px; text-align:center"><b>Toko</b></th>
            <th style="width: 100px; text-align:center"><b>Jumlah Stok</b></th>
            <th style="width: 150px; text-align:center"><b>Kurangi Stok </b></th> 
            <th style="width: 200px; text-align:center"><b>Dana Dipulihkan </b></th> 
        </tr> 
    </thead>
    <tbody>
        @foreach ($data as $d)
        <tr>
            <td> {{ $d['id'] }}</td>
            <td style="text-align:left;"> {{ $d['product_id'] }} </td>
            <td style="text-align:left;"> {{ $d['purchase_price'] }} </td>
            <td style="text-align:left;"> {{ $d['name'] }} </td>
            <td style="text-align:left;"> {{ $d['store'] }} </td>
            <td style="text-align:left;"> {{ $d['stock'] }} </td>
            <td style="text-align:left;">0</td> 
            <td style="text-align:left;">0</td> 
        </tr>
        @endforeach 
    </tbody> 
</table>
