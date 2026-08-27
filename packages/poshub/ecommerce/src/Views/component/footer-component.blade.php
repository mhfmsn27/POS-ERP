<footer class="main">

      <section class="featured section-padding">
            <div class="container">
                  <div class="row">
                        @foreach($featured as $f)
                        <div class="col-lg-1-5 col-md-4 col-12 col-sm-6 mb-md-4 mb-xl-0">
                              <div class="banner-left-icon d-flex align-items-center wow animate__animated animate__fadeInUp" data-wow-delay="0">
                                    <div class="banner-icon">
                                          <img src="{{asset($f->image)}}" alt="" />
                                    </div>
                                    <div class="banner-text">
                                          <h3 class="icon-box-title">{{$f->title}}</h3>
                                          <p>{{$f->subtitle}}</p>
                                    </div>
                              </div>
                        </div>
                        @endforeach
                  </div>
            </div>
      </section>
     
      <div class="container pb-30 wow animate__animated animate__fadeInUp" data-wow-delay="0">
            <div class="row align-items-center">
                  <div class="col-12 mb-30">
                        <div class="footer-bottom"></div>
                  </div>
                  <div class="col-xl-4 col-lg-6 col-md-6">
                        <p class="font-sm mb-0">&copy; <?= $setting->copyright; ?></p>
                  </div>
                  <div class="col-xl-4 col-lg-6 text-center d-none d-xl-block">

                        <div class="hotline d-lg-inline-flex mr-30">
                              <img src="{{asset('ecommerce/imgs/theme/icons/phone-call.svg')}}" alt="hotline" />
                              <p>{{$phoneCs->phone}}</p>
                        </div>
                        
                  </div>
                  <div class="col-xl-4 col-lg-6 col-md-6 text-end d-none d-md-block">
                        <div class="mobile-social-icon">
                              <h6>Ikuti Kami : </h6>

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
                  </div>
            </div>
      </div>
</footer>