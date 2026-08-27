<div class="menubar-footer footer-fixed">
    <ul class="inner-bar">
        <li {{ request()->is('m-ecommerce')  ? 'class=active' : '' }}><a href="{{route('ecommerce.mobile.home')}}"><i class="icon icon-home-active"></i> Home</a></li>
        <li {{ request()->is('m-ecommerce/shop*')  ? 'class=active' : '' }}><a href="{{route('ecommerce.mobile.shop')}}"><i class="icon icon-group"></i> Shop</a></li>
        <li><a href="{{route('ecommerce.mobile.cart')}}"><i class="icon icon-cart"></i> Keranjang</a></li>
        <li {{ request()->is('m-ecommerce/account/orders*')  ? 'class=active' : '' }}><a href="{{route('ecommerce.mobile.orders','hold')}}"><i class="icon icon-basket"></i> Pesanan</a></li>
        <li {{ request()->is('m-ecommerce/account/dashboard')  ? 'class=active' : '' }}><a href="{{route('ecommerce.mobile.dashboard')}}"><i class="icon icon-profile"></i> Profil</a></li>
    </ul>
</div>

<div class="modal fade modalRight" id="chooseStore">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="header fixed-top">
                <div class="left" data-bs-dismiss="modal">
                    <a href="javascript:void(0);" class="icon"><i class="icon-left-btn"></i></a>
                </div>
                <h6>Ganti Lokasi Toko</h6>
            </div>
            <div class="overflow-auto app-content style-7">
                <div class="tf-container">

                    <ul class="mt-20">
                        @foreach ($stores as $store)
                        <li class="d-flex align-items-center gap-20 py-16 line-bt">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                    <g clip-path="url(#clip0_1_5925)">
                                        <path d="M21 10.5C21 17.5 12 23.5 12 23.5C12 23.5 3 17.5 3 10.5C3 8.11305 3.94821 5.82387 5.63604 4.13604C7.32387 2.44821 9.61305 1.5 12 1.5C14.3869 1.5 16.6761 2.44821 18.364 4.13604C20.0518 5.82387 21 8.11305 21 10.5Z" stroke="#787982" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M12 13.5C13.6569 13.5 15 12.1569 15 10.5C15 8.84315 13.6569 7.5 12 7.5C10.3431 7.5 9 8.84315 9 10.5C9 12.1569 10.3431 13.5 12 13.5Z" stroke="#787982" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_1_5925">
                                            <rect width="24" height="24" fill="white" transform="translate(0 0.5)" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </div>
                            <a  href="javascript:void(0);" onclick="changeStore(<?= $store->id; ?>)">
                                <h6>{{$store->name}}</h6> 
                                <p class="mt-4 text-caption">{{$store->phone}} </p>
                                <p class="mt-2 text-caption">{{$store->address}} </p>
                            </a>
                        </li>
                        @endforeach


                    </ul>

                </div>
            </div>

        </div>
    </div>
</div>