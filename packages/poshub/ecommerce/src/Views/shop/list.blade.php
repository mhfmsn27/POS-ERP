@extends('ecommerce::layouts.web')

@section('content')
<main class="main">
    <div class="page-header mt-30 mb-50">
        <div class="container">
            <div class="archive-header">
                <div class="row align-items-center">
                    <div class="col-xl-12">
                        <h1 class="mb-15">Produk Kami</h1>
                        <div class="breadcrumb">
                            <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                            <span></span> Shop <span></span> Produk Kami
                        </div>
                    </div>
                    <!-- <div class="col-xl-9 text-end d-none d-xl-block">
                            <ul class="tags-list">
                                <li class="hover-up">
                                    <a href="blog-category-grid.html"><i class="fi-rs-cross mr-10"></i>Cabbage</a>
                                </li>
                                <li class="hover-up active">
                                    <a href="blog-category-grid.html"><i class="fi-rs-cross mr-10"></i>Broccoli</a>
                                </li>
                                <li class="hover-up">
                                    <a href="blog-category-grid.html"><i class="fi-rs-cross mr-10"></i>Artichoke</a>
                                </li>
                                <li class="hover-up">
                                    <a href="blog-category-grid.html"><i class="fi-rs-cross mr-10"></i>Celery</a>
                                </li>
                                <li class="hover-up mr-0">
                                    <a href="blog-category-grid.html"><i class="fi-rs-cross mr-10"></i>Spinach</a>
                                </li>
                            </ul>
                        </div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="container mb-30">
        <div class="row">
            <x-ecommerce-sidebar-shop-component></x-ecommerce-sidebar-shop-component>
            <div class="col-xl-9">
                <div class="shop-product-fillter">
                    <div class="totall-product">
                        <p>Kami Menemukan <strong class="text-brand">{{number_format($totalProducts)}}</strong> Produk Untuk Kamu!</p>
                    </div>
                    <div class="sort-by-product-area">
                        <div class="sort-by-cover">
                            <div class="sort-by-product-wrap">
                                <div class="sort-by">
                                    <span><i class="fi-rs-apps-sort"></i>Urutkan:</span>
                                </div>
                                <div class="sort-by-dropdown-wrap">
                                    <span>
                                        @if(request('sort') == 'price_asc')
                                            Harga: Terendah
                                        @elseif(request('sort') == 'price_desc')
                                            Harga: Tertinggi
                                        @elseif(request('sort') == 'name_asc')
                                            Nama: A-Z
                                        @elseif(request('sort') == 'name_desc')
                                            Nama: Z-A
                                        @elseif(request('sort') == 'oldest')
                                            Terlama
                                        @else
                                            Terbaru
                                        @endif
                                        <i class="fi-rs-angle-small-down"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="sort-by-dropdown">
                                <ul>
                                    <li><a class="@if(!request('sort') || request('sort') == 'newest') active @endif" href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}">Terbaru</a></li>
                                    <li><a class="@if(request('sort') == 'price_asc') active @endif" href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}">Harga: Terendah</a></li>
                                    <li><a class="@if(request('sort') == 'price_desc') active @endif" href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}">Harga: Tertinggi</a></li>
                                    <li><a class="@if(request('sort') == 'name_asc') active @endif" href="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}">Nama: A-Z</a></li>
                                    <li><a class="@if(request('sort') == 'name_desc') active @endif" href="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}">Nama: Z-A</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row product-grid">
                    @foreach($products as $product)
                    <div class="col-lg-3 col-md-4 col-12 col-sm-6">
                        <div class="product-cart-wrap mb-30 wow animate__animated animate__fadeIn" data-wow-delay=".1s">
                            <div class="product-img-action-wrap">
                                <div class="product-img product-img-zoom">
                                    <a href="{{route('ecommerce.shop_detail',$product->id)}}">
                                        <img class="default-img" src="{{asset($product->default_image)}}" loading="lazy" alt="{{$product->name}}" />
                                        <img class="hover-img" src="{{asset($product->default_image)}}" loading="lazy" alt="{{$product->name}}" />
                                    </a>
                                </div>
                                <div class="product-action-1">
                                    <a aria-label="Lihat Detail" href="{{route('ecommerce.shop_detail',$product->id)}}" class="action-btn"><i class="fi-rs-eye"></i></a>
                                </div>
                            </div>
                            <div class="product-content-wrap">
                                <div class="product-category">
                                    <a href="{{route('ecommerce.shop')}}?category={{$product->category_id}}">{{$product->category->name ?? ''}}</a>
                                </div>
                                <h2><a href="{{route('ecommerce.shop_detail',$product->id)}}">{{$product->name ?? ''}}</a></h2>
                                @if(show_stock() == 'yes')
                                <div class="product-rate-cover">
                                    <span class="font-small ml-5 text-muted"> ({{number_format($product->stock_in_website->sum('qty_available'))}}) Stok</span>
                                </div>
                                @endif
                                <div class="product-card-bottom">
                                    <div class="product-price">
                                        <span>Rp {{$product->price_sell_range}} </span>
                                    </div>
                                    <div class="add-cart">
                                        <a class="add" href="{{route('ecommerce.shop_detail',$product->id)}}"><i class="fi-rs-shopping-cart mr-5"></i>Detail </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!--product grid-->
                <div class="pagination-area mt-20 mb-20">
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
                                <a class="page-link" href="{{$paginate['url']}}"><i class="fi-rs-arrow-small-right"></i></a>
                            </li>
                            @endif

                            @endif
                            @endforeach
                            @endif

                        </ul>
                    </nav>
                </div>

                <!--End Deals-->
            </div>
        </div>
    </div>
</main>
@endsection