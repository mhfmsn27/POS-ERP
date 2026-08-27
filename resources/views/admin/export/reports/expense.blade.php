<table class="table table-striped">
    <thead>
        <tr>
            <th colspan="6" style="background-color: yellow; text-align:center; font-size: 30px; height: 50px; font-weight:50;"><b>{{__('sidebar.expense_report')}}</b></th>
        </tr>
        <tr>
            <th style="width: 100px; text-align:center; height: 40px;">Ref No</th>
            <th style="width: 100px; text-align:center; ">Kategori</th>
            <th style="width: 150px; text-align:center; ">Nama</th>
            <th style="width: 100px; text-align:center; ">Store </th>
            <th style="width: 100px; text-align:center; ">Tanggal</th>
            <th style="width: 100px; text-align:center; ">Jumlah Pengeluaran</th>  
        </tr>
        <tr>
            <th style="text-align: center; background-color:#3c8dbc; color:white">1</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">2</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">3</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">4</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">5</th>
            <th style="text-align: center; background-color:#3c8dbc; color:white">6</th> 
        </tr>
    </thead>
    <tbody>

        @foreach ($data as $d)
        <tr>

            <td style="text-align:left;"> {{ $d->ref_no }}</td>
            <td style="text-align:left;"> {{ $d->category->name ?? '' }} </td>
            <td style="text-align:left;"> {{ $d->name }} </td>
            <td style="text-align:left;"> {{ $d->store->name ?? '' }} </td>
            <td style="text-align:left;"> {{ transaction_date($d->created_at) }} </td>  
            <td style="text-align:right;"> {{ number_format($d->amount) }} </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
            <th colspan="5" style="height: 30px; font-size:100px; background-color:#5cb85c; text-align:center;">{{__('general.total')}}</th> 
            <th style="text-align:right;"><b>{{ number_format($jumlahTotal) }}</b></th> 
        </tr>
    </tfoot>
</table>
