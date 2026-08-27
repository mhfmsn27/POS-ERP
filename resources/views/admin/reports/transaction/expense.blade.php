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
                                    <i class="fa fa-search" style="margin-right: 5px;"></i>
                                    {{ __('general.search') }}
                                </button>
                            </h2>
                            <div id="searchdata" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-parent="#accordionSearching">
                                <div class="accordion-body">
                                    <div class="row">

                                        <div class="col-sm-12 col-md-3 mb-3">
                                            <label class="control-label">Filter Toko</label>
                                            <div class="input-group">
                                                <select class="form-control" id="store" name="store">
                                                    <option value="">Pilih Toko</option>
                                                    @foreach ($store as $st)
                                                    <option value="{{ $st->id }}" @if (isset($_GET['store'])) @if ($st->id == $_GET['store']) selected @endif
                                                        @endif>{{ $st->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-3 mb-3">
                                            <label class="control-label">Filter Kategori</label>
                                            <div class="input-group">
                                                <select class="form-control" id="category" name="category">
                                                    <option value="">Pilih category</option>
                                                    @foreach ($category as $st)
                                                    <option value="{{ $st->id }}" @if (isset($_GET['category'])) @if ($st->id == $_GET['category']) selected @endif
                                                        @endif>{{ $st->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-sm-12 col-md-3 mb-3">
                                            <label class="control-label">Mulai Tanggal</label>
                                            <div class="input-group">
                                                <input type="date" name="start_date" id="start_date" placeholder="Mulai Tanggal" class="form-control" value="{{ old('start_date') }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-3 mb-3">
                                            <label class="control-label">Sampai Tanggal</label>
                                            <div class="input-group">
                                                <input type="date" name="end_date" id="end_date" placeholder="Sampai Tanggal" class="form-control" value="{{ old('end_date') }}">
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
                            @can('Download Laporan Pengeluaran')
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#download" class="btn btn-sm btn-success float-end" style="margin-top: -13px; border: 2px solid white; margin-top: -5px"><i class="fa fa-download"></i> Download Laporan </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="expenseContent">
                                <thead>
                                    <tr>
                                        <th>Ref No</th>
                                        <th>Kategori</th>
                                        <th>Nama Pengeluaran</th>
                                        <th>Store</th>
                                        <th>Tanggal</th> 
                                        <th>Jumlah Pengeluaran</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                        <th colspan="5" style="height: 50px; font-size:30px">Total</th>
                                        <th style="font-size:20px">0</th>
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

@can('Download Laporan Pengeluaran')
<div class="modal fade" id="download" tabindex="-1" role="dialog" aria-labelledby="paymodal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl download" role="document">
        <form method="GET" target="_blank" action="{{ route('expense.download') }}" class="modal-content" style="height: 90vh;">
            <div class="modal-header header-modal" style="height: 5vh;">
                <h5 class="modal-title text-white" id="">Download Laporan</h5>
                <a href="javascript:void(0)" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-danger"></i>
                </a>
            </div>
            <div class="modal-body" style="overflow: hidden;">
                <div class="row">
                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Pilih Kategori Pengeluaran</label>
                        <div class="input-group" style="height: 6vh;">
                            <select class="form-control" name="category">
                                <option value="">Pilih category</option>
                                @foreach ($category as $st)
                                <option value="{{ $st->id }}" @if (isset($_GET['category'])) @if ($st->id == $_GET['category']) selected @endif @endif>{{ $st->name }}</option>
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
        const expense_table = $('#expenseContent').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [
                [2, 'asc']
            ],
            ajax: {
                "url": domain + domainpath + '/pos-admin/report/transaction/expense',
                "data": function(d) {
                    d.store = $("#store").val();
                    d.category = $("#category").val();
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
                    data: 'ref_no',
                    name: 'ref_no'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'my_store',
                    name: 'my_store'
                },
                {
                    data: 'mydate',
                    name: 'mydate'
                }, 
                {
                    data: 'amount',
                    name: 'amount'
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

                var jumlah_total = api
                    .column(5)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(5).footer()).html(formatRupiah(jumlah_total.toString()));
            },
        });

        $("body").on("change", "#store", function() {
            expense_table.ajax.reload();
        });

        $("body").on("change", "#start_date", function() {
            expense_table.ajax.reload();
        })

        $("body").on("change", "#category", function() {
            expense_table.ajax.reload();
        })

        $("body").on("change", "#end_date", function() {
            expense_table.ajax.reload();
        })

    });
</script>
@endsection
@endsection