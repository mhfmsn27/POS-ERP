@extends('ecommerce::layouts.web')

@section('content')


<main class="main">
      <div class="page-header breadcrumb-wrap">
            <div class="container">
                  <div class="breadcrumb">
                        <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                        <span></span> <a href="{{route('ecommerce.blog')}}">Blog & Berita</a> <span></span> {{$data->title}}
                  </div>
            </div>
      </div>
      <div class="page-content mb-50">
            <div class="container">
                  <div class="row">
                        <div class="col-xl-11 col-lg-12 m-auto mt-4">
                              <div class="row">
                                    <div class="col-lg-9">
                                          <div class="single-page pt-50 pr-30">
                                                <div class="single-header style-2">
                                                      <div class="row">
                                                            <div class="col-xl-10 col-lg-12 m-auto">
                                                                  <h6 class="mb-10"><a href="{{route('ecommerce.blog')}}?category={{$data->category_id}}">{{$data->category->name ?? ''}}</a></h6>
                                                                  <h2 class="mb-10">{{$data->title}}</h2>
                                                                  <div class="single-header-meta">
                                                                        <div class="entry-meta meta-1 font-xs mt-15 mb-15">
                                                                              <a class="author-avatar" href="#">
                                                                                    <img class="img-circle" src="{{asset($data->author->photo ?? '')}}" alt="" />
                                                                              </a>
                                                                              <span class="post-by">Author <a href="#">{{$data->author->name ?? ''}}</a></span>
                                                                              <span class="post-on has-dot">{{$data->created_at->format("Y-m-d")}}</span>
                                                                        </div>

                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                                <figure class="single-thumbnail">
                                                      <img src="{{asset($data->thumbnail)}}" alt="" />
                                                </figure>
                                                <div class="single-content">
                                                      <div class="row">
                                                            <div class="col-xl-10 col-lg-12 m-auto">

                                                                  <?= $data->description; ?> 

                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <x-ecommerce-sidebar-blog-component></x-ecommerce-sidebar-blog-component>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</main>

@endsection