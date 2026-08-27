@extends('layouts.m')
@section('content')

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


<div class="page-content-wrapper py-3">
      <x-mobile.alert-component></x-mobile.alert-component>

      <div class="container">
            <x-admin.validation-component></x-admin.validation-component>
            <div class="card invoice-card shadow">
                  <div class="card-body">
                        <div class="invoice-info text-end mb-4">
                              <h5 class="mb-1 fz-14">{{ $data->customer->name ?? '' }},</h5>
                              <h6 class="fz-12">No Ref #{{ $data->ref_no }}</h6>
                              <p class="mb-0 fz-12">Tanggal : {{ $data->created_at }}</p>
                        </div>
                        <div class="invoice-table">
                              <div class="table-responsive">
                                    <table class="table table-bordered caption-top">
                                          <caption>Daftar Produk Dibeli</caption>
                                          <thead class="table-light">
                                                <tr>
                                                      <th>Produk</th>
                                                      <th>Harga</th>
                                                      <th>Qty.</th>
                                                      <th>Total</th>
                                                </tr>
                                          </thead>
                                          <tbody>
                                                @php
                                                $no = 1;
                                                $jumlah = 0;
                                                @endphp
                                                @foreach($data->sell as $gd )

                                                @php
                                                $subtotal = 0;
                                                $subtl = 0;

                                                $subtl += $gd->unit_price * $gd->qty;
                                                $jumlah += $subtl;
                                                $subtotal += $gd->unit_price * $gd->qty;
                                                @endphp
                                                <tr>
                                                      <td>{{$gd->variation->product->name ?? ''}} @if($gd->variation->name != 'no-name') {{ ' - '. $gd->variation->name ?? '' }} @endif</td>
                                                      <td>{{ number_format($gd->unit_price) }}</td>
                                                      <td>{{ $gd->qty}}</td>
                                                      <td>{{ number_format($subtotal) }}</td>
                                                </tr>
                                                @endforeach
                                          </tbody>
                                          <tfoot class="table-light">
                                                <tr>
                                                      <td class="text-end" colspan="3">{{__('purchase.net_total')}} :</td>
                                                      <td class="text-end">{{number_format($jumlah)}}</td>
                                                </tr>
                                                <tr>
                                                      <td class="text-end" colspan="3">{{__('purchase.discount_total')}} :</td>
                                                      <td class="text-end">{{ number_format($data->discount_amount) }}%</td>
                                                </tr>
                                                <tr>
                                                      <td class="text-end" colspan="3">{{__('purchase.tax')}} :</td>
                                                      <td class="text-end">{{number_format($data->tax_amount)}}%</td>
                                                </tr>
                                                <tr>
                                                      <td class="text-end" colspan="3">{{__('purchase.shipping_cost')}} :</td>
                                                      <td class="text-end">{{ number_format($data->shipping_charges) }}</td>
                                                </tr>
                                                <tr>
                                                      <td class="text-end" colspan="3">{{__('purchase.other_payment')}} :</td>
                                                      <td class="text-end">{{ number_format($data->other_charges) }}</td>
                                                </tr>
                                                <tr>
                                                      <td class="text-end" colspan="3">{{__('general.total')}} :</td>
                                                      <td class="text-end">{{ number_format($data->final_total) }}</td>
                                                </tr>
                                                <tr>
                                                      <td class="text-end" colspan="3">Dibayar :</td>
                                                      <td class="text-end">{{ $data->pay_total }}</td>
                                                </tr>
                                                <tr>
                                                      <td class="text-end" colspan="3">Sisa Piutang :</td>
                                                      <td class="text-end">{{ number_format($data->due_total) }}</td>
                                                </tr>
                                                <tr>
                                                      <td class="text-end" colspan="3">{{__('general.payment_status')}} :</td>
                                                      <td class="text-end">{{ $status[$data->status] }}</td>
                                                </tr>
                                          </tfoot>
                                    </table>
                              </div>
                        </div>
                  </div>
            </div>

            <div class="card invoice-card shadow mt-2">
                  <div class="card-body">
                        <div class="invoice-table">
                              <div class="table-responsive">
                                    <table class="table table-bordered caption-top">
                                          <caption>Daftar List Pembayaran</caption>
                                          <thead class="table-light">
                                                <tr>
                                                      <th>Tanggal</th>
                                                      <th>Jumlah</th>
                                                      <th>Catatan</th>
                                                </tr>
                                          </thead>
                                          <tbody>
                                                @foreach ($payment as $d)
                                                <tr class="purchase_order">
                                                      @php
                                                      $method = '';
                                                      if ($d->method == 'cash') {
                                                      $method = 'Cash';
                                                      } elseif ($d->method == 'bank_transfer') {
                                                      $method = 'Bank Transfer';
                                                      } elseif ($pay->method == 'card') {
                                                      $method = 'Card';
                                                      } elseif ($d->method == 'other') {
                                                      $method = 'Lainnya';
                                                      }
                                                      @endphp
                                                      <td> {{ substr($d->created_at,0,10) }} </td>
                                                      <td> {{ number_format($d->amount) }} </td>
                                                      <td> {{ $d->note }} </td>
                                                </tr>
                                                @endforeach
                                          </tbody>

                                    </table>
                              </div>
                        </div>
                  </div>
            </div>

            <div class="card invoice-card shadow mt-2">
                  <div class="card-body">
                        <div class="row">
                              <div class="col-12">
                                    <button class="btn btn-block btn-primary" id="{{ $data->id }}" onclick="getpaymentmodal(this.id)" type="button">Tambah Cicilan</button>
                                    <button class="btn btn-block btn-primary" id="{{ $data->id }}" onclick="getstatusmodal(this.id)" type="button">Update Status</button>
                              </div>
                              <a href="javascript:void(0)" class="d-none" id="update_status" data-bs-toggle="modal" data-bs-target="#updatestatus"></a>
                              <a href="javascript:void(0)" class="d-none" id="add_payment" data-bs-toggle="modal" data-bs-target="#addpay"></a>
                        </div>
                  </div>
            </div>

      </div>

</div>

<div class="modal fade" id="addpay" tabindex="-1" aria-labelledby="addpay" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen-md-down">
            <form method="POST" action="{{route('m.add_pay')}}" class="modal-content">
                  @csrf
                  <div class="modal-header">
                        <h6 class="modal-title" id="addpay">Tambahkan Pembayaran / Cicilan</h6>
                        <button class="btn btn-close p-1 ms-auto" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                        <div class="form form-horizontal">
                              <div class="form-body">
                                    <div class="row" id="paymentsession">
                                          <div class="col-md-6 form-group">
                                                <label class="form-label">{{__('general.payment_method')}}</label>
                                                <input type="hidden" name="transaction_id" id="tri" value="">
                                                <select class="choices form-select" name="payment_method" id="payment_method">
                                                      <option value="cash">Cash</option>
                                                      <option value="bank_transfer">Bank Transfer</option>
                                                      <option value="card">Card</option>
                                                </select>
                                          </div>
                                          <div class="col-md-6 form-group">
                                                <label class="form-label">{{__('general.payment_date')}}</label>
                                                <div class="input-group mb-3">
                                                      <input type="text" class="form-control" value="{{date("Y-m-d H:i:s")}}" id="paid_date" name="paid_date" readonly="">
                                                </div>
                                          </div>
                                          <div class="col-12">
                                                <div class="row" id="paymentprocess">
                                                      <div class="col-md-6 form-group">
                                                            <label class="form-label">{{__('general.payment_total')}}</label>
                                                            <div class="input-group mb-3">
                                                                  <input type="text" class="form-control" value="0" id="payment_amount" name="payment_amount">
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                          <div class="col-md-12 form-group">
                                                <label class="form-label">{{__('general.payment_note')}}</label>
                                                <textarea class="form-control" name="payment_note" id="paymentnote"></textarea>
                                          </div>
                                    </div>
                                    <br>
                              </div>
                        </div>
                  </div>
                  <div class="modal-footer">
                        <button type="submit" style="width:100%" class="btn btn-lg btn-block btn-danger">
                              <i class="bx bx-x d-block d-sm-none"></i>
                              <span class=" d-sm-block">Tambahkan</span>
                        </button>
                  </div>
            </form>
      </div>
</div>

<div class="modal fade" id="updatestatus" tabindex="-1" aria-labelledby="updatestatus" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen-md-down">
            <form method="POST" action="{{route('m.update_status')}}" class="modal-content">
                  @csrf
                  <div class="modal-header">
                        <h6 class="modal-title">Ubah Status</h6>
                        <button class="btn btn-close p-1 ms-auto" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                        <div class="form form-horizontal">
                              <div class="form-group">
                                    <label for="system_name" class="form-label">{{__('general.payment_status')}}</label>
                                    <input type="hidden" name="transaction_id" id="ti" value="">
                                    <select class="form-control" name="status">
                                          <option value="final">Selesai</option>
                                    </select>
                              </div>
                        </div>
                  </div>
                  <div class="modal-footer">
                        <button type="submit" style="width:100%" class="btn btn-lg btn-block btn-danger">
                              <i class="bx bx-x d-block d-sm-none"></i>
                              <span class=" d-sm-block">Ubah</span>
                        </button>
                  </div>
            </form>
      </div>
</div>

<x-mobile.footer-component></x-mobile.footer-component>

@endsection

@section("scripts")
<script>
      $("body").on("keyup", "#payment_amount", function() {
            var nominal = $("#payment_amount").val();
            $("#payment_amount").val(formatRupiah(nominal.toString()))
      });

      function getstatusmodal(id) {
            $("#ti").val(id);
            document.getElementById("update_status").click();
      }

      function getpaymentmodal(id) {
            $("#tri").val(id);
            document.getElementById("add_payment").click();
      }
</script>
@endsection