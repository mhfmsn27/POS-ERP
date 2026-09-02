@extends('ecommerce::layouts.mobile')

@section('content')
<x-ecommerce-mobile-header-one-component></x-ecommerce-mobile-header-one-component>

<div class="app-content style-3">
    <div class="line4-bt pb-12">
        <div class="tf-container">
            <div class="d-flex gap-12 justify-content-between align-items-center">
                <form action="{{route('ecommerce.mobile.shop')}}" class="search-box">
                    <input type="text" name="name" class="search-field" placeholder="Cari Produk...">
                    <a type="submit" class="right-icon icon-search"></a>
                </form>
            </div>
        </div>
    </div>

    <div class="py-16">
        <div class="tf-container">
            <div class="mt-16 box-layout-2">
                @foreach($products as $product)
                <div class="card-product">
                    <div class="box-img">
                        <img src="{{asset($product->default_image)}}" loading="lazy" alt="{{$product->name ?? ''}}">
                    </div>
                    <div class="content">
                        <h6 class="text-caption text-onSurface"><a href="{{route('ecommerce.mobile.shop_detail',$product->id)}}">{{$product->name ?? ''}}</a></h6>
                        <div class="mt-4 d-flex justify-content-between align-items-end">
                            <div class="left">
                                <span class="text-onSurface fw-6">Rp {{$product->price_sell_range}}</span>
                                <ul>
                                    <li class="text-caption">({{number_format($product->stock_in_website->sum('qty_available'))}}) Stok</li>
                                </ul>
                            </div>
                            <a href="{{route('ecommerce.mobile.shop_detail',$product->id)}}" class="btn-cart style2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14" fill="none">
                                    <path d="M5.37484 12.8333C5.697 12.8333 5.95817 12.5721 5.95817 12.25C5.95817 11.9278 5.697 11.6666 5.37484 11.6666C5.05267 11.6666 4.7915 11.9278 4.7915 12.25C4.7915 12.5721 5.05267 12.8333 5.37484 12.8333Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M11.7917 12.8333C12.1139 12.8333 12.375 12.5721 12.375 12.25C12.375 11.9278 12.1139 11.6666 11.7917 11.6666C11.4695 11.6666 11.2084 11.9278 11.2084 12.25C11.2084 12.5721 11.4695 12.8333 11.7917 12.8333Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M0.708374 0.583252H3.04171L4.60504 8.39409C4.65838 8.66265 4.80449 8.9039 5.01778 9.07559C5.23106 9.24729 5.49795 9.3385 5.77171 9.33325H11.4417C11.7155 9.3385 11.9824 9.24729 12.1956 9.07559C12.4089 8.9039 12.555 8.66265 12.6084 8.39409L13.5417 3.49992H3.62504" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="pagination-area mt-20 mb-20 d-flex justify-content-center">
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-start">

                @if(count($pagination['links']) > 3)
                @foreach($pagination['links'] as $paginate)
                @if($paginate['url'] != null)

                @if($paginate['label'] == 'pagination.previous')
                <li class="page-item">
                    <a class="page-link" href="{{$paginate['url']}}"><i class="fa fa-arrow-left"></i></a>
                </li>
                @endif

                @if($paginate['label'] != 'pagination.previous' && $paginate['label'] != 'pagination.next')
                <li class="page-item @if($paginate['active'] == true) active @endif"><a class="page-link" href="{{$paginate['url']}}">{{$paginate['label']}} </a></li>
                @endif

                @if($paginate['label'] == 'pagination.next')
                <li class="page-item">
                    <a class="page-link" href="{{$paginate['url']}}"><i class="fa fa-arrow-right"></i></a>
                </li>
                @endif

                @endif
                @endforeach
                @endif

            </ul>
        </nav>
    </div>


    <div class="fixed-cart">
        <a href="{{route('ecommerce.mobile.cart')}}" class="cart-icon badge-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                <g clip-path="url(#clip0_1_751)">
                    <path d="M8.61989 25.7603C9.19263 25.7603 9.65693 25.2588 9.65693 24.6403C9.65693 24.0217 9.19263 23.5203 8.61989 23.5203C8.04715 23.5203 7.58286 24.0217 7.58286 24.6403C7.58286 25.2588 8.04715 25.7603 8.61989 25.7603Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M20.0281 25.7603C20.6008 25.7603 21.0651 25.2588 21.0651 24.6403C21.0651 24.0217 20.6008 23.5203 20.0281 23.5203C19.4554 23.5203 18.9911 24.0217 18.9911 24.6403C18.9911 25.2588 19.4554 25.7603 20.0281 25.7603Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M0.324066 2.23999H4.47221L7.25147 17.2368C7.3463 17.7524 7.60604 18.2156 7.98522 18.5453C8.3644 18.8749 8.83886 19.0501 9.32555 19.04H19.4055C19.8922 19.0501 20.3667 18.8749 20.7459 18.5453C21.1251 18.2156 21.3848 17.7524 21.4796 17.2368L23.1389 7.83999H5.50925" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </g>
                <defs>
                    <clipPath id="clip0_1_751">
                        <rect width="28" height="28" fill="white" />
                    </clipPath>
                </defs>
            </svg>
            <i class="badge danger">2</i>
        </a>
    </div>


</div>

<x-ecommerce-mobile-footer-component></x-ecommerce-mobile-footer-component>
@endsection