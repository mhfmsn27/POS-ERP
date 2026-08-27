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
                              <h5 class="mb-1 fz-14">Toko Tujuan {{ $data->transfer->tostore->name ?? '' }},</h5>
                              <h6 class="fz-12">Dari Toko {{ $data->transfer->fromstore->name }}</h6>
                              <p class="mb-0 fz-12">Tanggal : {{ substr($data->created_at,0,10) }}</p>
                        </div>
                        <!-- Invoice Table -->
                        <div class="invoice-table">
                              <div class="table-responsive">
                                    <table class="table table-bordered caption-top">
                                          <caption>Daftar Transaksi</caption>
                                          <thead class="table-light">
                                                <tr>
                                                      <th>Produk</th>
                                                      <th>Satuan</th>
                                                      <th>Qty.</th>
                                                      <th>Total</th>
                                                </tr>
                                          </thead>
                                          <tbody>
                                                @php
                                                $no = 1;
                                                $jumlah = 0;
                                                @endphp
                                                @foreach($data->manytransfer as $gd )
                                                @php
                                                $subtotal = 0;
                                                $subtl = 0;
                                                $subtl += $gd->stock->variation->purchase_price * $gd->transfer_qty;
                                                $jumlah += $subtl;
                                                @endphp
                                                @if($gd->transfer_qty != 0)
                                                <tr>
                                                      <td>
                                                            {{$gd->stock->variation->product->name ?? ''}} @if($gd->stock->variation->name != 'no-name') {{ ' - '. $gd->stock->variation->name ?? '' }} @endif
                                                      </td>
                                                      <td> {{ number_format($gd->stock->variation->purchase_price)}} </td>
                                                      <td> {{ $gd->transfer_qty }} </td>
                                                      <td>{{ number_format($subtl) }}</td>
                                                </tr>
                                                @endif
                                                @endforeach
                                          </tbody>
                                          <tfoot class="table-light">
                                               
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