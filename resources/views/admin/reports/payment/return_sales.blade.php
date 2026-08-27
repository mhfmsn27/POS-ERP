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
                                                                  <label class="form-label">Metode Pembayaran</label>
                                                                  <select class="form-control " style="width: 100%;" id="method" name="method">
                                                                        <option value="">Semua Metode </option>
                                                                        <option value="cash">Cash </option>
                                                                        <option value="bank_transfer">Bank Transger </option>
                                                                        <option value="card">Master Card </option>
                                                                  </select>
                                                            </div>

                                                            <div class="col-sm-12 col-md-4 mb-2">
                                                                  <label class="label-control">{{ __('store.choose_store') }}</label>
                                                                  <div class="input-group">
                                                                        <select class="form-control" id="store" name="store">
                                                                              <option value="">Semua Toko / Outlet</option>
                                                                              @foreach ($store as $st)
                                                                              <option value="{{ $st->id }}" @if (isset($_GET['store'])) @if ($st->id == $_GET['store']) selected @endif
                                                                                    @endif>{{ $st->name }}</option>
                                                                              @endforeach
                                                                        </select>
                                                                  </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-4 mb-2">
                                                                  <label>Integrasi Account</label>
                                                                  <select class=" form-control " style="width: 100%;" id="account" name="account">
                                                                        <option value="">Semua </option>
                                                                        <option value="ya">Sudah </option>
                                                                        <option value="no">Belum </option>
                                                                  </select>
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
                                          <table class="table table-bordered" id="paymentContent">
                                                <thead>
                                                      <tr>
                                                            <th>Keterangan</th>
                                                            <th>Tanggal </th>
                                                            <th>Toko</th>
                                                            <th>Metode Pembayaran</th>
                                                            <th>Ditambahkan Oleh</th>
                                                            <th>Nama Account</th>
                                                            <th>Jumlah Nominal</th>
                                                            <th>Aksi</th>
                                                      </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                      <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                                            <th colspan="6" style="height: 50px; font-size:30px"></th>
                                                            <th colspan="2" style="font-size:20px"></th>
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
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="add-pay" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl payment_modal" role="document">
            <div class="modal-content">
                  <div class="modal-header header-modal ">
                        <h5 class="modal-title" id="">Detail Pembayaran</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <i class="fa fa-times"></i> </button>
                  </div>
                  <div class="modal-body">
                        <div class="col-12 p-2">
                              <table class="table">
                                    <thead>
                                          <tr class="table-primary">
                                                <th scope="col">Keterangan</th>
                                                <th scope="col">Value</th>
                                          </tr>
                                    </thead>
                                    <tbody id="detailList">

                                    </tbody>
                              </table>
                              <p><b class="text-danger"><i>Note :</i></b></p>
                              <p class="mt-2" id="noteDetail"></p>
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
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="add-pay" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full payment_modal" role="document">
            <form method="POST" id="sincronAccount" class="modal-content">
                  @csrf
                  <div class="modal-header header-modal ">
                        <input type="hidden" name="transaction_id" id="tri" value="">
                        <h5 class="modal-title" id="">Integrasikan Pembayaran dengan Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <i data-feather="x"></i> </button>
                  </div>
                  <div class="modal-body">
                        <div class="form form-horizontal">
                              <div class="form-body p-2">
                                    <div class="row ">
                                          <div class="col-md-12 form-group">
                                                <label>Integrasikan Dengan Account</label>
                                                <input type="hidden" name="payment_id" id="paymentID">
                                                <input type="hidden" name="a_transaction" id="a_transaction">
                                                <select class="form-control" name="account_id" id="accid">
                                                      {{my_account()}}
                                                </select>
                                          </div>

                                    </div>
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
                              <span class="d-none d-sm-block">Sinkronkan</span>
                        </button>
                  </div>
            </form>
      </div>
</div>
@section('scripts')
<script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.js') }}"></script>
<script>
      $(document).ready(function() {
            const payment_table = $('#paymentContent').DataTable({
                  processing: true,
                  serverSide: true,
                  searching: false,
                  aaSorting: [
                        [0, 'asc']
                  ],
                  ajax: {
                        "url": domain + domainpath + '/pos-admin/payment/return_sell',
                        "data": function(d) {
                              d.method = $('#method').val();
                              d.account = $("#account").val();
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
                              data: 'mydate',
                              name: 'mydate'
                        },
                        {
                              data: 'my_store',
                              name: 'my_store'
                        },
                        {
                              data: 'method_pay',
                              name: 'method_pay'
                        },
                        {
                              data: 'created_by',
                              name: 'created_by'
                        },
                        {
                              data: 'account_name',
                              name: 'account_name'
                        },
                        {
                              data: 'amount',
                              name: 'amount'
                        },
                        {
                              data: 'action',
                              name: 'action'
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

                        var total = api
                              .column(6)
                              .data()
                              .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                              }, 0);



                        $(api.column(0).footer()).html("Total Akumulasi : ");
                        $(api.column(6).footer()).html(formatRupiah(total.toString()));
                  },
            });

            $(document).on('change', '#method, #store',
                  function() {
                        payment_table.ajax.reload();
                  });

            $("body").on("change", "#start_date", function() {
                  payment_table.ajax.reload();
            })

            $("body").on("change", "#date_now", function() {
                  payment_table.ajax.reload();
            });

            $("body").on("change", "#end_date", function() {
                  payment_table.ajax.reload();
            })

            $("body").on("change", "#account", function() {
                  payment_table.ajax.reload();
            })

            $("form#sincronAccount").on("submit", function(e) {
                  spinner.show();
                  e.preventDefault();
                  var formData = new FormData(this);
                  setTimeout(function() {
                        $.ajax({
                              url: domain + domainpath + "/pos-admin/payment/integrate-payment",
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
                                          toastr.success("Berhasil Menyinkronkan Pembayaran Dengan Account Tujuan", "Berhasil", {
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
                                          payment_table.ajax.reload();
                                          playSound(domainpath + '/public/sound/connection')
                                          $("#paymentModal").modal('toggle');
                                          $("#accid").val("").change();
                                          $("#paymentID").val("");
                                          $("#a_transaction").val("");
                                    }
                              },
                              cache: false,
                              contentType: false,
                              processData: false,
                        });
                  }, 130);
            });

      });

      function modalDetailPayment(id) {
            $.ajax({
                  url: domain + domainpath + "/pos-admin/payment/detail/" + id,
                  type: 'GET',
                  data: '',
                  success: function(data, json, errorThrown) {
                        var detail = ''
                        var type = '';

                        if (data.method == 'cash') {
                              detail = '<p><b><i>Pembayaran Via Cash / Uang Tunai</i></b></p>'
                        } else if (data.method == 'bank_transfer') {
                              detail = '<p><b><i>Pembayaran Via Transfer Bank</i></b><br>Nama Bank : ' + data.bank_name + ' <br> Nomor Rekening : ' + data.no_rek + '<br>Atas Nama : ' + data.an + ' '
                        } else {
                              if (data.method == 'visa') {
                                    type = "Visa"
                              } else if (data.method == 'master_card') {
                                    type = "Master Card";
                              }
                              detail = '<p><b><i>Pembayaran Via Kartu Credit</i></b><br>Nomor Kartu Transaksi : ' + data.card_transaction_number + ' <br> Nomor Kartu : ' + data.card_number + '<br>Tipe Kartu : ' + type + ' <br>Nama Holder Kartu : ' + data.card_holder_name + '<br> Kode Keamanan ' + data.card_security + '</p>'
                        }
                        var content = `<tr>
                                <td>Metode Pembayaran</td>
                                <td>: ` + data.methode + `</td>
                            </tr>
                            <tr>
                                <td>Detail : </td>
                                <td> ` + detail + ` </td>
                            </tr>
                              `;

                        $("#detailList").html(content);
                        $("#noteDetail").html(data.detail);
                        $("#detailModal").modal("show")
                  },

                  cache: false,
                  contentType: false,
                  processData: false
            });
      }

      function paymentIntegrationAccount(id) {
            $.ajax({
                  url: domain + domainpath + "/pos-admin/payment/detail/" + id,
                  type: 'GET',
                  data: '',
                  success: function(data, json, errorThrown) {
                        $("#accid").val(data.account).change();
                        $("#paymentID").val(data.id);
                        $("#a_transaction").val(data.account_id);
                        $("#paymentModal").modal("show")
                  },

                  cache: false,
                  contentType: false,
                  processData: false
            });
      }
</script>
@endsection
@endsection