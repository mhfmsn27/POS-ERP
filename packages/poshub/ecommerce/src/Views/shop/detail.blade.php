@extends("ecommerce::layouts.web")

@section("content")
<main class="main">
      <div class="page-header breadcrumb-wrap">
            <div class="container">
                  <div class="breadcrumb">
                        <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                        <span></span> <a href="{{route('ecommerce.shop')}}">Shop</a> <span></span> {{$product->name}}
                  </div>
            </div>
      </div>
      <div class="container mb-30">
            <div class="row">
                  <div class="col-xl-11 col-lg-12 m-auto">
                        <div class="row">
                              <div class="col-xl-9">
                                    <div class="product-detail accordion-detail">
                                          <div class="row mb-50 mt-30">
                                                <div class="col-md-6 col-sm-12 col-xs-12 mb-md-0 mb-sm-5">
                                                      <div class="detail-gallery">
                                                            <span class="zoom-icon"><i class="fi-rs-search"></i></span>
                                                            <!-- MAIN SLIDES -->
                                                            <div class="product-image-slider">

                                                                  @foreach($product->gallery as $gallery)
                                                                  <figure class="border-radius-10">
                                                                        <img src="{{asset($gallery->path ?? '')}}" alt="" />
                                                                  </figure>
                                                                  @endforeach
                                                            </div>
                                                            <!-- THUMBNAILS -->
                                                            <div class="slider-nav-thumbnails">
                                                                  @foreach($product->gallery as $gallery)
                                                                  <div> <img src="{{asset($gallery->path ?? '')}}" alt="" /></div>
                                                                  @endforeach

                                                            </div>
                                                      </div>
                                                      <!-- End Gallery -->
                                                </div>
                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                      <div class="detail-info pr-30 pl-30">
                                                            <!-- <span class="stock-status out-stock"> Sale Off </span> -->
                                                            <h2 class="title-detail">{{$product->name}} @if($product->type != 'single') - {{$product->single_variant->name ?? ''}} @endif </h2>
                                                            <div class="product-detail-rating">
                                                                  <!-- <div class="product-rate-cover text-end">
                                                                        <div class="product-rate d-inline-block">
                                                                              <div class="product-rating" style="width: 90%"></div>
                                                                        </div>
                                                                        <span class="font-small ml-5 text-muted"> (32 reviews)</span>
                                                                  </div> -->
                                                            </div>
                                                            <div class="clearfix product-price-cover">
                                                                  <div class="product-price primary-color float-left">
                                                                        <span class="current-price text-brand">Rp {{number_format($product->single_variant->selling_price ?? 0)}} </span>
                                                                        <!-- <span>
                                                                              <span class="save-price font-md color3 ml-15">26% Off</span>
                                                                              <span class="old-price font-md ml-15">$52</span>
                                                                        </span> -->
                                                                  </div>
                                                            </div>
                                                            <!-- <div class="short-desc mb-30">
                                                                  <p class="font-lg">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Aliquam rem officia, corrupti reiciendis minima nisi modi, quasi, odio minus dolore impedit fuga eum eligendi.</p>
                                                            </div> -->
                                                            @if($product->type != 'single')

                                                            <div class="attr-detail attr-size mb-30">
                                                                  <strong class="mr-10">Variant : </strong>
                                                                  <ul class="list-filter size-filter font-small">
                                                                        @foreach($product->variant as $variant)
                                                                        <li class="@if($variant->id == $product->single_variant->id) active @endif" id="listVariant{{$variant->id}}"><a href="javascript:void(0);" onclick="changeVariation(<?= $variant->id; ?>)">{{$variant->name}}</a></li>
                                                                        @endforeach
                                                                  </ul>
                                                            </div>

                                                            @endif

                                                            <div class="detail-extralink mb-50">
                                                                  <div class="detail-qty border radius">

                                                                        <input type="hidden" id="variationID" value="{{$product->single_variant->id ?? null}}">
                                                                        <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                                                                        <input type="text" id="qtyCart" name="quantity" class="qty-val" value="1" min="1">
                                                                        <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                                                                  </div>
                                                                  <div class="product-extra-link2">
                                                                        <button id="addToCartProduct" class="button button-add-to-cart"><i class="fi-rs-shopping-cart"></i>Tambah Keranjang</button>
                                                                        <!-- <a aria-label="Add To Wishlist" class="action-btn hover-up" href="shop-wishlist.html"><i class="fi-rs-heart"></i></a>
                                                                        <a aria-label="Compare" class="action-btn hover-up" href="shop-compare.html"><i class="fi-rs-shuffle"></i></a> -->
                                                                  </div>
                                                            </div>
                                                            <div class="font-xs">
                                                                  <ul class="mr-50 float-start">
                                                                        <li class="mb-5">Kategori : <span class="text-brand">{{$product->category->name ?? ''}}</span></li>
                                                                        <li class="mb-5">Merek :<span class="text-brand"> {{$product->brand->name ?? ''}}</span></li>

                                                                  </ul>
                                                                  <ul class="float-start">
                                                                        <li>Berat : <span class="text-brand">{{$product->weight}}</span></li>
                                                                        @if(show_stock() == 'yes')
                                                                        <!-- <li class="mb-5">Stok: <a href="#">FWM15VKT</a></li>
                                                                        <li class="mb-5">Tags: <a href="#" rel="tag">Snack</a>, <a href="#" rel="tag">Organic</a>, <a href="#" rel="tag">Brown</a></li> -->
                                                                        <li>Stok :<span class="in-stock text-brand ml-5">
                                                                                    @if($product->type == 'single')
                                                                                    {{number_format($product->stock_in_website->sum("qty_available"))}}
                                                                                    @else
                                                                                    {{number_format($product->single_variant->stock_in_website->sum("qty_available"))}}
                                                                                    @endif

                                                                                    Tersedia</span></li>
                                                                        @endif
                                                                  </ul>
                                                            </div>
                                                      </div>
                                                      <!-- Detail Info -->
                                                </div>
                                          </div>
                                          <div class="product-info">
                                                <div class="tab-style3">
                                                      <ul class="nav nav-tabs text-uppercase">
                                                            <li class="nav-item">
                                                                  <a class="nav-link active" id="Description-tab" data-bs-toggle="tab" href="#Description">Description</a>
                                                            </li>

                                                      </ul>
                                                      <div class="tab-content shop_info_tab entry-main-content">
                                                            <div class="tab-pane fade show active" id="Description">
                                                                  <div class="">
                                                                        <?= $product->description; ?>
                                                                        <?= $product->custom_field1; ?>
                                                                        <?= $product->custom_field2; ?>
                                                                        <?= $product->custom_field3; ?>
                                                                        <?= $product->custom_field4; ?>
                                                                  </div>
                                                            </div>

                                                      </div>
                                                </div>
                                          </div>
                                          <!-- <div class="row mt-60">
                                                <div class="col-12">
                                                      <h2 class="section-title style-1 mb-30">Related products</h2>
                                                </div>
                                                <div class="col-12">
                                                      <div class="row related-products">
                                                            <div class="col-lg-3 col-md-4 col-12 col-sm-6">
                                                                  <div class="product-cart-wrap hover-up">
                                                                        <div class="product-img-action-wrap">
                                                                              <div class="product-img product-img-zoom">
                                                                                    <a href="shop-product-right.html" tabindex="0">
                                                                                          <img class="default-img" src="assets/imgs/shop/product-2-1.jpg" alt="" />
                                                                                          <img class="hover-img" src="assets/imgs/shop/product-2-2.jpg" alt="" />
                                                                                    </a>
                                                                              </div>
                                                                              <div class="product-action-1">
                                                                                    <a aria-label="Quick view" class="action-btn small hover-up" data-bs-toggle="modal" data-bs-target="#quickViewModal"><i class="fi-rs-search"></i></a>
                                                                                    <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="shop-wishlist.html" tabindex="0"><i class="fi-rs-heart"></i></a>
                                                                                    <a aria-label="Compare" class="action-btn small hover-up" href="shop-compare.html" tabindex="0"><i class="fi-rs-shuffle"></i></a>
                                                                              </div>
                                                                              <div class="product-badges product-badges-position product-badges-mrg">
                                                                                    <span class="hot">Hot</span>
                                                                              </div>
                                                                        </div>
                                                                        <div class="product-content-wrap">
                                                                              <h2><a href="shop-product-right.html" tabindex="0">Ulstra Bass Headphone</a></h2>
                                                                              <div class="rating-result" title="90%">
                                                                                    <span> </span>
                                                                              </div>
                                                                              <div class="product-price">
                                                                                    <span>$238.85 </span>
                                                                                    <span class="old-price">$245.8</span>
                                                                              </div>
                                                                        </div>
                                                                  </div>
                                                            </div>
                                                            <div class="col-lg-3 col-md-4 col-12 col-sm-6">
                                                                  <div class="product-cart-wrap hover-up">
                                                                        <div class="product-img-action-wrap">
                                                                              <div class="product-img product-img-zoom">
                                                                                    <a href="shop-product-right.html" tabindex="0">
                                                                                          <img class="default-img" src="assets/imgs/shop/product-3-1.jpg" alt="" />
                                                                                          <img class="hover-img" src="assets/imgs/shop/product-4-2.jpg" alt="" />
                                                                                    </a>
                                                                              </div>
                                                                              <div class="product-action-1">
                                                                                    <a aria-label="Quick view" class="action-btn small hover-up" data-bs-toggle="modal" data-bs-target="#quickViewModal"><i class="fi-rs-search"></i></a>
                                                                                    <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="shop-wishlist.html" tabindex="0"><i class="fi-rs-heart"></i></a>
                                                                                    <a aria-label="Compare" class="action-btn small hover-up" href="shop-compare.html" tabindex="0"><i class="fi-rs-shuffle"></i></a>
                                                                              </div>
                                                                              <div class="product-badges product-badges-position product-badges-mrg">
                                                                                    <span class="sale">-12%</span>
                                                                              </div>
                                                                        </div>
                                                                        <div class="product-content-wrap">
                                                                              <h2><a href="shop-product-right.html" tabindex="0">Smart Bluetooth Speaker</a></h2>
                                                                              <div class="rating-result" title="90%">
                                                                                    <span> </span>
                                                                              </div>
                                                                              <div class="product-price">
                                                                                    <span>$138.85 </span>
                                                                                    <span class="old-price">$145.8</span>
                                                                              </div>
                                                                        </div>
                                                                  </div>
                                                            </div>
                                                            <div class="col-lg-3 col-md-4 col-12 col-sm-6">
                                                                  <div class="product-cart-wrap hover-up">
                                                                        <div class="product-img-action-wrap">
                                                                              <div class="product-img product-img-zoom">
                                                                                    <a href="shop-product-right.html" tabindex="0">
                                                                                          <img class="default-img" src="assets/imgs/shop/product-4-1.jpg" alt="" />
                                                                                          <img class="hover-img" src="assets/imgs/shop/product-4-2.jpg" alt="" />
                                                                                    </a>
                                                                              </div>
                                                                              <div class="product-action-1">
                                                                                    <a aria-label="Quick view" class="action-btn small hover-up" data-bs-toggle="modal" data-bs-target="#quickViewModal"><i class="fi-rs-search"></i></a>
                                                                                    <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="shop-wishlist.html" tabindex="0"><i class="fi-rs-heart"></i></a>
                                                                                    <a aria-label="Compare" class="action-btn small hover-up" href="shop-compare.html" tabindex="0"><i class="fi-rs-shuffle"></i></a>
                                                                              </div>
                                                                              <div class="product-badges product-badges-position product-badges-mrg">
                                                                                    <span class="new">New</span>
                                                                              </div>
                                                                        </div>
                                                                        <div class="product-content-wrap">
                                                                              <h2><a href="shop-product-right.html" tabindex="0">HomeSpeak 12UEA Goole</a></h2>
                                                                              <div class="rating-result" title="90%">
                                                                                    <span> </span>
                                                                              </div>
                                                                              <div class="product-price">
                                                                                    <span>$738.85 </span>
                                                                                    <span class="old-price">$1245.8</span>
                                                                              </div>
                                                                        </div>
                                                                  </div>
                                                            </div>
                                                            <div class="col-lg-3 col-md-4 col-12 col-sm-6 d-lg-block d-none">
                                                                  <div class="product-cart-wrap hover-up mb-0">
                                                                        <div class="product-img-action-wrap">
                                                                              <div class="product-img product-img-zoom">
                                                                                    <a href="shop-product-right.html" tabindex="0">
                                                                                          <img class="default-img" src="assets/imgs/shop/product-5-1.jpg" alt="" />
                                                                                          <img class="hover-img" src="assets/imgs/shop/product-3-2.jpg" alt="" />
                                                                                    </a>
                                                                              </div>
                                                                              <div class="product-action-1">
                                                                                    <a aria-label="Quick view" class="action-btn small hover-up" data-bs-toggle="modal" data-bs-target="#quickViewModal"><i class="fi-rs-search"></i></a>
                                                                                    <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="shop-wishlist.html" tabindex="0"><i class="fi-rs-heart"></i></a>
                                                                                    <a aria-label="Compare" class="action-btn small hover-up" href="shop-compare.html" tabindex="0"><i class="fi-rs-shuffle"></i></a>
                                                                              </div>
                                                                              <div class="product-badges product-badges-position product-badges-mrg">
                                                                                    <span class="hot">Hot</span>
                                                                              </div>
                                                                        </div>
                                                                        <div class="product-content-wrap">
                                                                              <h2><a href="shop-product-right.html" tabindex="0">Dadua Camera 4K 2022EF</a></h2>
                                                                              <div class="rating-result" title="90%">
                                                                                    <span> </span>
                                                                              </div>
                                                                              <div class="product-price">
                                                                                    <span>$89.8 </span>
                                                                                    <span class="old-price">$98.8</span>
                                                                              </div>
                                                                        </div>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div> -->
                                    </div>
                              </div>
                              <x-ecommerce-sidebar-shop-component></x-ecommerce-sidebar-shop-component>
                        </div>
                  </div>
            </div>
      </div>
</main>
@endsection