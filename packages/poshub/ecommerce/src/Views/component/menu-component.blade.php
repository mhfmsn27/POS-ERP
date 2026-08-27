<div class="mobile-header-active mobile-header-wrapper-style">
      <div class="mobile-header-wrapper-inner">
            <div class="mobile-header-top">
                  <div class="mobile-header-logo">
                        <a href="{{route('ecommerce.home')}}"><img src="{{asset($setting->logo)}}" alt="logo" /></a>
                  </div>
                  <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                        <button class="close-style search-close">
                              <i class="icon-top"></i>
                              <i class="icon-bottom"></i>
                        </button>
                  </div>
            </div>
            <div class="mobile-header-content-area">
                  <div class="mobile-search search-style-3 mobile-header-border">
                        <form action="{{route('ecommerce.shop')}}">
                              <input type="text" name="name" placeholder="Cari Item Disini…" />
                              <button type="submit"><i class="fi-rs-search"></i></button>
                        </form>
                  </div>
                  <div class="mobile-menu-wrap mobile-header-border">
                        <!-- mobile menu start -->
                        <nav>
                              <ul class="mobile-menu font-heading">
                                    <li class="menu-item-has-children">
                                          <a href="{{route('ecommerce.home')}}">Home</a>
                                    </li>
                                    <li class="menu-item-has-children">
                                          <a href="{{route('ecommerce.about')}}">About</a>
                                    </li>
                                    <li class="menu-item-has-children">
                                          <a href="{{route('ecommerce.shop')}}">shop</a>

                                    </li>
                                    <li class="menu-item-has-children">
                                          <a href="{{route('ecommerce.blog')}}">Blog</a>
                                    </li>

                                    <li class="menu-item-has-children">
                                          <a href="{{('ecommerce.top_sell')}}">Terlaris</a>
                                    </li>

                              </ul>
                        </nav>
                        <!-- mobile menu end -->
                  </div>
                  <div class="mobile-header-info-wrap">
                        <div class="single-mobile-header-info">
                              <a href="{{route('ecommerce.branch')}}"><i class="fi-rs-marker"></i> Cabang Toko </a>
                        </div>
                        @if(auth()->guard('customers')->check() == false)
                        <div class="single-mobile-header-info">
                              <a href="{{route('ecommerce.login')}}"><i class="fi fi-rs-sign-in"></i>Login</a>
                        </div>
                        <div class="single-mobile-header-info">
                              <a href="{{route('ecommerce.register')}}"><i class="fi fi-rs-sign-in"></i>Register </a>
                        </div>
                        @else
                        <div class="single-mobile-header-info">
                              <a href="{{route('ecommerce.dashboard')}}"><i class="fi-rs-user"></i>Akun Saya </a>
                        </div>
                        <div class="single-mobile-header-info">
                              <a href="{{route('ecommmerce.logout')}}"><i class="fi-rs-user"></i>Logout </a>
                        </div>
                        @endif

                  </div>
                  <div class="mobile-social-icon mb-50">
                        <h6 class="mb-15">Ikuti Kami</h6>
                        
                        @if($setting->facebook_url != '' && $setting->facebook_url != null)
                        <a href="{{$setting->facebook_url}}"><img src="{{asset('ecommerce/imgs/theme/icons/icon-facebook-white.svg')}}" alt="" /></a>
                        @endif

                        @if($setting->twitter_url != '' && $setting->twitter_url != null)
                        <a href="{{$setting->twitter_url}}"><img src="{{asset('ecommerce/imgs/theme/icons/icon-twitter-white.svg')}}" alt="" /></a>
                        @endif

                        @if($setting->instagram_url != '' && $setting->instagram_url != null)
                        <a href="{{$setting->instagram_url}}"><img src="{{asset('ecommerce/imgs/theme/icons/icon-instagram-white.svg')}}" alt="" /></a>
                        @endif

                        @if($setting->youtube_url != '' && $setting->youtube_url != null)
                        <a href="{{$setting->youtube_url}}"><img src="{{asset('ecommerce/imgs/theme/icons/icon-youtube-white.svg')}}" alt="" /></a>
                        @endif


                  </div>
                  <div class="site-copyright">{{$setting->copyright}}</div>
            </div>
      </div>
</div>