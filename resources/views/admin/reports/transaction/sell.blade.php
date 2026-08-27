@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/select3/dist/css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')

<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="accordion" id="accordionSearching">
                        <div class="accordion-item rounded">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-toggle="collapse" data-target="#searchdata" aria-expanded="false" aria-controls="searchdata">
                                    <i class="fa fa-search" style="margin-right: 5px;"></i>
                                    {{ __('general.search') }}
                                </button>
                            </h2>
                            <div id="searchdata" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-parent="#accordionSearching">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 mb-3">
                                            <label class="control-label">{{ __('report.choose_customer') }}</label>
                                            <div class="input-group">
                                                <select class="form-control select2" style="width: 100%;" id="customer" name="customer">
                                                    <option value="">{{ __('report.choose_customer') }} </option>
                                                    @foreach ($customer as $s)
                                                    <option value="{{ $s->id }}" @if (isset($_GET['customer'])) @if ($s->id == $_GET['customer'])
                                                        selected @endif
                                                        @endif>{{ $s->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6 mb-3">
                                            <label class="control-label">{{ __('store.choose_store') }}</label>
                                            <div class="input-group">
                                                <select class="form-control" id="store" name="store">
                                                    <option value="">{{ __('store.choose_store') }}</option>
                                                    @foreach ($store as $st)
                                                    <option value="{{ $st->id }}" @if (isset($_GET['store'])) @if ($st->id == $_GET['store']) selected @endif
                                                        @endif>{{ $st->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-sm-12 col-md-6 mb-3">
                                            <label class="control-label">{{ __('general.payment_status') }}</label>
                                            <div class="input-group">
                                                <select class="form-control" id="payment" name="payment">
                                                    <option value="">{{ __('general.payment_status') }}</option>
                                                    @foreach ($payment as $p => $pay)
                                                    <option value="{{ $p }}">{{ $pay }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-6 mb-3">
                                            <label class="control-label">{{ __('report.choose_kasir') }}</label>
                                            <div class="input-group">
                                                <select class="form-control" id="createdby" name="createdby">
                                                    <option value="">{{ __('report.choose_kasir') }}</option>
                                                    @foreach ($user as $u)
                                                    <option value="{{ $u->id }}">{{ $u->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-6 mb-4">
                                            <label class="control-label">Filter Tanggal</label>
                                            <div class="input-group">
                                                <select class="form-control" id="chooseFilter" name="chooseFilter">
                                                    <option value="">Pilih Jenis Filter</option>
                                                    <option value="multiple">Multi Tanggal</option>
                                                    <option value="single">Satu Tanggal</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 mb-4 d-none" id="dateNow">
                                            <label class="control-label">Filter Tanggal</label>
                                            <div class="input-group">
                                                <input type="date" name="date_now" id="date_now" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3 mb-4 d-none" id="startDate">
                                            <label class="control-label">{{__('general.start_date')}}</label>
                                            <div class="input-group">
                                                <input type="date" name="start_date" id="start_date" placeholder="Mulai Tanggal" class="form-control" value="{{ old('start_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3 mb-4 d-none" id="endDate">
                                            <label class="control-label">{{__('general.end_date')}}</label>
                                            <div class="input-group">
                                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            @can('Download Laporan Penjualan')
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#download" class="btn btn-sm btn-success float-end" style="margin-top: -13px; border: 2px solid white; margin-top: -5px"><i class="fa fa-download"></i> Download Laporan </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="sellContent">
                                <thead>
                                    <tr>
                                        <th>{{ __('general.action') }}</th>
                                        <th>{{ __('general.date') }}</th>
                                        <th>{{ __('general.ref_no') }}</th>
                                        <th>{{ __('general.store') }}</th>
                                        <th>{{ __('sidebar.customer') }}</th>
                                        <th>{{ __('general.payment_status') }}</th>
                                        <th>{{ __('report.product_sell') }}</th>
                                        <th>{{ __('report.qty_sell') }}</th>
                                        <th>Return Qty</th>
                                        <th>{{ __('hrm.amount_total') }}</th>
                                        <th>{{ __('general.pay_amount') }}</th>
                                        <th>{{ __('general.sell_due_amount') }}</th>
                                        <th>{{ __('report.profit_amount') }}</th>
                                        <th>{{ __('report.createdby') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                        <th colspan="6" style="height: 50px; font-size:30px"></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                            @can('Tambah Pembayaran Penjualan')
                            <a href="javascript:void(0)" class="d-none" id="add_payment" data-toggle="modal" data-target="#addpay"></a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@can('Tambah Pembayaran Penjualan')
<x-admin.modal.add-payment-purchase></x-admin.modal.add-payment-purchase>
@endcan
<x-admin.modal.show-payment-component></x-admin.modal.show-payment-component> 
@can('Download Laporan Penjualan')
<div class="modal fade" id="download" tabindex="-1" role="dialog" aria-labelledby="paymodal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl download" role="document">
        <form method="GET" target="_blank" action="{{ route('sell.download') }}" class="modal-content" style="height: 90vh;">
            <div class="modal-header header-modal" style="height: 5vh;">
                <h5 class="modal-title" id="">Download Laporan</h5>
                <a href="javascript:void(0)" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-danger"></i>
                </a>
            </div>
            <div class="modal-body" style="overflow: hidden;">
                <div class="row">

                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Pilih Pelanggan / Customer</label>
                        <div class="input-group" style="height: 6vh;">
                            <select class="form-control" name="customer">
                                <option value="">{{ __('report.choose_customer') }} </option>
                                @foreach ($customer as $s)
                                <option value="{{ $s->id }}" @if (isset($_GET['customer'])) @if ($s->id == $_GET['customer'])
                                    selected @endif
                                    @endif>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Pilih Toko</label>
                        <div class="input-group" style="height: 6vh;">
                            <select class="form-control" name="store">
                                <option value="">{{ __('store.choose_store') }}</option>
                                @foreach ($store as $st)
                                <option value="{{ $st->id }}" @if (isset($_GET['store'])) @if ($st->id == $_GET['store']) selected @endif @endif>
                                    {{ $st->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Pilih Status Pembayaran</label>
                        <div class="input-group" style="height: 6vh;">
                            <select class="form-control" name="payment">
                                <option value="">{{ __('general.payment_status') }}</option>
                                @foreach ($payment as $p => $pay)
                                <option value="{{ $p }}">{{ $pay }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Pilih Kasir</label>
                        <div class="input-group" style="height: 6vh;">
                            <select class="form-control" name="createdby">
                                <option value="">Pilih Kasir</option>
                                @foreach ($user as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Tanggal Awal</label>
                        <div class="input-group" style="height: 6vh;">
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Sampai Tanggal</label>
                        <div class="input-group" style="height: 6vh;">
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-12 m-4 p-4">
                        <table style="width:100%">
                            <tr>
                                <td style="width:50%; text-align:right">
                                    <button class="btn btn-primary btn-large text-center" type="submit" name="excel" value="true">
                                        <img class="p-4" src="{{ asset('assets/icon/excel.png') }}" style="width:200px;">
                                        <p> Download Excel</p>
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-large text-center" type="submit" name="excel" value="false">
                                        <img class="p-4" src="{{ asset('assets/icon/pdf.png') }}" style="width:165px">
                                        <p> Download PDF</p>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" style="width:100%" data-dismiss="modal" class="btn btn-lg btn-block btn-danger">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block"><i class="far fa-hand-paper"></i> Batalkan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@section('scripts')
<script src="{{ asset('assets/vendors/select3/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.js') }}"></script>
<script>
    $(".select2").select2({
        width: 'resolve',
    });

    $(document).ready(function() {
        const sell_table = $('#sellContent').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [
                [3, 'asc']
            ],
            ajax: {
                "url": domain + domainpath + '/pos-admin/report/transaction/sell',
                "data": function(d) {
                    d.customer = $('#customer').val();
                    d.store = $('#store').val();
                    d.createdby = $('#createdby').val();
                    d.payment = $('#payment').val();
                    d.end_date = $('#end_date').val();
                    d.start_date = $('#start_date').val();
                    d.date_now = $('#date_now').val();
                    d = datatable_poshub_callback(d);
                }
            },
            columnDefs: [{
                targets: [3],
                orderable: true,
                searchable: false,
            }, ],
            columns: [{
                    data: 'action',
                    name: 'action'
                },
                {
                    data: 'mydate',
                    name: 'mydate'
                },
                {
                    data: 'ref_no',
                    name: 'ref_no'
                },
                {
                    data: 'my_store',
                    name: 'my_store'
                },
                {
                    data: 'my_cystomer',
                    name: 'my_cystomer'
                },
                {
                    data: 'my_status',
                    name: 'my_status'
                },
                {
                    data: 'my_sale',
                    name: 'my_sale'
                },
                {
                    data: 'qty_sale',
                    name: 'qty_sale'
                },
                {
                    data: 'qty_return',
                    name: 'qty_return'
                },
                {
                    data: 'final_total',
                    name: 'final_total'
                },
                {
                    data: 'total_pay',
                    name: 'total_pay'
                },
                {
                    data: 'due_total',
                    name: 'due_total'
                },
                {
                    data: 'profit',
                    name: 'profit'
                },
                {
                    data: 'created_by',
                    name: 'created_by'
                },

            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                var incomeTotal = api
                    .column(12)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var productsell = api
                    .column(6)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var qtysell = api
                    .column(7)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var returnsell = api
                    .column(8)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var total = api
                    .column(9)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var paytotal = api
                    .column(10)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var duetotal = api
                    .column(11)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var income = api
                    .column(12)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(0).footer()).html("Total Income : " + formatRupiah(incomeTotal.toString()));
                $(api.column(6).footer()).html(formatRupiah(productsell.toString()));
                $(api.column(7).footer()).html(formatRupiah(qtysell.toString()));
                $(api.column(8).footer()).html(formatRupiah(returnsell.toString()));
                $(api.column(9).footer()).html(formatRupiah(total.toString()));
                $(api.column(10).footer()).html(formatRupiah(paytotal.toString()));
                $(api.column(11).footer()).html(formatRupiah(duetotal.toString()));
                $(api.column(12).footer()).html(formatRupiah(income.toString()));
            },
        });
        $(document).on('change', '#customer, #store, #createdby',
            function() {
                sell_table.ajax.reload();
            });

        $("body").on("change", "#date_now", function() {
            sell_table.ajax.reload();
        });

        $("body").on("change", "#payment", function() {
            sell_table.ajax.reload();
        });

        $("body").on("change", "#start_date", function() {
            sell_table.ajax.reload();
        })

        $("body").on("change", "#end_date", function() {
            sell_table.ajax.reload();
        })


        $("form#addPaymentPurchase").on("submit", function(e) {
            spinner.show();
            e.preventDefault();
            var formData = new FormData(this);
            setTimeout(function() {
                $.ajax({
                    url: domain + domainpath + "/pos-admin/purchase/add-pay",
                    type: "POST",
                    data: formData,
                    success: function(data, json, errorThrown) {
                        if (data.message == "error") {
                            var errorsHtml = "";
                            $.each(data.errors, function(index, value) {
                                errorsHtml +=
                                    '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
                                    value +
                                    "</li></ul>";
                            });
                            Swal.fire({
                                    title: data.message + " " + data.status, 
                                    html: errorsHtml,
                                    width: "auto",
                                    confirmButtonText: try_again,
                                    cancelButtonText: "Cancel",
                                    showCancelButton: false,
                                },
                                function(isConfirm) {
                                    if (isConfirm) {
                                        $("#openModal").on("click"); 
                                    }
                                }
                            );
                            spinner.hide();
                        } else {
                            toastr.success(success, {
                                timeOut: 5e3,
                                closeButton: !0,
                                debug: !1,
                                newestOnTop: !0,
                                progressBar: !0,
                                positionClass: "toast-top-right",
                                preventDuplicates: !0,
                                onclick: null,
                                showDuration: "100",
                                hideDuration: "1000",
                                extendedTimeOut: "1000",
                                showEasing: "swing",
                                hideEasing: "linear",
                                showMethod: "fadeIn",
                                hideMethod: "fadeOut",
                                tapToDismiss: !1,
                            });
                            spinner.hide();
                            sell_table.ajax.reload();
                            playSound(domainpath + '/public/sound/connection')
                            $("#addpay").modal('toggle');
                            $("#payment_amount").val(0);
                            $("#paymentnote").val("");
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                });
            }, 130);
        });

    });

    
    function getpaymentmodal(id) {
        $.ajax({
            url: domain + domainpath + "/pos-admin/report/transaction/sell-element/" + id,
            type: 'GET',
            data: '',
            success: function(data, json, errorThrown) {
                $("#tri").val(id);
                $("#maxPayment").val(data.max_amount); 
                $("#addpay").modal("show")
            },

            cache: false,
            contentType: false,
            processData: false
        }); 
    }
    
   

</script>
@endsection
@endsection