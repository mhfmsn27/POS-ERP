@extends('ecommerce::layouts.web')

@section('content')


<!--End header-->
<main class="main pages mb-80">
      <div class="page-header breadcrumb-wrap">
            <div class="container">
                  <div class="breadcrumb">
                        <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                        <span></span> Cabang Toko
                  </div>
            </div>
      </div>
      <div class="page-content pt-50">
            <div class="container">
                  <div class="archive-header-2 text-center mb-50">
                        <h1 class="display-2 mb-50">Daftar Cabang Kami</h1>
                        <div class="row">
                              <div class="col-lg-5 mx-auto">
                                    <div class="sidebar-widget-2 widget_search mb-50">

                                    </div>
                              </div>
                        </div>
                  </div>

                  <div class="row vendor-grid">
                        @foreach($data as $store)
                        <div class="col-lg-6 col-md-6 col-12 col-sm-6">
                              <div class="vendor-wrap style-2 mb-40">
                                    @if($store->id == session()->get('dfstore'))
                                    <div class="product-badges product-badges-position product-badges-mrg">
                                          <span class="hot">Saat ini</span>
                                    </div>
                                    @endif
                                    <div class="vendor-img-action-wrap">
                                          <div class="vendor-img">
                                                <a href="javascript:void(0);">
                                                      <img class="default-img" src="{{asset('ecommerce/imgs/vendor/store.png')}}" alt="" />
                                                </a>
                                          </div>
                                    </div>
                                    <div class="vendor-content-wrap">
                                          <div class="mb-30">
                                                <!-- <div class="product-category">
                                                      <span class="text-muted">Since 2012</span>
                                                </div> -->
                                                <h4 class="mb-5"><a href="javascript:void(0);">{{$store->name}}</a></h4>
                                                <!-- <div class="product-rate-cover">
                                                      <div class="product-rate d-inline-block">
                                                            <div class="product-rating" style="width: 90%"></div>
                                                      </div>
                                                      <span class="font-small ml-5 text-muted"> (4.0)</span>
                                                </div> -->
                                                <div class="vendor-info d-flex justify-content-between align-items-end mt-30">
                                                      <ul class="contact-infor text-muted">
                                                            <li><img src="assets/imgs/theme/icons/icon-location.svg" alt="" /><strong>Alamat: </strong> <span>{{$store->address}}</span></li>
                                                            <li><img src="assets/imgs/theme/icons/icon-contact.svg" alt="" /><strong>Nomor Kontak :</strong><span>{{$store->phone}}</span></li>
                                                      </ul>
                                                     
                                                </div>
                                                <a href="{{route('ecommerce.change_session',$store->id)}}" class="btn btn-xs mt-4">Pindah Cabang <i class="fi-rs-arrow-small-right"></i></a>
                                          </div>
                                    </div>
                              </div>
                        </div>
                        @endforeach
                  </div>

            </div>
      </div>
</main>

@endsection