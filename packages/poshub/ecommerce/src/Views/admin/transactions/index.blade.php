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
                                                                  <label class="control-label">Status</label>
                                                                  <div class="input-group">
                                                                        <select class="form-control" id="payment" name="payment">
                                                                              <option value="">Pilih Status</option>
                                                                              @foreach ($status as $p => $s)
                                                                              <option value="{{ $p }}">{{ $s }}
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
                                          <h4>Pesanan Masuk Via Website</h4>
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
                                                            <th>{{ __('hrm.amount_total') }}</th>
                                                            <th>{{ __('general.pay_amount') }}</th>
                                                            <th>{{ __('general.sell_due_amount') }}</th>
                                                      </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>

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
<x-ecommerce-show-payment-component></x-ecommerce-show-payment-component>

<div class="modal fade" id="sendOrder" tabindex="-1" role="dialog" aria-labelledby="send-order" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-md " role="document">
            <form method="POST" id="sendOrderShipping" class="modal-content">
                  @csrf
                  <div class="modal-header header-modal ">
                        <input type="hidden" name="transaction_id" id="transactionOrderId" value="">
                        <h5 class="modal-title" id="">Kirim Pesanan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <i data-feather="x"></i> </button>
                  </div>
                  <div class="modal-body">
                        <div class="form form-horizontal">
                              <div class="form-body p-2">
                                    <div class="row" id="">

                                          <div class="col-md-12 form-group">
                                                <label>Masukkan Nomor Resi Kurir</label>
                                                <div class="input-group mb-3">
                                                      <input type="text" class="form-control" value="" id="noResi" name="resi_no">
                                                </div>
                                          </div>

                                    </div>
                                    <br>
                              </div>
                        </div>
                  </div>
                  <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                              <i class="bx bx-x d-block d-sm-none"></i>
                              <span class="d-none d-sm-block">{{ __('general.close') }}</span>
                        </button>
                        <button type="submit" class="btn btn-primary ml-1">
                              <i class="bx bx-check d-block d-sm-none"></i>
                              <span class="d-none d-sm-block">Ubah Status Pesanan</span>
                        </button>
                  </div>
            </form>
      </div>
</div>

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
                        "url": domain + domainpath + '/pos-admin/ecommerce/orders',
                        "data": function(d) {
                              d.customer = $('#customer').val();
                              d.store = $('#store').val();
                              d.status = $('#payment').val();
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

            });
            $(document).on('change', '#customer, #store',
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

            $("form#sendOrderShipping").on("submit", function(e) {
                  spinner.show();
                  e.preventDefault();
                  var formData = new FormData(this);
                  setTimeout(function() {
                        $.ajax({
                              url: domain + domainpath + "/pos-admin/ecommerce/orders/send-order/" + $("#transactionOrderId").val(),
                              type: "POST",
                              data: formData,
                              success: function(data, json, errorThrown) {

                                    if (data.status == false) {
                                          toastr.error(data.message, "Berhasil", {
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
                                          $("#sendOrder").modal('toggle');
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

      function showPayment_ecommerce(id) {

            var url = domain + domainpath;
            $.ajax({
                  url: url + "/pos-admin/ecommerce/orders/show-payment/" + id,
                  type: 'GET',
                  data: '',
                  success: function(data, json, errorThrown) {
                        var dataContent = '';

                        $.each(data.payment, function(index, value) {

                              var color = ''
                              var status = ''
                              var aksi = '';
                              var metode = '';
                              var buktitf = '';

                              if (index % 2 === 0) {
                                    color = 'table-info'
                              } else {
                                    color = 'table-success'
                              }


                              if (value.payment_status == 'success') {
                                    status = 'Terbayar';
                              } else {
                                    status = 'Pending'

                                    if (value.method == 'bank_transfer') {
                                          aksi = '<a class="btn btn-sm btn-success" href="' + url + '/pos-admin/ecommerce/orders/confirmation-payment/' + value.id + '" ><i class="fa fa-check-circle"></i></a><a class="btn btn-sm btn-danger" href="' + url + '/pos-admin/ecommerce/orders/rejected-payment/' + value.id + '" ><i class="fa fa-times-circle"></i></a>'
                                    }
                              }


                              if (value.method == 'bank_transfer') {
                                    metode = 'Bank Transfer';
                                    buktitf = '<a class="btn btn-sm btn-info" href="' + value.file + '" target="_blank"><i class="fa fa-download"></i></a>'
                              } else if (value.method == 'cash') {
                                    metode = 'Uang Tunai'
                              } else if (value.method == 'other') {
                                    metode = 'Midtrans';
                              }


                              dataContent += `<tr class="` + color + `">
                              <td>` + value.date + `</td>
                              <td>` + metode + `</td>
                              <td>` + value.amount + `</td>
                              <td>` + value.bank_name + `</td>
                              <td>` + value.to_bank + `</td>
                              <td>` + value.no_rek + `</td>
                              <td>` + buktitf + `</td>
                              <td>` + status + `</td>
                              <td>` + aksi + `</td>
                              </tr>`;
                        })

                        console.log(dataContent);
                        $("#paymentList").html(dataContent);
                        $("#showPayment").modal("show")
                  },

                  cache: false,
                  contentType: false,
                  processData: false
            });

      }

      function sendOrder(id) {
            $("#transactionOrderId").val(id);
            $("#sendOrder").modal("show")
      }
</script>
@endsection
@endsection