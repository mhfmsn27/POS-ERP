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

                                                            <div class="col-sm-12 col-md-4 mb-2">
                                                                  <label class="label-control">{{ __('store.choose_store') }}</label>
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
                                                            <div class="col-sm-6 col-md-4 mb-2">
                                                                  <label>Nama Produk</label>
                                                                  <div class="input-group">
                                                                        <input type="text" name="name" id="productName" class="form-control">
                                                                  </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-4 mb-2" id="dateNow">
                                                                  <label>Filter Tanggal</label>
                                                                  <div class="input-group">
                                                                        <input type="date" name="date_now" id="date_now" class="form-control">
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
                                          <table class="table table-bordered" id="sellContent">
                                                <thead>
                                                      <tr>
                                                            <th>Nama Produk</th>
                                                            <th>Qty Terjual</th>
                                                            <th>Qty Return</th>
                                                            <th>Return Total</th>
                                                            <th>Pendapatan Kotor</th>
                                                            <th>Modal Terpakai</th>
                                                            <th>Income Bersih</th>
                                                      </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                      <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                                            <th colspan="1" style="height: 50px; font-size:20px"></th>
                                                            <th style="font-size:20px"></th>
                                                            <th style="font-size:20px"></th>
                                                            <th style="font-size:20px"></th>
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


@section('scripts')
<script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.js') }}"></script>
<script>
      $(document).ready(function() {
            const sell_table = $('#sellContent').DataTable({
                  processing: true,
                  serverSide: true,
                  searching: false,
                  pageLength: 25,
                  aaSorting: [
                        [0, 'asc']
                  ],
                  ajax: {
                        "url": domain + domainpath + '/pos-admin/report/detail/trends-monthly',
                        "data": function(d) {
                              d.name = $("#productName").val();
                              d.store = $('#store').val();
                              d.date = $('#date_now').val();
                              d = datatable_poshub_callback(d);
                        }
                  },
                  columnDefs: [{
                        targets: [1],
                        orderable: true,
                        searchable: true,
                  }, ],
                  columns: [{
                              data: 'name',
                              name: 'name'
                        },
                        {
                              data: 'qty_sell',
                              name: 'qty_sell'
                        },
                        {
                              data: 'qty_return',
                              name: 'qty_return'
                        },
                        {
                              data: 'returntotal',
                              name: 'returntotal'
                        },
                        {
                              data: 'profitloss',
                              name: 'profitloss'
                        },
                        {
                              data: 'modal',
                              name: 'modal'
                        },
                        {
                              data: 'income',
                              name: 'income'
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

                        var qtysell = api
                              .column(1)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);

                        var qtyreturn = api
                              .column(2)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);

                        var returntotal = api
                              .column(3)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);

                        var profitloss = api
                              .column(4)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);

                        var modal = api
                              .column(5)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);

                        var income = api
                              .column(6)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);


                        $(api.column(0).footer()).html("Total Income Didapat : ");
                        $(api.column(1).footer()).html(formatRupiah(qtysell.toString()));
                        $(api.column(2).footer()).html(formatRupiah(qtyreturn.toString()));
                        $(api.column(3).footer()).html(formatRupiah(returntotal.toString()));
                        $(api.column(4).footer()).html(formatRupiah(profitloss.toString()));
                        $(api.column(5).footer()).html(formatRupiah(modal.toString()));
                        $(api.column(6).footer()).html(formatRupiah(income.toString()));
                  },
            });
            $(document).on('change', '#store',
                  function() {
                        sell_table.ajax.reload();
                  });


            $("body").on("change", "#date_now", function() {
                  sell_table.ajax.reload();
            });

            $("body").on("keyup", "#productName", function() {
                  sell_table.ajax.reload();
            })

      });
</script>
@endsection
@endsection