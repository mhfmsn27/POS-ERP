<section class="banners mb-25">
      <div class="container">
            <div class="row">
                  @foreach($data as $banner)
                  <div class="col-lg-4 col-md-6">
                        <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay="0">
                              <img src="<?= $banner->image; ?>" alt="" />
                              <div class="banner-text">
                                    <h4>
                                          <?= $banner->title; ?>
                                    </h4>
                                    @if($banner->button == 'yes')
                                    <a href="{{$banner->button_url}}" class="btn btn-xs">{{$banner->button_name}} <i class="fi-rs-arrow-small-right"></i></a>
                                    @endif
                              </div>
                        </div>
                  </div>
                  @endforeach

            </div>
      </div>
</section>
<!--End banners-->