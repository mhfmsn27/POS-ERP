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
                                                                  <label class="control-label">{{ __('store.choose_store') }}</label>
                                                                  <div class="input-group">
                                                                        <select class="form-control" id="store" name="store">
                                                                              <option value="">{{ __('store.choose_store') }}</option>
                                                                              @foreach ($store as $st)
                                                                              <option value="{{ $st->id }}">{{ $st->name }}</option>
                                                                              @endforeach
                                                                        </select>
                                                                  </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-6 mb-4">
                                                                  <label class="control-label">Filter Status</label>
                                                                  <div class="input-group">
                                                                        <select class="form-control" id="status" name="status">
                                                                              <option value="">Pilih</option>
                                                                              <option value="due">Hutang </option>
                                                                              <option value="pay">Lunas</option>
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


                                                            <div class="col-sm-12 col-md-6 mb-3">
                                                                  <label class="control-label">Tipe Agent</label>
                                                                  <div class="input-group">
                                                                        <select class="form-control" id="type" name="type">
                                                                              <option value="">Pilih </option>
                                                                              <option value="none">Berdasarkan Login </option>
                                                                              <option value="user">Pengguna </option>
                                                                              <option value="employee">Pegawai </option>
                                                                              <option value="agent">Agent Penjualan </option>
                                                                        </select>
                                                                  </div>
                                                            </div>

                                                            <div class="col-sm-12 col-md-6 mb-3 d-none" id="userType">
                                                                  <label class="control-label">Pilih Pengguna</label>
                                                                  <div class="input-group">
                                                                        <select class="form-control" id="user" name="user">
                                                                              <option value="">Pilih </option>
                                                                              @foreach($user as $u)
                                                                              <option value="{{$u->id}}">{{$u->name}} </option>
                                                                              @endforeach
                                                                        </select>
                                                                  </div>
                                                            </div>

                                                            <div class="col-sm-12 col-md-6 mb-3 d-none" id="employeeType">
                                                                  <label class="control-label">Pilih Pegawai</label>
                                                                  <div class="input-group">
                                                                        <select class="form-control" id="employeeFilter" name="employee">
                                                                              <option value="">Pilih </option>
                                                                              @foreach($employee as $e)
                                                                              <option value="{{$e->id}}">{{$e->user->name ?? ''}} </option>
                                                                              @endforeach
                                                                        </select>
                                                                  </div>
                                                            </div>

                                                            <div class="col-sm-12 col-md-6 mb-3 d-none" id="agentType">
                                                                  <label class="control-label">Pilih Agent</label>
                                                                  <div class="input-group">
                                                                        <select class="form-control" id="agent" name="agent">
                                                                              <option value="">Pilih </option>
                                                                              @foreach($agent as $a)
                                                                              <option value="{{$a->id}}">{{$a->name ?? ''}} </option>
                                                                              @endforeach
                                                                        </select>
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
                                          <table class="table table-bordered" id="comissionContent">
                                                <thead>
                                                      <tr>
                                                            <th>{{ __('general.action') }}</th>
                                                            <th>{{ __('general.date') }}</th>
                                                            <th>No Ref Transaksi</th>
                                                            <th>Toko</th>
                                                            <th>Tipe Agent</th>
                                                            <th>Nama Agent</th>
                                                            <th>Total Komisi</th>
                                                            <th>Persentase Komisi</th>
                                                            <th>Return Komisi</th>
                                                            <th>Status Pembayaran</th>
                                                      </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                      <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                                            <th colspan="6" style="height: 50px; font-size:30px"></th>
                                                            <th style="font-size:20px"></th>
                                                            <th></th>
                                                            <th style="font-size:20px"></th>
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
<x-admin.modal.add-payment-purchase></x-admin.modal.add-payment-purchase> 
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="add-pay" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl payment_modal" role="document">
        <div class="modal-content">
            <div class="modal-header header-modal "> 
                <h5 class="modal-title" id="">Lihat Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <i class="fa fa-times"></i> </button>
            </div>
            <div class="modal-body">
                <div class="col-12 p-2">
                    <table class="table">
                        <thead>
                            <tr class="table-primary">
                                <th scope="col">Tanggal Pembayaran</th> 
                                <th scope="col">Total Pembayaran</th>
                                <th scope="col">Metode Pembayaran</th>
                                <th scope="col">Masuk Ke Account ? </th>
                            </tr>
                        </thead>
                        <tbody id="paymentList">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-block btn-danger ml-1" data-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">{{ __('general.close') }}</span>
                </button>
            </div>
        </div>
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
            const comission_table = $('#comissionContent').DataTable({
                  processing: true,
                  serverSide: true,
                  aaSorting: [
                        [3, 'asc']
                  ],
                  ajax: {
                        "url": domain + domainpath + '/pos-admin/report/transaction/commission',
                        "data": function(d) {
                              d.store = $('#store').val();
                              d.status = $('#status').val();
                              d.end_date = $('#end_date').val();
                              d.start_date = $('#start_date').val();
                              d.date_now = $('#date_now').val();
                              d.agent = $('#agent').val();
                              d.user = $('#user').val();
                              d.employee = $('#employeeFilter').val();
                              d.type = $('#type').val();
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
                              data: 'date',
                              name: 'date'
                        },
                        {
                              data: 'no_ref',
                              name: 'no_ref'
                        },
                        {
                              data: 'store',
                              name: 'store'
                        },
                        {
                              data: 'agent_t',
                              name: 'agent_t'
                        },
                        {
                              data: 'agent_n',
                              name: 'agent_n'
                        },
                        {
                              data: 'commission_total',
                              name: 'commission_total'
                        },
                        {
                              data: 'commission_percentase',
                              name: 'commission_percentase'
                        },
                        {
                              data: 'commission_total_return',
                              name: 'commission_total_return'
                        },
                        {
                              data: 'payment_status',
                              name: 'payment_status'
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

                        var commissonTotal = api
                              .column(6)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);

                        var returnTotal = api
                              .column(8)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);



                        $(api.column(0).footer()).html("Total  : ");
                        $(api.column(6).footer()).html(formatRupiah(commissonTotal.toString()));
                        $(api.column(8).footer()).html(formatRupiah(returnTotal.toString()));
                  },
            });
            $(document).on('change', '#type, #store',
                  function() {
                        comission_table.ajax.reload();
                  });

            $("body").on("change", "#date_now", function() {
                  comission_table.ajax.reload();
            });

            $("body").on("change", "#start_date", function() {
                  comission_table.ajax.reload();
            })

            $("body").on("change", "#end_date", function() {
                  comission_table.ajax.reload();
            })

            $("body").on("change", "#status", function() {
                  comission_table.ajax.reload();
            });

            $("body").on("change", "#user", function() {
                  comission_table.ajax.reload();
            });

            $("body").on("change", "#employeeFilter", function() {
                  comission_table.ajax.reload();
            });

            $("body").on("change", "#agent", function() {
                  comission_table.ajax.reload();
            });

            $("form#addPaymentPurchase").on("submit", function(e) {
                  spinner.show();
                  e.preventDefault();
                  var formData = new FormData(this);
                  setTimeout(function() {
                        $.ajax({
                              url: domain + domainpath + "/pos-admin/agent/payment",
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
                                          comission_table.ajax.reload();
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

      $("select[name='chooseFilter']").change(function() {
            if ($(this).val() == 'multiple') {
                  $("#startDate").removeClass("d-none");
                  $("#endDate").removeClass("d-none");
                  $("#dateNow").addClass("d-none");
            } else if ($(this).val() == 'single') {
                  $("#startDate").addClass("d-none");
                  $("#endDate").addClass("d-none");
                  $("#dateNow").removeClass("d-none");
            } else {
                  $("#startDate").addClass("d-none");
                  $("#endDate").addClass("d-none");
                  $("#dateNow").addClass("d-none");
            }
      });

      $("select[name='type']").change(function() {
            if ($(this).val() == 'none' || $(this).val() == 'user') {
                  $("#userType").removeClass("d-none");
                  $("#employeeType").addClass("d-none");
                  $("#agentType").addClass("d-none");
            } else if ($(this).val() == 'agent') {
                  $("#userType").addClass("d-none");
                  $("#employeeType").addClass("d-none");
                  $("#agentType").removeClass("d-none");
            } else if ($(this).val() == 'employee') {
                  $("#userType").addClass("d-none");
                  $("#employeeType").removeClass("d-none");
                  $("#agentType").addClass("d-none");
            } else {
                  $("#userType").addClass("d-none");
                  $("#employeeType").addClass("d-none");
                  $("#agentType").addClass("d-none");
            }
      });

      function showPaymentCommission(id) {
            $.ajax({
                  url: domain + domainpath + "/pos-admin/agent/show-payment/" + id,
                  type: 'GET',
                  data: '',
                  success: function(data, json, errorThrown) {
                        var dataContent = '';
                        var color = ''
                        var status = ''
                        $.each(data.payment, function(index, value) {

                              if (index % 2 === 0) {
                                    color = 'table-info'
                              } else {
                                    color = 'table-success'
                              }

                              if (value.account == null) {
                                    status = '<span class="badge bg-danger text-white">Belum Terkoneksi</span>'
                              } else {
                                    status = '<span class="badge bg-primary text-white">' + value.account + '</span>'
                              }

                              dataContent += `<tr class="` + color + `"><td>` + value.date + `</td><td>` + value.amount + `</td><td>` + value.method + `</td><td>` + status + `</td></tr>`;
                        })
                        $("#paymentList").append(dataContent);
                        $("#paymentModal").modal("show")
                  },

                  cache: false,
                  contentType: false,
                  processData: false
            });
      }

      function getpaymentmodal(id) {
            $.ajax({
                  url: domain + domainpath + "/pos-admin/report/transaction/commission/element/" + id,
                  type: 'GET',
                  data: '',
                  success: function(data, json, errorThrown) {
                        console.log(data)
                        $("#tri").val(id);
                        $("#maxPayment").val(data.max_amount);
                        $("#addpay").modal("show")
                  },

                  cache: false,
                  contentType: false,
                  processData: false
            });
      }
</script>
@endsection
@endsection