@extends('layouts.admin')
@section('content')
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/select3/dist/css/select2.min.css') }}" />
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
                                    <i class="fa fa-search" style="margin-right: 5px;"></i>
                                    {{ __('general.search') }}
                                </button>
                            </h2>
                            <div id="searchdata" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-parent="#accordionSearching">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-3 mb-3">
                                            <label class="control-label">{{ __('report.choose_customer') }}</label>
                                            <div class="input-group">
                                                <select class="form-control select2" style="width:100%" id="customer" name="customer">
                                                    <option value="">{{ __('report.choose_customer') }} </option>
                                                    @foreach ($customer as $s)
                                                    <option value="{{ $s->id }}" @if (isset($_GET['customer'])) @if ($s->id == $_GET['customer'])
                                                        selected @endif
                                                        @endif>{{ $s->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3 mb-3">
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
                                        <div class="col-sm-12 col-md-3 mb-3">
                                            <label class="control-label">{{ __('general.start_date') }}</label>
                                            <div class="input-group">
                                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3 mb-3">
                                            <label class="control-label">{{ __('general.end_date') }}</label>
                                            <div class="input-group">
                                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" onclick="searchProduct()"><i class="fa fa-search"></i></button>
                                                </div>
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
                            @can('Download Laporan Hutang')
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#download" class="btn btn-sm btn-success float-end" style="margin-top: -13px; border: 2px solid white; margin-top: -5px"><i class="fa fa-download"></i> Download Laporan </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dueContent">
                                <thead>
                                    <tr>
                                        <th>{{ __('general.action') }}</th>
                                        <th>{{ __('general.date') }}</th>
                                        <th>{{ __('general.ref_no') }}</th>
                                        <th>{{ __('general.store') }}</th>
                                        <th>{{ __('customer.name') }}</th>
                                        <th>{{ __('hrm.amount_total') }}</th>
                                        <th>{{ __('general.pay_amount') }}</th>
                                        <th>{{ __('general.sell_due_amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                        <th colspan="5" style="height: 50px; font-size:30px">
                                            {{ __('general.total') }}
                                        </th>
                                        <th style="font-size:20px"></th>
                                        <th style="font-size:20px"></th>
                                        <th style="font-size:20px"></th>
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


@can('Download Laporan Hutang')
<div class="modal fade" id="download" tabindex="-1" role="dialog" aria-labelledby="paymodal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl download" role="document">
        <form method="GET" target="_blank" action="{{ route('due.download') }}" class="modal-content" style="height: 90vh;">
            <div class="modal-header header-modal" style="height: 5vh;">
                <h5 class="modal-title" id="">Download Laporan</h5>
                <a href="javascript:void(0)" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-danger"></i>
                </a>
            </div>
            <div class="modal-body" style="overflow: hidden;">
                <div class="row">
                    <div class="col-md-6 col-sm-12 mb-2">
                        <label>Pilih Pelanggan</label>
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


                    <div class="col-md-6 col-sm-12 mb-2">
                        <label>Pilih Toko / Outlet</label>
                        <div class="input-group" style="height: 6vh;">
                            <select class="form-control" name="store">
                                <option value="">{{ __('general.choose_store') }}</option>
                                @foreach ($store as $st)
                                <option value="{{ $st->id }}" @if (isset($_GET['store'])) @if ($st->id == $_GET['store']) selected @endif @endif>{{ $st->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12 mb-2">
                        <label>Mulai Tanggal</label>
                        <div class="input-group" style="height: 6vh;">
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-12 mb-2">
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
        width: 'resolve'  
    });

    $(document).ready(function() {
        const due_table = $('#dueContent').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [
                [3, 'asc']
            ],
            ajax: {
                "url": domain + domainpath + '/pos-admin/report/transaction/due',
                "data": function(d) {
                    d.customer = $("#customer").val();
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
                    data: 'my_customer',
                    name: 'my_customer'
                },
                {
                    data: 'final_total',
                    name: 'final_total'
                },
                {
                    data: 'pay_total',
                    name: 'pay_total'
                },
                {
                    data: 'due_total',
                    name: 'due_total'
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

                var final_total = api
                    .column(5)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var pay_total = api
                    .column(6)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var due_total = api
                    .column(7)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(5).footer()).html(formatRupiah(final_total.toString()));
                $(api.column(6).footer()).html(formatRupiah(pay_total.toString()));
                $(api.column(7).footer()).html(formatRupiah([due_total].toString()));
            },
        });

        $("body").on("change", "#store", function() {
            due_table.ajax.reload();
        });

        $("body").on("change", "#customer", function() {
            due_table.ajax.reload();
        });

        $("body").on("change", "#start_date", function() {
            due_table.ajax.reload();
        })

        $("body").on("change", "#end_date", function() {
            due_table.ajax.reload();
        })

    });
</script>
@endsection
@endsection