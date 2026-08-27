<div class="iq-sidebar sidebar-dark-blue">
    <div class="iq-sidebar-logo d-flex justify-content-center">
        <a href="{{ route('index') }}">
            <img src="{{asset('assets/images/icon-white.png')}}" class="img-fluid" alt="">
        </a>
    </div>
    <div id="sidebar-scrollbar" data-scrollbar="true" tabindex="-1" style="overflow: hidden; outline: none;">
        <div class="scroll-content">
            <nav class="iq-sidebar-menu">
                <ul id="iq-sidebar-toggle" class="iq-menu">

                    <li class="active">
                        <a href="{{ route('index') }}" class="iq-waves-effect collapsed">
                            <i class="ri-home-4-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Settings Menu -->
                    @if(auth()->user()->can('setting_view') || auth()->user()->can('role_view') || auth()->user()->can('user_view'))
                    <li>
                        <a href="#settings" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="ri-settings-2-fill"></i>
                            <span>Pengaturan</span>
                            <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                        </a>
                        <ul id="settings" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                            @if(auth()->user()->can('setting_view') || auth()->user()->can('store_setting_view'))
                            <li>
                                <a href=" {{ route('sett.index') }}"><i class="ri-settings-line"></i><span>Preferensi</span></a>
                            </li>
                            @endif

                            <li>
                                <a href="{{ route('store.choose') }}"><i class="ri-store-2-line"></i><span>Pilih Toko</span> </a>
                            </li>

                            @if(my_store_detail()->accountant_use == 'yes' && auth()->user()->can('account_default_view'))
                            <li>
                                <a href="{{ route('account.default') }}"><i class="ri-bookmark-line"></i><span>Akun Default</span></a>
                            </li>
                            @endif

                            @can("role_view")
                            <li>
                                <a href=" {{ route('role.index') }}"><i class="ri-shield-line"></i><span>Akses Group</span></a>
                            </li>
                            @endcan

                            @can("user_view")
                            <li>
                                <a href=" {{ route('user.index') }}"><i class="ri-user-line"></i><span>Pengguna</span></a>
                            </li>
                            @endcan

                            <li>
                                <a href=" {{ route('device') }}"><i class="fa fa-phone"></i><span>Whatsapp Device</span></a>
                            </li>

                            <li>
                                <a href=" {{ route('template') }}"><i class="fa fa-file"></i><span>Template Notifikasi</span></a>
                            </li>

                            <li>
                                <a href="{{ route('settings.backup') }}"><i class="ri-database-2-line"></i><span>Backup Database</span></a>
                            </li>

                            <li>
                                <a href="{{ route('settings.maintenance') }}"><i class="ri-tools-line"></i><span>Pemeliharaan Sistem</span></a>
                            </li>

                        </ul>
                    </li>
                    @endif
                    <!-- End Settings Menu -->

                    <!-- Company Menu -->
                    @if(auth()->user()->can('term_payment_view') ||
                    auth()->user()->can('department_view') || auth()->user()->can('designation_view') || auth()->user()->can('employee_view') ||
                    auth()->user()->can('printer_view') || auth()->user()->can('tax_view') || auth()->user()->can('courier_view'))
                    <li>
                        <a href="#company" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="ri-building-line"></i>
                            <span>Perusahaan</span>
                            <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                        </a>
                        <ul id="company" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                            @can('term_payment_view')
                            <li><a href="{{route('master_data.module')}}"><i class="ri-database-2-line"></i><span>Syarat Pembayaran</span></a></li>
                            @endcan

                            @can('courier_view')
                            <li><a href="{{route('courier.index')}}"><i class="ri-truck-line"></i><span>Data Ekspedisi</span></a></li>
                            @endcan

                            @can("department_view")
                            <li><a href="{{route('department.index')}}"><i class="ri-shield-user-line"></i><span>Devisi</span></a></li>
                            @endcan

                            @can("designation_view")
                            <li><a href="{{route('designation.index')}}"><i class="ri-file-user-line"></i><span>Jabatan</span></a></li>
                            @endcan

                            @can("employee_view")
                            <li><a href="{{ route('employee.index') }}"><i class="ri-user-2-line"></i><span>Pegawai</span></a></li>
                            @endcan

                            @can("printer_view")
                            <li><a href="{{ route('printer.index') }}"><i class="fe fe-printer"></i><span>Printer</span></a></li>
                            @endcan

                            @can("tax_view")
                            <li><a href="{{ route('taxrate.index') }}"><i class="ri-percent-line"></i><span>Master Pajak</span></a></li>
                            @endcan
                        </ul>
                    </li>
                    @endif
                    <!-- End Company Menu -->

                    <!-- Buku besar -->
                    @if(auth()->user()->can('type_account_view') ||
                    auth()->user()->can('account_view') || auth()->user()->can('allowance_view') || auth()->user()->can('cutting_view') ||
                    auth()->user()->can('salary_view') || auth()->user()->can('kasbon_view'))
                    <li>
                        <a href="#bukubesar" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="ri-book-line"></i>
                            <span>Buku Besar</span>
                            <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                        </a>
                        <ul id="bukubesar" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                            @if(my_store_detail()->accountant_use == 'yes' && (auth()->user()->can('type_account_view') || auth()->user()->can('account_view')))
                            <li>
                                <a href="{{route('accounting.module')}}"><i class="ri-book-line"></i><span>Akun Perkiraan</span></a>
                            </li>

                            <li>
                                <a href="{{route('jurnal.module')}}"><i class="ri-file-list-line"></i><span>Jurnal Umum</span></a>
                            </li>
                            @endif

                            @can("allowance_view")
                            <li><a href="{{route('allowance.index')}}"><i class="ri-file-shield-line"></i><span>Tunjangan</span></a></li>
                            @endcan

                            @can("cutting_view")
                            <li><a href="{{route('cutting.index')}}"><i class="ri-shield-star-line"></i><span>Potongan</span></a></li>
                            @endcan

                            @if(auth()->user()->can('salary_view') || auth()->user()->can('kasbon_view'))
                            <li>
                                <a href="{{route('hrm.module')}}" class="iq-waves-effect"><i class="ri-bill-line"></i><span>Gaji & Kasbon</span></a>
                            </li>
                            @endif


                        </ul>
                    </li>
                    @endif
                    <!-- End Buku Besar -->

                    <!-- Kas Dan Bank -->
                    @if(auth()->user()->can('payment_method_view') ||
                    auth()->user()->can('expense_category_view') || auth()->user()->can('expense_view') || auth()->user()->can('cash_int_view') ||
                    auth()->user()->can('bank_history_view') || auth()->user()->can('rekonsiliasi'))
                    <li>
                        <a href="#cashbank" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="ri-bank-line"></i>
                            <span>Kas dan Bank</span>
                            <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                        </a>
                        <ul id="cashbank" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                            @can('payment_method_view')
                            <li>
                                <a href="{{route('cash_bank.module')}}"><i class="ri-bank-card-line"></i><span>Metode Pembayaran</span></a>
                            </li>
                            @endcan

                            @can('expense_category_view')
                            <li>
                                <a href="{{route('cash_bank.module')}}/categories"><i class="ri-bank-card-line"></i><span>Kategori Pembayaran / Penerimaan</span></a>
                            </li>
                            @endcan
                            @can('expense_view')
                            <li>
                                <a href="{{route('cash_bank.module')}}/expense"><i class="ri-currency-line"></i><span>Pembayaran</span></a>
                            </li>
                            @endcan
                            @can('cash_int_view')
                            <li>
                                <a href="{{route('cash_bank.module')}}/cashint"><i class="ri-wallet-line"></i><span>Penerimaan</span></a>
                            </li>
                            @endcan
                            @can('bank_history_view')
                            <li>
                                <a href="{{ route('cash_bank.module') }}/mutasi-bank"><i class="ri-list-ordered"></i><span>Histori bank</span></a>
                            </li>
                            @endcan
                            @can('rekonsiliasi')
                            <li>
                                <a href="{{route('cash_bank.module')}}/rekonsiliasi"><i class="ri-mastercard-line"></i><span>Rekonsiliasi Bank</span></a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endif
                    <!-- End Kas dan Bank -->

                    <!-- Penjualan -->
                    @if(auth()->user()->can('shipping_view') ||
                    auth()->user()->can('sales_faktur_view') || auth()->user()->can('sales_retur_view') || auth()->user()->can('sales_payment_view') ||
                    auth()->user()->can('customer_view'))
                    <li>
                        <a href="#sales" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="ri-shopping-cart-line"></i>
                            <span>Penjualan</span>
                            <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                        </a>
                        <ul id="sales" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li>
                                <a href="/pos/layer"><i class="ri-computer-line"></i><span>Pos</span></a>
                            </li>
                            @can('shipping_view')
                            <li>
                                <a href="{{route('sales.module')}}"><i class="ri-truck-line"></i><span>Pengiriman Pesanan</span></a>
                            </li>
                            @endcan
                            @can('sales_faktur_view')
                            <li>
                                <a href="{{route('sales.module')}}/faktur"><i class="ri-shopping-cart-line"></i><span>Faktur Penjualan</span></a>
                            </li>
                            @endcan
                            @can('sales_payment_view')
                            <li>
                                <a href="{{route('sales.module')}}/payment"><i class="ri-wallet-line"></i><span>Penerimaan Penjualan</span></a>
                            </li>
                            @endcan
                            @can('sales_retur_view')
                            <li>
                                <a href="{{route('sales.module')}}/return"><i class="ri-text-wrap"></i><span>Retur Penjualan</span></a>
                            </li>
                            @endcan
                            @can('customer_view')
                            <li>
                                <a href="{{route('sales.module')}}/customer"><i class="ri-user-location-line"></i><span>Pelanggan</span></a>
                            </li>
                            @endcan

                        </ul>
                    </li>
                    @endif
                    <!-- End Penjualan -->

                    <!-- Pembelian -->
                    @if(auth()->user()->can('received_view') ||
                    auth()->user()->can('purchase_faktur_view') || auth()->user()->can('purchase_retur_view') || auth()->user()->can('purchase_payment_view') ||
                    auth()->user()->can('supplier_view'))
                    <li>
                        <a href="#purchase" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="ri-shopping-bag-line"></i>
                            <span>Pembelian</span>
                            <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                        </a>
                        <ul id="purchase" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                            @can('received_view')
                            <li>
                                <a href="{{route('purchase.module')}}" class="iq-waves-effect"><i class="ri-shopping-bag-line"></i><span>Penerimaan Pembelian</span></a>
                            </li>
                            @endcan
                            @can('purchase_faktur_view')
                            <li>
                                <a href="{{route('purchase.module')}}/faktur"><i class="ri-shopping-bag-2-line"></i><span>Faktur Pembelian</span></a>
                            </li>
                            @endcan
                            @can('purchase_payment_view')
                            <li>
                                <a href="{{route('purchase.module')}}/payment"><i class="ri-currency-line"></i><span>Pembayaran Pembelian</span></a>
                            </li>
                            @endcan
                            @can('purchase_retur_view')
                            <li>
                                <a href="{{route('purchase.module')}}/return"><i class="ri-text-wrap"></i><span>Retur Pembelian</span></a>
                            </li>
                            @endcan
                            @can('supplier_view')
                            <li>
                                <a href="{{route('purchase.module')}}/supplier"><i class="ri-user-location-line"></i><span>Pemasok</span></a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endif
                    <!-- End Pembelian -->

                    <!-- Persediaan -->
                    @if(auth()->user()->can('category_view') ||
                    auth()->user()->can('brand_view') || auth()->user()->can('unit_view') || auth()->user()->can('rak_view') ||
                    auth()->user()->can('product_view') || auth()->user()->can('so_view') || auth()->user()->can('transfer_view') ||
                    auth()->user()->can('warehouse_view'))
                    <li>
                        <a href="#persediaan" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="ri-grid-fill"></i>
                            <span>Persediaan</span>
                            <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                        </a>
                        <ul id="persediaan" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                            @if(auth()->user()->can('category_view') ||
                            auth()->user()->can('brand_view') || auth()->user()->can('unit_view') || auth()->user()->can('rak_view') ||
                            auth()->user()->can('product_view'))
                            <li>
                                <a href="{{route('inventori.module')}}/products" class="iq-waves-effect"><i class="ri-grid-fill"></i><span>Barang dan Jasa</span></a>
                            </li>
                            @endif
                            @can('so_view')
                            <li>
                                <a href="{{route('inventori.module')}}/stock-opname" class="iq-waves-effect"><i class="ri-checkbox-circle-line"></i><span>Penyesuaian barang</span></a>
                            </li>
                            @endcan
                            @can('transfer_view')
                            <li>
                                <a href="{{route('inventori.module')}}/warehouse" class="iq-waves-effect"><i class="ri-building-4-line"></i><span>Gudang</span></a>
                            </li>
                            @endcan
                            @can('warehouse_view')
                            <li>
                                <a href="{{route('inventori.module')}}/stock-opname/warehouse-list" class="iq-waves-effect"><i class="ri-file-transfer-line"></i><span>Pemindahan Barang</span></a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endif
                    <!-- End Persediaan -->

                    <!-- Rma -->
                    <li>
                        <a href="{{ route('rma.module') }}" class="iq-waves-effect">
                            <i class="ri-computer-line"></i>
                            <span>Rma</span>
                        </a>
                    </li>
                    <!-- End Rma -->

                    <x-admin.menu-component></x-admin.menu-component>
                    
                    <!-- Smart Link -->
                    @if(my_store_detail()->tax_option == 'active')
                    <li>
                        <a href="#tax" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false">
                            <i class="ri-percent-fill"></i>
                            <span>SmartLink Tax</span>
                            <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                        </a>
                        <ul id="tax" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                            <li>
                                <a href="{{route('taxes.module')}}" class="iq-waves-effect"><i class="ri-grid-fill"></i><span>Penomoran Faktur Pajak</span></a>
                            </li>

                        </ul>
                    </li>
                    @endif
                    <!-- End SmartLink -->
 

                    <!-- Reports -->
                    <li>
                        <a href="<?= route('reports.module'); ?>" class="iq-waves-effect">
                            <i class="ri-bar-chart-line"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                    <!-- End Reports -->

                </ul>
            </nav>
            <div class="p-3"></div>
        </div>
        <div class="scrollbar-track scrollbar-track-x" style="display: block;">
            <div class="scrollbar-thumb scrollbar-thumb-x" style="width: 35.0246px; transform: translate3d(0px, 0px, 0px);"></div>
        </div>
        <div class="scrollbar-track scrollbar-track-y" style="display: block;">
            <div class="scrollbar-thumb scrollbar-thumb-y" style="height: 420.009px; transform: translate3d(0px, 0px, 0px);"></div>
        </div>
    </div>
</div>