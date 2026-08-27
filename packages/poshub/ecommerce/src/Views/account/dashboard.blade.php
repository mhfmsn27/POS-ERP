@extends("ecommerce::layouts.web")

@section('content')

<main class="main pages">
      <div class="page-header breadcrumb-wrap">
            <div class="container">
                  <div class="breadcrumb">
                        <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                        <span></span> Account <span></span> Dashboard
                  </div>
            </div>
      </div>
      <div class="page-content pt-150 pb-150">
            <div class="container">
                  <div class="row">
                        <div class="col-lg-12 m-auto">
                              <div class="row">
                                    <x-ecommerce-sidebar-account-component></x-ecommerce-sidebar-account-component>
                                    <div class="col-md-9">

                                          <div class="row">
                                                <div class="col-lg-3  col-12 col-sm-6 mb-md-4 mb-xl-0">
                                                      <div class="banner-left-icon d-flex align-items-center wow animate__ animate__fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">
                                                            <div class="banner-icon">
                                                                  <img src="{{asset('ecommerce/imgs/theme/pending.png')}}" alt="">
                                                            </div>
                                                            <div class="banner-text">
                                                                  <h3 class="icon-box-title">({{number_format($data['pending'])}}) Pending </h3>
                                                                  <p>Transaksi Menunggu Pembayaran</p>
                                                            </div>
                                                      </div>
                                                </div>

                                                <div class="col-lg-3  col-12 col-sm-6 mb-md-4 mb-xl-0">
                                                      <div class="banner-left-icon d-flex align-items-center wow animate__ animate__fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">
                                                            <div class="banner-icon">
                                                                  <img src="{{asset('ecommerce/imgs/theme/process.png')}}" alt="">
                                                            </div>
                                                            <div class="banner-text">
                                                                  <h3 class="icon-box-title">({{number_format($data['process'])}}) Proses </h3>
                                                                  <p>Transaksi Sedang Dikemas</p>
                                                            </div>
                                                      </div>
                                                </div>

                                                <div class="col-lg-3  col-12 col-sm-6 mb-md-4 mb-xl-0">
                                                      <div class="banner-left-icon d-flex align-items-center wow animate__ animate__fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">
                                                            <div class="banner-icon">
                                                                  <img src="{{asset('ecommerce/imgs/theme/transit.png')}}" alt="">
                                                            </div>
                                                            <div class="banner-text">
                                                                  <h3 class="icon-box-title">({{number_format($data['transit'])}}) Antar </h3>
                                                                  <p>Transaksi Dalam Perjalanan</p>
                                                            </div>
                                                      </div>
                                                </div>

                                                <div class="col-lg-3  col-12 col-sm-6 mb-md-4 mb-xl-0">
                                                      <div class="banner-left-icon d-flex align-items-center wow animate__ animate__fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">
                                                            <div class="banner-icon">
                                                                  <img src="{{asset('ecommerce/imgs/theme/complete.png')}}" alt="">
                                                            </div>
                                                            <div class="banner-text">
                                                                  <h3 class="icon-box-title">({{number_format($data['complete'])}}) Diterima </h3>
                                                                  <p>Transaksi Diterima / Selesai</p>
                                                            </div>
                                                      </div>
                                                </div>

                                                <div class="col-12 mt-50 tab-content account dashboard-content">
                                                      <div class="card">
                                                            <div class="card-header">
                                                                  <h3 class="mb-0">Pesanan Terbaru</h3>
                                                            </div>
                                                            <div class="card-body">
                                                                  <div class="table-responsive">
                                                                        <table class="table">
                                                                              <thead>
                                                                                    <tr>
                                                                                          <th style="width:70px;text-align: center;">Tanggal</th>
                                                                                          <th>No Referensi</th>
                                                                                          <th>Status</th>
                                                                                          <th>Subtotal</th>
                                                                                          <th>Pajak</th>
                                                                                          <th>Ongkos Kirim</th>
                                                                                          <th>Grand Total</th>
                                                                                    </tr>
                                                                              </thead>
                                                                              <tbody>
                                                                                    @foreach($data['list'] as $transaksi)
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
                        </div>
                  </div>
            </div>
      </div>
</main>

@endsection