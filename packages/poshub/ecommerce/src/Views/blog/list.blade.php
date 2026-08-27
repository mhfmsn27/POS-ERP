@extends('ecommerce::layouts.web')

@section('content')


<!--End header-->
<main class="main">
      <div class="page-header mt-30 mb-75">
            <div class="container">
                  <div class="archive-header">
                        <div class="row align-items-center">
                              <div class="col-xl-3">
                                    <h1 class="mb-15">Blog & Berita</h1>
                                    <div class="breadcrumb">
                                          <a href="{{('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                                          <span></span> Blog & Berita
                                    </div>
                              </div>

                        </div>
                  </div>
            </div>
      </div>
      <div class="page-content mb-50">
            <div class="container">
                  <div class="row">
                        <div class="col-lg-9">
                              <div class="shop-product-fillter mb-50 pr-30">
                                    <div class="totall-product">
                                          <h2>
                                                <img class="w-36px mr-10" src="{{asset('ecommerce/imgs/theme/icons/category-1.svg')}}" alt="" />
                                                Daftar Blog & Berita
                                          </h2>
                                    </div>

                              </div>
                              <div class="loop-grid loop-list pr-30 mb-50">
                                    @foreach($blogs as $blog)
                                    <article class="wow fadeIn animated hover-up mb-30 animated">
                                          <div class="post-thumb" style="background-image: url(<?= asset($blog->thumbnail); ?>)">

                                          </div>
                                          <div class="entry-content-2 pl-50">
                                                <h3 class="post-title mb-20">
                                                      <a href="{{route('ecommerce.blog_detail',$blog->slug)}}">{{$blog->title}}</a>
                                                </h3>
                                                <p class="post-exerpt mb-40">{{$blog->short_description}}</p>
                                                <div class="entry-meta meta-1 font-xs color-grey mt-10 pb-10">
                                                      <div>
                                                            <span class="post-on">{{$blog->created_at->format("Y-m-d")}}</span>
                                                            <span class="hit-count has-dot">{{number_format($blog->views)}} Di Lihat</span>
                                                      </div>
                                                      <a href="{{route('ecommerce.blog_detail',$blog->slug)}}" class="text-brand font-heading font-weight-bold">Selengkapnya <i class="fi-rs-arrow-right"></i></a>
                                                </div>
                                          </div>
                                    </article>
                                    @endforeach
                              </div>
                              <div class="pagination-area mt-15 mb-sm-5 mb-lg-0">
                                    <nav aria-label="Page navigation example">
                                          <ul class="pagination justify-content-start">
                                                @if(count($pagination['links']) > 3)
                                                @foreach($pagination['links'] as $paginate)
                                                @if($paginate['url'] != null)

                                                @if($paginate['label'] == 'pagination.previous')
                                                <li class="page-item">
                                                      <a class="page-link" href="{{$paginate['url']}}"><i class="fi-rs-arrow-small-left"></i></a>
                                                </li>
                                                @endif

                                                @if($paginate['label'] != 'pagination.previous' && $paginate['label'] != 'pagination.next')
                                                <li class="page-item @if($paginate['active'] == true) active @endif"><a class="page-link" href="{{$paginate['url']}}">{{$paginate['label']}} </a></li>
                                                @endif

                                                @if($paginate['label'] == 'pagination.next')
                                                <li class="page-item">
                                                      <a class="page-link" href="#"><i class="fi-rs-arrow-small-right"></i></a>
                                                </li>
                                                @endif

                                                @endif
                                                @endforeach
                                                @endif
                                          </ul>
                                    </nav>
                              </div>
                        </div>
                       
                        <x-ecommerce-sidebar-blog-component></x-ecommerce-sidebar-blog-component>
                        
                  </div>
            </div>
      </div>

</main>

@endsection