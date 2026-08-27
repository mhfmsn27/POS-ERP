@extends('ecommerce::layouts.mobile')
@section('content')

<div class="header absolute">
    <div class="left">
        <a href="{{ url()->previous() }}" class="icon back-btn"><i class="icon-left-btn"></i></a>
    </div>

    <a href="{{route('ecommerce.mobile.cart')}}" class="right cart-product style2">
        <span class="badge-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="19" viewBox="0 0 20 19" fill="none">
                <path d="M7.24947 18.3334C7.70971 18.3334 8.0828 17.9603 8.0828 17.5001C8.0828 17.0398 7.70971 16.6667 7.24947 16.6667C6.78923 16.6667 6.41614 17.0398 6.41614 17.5001C6.41614 17.9603 6.78923 18.3334 7.24947 18.3334Z" stroke="#F5F5F5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M16.4163 18.3334C16.8766 18.3334 17.2497 17.9603 17.2497 17.5001C17.2497 17.0398 16.8766 16.6667 16.4163 16.6667C15.9561 16.6667 15.583 17.0398 15.583 17.5001C15.583 17.9603 15.9561 18.3334 16.4163 18.3334Z" stroke="#F5F5F5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M0.583008 0.833252H3.91634L6.14967 11.9916C6.22588 12.3752 6.4346 12.7199 6.7393 12.9652C7.04399 13.2104 7.42526 13.3407 7.81634 13.3333H15.9163C16.3074 13.3407 16.6887 13.2104 16.9934 12.9652C17.2981 12.7199 17.5068 12.3752 17.583 11.9916L18.9163 4.99992H4.74967" stroke="#F5F5F5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <i class="badge danger cart-total">{{$totalCart}}</i>

        </span>
    </a>
</div>
<div class="app-content style-5">
    <div class="swiper tf-swiper" data-space-between="0" data-preview="1" data-tablet="1" data-desktop="1">
        <div class="swiper-wrapper">
            @if($product->gallery()->count() > 0)
            @foreach ($product->gallery as $gallery)
            <div class="swiper-slide">
                <div class="banner-img">
                    <img src="{{asset($gallery->path)}}" alt="banner-img">
                </div>
            </div>
            @endforeach

            @else
            <div class="swiper-slide">
                <div class="banner-img">
                    <img src="{{asset($product->default_image)}}" alt="banner-img">
                </div>
            </div>
            @endif
        </div>
        <div class="swiper-pagination dots-tes style-2"></div>


    </div>

    <div class="line4-bt py-16">
        <div class="tf-container">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="title-detail">{{$product->name}} @if($product->type != 'single') - {{$product->single_variant->name ?? ''}} @endif</h6>

            </div>
            <h5 class="current-price text-primary mt-8">Rp {{number_format($product->single_variant->selling_price ?? 0)}}</h5>
            <p>
                Sisa Stok @if($product->type == 'single')
                <b class="in-stock">{{number_format($product->stock_in_website->sum("qty_available"))}}</b>
                @else
                <b class="in-stock">{{number_format($product->single_variant->stock_in_website->sum("qty_available"))}}</b>
                @endif Tersedia
            </p>

        </div>
    </div>


    @if($product->type != 'single')
    <div class="line4-bt py-16">
        <div class="tf-container">
            <h4 class="fw-6 mb-3">Pilih Variant</h4>
            @foreach($product->variant as $variant)
            <fieldset class="fieldset-radio mb-3">
                <input type="radio" class="tf-radio square choosevariant" value="<?= $variant->id; ?>" name="chooseVariant" id="chooseVariant1" <?= $variant->id == $product->single_variant->id ? 'checked=""' : ''; ?>>
                <label for="chooseVariant1">{{$variant->name}}</label>
            </fieldset>
            @endforeach

        </div>
    </div>
    @endif

    <div class="py-16">
        <div class="tf-container">
            <ul class="tab-3-item" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">Description</a>
                </li>

            </ul>
            <div class="tab-content mt-16">
                <div class="tab-pane fade active show" id="description" role="tabpanel">

                    <p>
                        Kategori : <span class="text-brand">{{$product->category->name ?? ''}}</span>
                    </p>
                    <p>
                        Merk : <span class="text-brand"> {{$product->brand->name ?? ''}}</span>
                    </p>
                    <p class="mb-3">
                        Berat : <span class="text-brand">{{$product->weight}}</span>
                    </p>
                    <?= $product->description; ?>


                </div>

            </div>



        </div>
    </div>

</div>

<form class="cart-footer footer-fixed">
    <input type="hidden" id="variationID" value="{{$product->single_variant->id ?? null}}">
    <input type="hidden" id="maxStock" value="{{(int)$product->single_variant->stock_in_website->sum('qty_available')}}">
    <input type="hidden" id="priceVariation" value="<?= $product->single_variant->selling_price ?? 0; ?>">
    <div class="inner">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-caption">Select quantity</span>
            <span class="text-caption">Total</span>
        </div>
        <div class="mt-8 d-flex justify-content-between align-items-center">
            <div class="tf-stepper round-2 sm surface">
                <input id="qtyCart" class="stepper" type="text" name="quantity" value="1" min="1">
            </div>
            <h5 class="pricetotal">Rp {{number_format($product->single_variant->selling_price ?? 0)}} </h5>
        </div>
        <div class="bottom-btn">
            <!-- <span class="press-toggle default-press">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                    <path d="M17.3671 4.34172C16.9415 3.91589 16.4361 3.5781 15.8799 3.34763C15.3237 3.11716 14.7275 2.99854 14.1254 2.99854C13.5234 2.99854 12.9272 3.11716 12.371 3.34763C11.8147 3.5781 11.3094 3.91589 10.8838 4.34172L10.0004 5.22506L9.11709 4.34172C8.25735 3.48198 7.09129 2.99898 5.87542 2.99898C4.65956 2.99898 3.4935 3.48198 2.63376 4.34172C1.77401 5.20147 1.29102 6.36753 1.29102 7.58339C1.29102 8.79925 1.77401 9.96531 2.63376 10.8251L3.51709 11.7084L10.0004 18.1917L16.4838 11.7084L17.3671 10.8251C17.7929 10.3994 18.1307 9.89407 18.3612 9.33785C18.5917 8.78164 18.7103 8.18546 18.7103 7.58339C18.7103 6.98132 18.5917 6.38514 18.3612 5.82893C18.1307 5.27271 17.7929 4.76735 17.3671 4.34172V4.34172Z" stroke="#787982" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span> -->
            <a href="javascript:void(0);" id="addToCartProduct" class="tf-btn primary">Tambah Keranjang</a>
        </div>

    </div>

</form>

@endsection