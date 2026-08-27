<div class="iq-sidebar-small bg-primary">
    <div class="sidebar-top">
        <div class="iq-sidebar-small-logo">
            
        </div>
        <div class="sidebar-menu-icon">
            <a href="" class="iq-waves-effect" data-toggle="tooltip" data-placement="right" title="" data-original-title="Dashboard"><i class="ri-home-smile-line"></i></a>
            @can("POS")
            <a href="{{route('pos.index')}}" class="iq-waves-effect" data-toggle="tooltip" data-placement="right" title="" data-original-title="POS"><i class="ri-computer-line"></i></a>
            @endcan
            @can("Daftar Penjualan")
            <a href="{{ route('sales.reports') }}" class="iq-waves-effect" data-toggle="tooltip" data-placement="right" title="" data-original-title="Riwayat Penjualan"><i class="ri-shopping-cart-line"></i></a>
            @endcan

            @can("Peringatan Stock")
            <a href="{{ route('all.stock') }}" class="iq-waves-effect" data-toggle="tooltip" data-placement="right" title="" data-original-title="Stok Opname"><i class="ri-file-list-line"></i></a>
            @endcan
            <a href="javascript:void(0);" class="iq-waves-effect" data-toggle="tooltip" data-placement="right" title="" data-original-title="Akutansi"><i class="ri-line-chart-line"></i></a>
        </div>
    </div>
    <div class="sidebar-bottom">
        <div class="sidebar-menu-icon">
            <a href="{{ route('sett.index') }}" class="iq-waves-effect" data-toggle="tooltip" data-placement="right" title="" data-original-title="Pengaturan Umum"><i class="ri-settings-2-fill"></i></a>
            <div class="iq-menu-bt align-self-center">
                <div class="wrapper-menu">
                    <div class="menu-close"><i class="ri-arrow-left-line"></i></div>
                    <div class="menu-open"><i class="ri-arrow-right-line"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="iq-sidebar">
    <div class="iq-sidebar-logo d-flex justify-content-between">
        <a href="{{ route('index') }}">
            <span>{{$data->name}}</span>
        </a>
    </div>
    <div id="sidebar-scrollbar">
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">

                <!-- Master Data Produk -->

                

                <!-- Akuntansi -->
                
                <!-- End Akuntansi -->


              

                <!-- Purchase -->
                
                <!-- End Purchase -->

                <!-- Stok Opname -->
                
                <!-- End Stok -->

                <!-- Cash int Out -->
                
                <!-- End Cash Int Out -->


                <!-- Kasbon & Gaji -->
               
                <!-- End Kasbon & Gaji -->

                <!-- Pajak & spt -->
                @if(my_store_detail()->accountant_use == 'yes')
                <li {{ request()->is('pos-admin/taxrates*')  ? 'class=active' : '' }}>
                    <a href="{{route('taxrate.module')}}" class="iq-waves-effect"><i class="ri-bill-line"></i><span>Pajak & SPT</span></a>
                </li>
                @endif
                <!-- End Pajak & SPT -->

                <li class="iq-menu-title"><i class="ri-separator"></i><span>HRM</span></li>

                 
                <!-- Attendance Today -->
                @can("Absensi Hari ini")
                <li {{ request()->is('pos-admin/attendance*') ? 'class=active' : '' }}>
                    <a href="#attendancetoday" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-calendar-line"></i><span>{{ __('sidebar.attendance') }}</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                    <ul id="attendancetoday" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li {{ request()->is('pos-admin/attendance*') ? 'class=active' : '' }}><a href="{{ route('attendance.today') }}">{{ __('sidebar.today_attendance') }}</a></li>
                    </ul>
                </li>
                @endcan

                <!-- Pengajihan / Salary -->


                <x-admin.menu-component></x-admin.menu-component>


                <li {{ request()->is('pos-admin/payment*') ? 'class=active' : '' }}>
                    <a href="#reportspayment" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-currency-line"></i><span>Laporan Keuangan</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                    <ul id="reportspayment" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li {{ request()->is('pos-admin/payment/sell*') ? 'class=active' : '' }}><a href="{{ route('payment.sell') }}"> Uang Masuk Penjualan </a></li>

                    </ul>
                </li>

                <li class="iq-menu-title"><i class="ri-separator"></i><span>Laporan</span></li>

                <!-- Laporan Transaksi -->

                <li {{ request()->is('pos-admin/reports-sales*') ? 'class=active' : '' }}>
                    <a href="#transactionreport" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-bar-chart-box-line"></i><span>Laporan</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                    <ul id="transactionreport" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li {{ request()->is('pos-admin/report/transaction/sell*') || request()->is('pos-admin/sell*') ? 'class=active' : '' }}>
                            <a href="{{ route('reports.profit_sell') }}">Laporan Laba Rugi</a>
                        </li>
 

                        <li {{ request()->is('pos-admin/reports-crm*') ? 'class=active' : '' }}>
                            <a href="{{ route('reports.crm') }}">Laporan CRM</a>
                        </li>
 
                        <li {{ request()->is('pos-admin/report/shift-register*')   ? 'class=active' : '' }}>
                            <a href="{{ route('shift.report') }}">Shift Register</a>
                        </li>


                    </ul>
                </li>

                <!-- Laporan Transaksi Detail -->
                @if(Auth()->user()->can('Daftar Penjualan') || Auth()->user()->can('Laporan Purchase') || Auth()->user()->can('Laporan Return')
                || Auth()->user()->can('Laporan Hutang') || Auth()->user()->can('Daftar Laporan Pengeluaran') || Auth()->user()->can('Profit Loss Report') )
                <li {{ request()->is('pos-admin/report/detail*') ? 'class=active' : '' }}>
                    <a href="#transactiondetailreport" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-bar-chart-line"></i><span>Laporan Detail</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                    <ul id="transactiondetailreport" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li {{ request()->is('pos-admin/report/detail/trends-daily') ? 'class=active' : '' }}>
                            <a href="{{ route('trends.daily') }}">Trends Produk Harian</a>
                        </li>

                        <li {{ request()->is('pos-admin/report/detail/trends-monthly') ? 'class=active' : '' }}>
                            <a href="{{ route('trends.monthly') }}">Trends Produk Bulanan</a>
                        </li>

                        @can("Daftar Penjualan")
                        <li {{ request()->is('pos-admin/report/detail/sales*') ? 'class=active' : '' }}>
                            <a href="{{ route('sales.reports') }}">Detail Penjualan</a>
                        </li>
                        @endcan

                        @can("Laporan Purchase")
                        <li {{ request()->is('pos-admin/report/detail/purchase*') ? 'class=active' : '' }}>
                            <a href="{{ route('purchases.reports') }}">Detail PO / Pembelian</a>
                        </li>
                        @endcan

                    </ul>
                </li>
                @endif

                <!-- Laporan Stok Produk -->
                @if(Auth()->user()->can('Top Product') || Auth()->user()->can('Peringatan Stock') || Auth()->user()->can('Laporan Stock Adjustment') || Auth()->user()->can('Laporan Stock Transfer') )
                <li {{ request()->is('pos-admin/report/stock-product/stock-alert*') 
                    ||  request()->is('pos-admin/report/stock-product/top-product') 
                || request()->is('pos-admin/report/stock-product/transfer*') || request()->is('pos-admin/rreports-products*') || request()->is('pos-admin/report/stock-product/adjustment*') ? 'class=active' : '' }}>
                    <a href="#stockreports" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-pie-chart-line"></i><span>{{ __('sidebar.stock_n_product')}}</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                    <ul id="stockreports" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li {{ request()->is('pos-admin/reports-products*')  ? 'class=active' : '' }}>
                            <a href="{{ route('reports.products') }}">Laporan Stok Produk</a>
                        </li>
                        @can("Top Product")
                        <li {{ request()->is('pos-admin/report/stock-product/top-product*') ? 'class=active' : '' }}>
                            <a href="{{ route('top.product') }}">{{ __('sidebar.top_product')}}</a>
                        </li>
                        @endcan
                        @can("Peringatan Stock")
                        <li {{ request()->is('pos-admin/report/stock-product/stock-alert*')  ? 'class=active' : '' }}>
                            <a href="{{ route('stock.alert') }}">{{ __('sidebar.stock_alert')}}</a>
                        </li>
                        @endcan
                        @can("Laporan Stock Adjustment")
                        <li {{ request()->is('pos-admin/report/stock-product/adjustment*') ? 'class=active' : '' }}>
                            <a href="{{route('stock.adjustment')}}">{{ __('sidebar.stock_adjs')}}</a>
                        </li>
                        @endcan
                        @can("Laporan Stock Transfer")
                        <li {{ request()->is('pos-admin/report/stock-product/transfer*') ? 'class=active' : '' }}>
                            <a href="{{ route('stock.transfer') }}">{{ __('sidebar.r_stock_transfer')}}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif


                <li class="iq-menu-title"><i class="ri-separator"></i><span>Pengaturan</span></li>

              
                <!-- Pengaturan Sistem -->
                @if(Auth()->user()->can('Setting') || Auth()->user()->can('HRM Setting') || Auth()->user()->can('Daftar Negara')
                || Auth()->user()->can('Daftar Mata Uang') || Auth()->user()->can('Daftar Bank') || Auth()->user()->can('Daftar Printer')
                || Auth()->user()->can('Daftar Pajak'))
                <li {{ request()->is('pos-admin/system*') ? 'class=active' : '' }}>
                    <a href="#systemsett" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-settings-2-line"></i><span>{{__('sidebar.system_setting')}}</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                    <ul id="systemsett" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                       
                       

                    </ul>
                </li>
                @endif

                <!-- Toko -->
                @if(Auth()->user()->can('Tambah Toko') || Auth()->user()->can('Update Toko') || Auth()->user()->can('Pilih Toko') )
                <li {{ request()->is('pos-admin/store*') ? 'class=active' : '' }}>
                    <a href="#storeapp" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-store-2-line"></i><span>{{ __('sidebar.store') }}</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                    <ul id="storeapp" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        @can("Tambah Toko")
                        <li {{ request()->is('pos-admin/store/create') ? 'class=active' : '' }}>
                            <a href=" {{ route('store.create') }}">{{ __('sidebar.add_store') }}</a>
                        </li>
                        @endcan
                        @can("Update Toko")
                        <li {{ request()->is('pos-admin/store/update') ? 'class=active' : '' }}>
                            <a href="{{ route('store.update') }}">{{ __('sidebar.update_store') }}</a>
                        </li>
                        @endcan
                        @can("Pilih Toko")
                        <li class="submenu-item">
                            <a href="{{ route('store.choose') }}">{{ __('sidebar.choose_store') }} </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif

            </ul>
        </nav>
        <div class="p-3"></div>
    </div>
</div>