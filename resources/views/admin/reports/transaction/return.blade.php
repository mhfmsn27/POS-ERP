@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

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
                                    <i class="fa fa-search" style="margin-right: 5px;"></i> {{__('general.search')}}
                                </button>
                            </h2>
                            <div id="searchdata" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-parent="#accordionSearching">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-4 mb-3">
                                            <label class="control-label">{{__('general.store')}}</label>
                                            <div class="input-group">
                                                <select class="form-control" id="store" name="store">
                                                    <option value="">{{__('general.choose_store')}}</option>
                                                    @foreach ($store as $st)
                                                    <option value="{{ $st->id }}" @if (isset($_GET['store'])) @if ($st->id==$_GET['store']) selected @endif
                                                        @endif>{{ $st->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4 mb-3">
                                            <label class="control-label">{{__('general.start_date')}}</label>
                                            <div class="input-group">
                                                <input type="date" name="start_date" id="start_date" placeholder="{{__('general.start_date')}}" class="form-control" value="{{ old('start_date') }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-4 mb-3">
                                            <label class="control-label">{{__('general.end_date')}}</label>
                                            <div class="input-group">
                                                <input type="date" name="end_date" id="end_date" placeholder="{{__('general.end_date')}}" class="form-control" value="{{ old('end_date') }}">
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
                            @can('Download Laporan Return')
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#download" class="btn btn-sm btn-success float-end" style="margin-top: -13px; border: 2px solid white; margin-top: -5px"><i class="fa fa-download"></i> Download Laporan </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="returnContent">
                                <thead>
                                    <tr>
                                        <th>{{ __('general.action') }}</th>
                                        <th>{{ __('general.date') }}</th>
                                        <th>{{ __('general.ref_no') }}</th>
                                        <th>{{ __('general.store') }}</th>
                                        <th>{{ __('supplier.name') }}</th>
                                        <th>{{ __('report.return_product') }}</th>
                                        <th>{{ __('report.return_qty') }}</th>
                                        <th>{{ __('general.total') }}</th> 
                                        <th>Hutang</th>
                                        <th>Status Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                        <th colspan="5" style="height: 50px; font-size:30px">
                                            {{ __('general.total') }}
                                        </th>
                                        <th style="font-size:18px"></th>
                                        <th style="font-size:18px"></th>
                                        <th style="font-size:18px"></th> 
                                        <th></th>
                                        <th></th> 
                                    </tr>
                                </tfoot>
                            </table> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@can("Tambah Pembayaran Return")
<x-admin.modal.add-payment-purchase></x-admin.modal.add-payment-purchase>
@endcan
<x-admin.modal.show-payment-component></x-admin.modal.show-payment-component> 
@can('Download Laporan Return')
<div class="modal fade" id="download" tabindex="-1" role="dialog" aria-labelledby="paymodal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl download" role="document">
        <form method="GET" target="_blank" action="{{ route('return.download') }}" class="modal-content" style="height: 90vh;">
            <div class="modal-header header-modal" style="height: 5vh;">
                <h5 class="modal-title" id="">Download Laporan</h5>
                <a href="javascript:void(0)" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-danger"></i>
                </a>
            </div>
            <div class="modal-body" style="overflow: hidden;">
                <div class="row">

                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Pilih Toko / Outlet</label>
                        <div class="input-group" style="height: 6vh;">
                            <select class="form-control" name="store">
                                <option value="">{{ __('general.choose_store') }}</option>
                                @foreach ($store as $st)
                                <option value="{{ $st->id }}" @if (isset($_GET['store'])) @if ($st->id == $_GET['store']) selected @endif @endif>{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Mulai Tanggal</label>
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
<script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.js') }}"></script>
<script>
    $(document).ready(function() {
        const return_table = $('#returnContent').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [
                [3, 'asc']
            ],
            ajax: {
                "url": domain + domainpath + '/pos-admin/report/transaction/return',
                "data": function(d) {
                    d.store = $('#store').val();
                    d.end_date = $('#end_date').val();
                    d.start_date = $('#start_date').val();
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
                    data: 'my_supplier',
                    name: 'my_supplier'
                },
                {
                    data: 'product_total',
                    name: 'product_total'
                },
                {
                    data: 'total_return',
                    name: 'total_return'
                },
                {
                    data: 'total_nominal',
                    name: 'total_nominal'
                },
                {
                    data: 'due_return',
                    name: 'due_return'
                },
                {
                    data: 'my_payment_status',
                    name: 'my_payment_status'
                }

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

                var product_qty = api
                    .column(5)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var qty_total = api
                    .column(6)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var price_total = api
                    .column(7)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(5).footer()).html(formatRupiah(product_qty.toString()));
                $(api.column(6).footer()).html(formatRupiah(qty_total.toString()));
                $(api.column(7).footer()).html(formatRupiah([price_total].toString()));
            },
        });

        $("body").on("change", "#store", function() {
            return_table.ajax.reload();
        });

        $("body").on("change", "#start_date", function() {
            return_table.ajax.reload();
        })

        $("body").on("change", "#end_date", function() {
            return_table.ajax.reload();
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
                            playSound(domainpath + '/public/sound/connection')
                            return_table.ajax.reload();
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
 
</script>
@endsection
@endsection