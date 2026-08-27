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
                                    <i class="fa fa-search" style="margin-right: 5px;"></i> {{ __('general.search') }}
                                </button>
                            </h2>
                            <div id="searchdata" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-parent="#accordionSearching">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 mb-6">
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
                                        <div class="col-sm-12 col-md-6 mb-6">
                                            <div class="input-group">
                                                <div class="input-group">
                                                    <input class="form-control" name="name" id="name" placeholder="{{ __('produk.name') }} / {{ __('produk.variant_name') }} ">
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
                            <h4 class="card-title">{{$page}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="productstock">
                                <thead>
                                    <tr>
                                        <th class="text-center">Detail</th>
                                        <th class="text-center">Keterangan</th> 
                                        <th class="text-center">Stok Terjual ( Stok Keluar )</th>
                                        <th class="text-center">Purchase Order ( Stok Masuk ) </th>
                                        <th class="text-center">Return Pembelian <br> ( Stok Keluar )</th>
                                        <th class="text-center">Return Penjualan <br> ( Stok Masuk, Jika Kondisi Masih bagus )</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.js') }}"></script>
<script>
    function format(d) {
        return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
           
            '<tr class="mt-2">' +
            '<td>Transfer stok Keluar </td>' +
            '<td>: ' + d.transfer_stock_out + '</td>' +
            '</tr>' +
            '<tr class="mt-2">' +
            '<td>Transfer Stok Masuk </td>' +
            '<td>: ' + d.transfer_stock_entry + '</td>' +
            '</tr>' +
            '<tr class="mt-2">' +
            '<td>Stok Qty Expire </td>' +
            '<td>: ' + d.expire_stock + '</td>' +
            '</tr>' +
            '</table>';
    }

    $(document).ready(function() {
        const stock_table = $('#productstock').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            aaSorting: [
                [2, 'asc']
            ],
            ajax: {
                "url": domain + domainpath + '/pos-admin/report/stock-product/all-stock',
                "data": function(d) {
                    d.store = $('#store').val();
                    d.name = $('#name').val();
                    d = datatable_poshub_callback(d);
                }
            },

            columns: [{
                    "className": 'dt-control',
                    "orderable": false,
                    "data": null,
                    "defaultContent": '<a class="btn btn-sm btn-primary" href="javascript:void(0)"><i class="fa fa-list"></i></a>'
                },
                {
                    data: 'detail',
                    name: 'detail'
                },
                {
                    data: 'sell_stock',
                    name: 'sell_stock'
                },
                {
                    data: 'purchases_stock',
                    name: 'purchases_stock'
                },
                {
                    data: 'return_purchase_stock',
                    name: 'return_purchase_stock'
                },
                {
                    data: 'return_sell_stock',
                    name: 'return_sell_stock'
                }

            ],
        });

        $('#productstock tbody').on('click', 'td.dt-control', function() {
            var tr = $(this).closest('tr');
            var row = stock_table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });

        $("body").on("change", "#store", function() {
            stock_table.ajax.reload();
        });

        $("body").on("keyup", "#name", function() {
            stock_table.ajax.reload();
        })

    });
</script>
@endsection
@endsection