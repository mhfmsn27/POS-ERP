@extends('layouts.admin')

@section('styles')
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
                                    <i class="fa fa-search" style="margin-right: 5px;"></i> {{__('general.search')}}
                                </button>
                            </h2>
                            <div id="searchdata" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-parent="#accordionSearching">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 mb-4">
                                            <label class="control-label">{{__('general.choose_supplier')}}</label>
                                            <div class="input-group">
                                                <select class="form-control" id="supplier" name="supplier">
                                                    <option value="">{{__('general.choose_supplier')}}</option>
                                                    @foreach ($supplier as $s)
                                                    <option value="{{ $s->id }}" @if (isset($_GET['supplier'])) @if ($s->id==$_GET['supplier'])
                                                        selected @endif
                                                        @endif>{{ $s->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 mb-4">
                                            <label class="control-label">{{__('general.choose_store')}}</label>
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
                                        <div class="col-sm-6 col-md-6 mb-4">
                                            <label class="control-label">{{__('purchase.received_status')}}</label>
                                            <div class="input-group">
                                                <select class="form-control" id="statuspenerimaan" name="status">
                                                    <option value="">{{__('purchase.received_status')}}</option>
                                                    <option value="received">{{__('purchase.received')}}</option>
                                                    <option value="pending">{{__('purchase.pending')}}</option>
                                                    <option value="ordered">{{__('purchase.ordered')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 mb-4">
                                            <label class="control-label">{{__('general.payment_status')}}</label>
                                            <div class="input-group">
                                                <select class="form-control" id="statuspayment" name="payment">
                                                    <option value="">{{__('general.payment_status')}}</option>
                                                    <option value="due">{{__('general.po_due')}}</option>
                                                    <option value="paid">{{__('general.paid')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4 mb-4">
                                            <label class="control-label">Filter Tanggal</label>
                                            <div class="input-group">
                                                <select class="form-control" id="chooseFilter" name="chooseFilter">
                                                    <option value="">Pilih Jenis Filter</option>
                                                    <option value="multiple">Multi Tanggal</option>
                                                    <option value="single">Satu Tanggal</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-8 mb-4 d-none" id="dateNow">
                                            <label class="control-label">Filter Tanggal</label>
                                            <div class="input-group">
                                                <input type="date" name="date_now" id="date_now" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4 mb-4 d-none" id="startDate">
                                            <label class="control-label">{{__('general.start_date')}}</label>
                                            <div class="input-group">
                                                <input type="date" name="start_date" id="start_date" placeholder="Mulai Tanggal" class="form-control" value="{{ old('start_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4 mb-4 d-none" id="endDate">
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
                            @can('Download Laporan Purchase')
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#download" class="btn btn-sm btn-success float-end" style="margin-top: -13px; border: 2px solid white; margin-top: -5px"><i class="fa fa-download"></i> Download Laporan </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="purchaseContent">
                                <thead>
                                    <tr>
                                        <th>{{ __('general.action') }}</th>
                                        <th>{{ __('general.date') }}</th>
                                        <th>{{ __('general.ref_no') }}</th>
                                        <th>{{ __('general.store') }}</th>
                                        <th>{{ __('supplier.name') }}</th>
                                        <th>{{ __('purchase.received_status') }}</th>
                                        <th>{{ __('general.payment_status') }}</th>
                                        <th>{{ __('report.product_purchase') }}</th>
                                        <th>{{ __('report.qty_purchase') }}</th>
                                        <th>Qty Return</th>
                                        <th>{{ __('purchase.net_total') }}</th>
                                        <th>{{ __('general.pay_amount') }}</th>
                                        <th>{{ __('general.po_due_amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                        <th colspan="7" style="height: 50px; font-size:30px">
                                            {{ __('general.total') }} :
                                        </th>
                                        <th style="font-size:20px"></th>
                                        <th style="font-size:20px"></th>
                                        <th style="font-size:20px"></th>
                                        <th style="font-size:20px"></th>
                                        <th style="font-size:20px"></th>
                                        <th style="font-size:20px"></th>
                                    </tr>
                                </tfoot>
                            </table>
                            @can('Update Status Purchase')
                            <a href="javascript:void(0)" class="d-none" id="update_status" data-toggle="modal" data-target="#updatestatus"></a>
                            <a href="javascript:void(0)" class="d-none" id="update_payment" data-toggle="modal" data-target="#updatepayment"></a>
                            @endcan 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@can("Update Status Purchase")
<x-admin.modal.update-status-purchase></x-admin.modal.update-status-purchase>
<x-admin.modal.update-payment-purchase></x-admin.modal.update-payment-purchase>
@endcan
<x-admin.modal.show-payment-component></x-admin.modal.show-payment-component> 
@can("Tambah Pembayaran Purchase")
<x-admin.modal.add-payment-purchase></x-admin.modal.add-payment-purchase>
@endcan
@can('Download Laporan Purchase')
<div class="modal fade" id="download" tabindex="-1" role="dialog" aria-labelledby="paymodal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl download" role="document">
        <form method="GET" target="_blank" action="{{ route('purchase.download') }}" class="modal-content" style="height: 90vh;">
            <div class="modal-header header-modal" style="height: 5vh;">
                <h5 class="modal-title" id="">Download Laporan</h5>
                <a href="javascript:void(0)" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-danger"></i>
                </a>
            </div>
            <div class="modal-body" style="overflow: hidden;">
                <div class="row">

                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Pilih Supplier</label>
                        <div class="input-group" style="height: 6vh;">
                            <select class="form-control" name="supplier">
                                <option value="">{{ __('general.choose_supplier') }}</option>
                                @foreach ($supplier as $s)
                                <option value="{{ $s->id }}" @if (isset($_GET['supplier'])) @if ($s->id == $_GET['supplier'])
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
                        <label>Pilih Status Penerimaan</label>
                        <div class="input-group" style="height: 6vh;">
                            <select class="form-control" name="status">
                                <option value="">{{ __('purchase.received_status') }}</option>
                                <option value="received">{{ __('purchase.received') }}</option>
                                <option value="pending">{{ __('purchase.pending') }}</option>
                                <option value="ordered">{{ __('purchase.ordered') }}</option>
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
                        <label>Tanggal Akhir</label>
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
        const purchase_table = $('#purchaseContent').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [
                [3, 'asc']
            ],
            ajax: {
                "url": domain + domainpath + '/pos-admin/report/transaction/purchase',
                "data": function(d) {
                    d.supplier = $('#supplier').val();
                    d.store = $('#store').val();
                    d.status = $('#statuspenerimaan').val();
                    d.payment = $('#statuspayment').val();
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
                    data: 'my_supplier',
                    name: 'my_supplier'
                },
                {
                    data: 'my_status',
                    name: 'my_status'
                },
                {
                    data: 'my_payment_status',
                    name: 'my_payment_status'
                },
                {
                    data: 'my_product',
                    name: 'my_product'
                },
                {
                    data: 'my_qty',
                    name: 'my_qty'
                },
                {
                    data: 'my_return',
                    name: 'my_return'
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

                var product_ty = api
                    .column(7)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var qty_total = api
                    .column(8)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var qty_return = api
                    .column(9)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var price_total = api
                    .column(10)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);


                var paytotal = api
                    .column(11)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var duetotal = api
                    .column(12)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(7).footer()).html(formatRupiah(product_ty.toString()));
                $(api.column(8).footer()).html(formatRupiah(qty_total.toString()));
                $(api.column(9).footer()).html(formatRupiah(qty_return.toString()));
                $(api.column(10).footer()).html(formatRupiah(price_total.toString()));
                $(api.column(11).footer()).html(formatRupiah(paytotal.toString()));
                $(api.column(12).footer()).html(formatRupiah(duetotal.toString()));
            },
        });
        $(document).on('change', '#supplier, #store, #statuspenerimaan',
            function() {
                purchase_table.ajax.reload();
            });

        $("body").on("change", "#date_now", function() {
            purchase_table.ajax.reload();
        });

        $("body").on("change", "#statuspayment", function() {
            purchase_table.ajax.reload();
        });

        $("body").on("change", "#start_date", function() {
            purchase_table.ajax.reload();
        })

        $("body").on("change", "#end_date", function() {
            purchase_table.ajax.reload();
        })

        $("form#changeReceivedStatus").on("submit", function(e) {
            spinner.show();
            e.preventDefault();
            var formData = new FormData(this);
            setTimeout(function() {
                $.ajax({
                    url: domain + domainpath + "/pos-admin/purchase/update-status",
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
                            purchase_table.ajax.reload();
                            $("#updatestatus").modal('toggle');
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                });
            }, 130);
        });

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
                            purchase_table.ajax.reload();
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

    function getstatusmodal(id) {
        $("#ti").val(id);
        document.getElementById("update_status").click();
    }
</script>
@endsection
@endsection