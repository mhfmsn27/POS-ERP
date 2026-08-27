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

                                                      <div class="col-sm-12 col-md-6 mb-4" id="dateNow">
                                                            <label class="control-label">Filter Tanggal</label>
                                                            <div class="input-group">
                                                                  <input type="date" name="date_now" id="date_now" class="form-control">
                                                            </div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6 mb-4" id="dateNow">
                                                            <label class="control-label">Nama Produk</label>
                                                            <div class="input-group">
                                                                  <input type="text" name="name" id="productName" class="form-control">
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
                                          <th>Produk</th>
                                          <th>Qty Terjual</th>
                                    </tr>
                              </thead>
                              <tbody>
                              </tbody>
                        </table>
                        <a href="javascript:void(0)" class="d-none" id="return_update" data-bs-toggle="modal" data-bs-target="#returnUpdate"></a>
                  </div>
            </div>
      </div>
</div>

<br>

<div class="modal fade" id="returnUpdate" tabindex="-1" aria-labelledby="returnUpdateLabel" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen-md-down">
            <form method="POST" id="salesReturnstore" class="modal-content">
                  @csrf
                  <div class="modal-header">
                        <h6 class="modal-title" id="returnUpdateLabel">Update Return Produk</h6>
                        <button class="btn btn-close p-1 ms-auto" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                        <div class="form form-horizontal">
                              <div class="form-body">
                                    <div class="row" id="returnform">

                                    </div>
                                    <br>
                              </div>
                        </div>
                  </div>
                  <div class="modal-footer">
                        <button type="submit" style="width:100%" class="btn btn-lg btn-block btn-danger">
                              <i class="bx bx-x d-block d-sm-none"></i>
                              <span class=" d-sm-block">Tambahkan Return</span>
                        </button>
                  </div>
            </form>
      </div>
</div>

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
                  '<td>Pelanggan </td>' +
                  '<td>: ' + d.my_cystomer + '</td>' +
                  '</tr>' +
                  '<tr class="mt-2">' +
                  '<td>Qty Return</td>' +
                  '<td>: ' + d.qty_return + '</td>' +
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
                  '<td>Aksi </td>' +
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
                        "url": domain + domainpath + '/mobile/transaction/sale-detail',
                        "data": function(d) {
                              d.store = $('#store').val();
                              d.date_now = $('#date_now').val();
                              d.name = $("#productName").val();
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
                              data: 'name',
                              name: 'name'
                        },
                        {
                              data: 'qty_sale',
                              name: 'qty_sale'
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

            $("form#salesReturnstore").on("submit", function(e) {
                  spinner.show();
                  e.preventDefault();
                  var formData = new FormData(this);
                  setTimeout(function() {
                        $.ajax({
                              url: domain + domainpath + "/mobile/transaction/store-return",
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
                                          toastr.success("Pengembalian Produk Berhasil ditambahkan", "Berhasil", {
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
                                          $("#returnUpdate").modal('toggle');
                                    }
                              },
                              cache: false,
                              contentType: false,
                              processData: false,
                        });
                  }, 130);
            });


      });

      function getreturnmodal(id) {
            var url = domainpath + "/mobile/transaction/return-dom/" + id;
            setTimeout(function() {
                  $.ajax({
                        url: domain + url,
                        type: 'GET',
                        data: '',
                        success: function(data) {
                              console.log(data, url, domain)
                              var product = data.product;
                              dataContent = `<div class="col-md-12 form-group">
                                                <label class="form-label">Kondisi Pengembalian</label>
                                                <select class="form-control" name="condition">
                                                      <option value="good">{{__('sell.good')}}</option>
                                                      <option value="broken">{{__('sell.broken')}}</option>
                                                </select>
                                          </div>
                                          <div class="col-md-12 form-group">
                                                <label class="form-label">Qty Dikembalikan</label>
                                                <input type="hidden" name="sell_id" value="` + product.sell_id + `">
                                                <input type="hidden" name="product_id" value="` + product.product_id + `">
                                                <input type="hidden" name="variation_id" value="` + product.var_id + `">
                                                <input type="hidden" value="` + product.price + `">
                                                <input type="hidden" id="sellprice" value="` + product.price + `">
                                                <input type="hidden" name="transaction_id" value="` + product.id_transaksi + `">
                                                <input type="number" required class="form-control qty_return" name="qty_return" id="qte-` + product.sell_id + `" min="0" max="` + product.stock + `" value="0">
                                                <p class="errorreturn d-none" style="color: red;">* {{__('purchase.max_qty')}}</p>
                                          </div>
                                          <div class="col-md-12 form-group">
                                                <label class="form-label">Subtotal</label>
                                                <input type="text" required name="subtotal_return" class="form-control subtotalreturn" id="subtotalreturn" value="0" readonly>
                                          </div>`;
                              $("#returnform").html(dataContent);
                              document.getElementById("return_update").click();
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                  });
            });

      }

      $("#returnform").on("change", ".qty_return", function(e) {
            var value = e.target.value;
            var max = e.target.max;
            var price = $("#sellprice").val();
            var sbtl = parseInt(price) * parseInt(value);
            $("#subtotalreturn").val(formatRupiah(sbtl.toString()));

            if (parseInt(value) > parseInt(max)) {
                  $(".errorreturn").removeClass("d-none");
                  $(".qty_return").val(max);
                  var sbtl = parseInt(price) * parseInt(max);
                  $("#subtotalreturn").val(formatRupiah(sbtl.toString()));
                  return false;
            } else {
                  $(".errorreturn").addClass("d-none");
            }
      });
</script>
@endsection