@extends('ecommerce::layouts.mobile')
@section('content')
<div class="header fixed-top line4-bt">
    <div class="left">
        <a href="{{ url()->previous() }}" class="icon back-btn"><i class="icon-left-btn"></i></a>
    </div>
    <h6>Pesanan Saya</h6>
</div>
<div class="app-content bg-surface" style="height: 100vh;">
    <div class="bg-white pt-12 pb-12">
        <div class="tf-container">
            <ul class="product-tabs style2" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link @if($status == 'hold') active @endif" href="{{route('ecommerce.mobile.orders','hold')}}">Belum Bayar</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link @if($status == 'ordered') active @endif" href="{{route('ecommerce.mobile.orders','ordered')}}">Di Kemas</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link @if($status == 'transit') active @endif" href="{{route('ecommerce.mobile.orders','transit')}}">Di Kirim</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link @if($status == 'final') active @endif" href="{{route('ecommerce.mobile.orders','final')}}">Selesai</a>
                </li>
            </ul>
        </div>

    </div>
    <div class="tf-container">
        <div class="tab-content mt-16">
            <div class="tab-pane fade active show" id="processing" role="tabpanel">
                @if(count($transactions) > 0)

                @foreach ($transactions as $t)
                <div class="card-style2">
                    <div class="d-flex justify-content-between">
                        <p><span class="text-black"></span>#<?= $t->ref_no; ?></p>
                        <span><?= substr($t->created_at, 0, 10); ?></span>
                    </div>
                    <ul class="order-box mt-12">
                        <li>
                            <a href="order-details.html" class="order-item">
                                <div class="img">
                                    <img src="{{asset($t->sell_one->product->default_image ?? 'uploads/image.jpg')}}" class="rounded" alt="img">
                                </div>
                                <div class="content">
                                    <div class="left">
                                        <h6>Total {{count($t->sell)}} Item </h6>
                                        <p class="text-black mt-8">Ke Alamat {{$t->shipping_detail->name ?? ''}}  </p>
                                        <span class="text-caption">Jasa Antar : {{$t->shipping_detail->curir_name ?? ''}} </span>
                                    </div> 
                                </div>
                            </a>
                        </li>
                        
                    </ul>
                    <div class="bottom mt-12 d-flex align-items-center justify-content-between">
                        <a href="{{route('ecommerce.mobile.order_detail',$t->id)}}" class="btn-order shipped">
                            Lihat Detail
                        </a>
                        <div>
                            <span class="text-caption text-black">Total</span>
                            <span class="order-price">Rp {{number_format($t->final_total)}} </span>
                        </div>
                    </div>

                </div>
                @endforeach

                @else
                <div class="text-center">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" viewBox="0 0 90 90" fill="none">
                            <path d="M19.7566 31.3303L58.4464 10.643L45 3.875L5.1073 23.9558L19.7566 31.3303Z" fill="#787982" />
                            <path d="M70.1964 16.5577L31.5066 37.245L45 44.038L84.8927 23.9558L70.1964 16.5577Z" fill="#787982" />
                            <path d="M43.8984 45.9618L30.3125 39.1233V54.1486L24.4375 48.2339H18.5625V33.2086L3.875 25.8164V65.9779L43.8984 86.1248V45.9618Z" fill="#787982" />
                            <path d="M46.1016 45.9618V86.1248L86.125 65.9779V25.8164L46.1016 45.9618Z" fill="#787982" />
                        </svg>
                    </span>
                    <p class="fw-6 mt-20">Belum Ada Transaksi</p>
                    <a href="{{route('ecommerce.mobile.shop')}}" class="mt-40 tf-btn primary">Kembali berbelanja</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<x-ecommerce-mobile-footer-component></x-ecommerce-mobile-footer-component>

@endsection