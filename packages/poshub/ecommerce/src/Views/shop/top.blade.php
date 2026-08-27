@extends('ecommerce::layouts.web')

@section('content')
<main class="main">
      <div class="page-header mt-30 ">
            <div class="container">
                  <div class="archive-header-2 text-center pt-80 pb-50">
                        <h1 class="display-2 mb-50">Produk Terlaris <br> 30 Hari Terakhir</h1>
                        <div class="row">
                              <div class="col-lg-5 mx-auto">
                                    <div class="sidebar-widget-2 widget_search mb-50">
                                           
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
      <div class="container mb-30">
            <div class="row">
                  <div class="col-xl-9">
                       
                        <div class="row product-grid">
                              @foreach($data as $product)
                              <div class="col-lg-3 col-md-4 col-12 col-sm-6">
                                    <div class="product-cart-wrap mb-30 wow animate__animated animate__fadeIn" data-wow-delay=".1s">
                                          <div class="product-img-action-wrap">
                                                <div class="product-img product-img-zoom">
                                                      <a href="{{route('ecommerce.shop_detail',$product->product->id)}}">
                                                            <img class="default-img" src="{{asset($product->product->image)}}" alt="" />
                                                            <img class="hover-img" src="{{asset($product->product->image)}}" alt="" />
                                                      </a>
                                                </div>
                                                <div class="product-action-1">
                                                      <!-- <a aria-label="Add To Wishlist" class="action-btn" href="shop-wishlist.html"><i class="fi-rs-heart"></i></a>
                                                      <a aria-label="Compare" class="action-btn" href="shop-compare.html"><i class="fi-rs-shuffle"></i></a> -->
                                                      <a class="action-btn" href="{{route('ecommerce.shop_detail',$product->product->id)}}"><i class="fi-rs-eye"></i></a>
                                                </div>
                                                <div class="product-badges product-badges-position product-badges-mrg">
                                                      <span class="hot">Hot</span>
                                                </div>
                                          </div>
                                          <div class="product-content-wrap">
                                                <div class="product-category">
                                                      <a href="{{route('ecommerce.shop')}}?category={{$product->product->category_id}}">{{$product->product->category->name ?? ''}}</a>
                                                </div>
                                                <h2><a href="{{route('ecommerce.shop_detail',$product->product->id)}}">{{$product->product->name ?? ''}}</a></h2>
                                                <div class="product-rate-cover">
                                                      <!-- <div class="product-rate d-inline-block">
                                                            <div class="product-rating" style="width: 90%"></div>
                                                      </div>
                                                      <span class="font-small ml-5 text-muted"> (4.0)</span> -->

                                                      <span class="font-small ml-5 text-muted"> ({{number_format($product->quantity)}}) Terjual</span>
                                                </div>
                                                <div>
                                                      <span class="font-small text-muted">Toko <a href="javascript:void(0);">{{$product->store->name ?? ''}}</a></span>
                                                </div>
                                                <div class="product-card-bottom">
                                                      <div class="product-price">
                                                            <span>Rp {{$product->product->price_sell_range}} </span>
                                                            <!-- <span class="old-price">$32.8</span> -->
                                                      </div>
                                                      <div class="add-cart">
                                                            <a class="add" href="{{route('ecommerce.shop_detail',$product->product->id)}}"><i class="fi-rs-eye mr-5"></i> Detail </a>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                              @endforeach
                        </div>

                  </div>
                  <x-ecommerce-sidebar-shop-component></x-ecommerce-sidebar-shop-component>
            </div>
      </div>
</main>
@endsection