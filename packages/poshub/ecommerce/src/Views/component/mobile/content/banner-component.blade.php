<div class="pt-16 pb-16 line4-bt">
    <div class="tf-container">
        <div class="swiper tf-swiper swiper-wrapper-lr" data-space-between="12" data-preview="1.2" data-tablet="1.5" data-desktop="2">
            <div class="swiper-wrapper">
                @foreach ($data as $banner)
                <div class="swiper-slide">
                    <div class="banner-style2" style="background-image: url(images/background/bg-1.jpg);">
                        <div class="content-left">
                            <p class="text-white fw-5 text-caption"><?= $banner->title; ?> </p>
                            @if($banner->button == 'yes')
                            <a href="{{$banner->button_url}}" class="mt-12 tf-btn">{{$banner->button_name}}</a>
                            @endif
                        </div>
                        <div class="box-img">
                            <img src="{{asset($banner->image)}}" style="max-height:100px" alt="">
                        </div>
                    </div>
                </div>
                @endforeach


            </div>
        </div>
    </div>
</div>