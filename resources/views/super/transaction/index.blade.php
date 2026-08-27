@extends('layouts.super')

@section('content')
<div class="row row-sm">
    <div class="col-12">
        <div class="card custom-card">
            <form action="{{route('admin.transaction.package')}}" method="GET" class="card-body row">
                <div class="col-lg-3 col-sm-6">
                    <label class="main-content-label tx-11 tx-medium tx-gray-600">Pilih Bisnis</label>
                    <select name="merchant" id="businessData" class="form-control choose_business">
                        <option value="">Pilih Merchant</option>
                    </select>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <label class="main-content-label tx-11 tx-medium tx-gray-600">Pilih Toko</label>
                    <select name="store" id="storeData" class="form-control choose_store">
                        <option value="">Pilih Toko</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="main-content-label tx-11 tx-medium tx-gray-600">Tanggal Awal</label>
                    <input type="date" class="form-control" name="start_date">
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="main-content-label tx-11 tx-medium tx-gray-600">Sampai Awal</label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="end_date">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit">
                                <span class="input-group-btn">
                                    <i class="fa fa-search"></i>
                                </span>
                            </button>
                        </span>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <div class="col-md-12 col-xl-4">
        <div class=" main-content-body-invoice">
            <div class="card card-invoice">
                <div class="card-header ps-3 pe-3 pt-3 pb-0">
                    <h2 class="card-title">Daftar Transaksi</h2>
                </div>
                <div class="p-0">
                    <div class="main-invoice-list" id="mainInvoiceList">
                        @foreach ($data as $transaction)
                        <div class="media" onclick="detailTransaction('<?= $transaction->id; ?>')">
                            <div class="media-icon">
                                <i class="far fa-file-alt"></i>
                            </div>
                            <div class="media-body">
                                <h6><span>{{$transaction->store->name ?? ''}}</span> <span>Rp {{number_format($transaction->grand_total)}}</span></h6>
                                <div>
                                    <p><span>Tanggal :</span> {{$transaction->created_at->format("Y-m-d")}}</p>
                                    <p>
                                        @if($transaction->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                        @endif

                                        @if($transaction->status == 'rejected')
                                        <span class="badge bg-warning">Di Batalkan</span>
                                        @endif

                                        @if($transaction->status == 'success')
                                        <span class="badge bg-info text-white">Lunas</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="example d-flex justify-content-center">
                            <ul class="pagination pagination-circled mb-0">

                                @if(count($pagination['links']) > 3)
                                @foreach($pagination['links'] as $paginate)
                                @if($paginate['url'] != null)

                                @if($paginate['label'] == '&laquo; Previous')
                                <li class="page-item"><a class="page-link" href="{{$paginate['url']}}"><i class="icon ion-ios-arrow-back"></i></a></li>
                                @endif

                                @if($paginate['label'] != '&laquo; Previous' && $paginate['label'] != 'Next &raquo;')

                                @if($paginate['active'] == true)
                                <li class="page-item active"><a class="page-link" href="javascript:void(0);">{{$paginate['label']}}</a></li>
                                @else
                                <li class="page-item"><a class="page-link" href="{{$paginate['url']}}">{{$paginate['label']}}</a></li>
                                @endif
                                @endif

                                @if($paginate['label'] == 'Next &raquo;')
                                <li class="page-item"><a class="page-link" href="{{$paginate['url']}}"><i class="icon ion-ios-arrow-forward"></i></a></li>
                                @endif

                                @endif
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xl-8">
        <div class=" main-content-body-invoice">
            <div class="card card-invoice" id="invoiceDetail">
                <div class="card-body d-flex justify-content-center" style="height: 80vh;">
                    <h2>Silahkan Pilih Detail</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/select3/dist/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.choose_business').select2();
        $(".choose_store").select2();

        $('#businessData').select2({
            placeholder: 'Pilih Bisnis...',
            ajax: {
                url: '/administrator/administrator/components/merchants',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id,
                            }
                        }),
                    }
                },
                cache: false,
            },
        });

        $("select[name='merchant']").on('change', function(e) {
            $('#storeData').val('')

            $('#storeData').select2({
                placeholder: 'Pilih Toko / Cabang',
                ajax: {
                    url: '/administrator/administrator/components/stores?merchant=' + $(this).val(),
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id,
                                }
                            }),
                        }
                    },
                    cache: false,
                },
            })
        })

    });

    function detailTransaction(iddata) {
        $.ajax({
            url: '/administrator/administrator/transactions/detail/' + iddata,
            type: 'GET',
            success: function(data, json, errorThrown) {

                var partnership = data.detail.partnership;
                var business = data.detail.business;
                var store = data.detail.store;
                var package = data.detail.package
                var infoPartnership = '';
                var status = '<h4 class="tx-danger tx-bold">HUTANG</h4>';
                var button = `<a class="btn btn-primary btn-block" href="javascript:void(0);" onclick="paymentNow('` + iddata + `')">Ubah Status Ke Lunas</a>`



                if (data.detail.status == 'success') {
                    status = '<h4 class="tx-success tx-bold">LUNAS</h4>'
                }

                if (data.detail.status != 'pending') {
                    button = `<a class="btn btn-warning btn-block" href="javascript:void(0);" onclick="paymentNow('` + iddata + `')">Ubah Status Ke Pending</a>`
                }

                var invoiceDatil = `<div class="card-body">
                    <div class="invoice-header">
                        <h1 class="invoice-title">Invoice</h1>
                        
                    </div>
                    <div class="row mg-t-20">
                        <div class="col-md">
                            <label class="tx-gray-600">Informasi Bisnis</label>
                            <div class="billed-to">
                                <h6>` + business.name + `</h6> <p>` + business.address + `<br> Email: ` + business.email + ` <br/> Phone ` + business.phone + ` </p>
                            </div>
                        </div>
                        <div class="col-md">
                            <label class="tx-gray-600">Informasi Toko</label>
                            <p class="invoice-info-row"><span>Nama Toko</span> <span>` + store.name + `</span></p>
                            <p class="invoice-info-row"><span>Email PIC</span> <span>` + store.email + `</span></p>
                            <p class="invoice-info-row"><span>Nomor Whatsapp PIC:</span> <span>` + store.phone + `</span></p>
                        </div>
                    </div>
                    <div class="table-responsive mg-t-40">
                        <table class="table table-invoice border text-md-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th class="wd-20p">Nama Paket</th>
                                    <th class="tx-center">Durasi Limit</th>
                                    <th class="tx-left">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>` + package.name + `</td>
                                    <td class="tx-center">` + package.duration + ` Hari</td>
                                    <td class="tx-left">Rp ` + package.price + ` </td>
                                </tr> 
                                <tr>
                                    <td class="tx-right" colspan="2">Pajak </td>
                                    <td class="tx-left" >` + data.detail.tax + `%</td>
                                </tr>
                                <tr>
                                    <td class="tx-right" colspan="2">Subtotal</td>
                                    <td class="tx-left" >Rp ` + data.detail.subtotal + `</td>
                                </tr>
                                <tr>
                                    <td class="tx-right" colspan="2">Perlu Di Bayar Customer</td>
                                    <td class="tx-left" >Rp ` + data.detail.grand_total + ` </td>
                                </tr>  
                                <tr>
                                    <td class="tx-right tx-uppercase tx-bold tx-inverse">Tanggal</td>
                                    <td class="tx-right" colspan="2">
                                        <h4 class="tx-info tx-bold">` + data.detail.created + `</h4>
                                    </td>
                                </tr>
                               
                                <tr>
                                    <td class="tx-right tx-uppercase tx-bold tx-inverse">Status</td>
                                    <td class="tx-right" colspan="2">
                                        ` + status + `
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr class="mg-b-40">
                    ` + button + `
                </div>`;

                $("#invoiceDetail").html(invoiceDatil)
            },
            cache: false,
            contentType: false,
            processData: false,
        })
    }

    function paymentNow(iddata) {
        $.ajax({
            url: '/administrator/administrator/transactions/change-status/' + iddata,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                Accept: 'application/json',
                'Content-Type': 'application/json',
                timeout: 0,
            },
            data: "",
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
                    location.reload();
                }
            },
            cache: false,
            contentType: false,
            processData: false,
        })
    }
</script>
@endsection