<div class="footer-nav-area" id="footerNav">
    <div class="container px-0">
        <div class="footer-nav position-relative">
            <ul class="h-100 d-flex align-items-center justify-content-between ps-0">
                <li  {{ request()->is('mobile/home') ? 'class=active' : '' }}>
                    <a href="{{route('m.index')}}"> <i style="font-size: 15px;" class="ti-home"></i><span>Beranda</span></a>
                </li>
                <li {{ request()->is('mobile/transaction*') ? 'class=active' : '' }}>
                    <a href="{{route('m.transaction')}}"> <i style="font-size: 15px;" class="fas fa-chart-area"></i><span>Transaksi</span></a>
                </li>
                <li>
                    <a href="{{ route('pos.index') }}"> <i style="font-size: 15px;" class="fas fa-desktop"></i><span>POS</span></a>
                </li>
                <li {{ request()->is('mobile/analityc*') || request()->is('mobile/expense*') ? 'class=active' : '' }}>
                    <a href="{{route('m.analytic')}}"><i style="font-size: 15px;" class="fas fa-chart-pie"></i><span>Analisa</span></a>
                </li>
                <li {{ request()->is('mobile/setting*') ? 'class=active' : '' }}>
                    <a href="{{route('m.setting')}}"><i style="font-size: 15px;" class="fas fa-cogs"></i><span>Pengaturan</span></a>
                </li>
            </ul>
        </div>
    </div>
</div>