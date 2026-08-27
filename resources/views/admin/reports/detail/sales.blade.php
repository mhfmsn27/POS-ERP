@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
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
                                                <form action="{{route('export.detail.sale')}}" target="_blank" method="GET" class="accordion-body">
                                                      <div class="row">
                                                            <div class="col-sm-12 col-md-4 mb-2">
                                                                  <label class="form-label">{{ __('report.choose_customer') }}</label>
                                                                  <select class="choices form-control " style="width: 100%;" id="customer" name="customer">
                                                                        <option value="">{{ __('report.choose_customer') }} </option>
                                                                        @foreach ($customer as $s)
                                                                        <option value="{{ $s->id }}" @if (isset($_GET['customer'])) @if ($s->id == $_GET['customer'])
                                                                              selected @endif
                                                                              @endif>{{ $s->name }}</option>
                                                                        @endforeach
                                                                  </select>
                                                            </div>

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
                                                            <div class="col-sm-6 col-md-4 mb-2">
                                                                  <label>Filter Tanggal</label>
                                                                  <div class="input-group">
                                                                        <select class="form-control" id="chooseFilter" name="chooseFilter">
                                                                              <option value="">Pilih Jenis Filter</option>
                                                                              <option value="multiple">Multi Tanggal</option>
                                                                              <option value="single">Satu Tanggal</option>
                                                                        </select>
                                                                  </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-4 mb-2 d-none" id="dateNow">
                                                                  <label>Filter Tanggal</label>
                                                                  <div class="input-group">
                                                                        <input type="date" name="date_now" id="date_now" class="form-control">
                                                                  </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-4 mb-2 d-none" id="startDate">
                                                                  <label>{{__('general.start_date')}}</label>
                                                                  <div class="input-group">
                                                                        <input type="date" name="start_date" id="start_date" placeholder="Mulai Tanggal" class="form-control" value="{{ old('start_date') }}">
                                                                  </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-4 mb-2 d-none" id="endDate">
                                                                  <label>{{__('general.end_date')}}</label>
                                                                  <div class="input-group">
                                                                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                                                                  </div>
                                                            </div>
                                                      </div>
                                                      <button class="btn btn-success mt-4" type="submit"><i class="fa fa-download"></i> Download Laporan</button>
                                                </form>
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
                                                            <th>Detail</th>
                                                            <th>{{ __('sidebar.customer') }}</th>
                                                            <th>{{ __('report.qty_sell') }}</th>
                                                            <th>Return Qty</th>
                                                            <th>Harga Satuan</th>
                                                            <th>Subtotal</th>
                                                            <th>Profit</th>
                                                            <th>{{ __('report.createdby') }}</th>
                                                      </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                      <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                                            <th colspan="2" style="height: 50px; font-size:20px"></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
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


@section('scripts')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.js') }}"></script>
<script>
      $(document).ready(function() {
            const sell_table = $('#sellContent').DataTable({
                  processing: true,
                  serverSide: true,
                  searching: false,
                  aaSorting: [
                        [0, 'asc']
                  ],
                  ajax: {
                        "url": domain + domainpath + '/pos-admin/report/detail/sales',
                        "data": function(d) {
                              d.customer = $('#customer').val();
                              d.name = $("#productName").val();
                              d.store = $('#store').val();
                              d.end_date = $('#end_date').val();
                              d.start_date = $('#start_date').val();
                              d.date_now = $('#date_now').val();
                              d = datatable_poshub_callback(d);
                        }
                  },
                  columnDefs: [{
                        targets: [1],
                        orderable: true,
                        searchable: true,
                  }, ],
                  columns: [{
                              data: 'detail',
                              name: 'detail'
                        },
                        {
                              data: 'my_cystomer',
                              name: 'my_cystomer'
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
                              data: 'satuan',
                              name: 'satuan'
                        },
                        {
                              data: 'subtotal',
                              name: 'subtotal'
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

                        var qtytotal = api
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

                        var satuantotal = api
                              .column(4)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);

                        var subtotal = api
                              .column(5)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);

                        var profittotal = api
                              .column(6)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);


                        $(api.column(0).footer()).html("Total Income Didapat : " + formatRupiah(profittotal.toString()));
                        $(api.column(2).footer()).html(formatRupiah(qtytotal.toString()));
                        $(api.column(3).footer()).html(formatRupiah(returntotal.toString()));
                        $(api.column(4).footer()).html(formatRupiah(satuantotal.toString()));
                        $(api.column(5).footer()).html(formatRupiah(subtotal.toString()));
                        $(api.column(6).footer()).html(formatRupiah(profittotal.toString()));
                  },
            });
            $(document).on('change', '#customer, #store',
                  function() {
                        sell_table.ajax.reload();
                  });

            $("body").on("change", "#start_date", function() {
                  sell_table.ajax.reload();
            })

            $("body").on("change", "#date_now", function() {
                  sell_table.ajax.reload();
            });

            $("body").on("change", "#end_date", function() {
                  sell_table.ajax.reload();
            })

            $("body").on("keyup", "#productName", function() {
                  sell_table.ajax.reload();
            })

      });
</script>
@endsection
@endsection