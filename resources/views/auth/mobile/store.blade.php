@extends('layouts.m')
@section('content')
<div class="header-area" id="headerArea">
      <div class="container">
            <div class="header-content header-style-five position-relative d-flex align-items-center justify-content-between">
                  <div></div>
                  <div class="page-heading">
                        <h6 class="mb-0">{{$page}}</h6>
                  </div>
                  <div></div>
            </div>
      </div>
</div>

<div class="page-content-wrapper py-3">

      <div class="top-products-area product-list-wrap">
            <div class="container">
                  <div class="row g-3">

                        @foreach($data as $d)
                        <div class="col-12 mb-2">
                              <div class="card single-product-card">
                                    <div class="card-body">
                                          <div class="d-flex align-items-center">
                                                <div class="card-side-img">
                                                      <a class="product-thumbnail d-block" href="{{route('choose.store',$d->id)}}">
                                                            <img src="{{asset('assets/vendors/maps/images/marker-icon-2x.png')}}" style="width: 100px;" alt="">
                                                      </a>
                                                </div>
                                                <div class="card-content px-4 py-2">
                                                      <a class="product-title d-block text-truncate mt-0" href="{{route('choose.store',$d->id)}}">{{$d->name}}</a>
                                                      <p>{{$d->phone}} </p>
                                                      <a class="btn btn-outline-info btn-sm" href="{{route('choose.store',$d->id)}}"> Masuk Toko</a>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>
                        @endforeach

                  </div>
            </div>
      </div>

</div>
<x-mobile.footer-component></x-mobile.footer-component>
@endsection