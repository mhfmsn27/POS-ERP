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
                              <h5 class="mb-1 fz-14">Toko {{$data->store->name ?? '' }}</h5>
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
                                                @foreach($data->adjustment as $gd )
                                                @php
                                                $subtotal = 0;
                                                $subtl = 0;
                                                $subtl += $gd->variation->purchase_price * $gd->qty_adjustment;
                                                $jumlah += $subtl;
                                                @endphp
                                                <tr>
                                                      <td>
                                                            {{$gd->variation->product->name ?? ''}} @if($gd->variation->name != 'no-name') {{ ' - '. $gd->variation->name ?? '' }} @endif
                                                      </td>
                                                      <td> {{ number_format($gd->variation->purchase_price)}} </td>
                                                      <td> {{ $gd->qty_adjustment }} </td>
                                                      <td>{{ number_format($subtl) }}</td>
                                                </tr>
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