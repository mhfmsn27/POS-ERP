<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page }}</title> 
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">
    <style type="text/css">
       
        @media  print{
           

        }
    </style>
    <script>
        window.onafterprint = window.close;
        window.print();
    </script>
</head>

<body>

    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card ">
                <div class="card-content">
                    <div class="card-body" id="printarea">
                        <div class="row">
                            <div class="col-sm-12">
                                <p class="pull-right"><b>{{ __('general.date') }} : </b>
                                    {{ my_date($data->created_at) }} </p>
                            </div>
                        </div>
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                                Kasir :
                                <address>
                                    {{ $data->user->name ?? '' }},
                                    {{ $data->user->email ?? '' }}
                                </address>
                            </div>

                            <div class="col-sm-4 invoice-col">
                                {{ __('general.store') }} :
                                <address>
                                    <strong>{{ $data->store->name ?? '' }},</strong>
                                    <br> {{ $data->store->address ?? '' }}
                                </address>
                            </div>

                            <div class="col-sm-4 invoice-col">
                                <b>Jam Buka : </b> {{ shiftTime($data->created_at) }} <br>
                                <b>Jam Tutup : </b> {{ substr($data->closed_at, 11, 5) }}<br>
                                <b>Status :</b>
                                @if ($data->status == 'close')
                                    Sudah Ditutup
                                @else
                                    Masih Dibuka
                                @endif
                            </div>
                        </div>

                        <br>
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nama Transaksi</th>
                                                <th>Value ( + / - ) </th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                                <td>1</td>
                                                <td> Open Shift Register / Cash in Hand </td>
                                                <td> ( + ) </td>
                                                <td>{{ number_format($data->opening_shift) }}</td>
                                            </tr>

                                            <tr>
                                                <td>2</td>
                                                <td> Penjualan Cash </td>
                                                <td> ( + ) </td>
                                                <td>{{ number_format($data->sell_cash_transaction) }}</td>
                                            </tr>

                                            <tr>
                                                <td>3</td>
                                                <td> Penjualan Via Bank / Debit </td>
                                                <td> ( + ) </td>
                                                <td>{{ number_format($data->sell_bank_transaction) }}</td>
                                            </tr>

                                            <tr>
                                                <td>4</td>
                                                <td> Penjualan Dengan Payment Lainnya </td>
                                                <td> ( + ) </td>
                                                <td>{{ number_format($data->sell_other_transaction) }}</td>
                                            </tr>

                                            <tr>
                                                <td>5</td>
                                                <td> Pengeluaran </td>
                                                <td> ( - ) </td>
                                                <td>{{ number_format($data->expense_transaction) }}</td>
                                            </tr>

                                            <tr>
                                                <td>6</td>
                                                <td> Return Penjualan </td>
                                                <td> ( - ) </td>
                                                <td>{{ number_format($data->return_transaction) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <b>Total Cash di Tangan</b>
                                                </td>
                                                <td>
                                                    <b>
                                                        {{ number_format($data->cash_in_hand) }}
                                                    </b>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <h4>Detail Transaksi Penjualan :</h4>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>No</th>
                                                <th>No Ref</th>
                                                <th>Pelanggan</th>
                                                <th>Metode Pembayaran</th>
                                                <th>Detail</th>
                                            </tr>
                                            @php
                                                $no = 1;
                                            @endphp
                                            @foreach ($data->saletransaction as $t)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $t->transaction->ref_no ?? '' }} </td>
                                                    <td>{{ $t->transaction->customer->name ?? '' }} </td>
                                                    <td>{{ $t->pay_method }} </td>
                                                    <td>
                                                        <table class="table table-striped">
                                                            <tr>
                                                                <td> Nama Produk
                                                                </td>
                                                                <td> Qty Terjual </td>
                                                                <td> Subtotal </td>
                                                            </tr>
                                                            @php
                                                                $sale = $t->transaction->sale ?? '';
                                                            @endphp
                                                            @if ($sale != '')
                                                                @foreach ($sale as $s)
                                                                    @php
                                                                        $subtotal = $s->unit_price * $s->qty;
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $s->product->name ?? ('' . ' - ' . $s->variation->name ?? '') }}
                                                                        </td>
                                                                        <td> {{ number_format($s->qty) }}</td>
                                                                        <td> {{ number_format($subtotal) }}</td>
                                                                    </tr>
                                                                @endforeach

                                                            @endif
                                                            <tr>
                                                                <td colspan="2"> Total </td>
                                                                <td> {{ number_format($t->transaction->total_before_tax ?? 0) }}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <h4>Detail Return Penjualan :</h4>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>No</th>
                                                <th>No Ref</th>
                                                <th>Pelanggan</th>
                                                <th>Detail</th>
                                            </tr>
                                            @php
                                                $no = 1;
                                            @endphp
                                            @foreach ($data->returnsale as $r)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $r->transaction->ref_no ?? '' }} </td>
                                                    <td>{{ $r->transaction->customer->name ?? '' }} </td>
                                                    <td>
                                                        <table class="table table-striped">
                                                            <tr>
                                                                <td> Nama Produk
                                                                </td>
                                                                <td> Qty Terjual </td>
                                                            </tr>
                                                            @php
                                                                $return = $r->transaction->sellreturn ?? '';
                                                            @endphp
                                                            @if ($sale != '')
                                                                @foreach ($return as $rn)
                                                                    @php
                                                                        $subtotal = $rn->unit_price * $rn->qty;
                                                                        $name = $rn->sell->product->name ?? '';
                                                                        $var_name = $rn->sell->variation->name ?? '';
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $name . ' - ' . $var_name }}
                                                                        </td>
                                                                        <td> {{ number_format($rn->return_qty) }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                            @endif
                                                        </table>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <h4>Detail Pengeluaran :</h4>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>No</th>
                                                <th>Kategori</th>
                                                <th>Nama Pengeluaran</th>
                                                <th>Total Pengeluaran</th>
                                            </tr>
                                            @php
                                                $no = 1;
                                            @endphp
                                            @foreach ($data->expenses as $e)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $e->expense->category->name ?? '' }} </td>
                                                    <td>{{ $e->expense->name ?? '' }} </td>
                                                    <td>{{ number_format($e->expense->amount ?? 0) }} </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
