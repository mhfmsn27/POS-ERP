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
            <div class="card invoice-card shadow">
                  <div class="card-body">

                        <div class="invoice-info text-end mb-4">
                              <h5 class="mb-1 fz-14">{{ $data->customer->name ?? '' }},</h5>
                              <h6 class="fz-12">No Ref #{{ $data->ref_no }}</h6>
                              <p class="mb-0 fz-12">Tanggal : {{ $data->created_at }}</p>
                        </div>
                        <!-- Invoice Table -->
                        <div class="invoice-table">
                              <div class="table-responsive">
                                    <table class="table table-bordered caption-top">
                                          <caption>Daftar Transaksi</caption>
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
                                                      <td class="text-end" colspan="3">{{__('general.payment_status')}} :</td>
                                                      <td class="text-end">{{ $status[$data->status] }}</td>
                                                </tr>
                                          </tfoot>
                                    </table>
                              </div>
                        </div>
                        <!-- NOTE FOR PRINT -->
                  </div>
            </div>
      </div>

</div>

<x-mobile.footer-component></x-mobile.footer-component>

@endsection