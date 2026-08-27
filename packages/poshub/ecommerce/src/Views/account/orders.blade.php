@extends("ecommerce::layouts.web")


@section('styles')
<link rel="stylesheet" href="{{asset('assets/vendors/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('content')

<main class="main pages">
      <div class="page-header breadcrumb-wrap">
            <div class="container">
                  <div class="breadcrumb">
                        <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                        <span></span> Account <span></span> Pesanan Saya
                  </div>
            </div>
      </div>
      <div class="page-content pt-150 pb-150">
            <div class="container">
                  <div class="row">
                        <div class="col-lg-12 m-auto">
                              <div class="row">
                                    <x-ecommerce-sidebar-account-component></x-ecommerce-sidebar-account-component>

                                    <!-- Payment Methode  -->
                                    <input type="hidden" id="paymentMethodeEcommerce" value="{{$settings->payment_method}}">
                                    <!-- End Payment Methode -->

                                    <div class="col-md-9">
                                          <div class="tab-content account dashboard-content pl-50">

                                                <div class="tab-pane fade  show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                                                      <div class="card">
                                                            <div class="card-header">
                                                                  <h3 class="mb-0">Daftar Pesanan</h3>
                                                            </div>
                                                            <div class="card-body">
                                                                  <div class="table-responsive">
                                                                        <table class="table table-striped" id="table-1">
                                                                              <thead>
                                                                                    <tr>
                                                                                          <th style="width:70px;text-align: center;">Tanggal</th>
                                                                                          <th>No Referensi</th>
                                                                                          <th>Status</th>
                                                                                          <th>Subtotal</th>
                                                                                          <th>Pajak</th>
                                                                                          <th>Ongkos Kirim</th>
                                                                                          <th>Grand Total</th>
                                                                                          <th>Aksi</th>
                                                                                    </tr>
                                                                              </thead>
                                                                              <tbody>
                                                                                    @foreach($transactions as $transaksi)
                                                                                    <tr>
                                                                                          <td>{{$transaksi->created_at->format("Y-m-d")}} </td>
                                                                                          <td>{{$transaksi->ref_no}} </td>
                                                                                          <td>
                                                                                                @if($transaksi->payment_status == 'due')
                                                                                                <span class="badge badge-danger">Menunggu Pembayaran</span>
                                                                                                @else

                                                                                                @if($transaksi->status == 'ordered')
                                                                                                <span class="badge bg-info">Pesanan Sedang Disipankan</span>
                                                                                                @endif

                                                                                                @if($transaksi->status == 'transit')
                                                                                                <span class="badge bg-info">Pesanan Dalam Perjalanan</span>
                                                                                                @endif


                                                                                                @if($transaksi->status == 'final')
                                                                                                <span class="badge bg-success">Pesanan Diterima</span>
                                                                                                @endif

                                                                                                @endif
                                                                                          </td>
                                                                                          <td>
                                                                                                {{number_format($transaksi->total_before_tax)}}
                                                                                          </td>
                                                                                          <td>
                                                                                                {{number_format($transaksi->tax_amount)}}
                                                                                          </td>
                                                                                          <td>
                                                                                                {{number_format($transaksi->shipping_charges)}}
                                                                                          </td>
                                                                                          <td>
                                                                                                {{number_format($transaksi->final_total)}}
                                                                                          </td>
                                                                                          <td>
                                                                                                <button onclick="detailTransaction(<?= $transaksi->id; ?>)" class="btn btn-xs">
                                                                                                      Detail
                                                                                                </button>
                                                                                          </td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                              </tbody>
                                                                        </table>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>

                                                <div class="tab-pane fade" id="orderDetails">
                                                      <div class="row">
                                                            <div class="col-lg-12" id="invoiceOrders">

                                                            </div>

                                                      </div>
                                                </div>

                                                <div class="tab-pane fade" id="trackingOrders">
                                                      <div class="row">
                                                            <div class="col-12 mb-4">
                                                                  <div class="cart-action d-flex justify-content-end" role="tablist">
                                                                        <a class="btn  mr-10 mb-sm-15 nav-link" href="javascript:void(0);" onclick="backToList();">Kembali Ke Daftar Pesanan</a>
                                                                  </div>
                                                            </div>
                                                            <div class="col-12">
                                                                  <div class="card">
                                                                        <div class="card-header">
                                                                              <h3 class="mb-0">Tracking Pesanan </h3>
                                                                        </div>
                                                                        <div class="card-body">
                                                                              <div class="table-responsive">
                                                                                    <table class="table">
                                                                                          <thead>
                                                                                                <tr>
                                                                                                      <th>Deskripsi</th>
                                                                                                      <th>Tanggal</th>
                                                                                                      <th>Jam</th>
                                                                                                      <th>Kota</th>
                                                                                                </tr>
                                                                                          </thead>
                                                                                          <tbody id="listTracking">


                                                                                          </tbody>
                                                                                    </table>
                                                                              </div>
                                                                        </div>
                                                                  </div>
                                                            </div>
                                                      </div>


                                                </div>


                                          </div>
                                    </div>

                                    <!-- Modal Payment -->
                                    <div class="modal fade" id="addpayEcommerce" tabindex="-1" role="dialog" aria-labelledby="add-pay" aria-hidden="true">
                                          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl payment_modal" role="document">
                                                <form method="POST" id="addpayEcommercementPurchase" class="modal-content">
                                                      @csrf
                                                      <div class="modal-header header-modal ">
                                                            <input type="hidden" name="transaction_id" id="tri" value="">
                                                            <h5 class="modal-title" id="">{{__('general.add_payment')}}</h5>
                                                            <button type="button" class="close" onclick="closePayment()"> <i data-feather="x"></i> </button>
                                                      </div>
                                                      <div class="modal-body">
                                                            <div class="form form-horizontal">
                                                                  <div class="form-body p-2">
                                                                        <div class="row payment_modal_" id="paymentsession">
                                                                              <div class="col-md-6 form-group">
                                                                                    <label>Dari Bank</label>
                                                                                    <select class=" form-control" name="from_bank" required>
                                                                                          <option value="">Pilih Opsi</option>
                                                                                          @foreach($banks as $b)
                                                                                          <option value="{{$b->id}}">{{$b->bank_name}}</option>
                                                                                          @endforeach
                                                                                    </select>
                                                                              </div>
                                                                              <div class="col-md-6 form-group">
                                                                                    <label>Ke Bank</label>
                                                                                    <select class=" form-control" name="to_bank" required>
                                                                                          <option value="">Pilih Opsi</option>
                                                                                          @foreach($ecommercebank as $ebanks)
                                                                                          <option value="{{$ebanks->id}}">{{$ebanks->name}}</option>
                                                                                          @endforeach
                                                                                    </select>
                                                                              </div>
                                                                              <div class="col-md-6 form-group">
                                                                                    <label>Nomor Rekening</label>
                                                                                    <div class="input-group mb-3">
                                                                                          <input type="text" required class="form-control" name="no_rek" id="no_rek">
                                                                                    </div>
                                                                              </div>
                                                                              <div class="col-md-6 form-group">
                                                                                    <label>Jumlah Transfer</label>
                                                                                    <div class="input-group mb-3">
                                                                                          <input type="text" class="form-control" value="0" id="payment_amount" name="amount">
                                                                                    </div>
                                                                              </div>

                                                                              <div class="col-12">
                                                                                    <div class="row" id="paymentprocess">
                                                                                          <div class="col-md-6 form-group">
                                                                                                <label>Bukti Pembayaran</label>
                                                                                                <div class="input-group mb-3">
                                                                                                      <input type="file" class="form-control" name="file">
                                                                                                </div>
                                                                                          </div>
                                                                                    </div>
                                                                              </div>
                                                                              
                                                                        </div>
                                                                        <br>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                      <div class="modal-footer">
                                                            <button type="button" onclick="bankModal()" class="btn btn-primary ml-1">
                                                                  <i class="bx bx-check d-block d-sm-none"></i>
                                                                  <span class="d-none d-sm-block">Daftar Bank</span>
                                                            </button>
                                                            <button type="submit" class="btn btn-primary ml-1">
                                                                  <i class="bx bx-check d-block d-sm-none"></i>
                                                                  <span class="d-none d-sm-block">Kirim Bukti Pembayaran</span>
                                                            </button>
                                                      </div>
                                                </form>
                                          </div>
                                    </div>
                                    <!-- End Payment Modal -->

                                    <!-- Modal Bank -->
                                    <div class="modal fade" id="bank_modal" tabindex="-1" role="dialog" aria-labelledby="bank-modal" aria-hidden="true">
                                          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl payment_modal" role="document">
                                                <form method="POST" id="bank_modalEcommercementPurchase" class="modal-content">
                                                      @csrf
                                                      <div class="modal-header header-modal ">
                                                            <h5 class="modal-title" id="">Daftar Tujuan Transfer Bank</h5>
                                                            <button type="button" onclick="closeBank()"> <i data-feather="x"></i> </button>
                                                      </div>
                                                      <div class="modal-body">
                                                            <div class="form form-horizontal">
                                                                  <div class="form-body p-2">
                                                                        <div class="row payment_modal_ p-4" id="paymentsession">

                                                                              @foreach($ecommercebank as $ebank)
                                                                              <div class="col-md-6 form-group">
                                                                                    <div class="vendor-wrap mb-40">
                                                                                          <div class="vendor-img-action-wrap">
                                                                                                <div class="vendor-img">
                                                                                                      <a href="#">
                                                                                                            <img class="default-img" src="{{asset($ebank->logo)}}" alt="">
                                                                                                      </a>
                                                                                                </div>
                                                                                          </div>
                                                                                          <div class="vendor-content-wrap">
                                                                                                <div class="d-flex justify-content-between align-items-end mb-30">
                                                                                                      <div>
                                                                                                            <h4 class="mb-5"><a href="#">{{$ebank->name}}</a></h4>

                                                                                                      </div>
                                                                                                </div>
                                                                                                <div class="vendor-info mb-30">
                                                                                                      <ul class="contact-infor text-muted">
                                                                                                            <li><strong>Nomor Rekening : </strong> <span>{{$ebank->no_rek}}</span></li>
                                                                                                            <li><strong>Atas Nama :</strong><span>{{$ebank->an}}</span></li>
                                                                                                      </ul>
                                                                                                </div>
                                                                                          </div>
                                                                                    </div>
                                                                              </div>
                                                                              @endforeach
                                                                        </div>
                                                                        <br>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                      <div class="modal-footer">
                                                            
                                                            <button type="button" onclick="showPayment(null)" class="btn btn-primary ml-1">
                                                                  <i class="bx bx-check d-block d-sm-none"></i>
                                                                  <span class="d-none d-sm-block">Kirim Bukti Bayar</span>
                                                            </button>
                                                      </div>
                                                </form>
                                          </div>
                                    </div>
                                    <!-- End Modal Bank -->

                              </div>
                        </div>
                  </div>
            </div>
      </div>
</main>

@endsection

@section('scripts')
<script src="{{asset('assets/vendors/datatables/datatables.min.js')}}"></script>
<script src="{{asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/vendors/datatables/datatables.js')}}"></script>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{$settings->client_key}}"></script>
<script src="{{asset('ecommerce/js/orders.js')}}"></script>
@endsection