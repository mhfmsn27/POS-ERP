<div class="py-16">
    <div class="tf-container">

        <div class="mt-16 box-layout-2">
            @foreach($data as $product)
            <div class="card-product">
                <div class="box-img">
                    <img src="{{asset($product->default_image)}}" alt="">
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