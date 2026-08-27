@extends('ecommerce::layouts.mobile')

@section('content')
<x-ecommerce-mobile-header-one-component></x-ecommerce-mobile-header-one-component>

<div class="app-content style-3">
    <div class="line4-bt pb-12">
        <div class="tf-container">
            <div class="d-flex gap-12 justify-content-between align-items-center">
                <form action="{{route('ecommerce.mobile.shop')}}" method="GET" class="search-box">
                    <input type="text" name="name" class="search-field" placeholder="Cari Produk...">
                    <a role="button" href="javascript:void(0);" type="submit" class="right-icon icon-search"></a>
                </form>
            </div>
        </div>
    </div>

    <x-ecommerce-mobile-category-component></x-ecommerce-mobile-category-component>
    <x-ecommerce-mobile-banner-component></x-ecommerce-mobile-banner-component>
    <x-ecommerce-mobile-product-component></x-ecommerce-mobile-product-component>


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
            <i class="badge danger"><?= $totalCart; ?></i>
        </a>
    </div>


</div>
<x-ecommerce-mobile-footer-component></x-ecommerce-mobile-footer-component>
@endsection