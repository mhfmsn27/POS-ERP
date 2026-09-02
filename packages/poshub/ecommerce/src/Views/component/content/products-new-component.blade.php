<section class="section-padding pb-5">
      <div class="container">
            <div class="section-title wow animate__animated animate__fadeIn">
                  <h3 class="">Produk Baru Ditambahkan</h3>

            </div>
            <div class="row">
                  @if($banner)
                  <div class="col-lg-3 d-none d-lg-flex wow animate__animated animate__fadeIn">
                        <div class="banner-img style-2" style="background-image:url(<?= asset($banner->image); ?>)">
                              <div class="banner-text">
                                    <h2 class="mb-100"><?= $banner->title; ?> </h2>
                                    @if($banner->button == 'yes')
                                    <a href="{{$banner->button_url}}" class="btn btn-xs">{{$banner->button_name}} <i class="fi-rs-arrow-small-right"></i></a>
                                    @endif
                              </div>
                        </div>
                  </div>
                  @endif
                  <div class="@if($banner) col-lg-9 @endif col-md-12 wow animate__animated animate__fadeIn" data-wow-delay=".4s">
                        <div class="tab-content" id="myTabContent-1">
                              <div class="tab-pane fade show active" id="tab-one-1" role="tabpanel" aria-labelledby="tab-one-1">
                                    <div class="carausel-4-columns-cover arrow-center position-relative">
                                          <div class="slider-arrow slider-arrow-2 carausel-4-columns-arrow" id="carausel-4-columns-arrows"></div>
                                          <div class="carausel-4-columns carausel-arrow-center" id="carausel-4-columns">

                                          @foreach($data as $product)
                                                <div class="product-cart-wrap">
                                                      <div class="product-img-action-wrap">
                                                            <div class="product-img product-img-zoom">
                                                                  <a href="{{route('ecommerce.shop_detail',$product->id)}}">
                                                                        <img class="default-img" src="{{asset($product->default_image)}}" loading="lazy" alt="{{$product->name}}" />
                                                                        <img class="hover-img" src="{{asset($product->default_image)}}" loading="lazy" alt="{{$product->name}}" />
                                                                  </a>
                                                            </div>
                                                            <div class="product-action-1">
                                                                  <a  class="action-btn small hover-up" href="{{route('ecommerce.shop_detail',$product->id)}}"> <i class="fi-rs-eye"></i></a>
                                                                  <!-- <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="shop-wishlist.html"><i class="fi-rs-heart"></i></a>
                                                                  <a aria-label="Compare" class="action-btn small hover-up" href="shop-compare.html"><i class="fi-rs-shuffle"></i></a> -->
                                                            </div>
                                                            <div class="product-badges product-badges-position product-badges-mrg">
                                                                  <span class="hot">Produk Baru</span>
                                                            </div>
                                                      </div>
                                                      <div class="product-content-wrap">
                                                            <div class="product-category">
                                                                  <a href="{{route('ecommerce.shop')}}?category={{$product->category_id}}">{{$product->category->name ?? ''}}</a>
                                                            </div>
                                                            <h2><a href="{{route('ecommerce.shop_detail',$product->id)}}">{{$product->name}}</a></h2>
                                                            <!-- <div class="product-rate d-inline-block">
                                                                  <div class="product-rating" style="width: 80%"></div>
                                                            </div> -->
                                                            <div class="product-price mt-10">
                                                                  <span>Rp {{$product->price_sell_range}} </span>
                                                                  <!-- <span class="old-price">$245.8</span> -->
                                                            </div>
                                                            <!-- <div class="sold mt-15 mb-15">
                                                                  <div class="progress mb-5">
                                                                        <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuemin="0" aria-valuemax="100"></div>
                                                                  </div>
                                                                  <span class="font-xs text-heading"> Sold: 90/120</span>
                                                            </div> -->

                                                            @if(show_stock() == 'yes')
                                                            <div class="sold mt-15 mb-15">
                                                                  <!-- <div class="progress mb-5">
                                                                        <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuemin="0" aria-valuemax="100"></div>
                                                                  </div> -->
                                                                  <span class="font-xs text-heading"> Stok : {{number_format($product->stock_in_website->sum("qty_available"))}}</span>
                                                            </div>
                                                            @endif
                                                            <a href="{{route('ecommerce.shop_detail',$product->id)}}" class="btn w-100 hover-up"><i class="fi-rs-eye mr-5"></i> Detail Produk </a>
                                                      </div>
                                                </div>
                                                @endforeach
                                                
                                          </div>
                                    </div>
                              </div>
                              
                        </div>
                        <!--End tab-content-->
                  </div>
                  <!--End Col-lg-9-->
            </div>
      </div>
</section>