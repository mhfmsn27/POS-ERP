<header class="header-area header-style-1 header-height-2">

      <div class="header-middle header-middle-ptb-1 d-none d-lg-block">
            <div class="container">
                  <div class="header-wrap">
                        <div class="logo logo-width-1">
                              <a href="{{route('ecommerce.home')}}"><img src="{{asset($setting->logo)}}" alt="{{$setting->name}}" /></a>
                        </div>
                        <div class="header-right">
                              <div class="search-style-2">
                                    <form action="{{route('ecommerce.shop')}}">
                                          <select class="select-active" id="ourCategory" name="category">
                                                <option value="">Semua Kategori</option>
                                          </select>
                                          <input type="text" name="name" placeholder="Cari Produk Disini..." />
                                    </form>
                              </div>
                              <div class="header-action-right">
                                    <div class="header-action-2">


                                          <div class="header-action-icon-2">
                                                <a class="mini-cart-icon" href="{{route('ecommerce.cart')}}">
                                                      <img alt="Nest" src="{{asset('ecommerce/imgs/theme/icons/icon-cart.svg')}}" />
                                                      <span class="pro-count blue" id="proCount">0</span>
                                                </a>
                                                <a href="{{route('ecommerce.cart')}}"><span class="lable">Cart</span></a>
                                                <div class="cart-dropdown-wrap cart-dropdown-hm2">
                                                      <ul id="cartProducts">
                                                            <li class="cart_website_no"></li>
                                                      </ul>
                                                      <div class="shopping-cart-footer">
                                                            <div class="shopping-cart-total">
                                                                  <h4>Total <span id="totalinCart">0</span></h4>
                                                            </div>
                                                            <div class="shopping-cart-button d-none" id="shopCartWebsite">
                                                                  <a href="{{route('ecommerce.cart')}}" class="outline"> Keranjang</a>
                                                                  <a href="{{route('ecommerce.checkout')}}">Checkout</a>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                          <div class="header-action-icon-2">
                                                <a href="{{route('ecommerce.dashboard')}}">
                                                      <img class="svgInject" alt="Nest" src="{{asset('ecommerce/imgs/theme/icons/icon-user.svg')}}" />
                                                </a>
                                                <a href="{{route('ecommerce.dashboard')}}"><span class="lable ml-0">Account</span></a>
                                                <div class="cart-dropdown-wrap cart-dropdown-hm2 account-dropdown">
                                                      <ul>
                                                            @if(auth()->guard('customers')->check() == false)
                                                            <li>
                                                                  <a href="{{route('ecommerce.login')}}"><i class="fi fi-rs-sign-in mr-10"></i>Login</a>
                                                            </li>
                                                            <li>
                                                                  <a href="{{route('ecommerce.register')}}"><i class="fi fi-rs-sign-in mr-10"></i>Register</a>
                                                            </li>
                                                            @else
                                                            <li>
                                                                  <a href="{{route('ecommerce.dashboard')}}"><i class="fi fi-rs-user mr-10"></i>Akun Saya</a>
                                                            </li>
                                                            <li>
                                                                  <a href="{{route('ecommerce.address')}}"><i class="fi fi-rs-location-alt mr-10"></i>Atur Alamat</a>
                                                            </li>
                                                            <li>
                                                                  <a href="{{route('ecommmerce.logout')}}"><i class="fi fi-rs-sign-out mr-10"></i>Logout</a>
                                                            </li>
                                                            @endif
                                                            <!-- <li>
                                                                  <a href="page-account.html"><i class="fi fi-rs-label mr-10"></i>My Voucher</a>
                                                            </li>
                                                            <li>
                                                                  <a href="shop-wishlist.html"><i class="fi fi-rs-heart mr-10"></i>My Wishlist</a>
                                                            </li>
                                                            <li>
                                                                  <a href="{{route('ecommmerce.logout')}}"><i class="fi fi-rs-settings-sliders mr-10"></i>Setting</a>
                                                            </li>
                                                             -->

                                                      </ul>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
      <div class="header-bottom header-bottom-bg-color sticky-bar">
            <div class="container">
                  <div class="header-wrap header-space-between position-relative">
                        <div class="logo logo-width-1 d-block d-lg-none">
                              <a href="{{route('ecommerce.home')}}"><img src="{{asset($setting->logo)}}" alt="logo" /></a>
                        </div>
                        <div class="header-nav d-none d-lg-flex">
                              <div class="main-categori-wrap d-none d-lg-block">
                                    <a class="categories-button-active" href="#">
                                          <span class="fi-rs-apps"></span> <span class="et">Kategori</span> Unggulan
                                          <i class="fi-rs-angle-down"></i>
                                    </a>
                                    <div class="categories-dropdown-wrap categories-dropdown-active-large font-heading">
                                          <div class="d-flex categori-dropdown-inner">
                                                <ul>
                                                      @php
                                                      $no = 1;
                                                      @endphp

                                                      @foreach($featured as $feature)
                                                      @if($no++ % 2 == 0)
                                                      <li>
                                                            <a href="{{route('ecommerce.shop')}}?category={{$feature->id}}"> <img src="{{asset($feature->image)}}" alt="" />{{$feature->name}}</a>
                                                      </li>
                                                      @endif
                                                      @endforeach

                                                </ul>
                                                <ul class="end">
                                                      @php
                                                      $no = 1;
                                                      @endphp

                                                      @foreach($featured as $feature)
                                                      @if($no++ % 2 != 0)
                                                      <li>
                                                            <a href="{{route('ecommerce.shop')}}?category={{$feature->id}}"> <img src="{{asset($feature->image)}}" alt="" />{{$feature->name}}</a>
                                                      </li>
                                                      @endif
                                                      @endforeach

                                                </ul>

                                          </div>

                                    </div>
                              </div>
                              <div class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-block font-heading">
                                    <nav>
                                          <ul>
                                                <li class="hot-deals">
                                                      <img src="{{asset('ecommerce/imgs/theme/icons/icon-hot.svg')}}" alt="hot deals" /><a {{ request()->is('web/top-sells') ? 'class=active' : '' }} href="{{route('ecommerce.top_sell')}}">Terlaris</a>
                                                </li>
                                                <li>
                                                      <a {{ request()->is('web') ? 'class=active' : '' }} href="{{route('ecommerce.home')}}">Home</a>

                                                </li>
                                                <li>
                                                      <a {{ request()->is('web/shop*') ? 'class=active' : '' }} href="{{route('ecommerce.shop')}}">Shop </a> 
                                                </li>
                                                <li>
                                                      <a {{ request()->is('web/about') ? 'class=active' : '' }} href="{{route('ecommerce.about')}}">About</a>
                                                </li> 
                                                <li>
                                                      <a {{ request()->is('web/blog*') ? 'class=active' : '' }} href="{{route('ecommerce.blog')}}">Blog </a>
                                                </li>

                                          </ul>
                                    </nav>
                              </div>
                        </div>
                        <div class="hotline d-none d-lg-flex">
                              <img src="{{asset('ecommerce/imgs/theme/icons/icon-headphone.svg')}}" alt="hotline" />
                              <p>{{$setting->phone}}<span>24/7 Customer Service</span></p>
                        </div>
                        <div class="header-action-icon-2 d-block d-lg-none">
                              <div class="burger-icon burger-icon-white">
                                    <span class="burger-icon-top"></span>
                                    <span class="burger-icon-mid"></span>
                                    <span class="burger-icon-bottom"></span>
                              </div>
                        </div>
                        <div class="header-action-right d-block d-lg-none">
                              <div class="header-action-2">
                                    <!-- <div class="header-action-icon-2">
                                          <a href="shop-wishlist.html">
                                                <img alt="Nest" src="{{asset('ecommerce/imgs/theme/icons/icon-heart.svg')}}" />
                                                <span class="pro-count white">4</span>
                                          </a>
                                    </div> -->
                                    <div class="header-action-icon-2">
                                          <a class="mini-cart-icon" href="#">
                                                <img alt="POSHUB Ecommerce" src="{{asset('ecommerce/imgs/theme/icons/icon-cart.svg')}}" />
                                                <span class="pro-count white" id="proCountMobile">0</span>
                                          </a>
                                          <div class="cart-dropdown-wrap cart-dropdown-hm2">
                                                <ul id="mobileCartData">
                                                      <li class="cart_website_no_mobile"></li>
                                                      <!-- <li>
                                                            <div class="shopping-cart-img">
                                                                  <a href="shop-product-right.html"><img alt="Nest" src="{{asset('ecommerce/imgs/shop/thumbnail-3.jpg')}}" /></a>
                                                            </div>
                                                            <div class="shopping-cart-title">
                                                                  <h4><a href="shop-product-right.html">Plain Striola Shirts</a></h4>
                                                                  <h3><span>1 × </span>$800.00</h3>
                                                            </div>
                                                            <div class="shopping-cart-delete">
                                                                  <a href="#"><i class="fi-rs-cross-small"></i></a>
                                                            </div>
                                                      </li>
                                                      <li>
                                                            <div class="shopping-cart-img">
                                                                  <a href="shop-product-right.html"><img alt="Nest" src="{{asset('ecommerce/imgs/shop/thumbnail-4.jpg')}}" /></a>
                                                            </div>
                                                            <div class="shopping-cart-title">
                                                                  <h4><a href="shop-product-right.html">Macbook Pro 2022</a></h4>
                                                                  <h3><span>1 × </span>$3500.00</h3>
                                                            </div>
                                                            <div class="shopping-cart-delete">
                                                                  <a href="#"><i class="fi-rs-cross-small"></i></a>
                                                            </div>
                                                      </li> -->
                                                </ul>
                                                <div class="shopping-cart-footer ">
                                                      <div class="shopping-cart-total">
                                                            <h4>Total <span id="totalinCartMobile">0</span></h4>
                                                      </div>
                                                      <div class="shopping-cart-button d-none" id="shopCartMobile">
                                                            <a href="{{route('ecommerce.cart')}}"> Keranjang</a>
                                                            <a href="{{route('ecommerce.checkout')}}">Checkout</a>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</header>