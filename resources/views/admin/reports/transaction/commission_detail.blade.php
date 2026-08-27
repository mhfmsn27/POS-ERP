@extends('layouts.admin')
@section('content')
<div class="content-page">
      <div class="container-fluid">
            <x-admin.validation-component></x-admin.validation-component>
            <div class="card ">
                  <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                              <h4 class="card-title">{{$page}}</h4>
                        </div>
                  </div>
                  <div class="card-body">
                        <div class="row">
                              <div class="col-sm-12">
                                    <p><b>{{ __('general.date') }} : </b>
                                          {{ my_date($data->created_at) }}
                                    </p>
                              </div>
                        </div>

                        <br>
                        <div class="row">
                              <div class="col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                          <table class="table table-bordered">
                                                <thead>
                                                      <tr style="background-color: #3c8dbc; border: 1px solid white" class="text-white">
                                                            <th>#</th>
                                                            <th>Keterangan</th>
                                                            <th>Value</th>
                                                      </tr>
                                                </thead>
                                                <tbody>

                                                      <tr>
                                                            <td>1</td>
                                                            <td>Tipe Agent </td>
                                                            <td>{{ $data->type_name }}</td>
                                                      </tr>

                                                      <tr>
                                                            <td>2</td>
                                                            <td> Nama Agent </td>
                                                            <td>{{ $data->agent_name }}</td>
                                                      </tr>

                                                      <tr>
                                                            <td>3</td>
                                                            <td>Status Pembayaran </td>
                                                            <td>{{ $data->status_name }} </td>
                                                      </tr>

                                                      <tr>
                                                            <td>4</td>
                                                            <td>Toko </td>
                                                            <td>{{ $data->transaction->store->name ?? '' }}</td>
                                                      </tr>

                                                      <tr>
                                                            <td>5</td>
                                                            <td> No Ref Transaksi </td>
                                                            <td>{{ $data->transaction->ref_no ?? '' }}</td>
                                                      </tr>



                                                </tbody>
                                          </table>
                                    </div>
                              </div>
                        </div>
                        <br>
                        <div class="row">
                              <div class="col-sm-12 col-xs-12 mb-3">
                                    <h4>Detail Penjualan Produk :</h4>
                              </div>
                              <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                          <table class="table table-striped">
                                                <tr style="background-color: #3c8dbc; border: 1px solid white" class="text-white">
                                                      <td> Nama Produk
                                                      </td>
                                                      <td> Qty Terjual </td>
                                                      <td> Subtotal </td>
                                                </tr>
                                                @php
                                                $sale = $data->transaction->sale ?? '';
                                                @endphp
                                                @if ($sale != '')
                                                @foreach ($sale as $s)
                                                @php
                                                $subtotal = $s->unit_price * $s->qty;
                                                @endphp
                                                <tr>
                                                      <td>{{ $s->product->name ?? ('' . ' - ' . $s->variation->name ?? '') }}
                                                      </td>
                                                      <td> {{ number_format($s->qty) }}</td>
                                                      <td> {{ number_format($subtotal) }}</td>
                                                </tr>
                                                @endforeach

                                                @endif
                                                <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                                      <td colspan="2"> Total </td>
                                                      <td> {{ number_format($data->transaction->total_before_tax ?? 0) }}
                                                      </td>
                                                </tr>
                                          </table>
                                    </div>
                              </div>
                        </div>

                        <div class="row">
                              <div class="col-sm-12 col-xs-12 mt-4 mb-3">
                                    <h4>Detail Return Pengembalian Produk :</h4>
                              </div>
                              <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                          <table class="table table-bordered">
                                                <tbody>
                                                      <tr style="background-color: #3c8dbc; border: 1px solid white" class="text-white">
                                                            <th>No</th>
                                                            <th>No Ref</th>
                                                            <th>Pelanggan</th>
                                                            <th>Detail</th>
                                                      </tr>
                                                      @php
                                                      $no = 1;
                                                      @endphp
                                                      @foreach ($data->transaction->sales_return as $r)
                                                      <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $r->ref_no ?? '' }} </td>
                                                            <td>{{ $r->customer->name ?? '' }} </td>
                                                            <td>
                                                                  <table class="table table-striped">
                                                                        <tr>
                                                                              <td> Nama Produk
                                                                              </td>
                                                                              <td> Qty Terjual </td>
                                                                        </tr>
                                                                        @php
                                                                        $return = $r->sellreturn ?? ''; 
                                                                        @endphp 
                                                                        @foreach ($return as $rn)
                                                                        @php
                                                                        $subtotal = $rn->unit_price * $rn->qty;
                                                                        $name = $rn->sell->product->name ?? '';
                                                                        $var_name = $rn->sell->variation->name ?? '';
                                                                        @endphp
                                                                        <tr>
                                                                              <td>{{ $name . ' - ' . $var_name }}
                                                                              </td>
                                                                              <td> {{ number_format($rn->return_qty) }}
                                                                              </td>
                                                                        </tr>
                                                                        @endforeach 
                                                                  </table>
                                                            </td>
                                                      </tr>
                                                      @endforeach
                                                </tbody>
                                          </table>
                                    </div>
                              </div>
                        </div> 


                  </div>
            </div>
      </div>
</div>

@endsection