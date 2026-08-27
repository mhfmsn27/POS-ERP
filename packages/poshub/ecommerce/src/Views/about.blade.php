@extends('ecommerce::layouts.web')

@section('content')


<!--End header-->
<main class="main pages mb-80">
      <div class="page-header breadcrumb-wrap">
            <div class="container">
                  <div class="breadcrumb">
                        <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                        <span></span> Tentang Kami
                  </div>
            </div>
      </div>
      <div class="page-content pt-50">
            <div class="container">
                  <div class="row">
                        <div class="col-xl-10 col-lg-12 m-auto">
                              <section class="row align-items-center mb-50">
                                    <div class="col-lg-6">
                                          <img src="{{asset($data->about_image)}}" alt="" class="border-radius-15 mb-md-3 mb-lg-0 mb-sm-4" />
                                    </div>
                                    <div class="col-lg-6">
                                          <div class="pl-25">
                                                <h2 class="mb-30">{{$data->about_title}}</h2>
                                                <?= $data->about_text; ?>

                                          </div>
                                    </div>
                              </section>
                              <section class="text-center mb-50">
                                    <h2 class="title style-3 mb-40">Apa Keunggulan Kami ?</h2>
                                    <div class="row">
                                          @foreach($featured as $f)
                                          <div class="col-lg-4 col-md-6 mb-24">
                                                <div class="featured-card">
                                                      <img src="{{asset($f->image)}}" alt="" />
                                                      <h4>{{$f->title}}</h4>
                                                      <p>{{$f->subtitle}}</p> 
                                                </div>
                                          </div>
                                          @endforeach
                                           
                                    </div>
                              </section>
                              
                        </div>
                  </div>
            </div>
            
      </div>
</main>

@endsection