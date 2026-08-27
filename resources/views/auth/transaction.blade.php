@extends('layouts.welcome')

@section('styles') 

<link rel="stylesheet" href="{{asset('assets/vendors/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">

@endsection
@section('content')
<div class="row justify-content-center mt-4"> 

    <div class="col-md-12 col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header">
                <h4>Daftar Histori Transaksi Berlangganan</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-1">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Toko / Cabang</th>
                                <th>Paket</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                            <tr>
                                <td>
                                    {{$transaction->created_at->format('Y-m-d')}}
                                </td>
                                <td>
                                    {{$transaction->store->name ?? ''}}
                                </td>
                                <td>
                                    {{$transaction->package->name ?? ''}}
                                </td>
                                <td>
                                    {{number_format($transaction->grand_total)}}
                                </td>
                                <td>
                                    @if($transaction->status == 'pending')
                                    MENUNGGU PEMBAYARAN
                                    @elseif($transaction->status == 'process')
                                    PROSES
                                    @else
                                    SELESAI
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->status == 'pending')
                                    <a href="{{route('package.order.delete',$transaction->id)}}" class="btn btn-danger deletebutton">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <a href="javascript:void(0);" onclick="payTransaction(<?= $transaction->id; ?>)" class="btn btn-info">
                                        <i class="fa fa-money"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="{{asset('assets/vendors/datatables/datatables.min.js')}}"></script>
<script src="{{asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/vendors/datatables/datatables.js')}}"></script>
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{$settings->midtrans_client}}"></script>

<script>
    function payTransaction(id) {
        setTimeout(function() {
            $.ajax({
                url: '/pos-admin/transaction-package/add-payment/' + id,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    timeout: 0,
                },
                data: '',
                success: function(data, json, errorThrown) {
                    if (data.status == false) {
                        Swal.fire({
                            title: 'Error',
                            html: data.message,
                            width: 'auto',
                            confirmButtonText: 'Ok Saya Mengerti',
                            showCancelButton: false,
                        })
                        return false
                    } else {
                        snap.pay(data.snap, {
                            onSuccess: function(result) {
                                location.reload();
                            },
                            onPending: function(result) {
                                location.reload();
                            },
                            onError: function(result) {
                                location.reload();
                            },
                            onClose: function(result) {
                                location.reload();
                            },
                        })
                    }
                },

                cache: false,
                contentType: false,
                processData: false,
            })
        }, 130)
    }
</script>
@endsection