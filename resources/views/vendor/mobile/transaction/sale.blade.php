@extends('layouts.m')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

<div class="header-area" id="headerArea">
      <div class="container">
            <div class="header-content header-style-five position-relative d-flex align-items-center justify-content-between">
                  <div class="logo-wrapper"><a href="{{route('m.index')}}"><img src="{{asset('uploads/logo2.png')}}" alt=""></a></div>
                  <div class="page-heading"> </div>
                  <div>
                        <h6 class="mb-0">{{$page}}</h6>
                  </div>
            </div>
      </div>
</div>


<div class="page-content-wrapper">
      <br>
      <x-mobile.alert-component></x-mobile.alert-component> 
      <div class="container ">
            <div class="card">
                  <div class="card-body">
                        <div class="accordion" id="accordionSearching">
                              <div class="accordion-item rounded">
                                    <h2 class="accordion-header" id="headingTwo">
                                          <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#searchdata" aria-expanded="false" aria-controls="searchdata">
                                                <i class="fa fa-search" style="margin-right: 5px;"></i>
                                                {{ __('general.search') }}
                                          </button>
                                    </h2>
                                    <div id="searchdata" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionSearching">
                                          <div class="accordion-body">
                                                <div class="row">

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

                                                      <div class="col-sm-12 col-md-6 mb-4" id="dateNow">
                                                            <label class="control-label">Filter Tanggal</label>
                                                            <div class="input-group">
                                                                  <input type="date" name="date_now" id="date_now" class="form-control">
                                                            </div>
                                                      </div>

                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>

                        <table class="table table-bordered w-100 mt-4" id="sellContent">
                              <thead>
                                    <tr>
                                          <th>Detail</th>
                                          <th>Pelanggan</th>
                                          <th>Total</th>
                                    </tr>
                              </thead>
                              <tbody> 
                              </tbody>
                        </table> 
                  </div> 
            </div>
      </div>
</div>

<br>

<x-mobile.footer-component></x-mobile.footer-component>

@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.js') }}"></script>
<script>
      function format(d) { 
            return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
                  '<tr>' +
                  '<td>Tanggal</td>' +
                  '<td>: ' + d.mydate + '</td>' +
                  '</tr>' +
                  '<tr class="mt-2">' +
                  '<td>Toko</td>' +
                  '<td>: ' + d.my_store + '</td>' +
                  '</tr>' +
                  '<tr class="mt-2">' +
                  '<td> Status</td>' +
                  '<td>: ' + d.my_status + '</td>' +
                  '</tr>' +
                  '<tr class="mt-2">' +
                  '<td> Jenis Produk</td>' +
                  '<td>: ' + d.my_sale + '</td>' +
                  '</tr>' +
                  '<tr class="mt-2">' +
                  '<td>Qty Total</td>' +
                  '<td>: ' + d.qty_sale + '</td>' +
                  '</tr>' +
                  '<tr class="mt-2">' +
                  '<td>Qty Return</td>' +
                  '<td>: ' + d.qty_return + '</td>' +
                  '</tr>' +
                  '<tr class="mt-2">' +
                  '<td>Terbayar</td>' +
                  '<td>: ' + d.total_pay + '</td>' +
                  '</tr>' +
                  '<tr class="mt-2">' +
                  '<td>Hutang </td>' +
                  '<td>: ' + d.due_total + '</td>' +
                  '</tr>' +
                  '<tr class="mt-2">' +
                  '<td>Profit </td>' +
                  '<td>: ' + d.profit + '</td>' +
                  '</tr>' +
                  '<tr class="mt-2">' +
                  '<td>Kasir </td>' +
                  '<td>: ' + d.created_by + '</td>' +
                  '</tr>' +
                  '<tr class="mt-2">' +
                  '<td>Detail </td>' +
                  '<td>: ' + d.action + '</td>' +
                  '</tr>' +
                  '</table>';
      }

      $(document).ready(function() {
            var sell_table = $('#sellContent').DataTable({
                  processing: true,
                  serverSide: true,
                  searching: false,
                  info: false,
                  lengthChange: false,
                  pageLength: 20,
                  pagingType: "simple",
                  aaSorting: [
                        [2, 'asc']
                  ],
                  ajax: {
                        "url": domain + domainpath + '/mobile/transaction/sale-transaction',
                        "data": function(d) {
                              d.store = $('#store').val();
                              d.createdby = $('#createdby').val();
                              d.payment = $('#payment').val();
                              d.date_now = $('#date_now').val();
                              d = datatable_poshub_callback(d);
                        }
                  },
                  columnDefs: [{
                        targets: [2],
                        orderable: true,
                        searchable: false,
                  }, ],
                  columns: [{
                              "className": 'dt-control',
                              "orderable": false,
                              "data": null,
                              "defaultContent": '<a class="btn btn-sm btn-primary" href="javascript:void(0)"><i class="fa fa-list"></i></a>'
                        },
                        {
                              data: 'my_cystomer',
                              name: 'my_cystomer'
                        },
                        {
                              data: 'final_total',
                              name: 'final_total'
                        },

                  ]
            });

            $('#sellContent tbody').on('click', 'td.dt-control', function() {
                  var tr = $(this).closest('tr');
                  var row = sell_table.row(tr);

                  if (row.child.isShown()) { 
                        row.child.hide();
                        tr.removeClass('shown');
                  } else { 
                        row.child(format(row.data())).show();
                        tr.addClass('shown');
                  }
            });

            $(document).on('change', '#store, #createdby',
                  function() {
                        sell_table.ajax.reload();
                  });

            $("body").on("change", "#date_now", function() {
                  sell_table.ajax.reload();
            });

            $("body").on("change", "#payment", function() {
                  sell_table.ajax.reload();
            });


      });
</script>
@endsection