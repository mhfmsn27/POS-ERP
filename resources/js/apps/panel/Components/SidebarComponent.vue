<template>
    <div class="sticky">
        <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
        <div class="app-sidebar">
            <div class="side-header">
                <a
                    class="header-brand1"
                    href="javascript:void(0);"
                    @click="
                        $goTo({
                            name: 'home',
                        })
                    "
                >
                    <img
                        :src="asset.logo"
                        class="header-brand-img desktop-logo"
                        alt="logo"
                    />
                    <img
                        :src="asset.icon"
                        class="header-brand-img toggle-logo"
                        alt="logo"
                    />
                    <img
                        :src="asset.icon"
                        class="header-brand-img light-logo"
                        alt="logo"
                    />
                    <img
                        :src="asset.logo"
                        class="header-brand-img light-logo1"
                        alt="logo"
                    />
                </a>
                <!-- LOGO -->
            </div>
            <div class="main-sidemenu">
                <div class="slide-left disabled" id="slide-left">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="#7b8191"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"
                        />
                    </svg>
                </div>
                <ul class="side-menu">
                    <li class="sub-category">
                        <h3>Main</h3>
                    </li>
                    <li>
                        <a
                            class="side-menu__item has-link"
                            href="javascript:void(0);"
                            @click="
                                $goTo({
                                    name: 'home',
                                })
                            "
                        >
                            <img
                                :src="asset.sidebar.home"
                                class="side-menu__icon"
                            />
                            <!-- <i class="side-menu__icon fe fe-airplay"></i> -->
                            <span class="side-menu__label">Dashboard</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a
                            class="side-menu__item"
                            data-bs-toggle="slide"
                            href="javascript:void(0);"
                        >
                            <!-- <i class="side-menu__icon fe fe-box"></i> -->
                            <img
                                :src="asset.sidebar.product"
                                class="side-menu__icon"
                            />
                            <span class="side-menu__label">Persediaan</span>
                            <i class="angle fe fe-chevron-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li class="panel sidetab-menu">
                                <div
                                    class="panel-body tabs-menu-body p-0 border-0"
                                >
                                    <div class="tab-content">
                                        <div class="tab-pane" id="side5">
                                            <ul class="sidemenu-list">
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'products',
                                                            })
                                                        "
                                                        class="slide-item"
                                                    >
                                                        Barang dan Jasa</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'categories',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Kategori</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'brands',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Brand</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'units',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Satuan</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'raks',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Etalase</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'warehouse',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Gudang</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'warehouse_transfer',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Pemindahan Barang</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'stock_opname_list',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Stok Opname</a
                                                    >
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="sub-category">
                        <h3>Konfigurasi</h3>
                    </li>
                    <li class="slide">
                        <a
                            class="side-menu__item"
                            data-bs-toggle="slide"
                            href="javascript:void(0);"
                        >
                            <!-- <i class="side-menu__icon fe fe-settings"></i> -->
                            <img
                                :src="asset.sidebar.setting"
                                class="side-menu__icon"
                            />
                            <span class="side-menu__label">Pengaturan</span>
                            <i class="angle fe fe-chevron-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li class="panel sidetab-menu">
                                <div
                                    class="panel-body tabs-menu-body p-0 border-0"
                                >
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="side5">
                                            <ul class="sidemenu-list">
                                                <li class="side-menu-label1">
                                                    <a
                                                        href="javascript:void(0);"
                                                        >Preferensi</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'setting_account_crm',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Preferensi</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'group',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Group Pengguna</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'user_list',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Pengguna</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'whatsapp_device',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Whatsapp Device</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'template_list',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Notifikasi Template</a
                                                    >
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="slide">
                        <a
                            class="side-menu__item"
                            data-bs-toggle="slide"
                            href="javascript:void(0);"
                        >
                            <!-- <i class="side-menu__icon fe fe-bookmark"></i> -->
                            <img
                                :src="asset.sidebar.company"
                                class="side-menu__icon"
                            />
                            <span class="side-menu__label">Perusahaan</span>
                            <i class="angle fe fe-chevron-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li class="panel sidetab-menu">
                                <div
                                    class="panel-body tabs-menu-body p-0 border-0"
                                >
                                    <div class="tab-content">
                                        <div class="tab-pane" id="side5">
                                            <ul class="sidemenu-list">
                                                <li class="side-menu-label1">
                                                    <a
                                                        href="javascript:void(0);"
                                                        >Syarat Pembayaran</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'term',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Syarat Pembayaran</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'couerier',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Data Ekspedisi</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'devision',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Data Devisi</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'designation',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Data Jabatan</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'employee_list',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Data Pegawai</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'printer',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Data Printer</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'taxrate',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Data Pajak</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'allowance',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Tunjangan</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'potongan',
                                                            })
                                                        "
                                                        class="slide-item"
                                                        >Potongan</a
                                                    >
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="sub-category">
                        <h3>Keuangan</h3>
                    </li>
                    <li class="slide">
                        <a
                            class="side-menu__item"
                            data-bs-toggle="slide"
                            href="javascript:void(0);"
                        >
                            <img
                                :src="asset.sidebar.book"
                                class="side-menu__icon"
                            />
                            <!-- <i class="side-menu__icon fe fe-book"></i> -->
                            <span class="side-menu__label">Buku Besar</span>
                            <i class="angle fe fe-chevron-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li class="panel sidetab-menu">
                                <div
                                    class="panel-body tabs-menu-body p-0 border-0"
                                >
                                    <div class="tab-content">
                                        <div class="tab-pane" id="side5">
                                            <ul class="sidemenu-list">
                                                <li class="side-menu-label1">
                                                    <a
                                                        href="javascript:void(0);"
                                                        >Akun Perkiraan</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'account_type',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Tipe Akun</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'accountant',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Akun Perkiraan</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'jurnal',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Jurnal Umum</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'kasbon',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Kasbon Pegawai</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'salary',
                                                            })
                                                        " s
                                                        class="slide-item"
                                                        >Gaji Pegawai</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'commission',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Komisi Pegawai</a
                                                    >
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="slide">
                        <a
                            class="side-menu__item"
                            data-bs-toggle="slide"
                            href="javascript:void(0);"
                        >
                            <img
                                :src="asset.sidebar.bank"
                                class="side-menu__icon"
                            />
                            <span class="side-menu__label">Kas dan Bank</span>
                            <i class="angle fe fe-chevron-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li class="panel sidetab-menu">
                                <div
                                    class="panel-body tabs-menu-body p-0 border-0"
                                >
                                    <div class="tab-content">
                                        <div class="tab-pane" id="side5">
                                            <ul class="sidemenu-list">
                                                <li class="side-menu-label1">
                                                    <a
                                                        href="javascript:void(0);"
                                                        >Metode Pembayaran</a
                                                    >
                                                </li>
                                                <a
                                                    href="javascript:void(0);"
                                                    @click="
                                                        $goTo({
                                                            name: 'payment_method',
                                                        })
                                                    " 
                                                    class="slide-item"
                                                    >Metode Pembayaran</a
                                                >
                                                <a
                                                    href="javascript:void(0);"
                                                    @click="
                                                        $goTo({
                                                            name: 'smartlink',
                                                        })
                                                    " 
                                                    class="slide-item"
                                                    >SmartLink e-Banking</a
                                                >
                                                <a
                                                    href="javascript:void(0);"
                                                    @click="
                                                        $goTo({
                                                            name: 'expense_category',
                                                        })
                                                    " 
                                                    class="slide-item"
                                                    >Kategori Pembayaran</a
                                                >
                                                <a
                                                    href="javascript:void(0);"
                                                    @click="
                                                        $goTo({
                                                            name: 'cash_int',
                                                        })
                                                    " 
                                                    class="slide-item"
                                                    >Penerimaan</a
                                                >
                                                <a
                                                    href="javascript:void(0);"
                                                    @click="
                                                        $goTo({
                                                            name: 'expense',
                                                        })
                                                    " 
                                                    class="slide-item"
                                                    >Pembayaran</a
                                                >
                                                <a
                                                    href="javascript:void(0);"
                                                    @click="
                                                        $goTo({
                                                            name: 'bank_mutation',
                                                        })
                                                    " 
                                                    class="slide-item"
                                                    >Mutasi Bank</a
                                                >
                                                <a
                                                    href="javascript:void(0);"
                                                    @click="
                                                        $goTo({
                                                            name: 'rekonsiliasi',
                                                        })
                                                    " 
                                                    class="slide-item"
                                                    >Rekonsiliasi Bank</a
                                                >
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="sub-category">
                        <h3>Transaksi</h3>
                    </li>
                    <li class="slide">
                        <a
                            class="side-menu__item"
                            data-bs-toggle="slide"
                            href="javascript:void(0);"
                        >
                            <img
                                :src="asset.sidebar.cart"
                                class="side-menu__icon"
                            />
                            <!-- <i class="side-menu__icon fe fe-shopping-cart"></i> -->
                            <span class="side-menu__label">Penjualan</span>
                            <i class="angle fe fe-chevron-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li class="panel sidetab-menu">
                                <div
                                    class="panel-body tabs-menu-body p-0 border-0"
                                >
                                    <div class="tab-content">
                                        <div class="tab-pane" id="side5">
                                            <ul class="sidemenu-list">
                                                <li class="side-menu-label1">
                                                    <a
                                                        href="javascript:void(0);"
                                                        >POS</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'sales_offer',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Penawaran Pesanan</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'sales_shipping',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Pengiriman Pesanan</a
                                                    >
                                                </li>

                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'sales_list',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Faktur Penjualan</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'sales_payment_list',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Penerimaan Penjualan</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'sales_return_list',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Retur Penjualan</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'customer',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Pelanggan</a
                                                    >
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="slide">
                        <a
                            class="side-menu__item"
                            data-bs-toggle="slide"
                            href="javascript:void(0);"
                        >
                            <img
                                :src="asset.sidebar.bag"
                                class="side-menu__icon"
                            />
                            <!-- <i class="side-menu__icon fe fe-shopping-bag"></i> -->
                            <span class="side-menu__label">Pembelian</span>
                            <i class="angle fe fe-chevron-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li class="panel sidetab-menu">
                                <div
                                    class="panel-body tabs-menu-body p-0 border-0"
                                >
                                    <div class="tab-content">
                                        <div class="tab-pane" id="side5">
                                            <ul class="sidemenu-list">
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'purchase_po',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Pemesanan Pembelian</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'purchase_received',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Penerimaan Pembelian</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'purchase_list',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Faktur Pembelian</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'purchase_payment_list',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Pembayaran Pembelian</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'purchase_return_list',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                        >Retur Pembelian</a
                                                    >
                                                </li>

                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'supplier',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                    >
                                                        Pemasok</a
                                                    >
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a
                            class="side-menu__item has-link" 
                            href="javascript:void(0);"
                            @click="
                                $goTo({
                                    name: 'rma_list',
                                })
                            "
                        >
                            <img
                                :src="asset.sidebar.service"
                                class="side-menu__icon"
                            />
                            <span class="side-menu__label">RMA</span>
                        </a>
                    </li>
                    <li class="sub-category">
                        <h3>Lainnya</h3>
                    </li>

                    <li>
                        <a 
                            class="side-menu__item has-link"
                            href="javascript:void(0);"
                            @click="
                                $goTo({
                                    name: 'report_list',
                                })
                            "
                        >
                            <img
                                :src="asset.sidebar.report"
                                class="side-menu__icon"
                            />
                            <!-- <i class="side-menu__icon fe fe-list"></i> -->
                            <span class="side-menu__label">Laporan</span>
                        </a>
                    </li>
                    <li class="sub-category">
                        <h3>Module Adds-ons</h3>
                    </li>
                    <li class="slide">
                        <a
                            class="side-menu__item"
                            data-bs-toggle="slide"
                            href="javascript:void(0);"
                        >
                            <!-- <i class="side-menu__icon fe fe-box"></i> -->
                            <img
                                :src="asset.sidebar.ecommerce"
                                class="side-menu__icon"
                            />
                            <span class="side-menu__label">E-Commerce</span>
                            <i class="angle fe fe-chevron-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li class="panel sidetab-menu">
                                <div
                                    class="panel-body tabs-menu-body p-0 border-0"
                                >
                                    <div class="tab-content">
                                        <div class="tab-pane" id="side5">
                                            <ul class="sidemenu-list">
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'e_commerce_blank',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                    >
                                                        Media Konten</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'e_commerce_blank_content',
                                                            })
                                                        " 
                                                        class="slide-item"
                                                    >
                                                        Blog & Page</a
                                                    >
                                                </li>

                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'e_commerce_setting',
                                                            })
                                                        "
                                                        class="slide-item"
                                                    >
                                                        Pengaturan E-Commerce</a
                                                    >
                                                </li>
                                                <li>
                                                    <a
                                                        href="javascript:void(0);"
                                                        @click="
                                                            $goTo({
                                                                name: 'e_commerce_transaction_blank',
                                                            })
                                                        "
                                                        class="slide-item"
                                                    >
                                                        Transaksi E-Commerce</a
                                                    >
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="slide-right" id="slide-right">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="#7b8191"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"
                        />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import logoApp from "@/assets/images/logo.webp";
import iconApp from "@/assets/images/icon.png";

// Icon for Sidebar
import homeIcon from "@/assets/icons/house.png";
import settingIcon from "@/assets/icons/gear.png";
import companyIcon from "@/assets/icons/office.png";
import bookIcon from "@/assets/icons/book.png";
import bankIcon from "@/assets/icons/bank.png";
import cartIcon from "@/assets/icons/carts.png";
import bagIcon from "@/assets/icons/shopping-bag.png";
import serviceIcon from "@/assets/icons/customer-service.png";
import taxIcon from "@/assets/icons/taxes.png";
import reportIcon from "@/assets/icons/report.png";
import productIcon from "@/assets/icons/best-product.png";
import ecommercerIcon from "@/assets/icons/shopping.png";

export default {
    components: {},
    data() {
        return {
            asset: {
                logo: logoApp,
                icon: iconApp,
                sidebar: {
                    home: homeIcon,
                    setting: settingIcon,
                    product: productIcon,
                    company: companyIcon,
                    book: bookIcon,
                    bank: bankIcon,
                    cart: cartIcon,
                    bag: bagIcon,
                    service: serviceIcon,
                    tax: taxIcon,
                    report: reportIcon,
                    ecommerce: ecommercerIcon,
                },
            },
        };
    },
    computed: {
        ...mapGetters("general", ["get_tabs_menus"]),
    },
    methods: {
        ...mapActions(["general/set_open_menu"]),

        openMenu(data) {
            this.$router.push({ name: data.links.name });
            this["general/set_open_menu"](data);
        },

        onloadSidebar() {
            let currentWidth;
            currentWidth = [window.innerWidth];
            $(document).on(
                "click",
                '[data-bs-toggle="sidebar"]',
                function (event) {
                    event.preventDefault();
                    // $('.app').toggleClass('sidenav-toggled');
                    if ($(".app").hasClass("sidenav-toggled")) {
                        $(".app").removeClass("sidenav-toggled");
                        if (
                            (document.body.classList.contains("double-menu") ||
                                document.body.classList.contains(
                                    "double-menu-tabs"
                                )) &&
                            !document.body.classList.contains("horizontal")
                        ) {
                            if (
                                document.querySelector(".slide-menu") &&
                                window.innerWidth >= 992
                            ) {
                                let slidemenu =
                                    document.querySelectorAll(".slide-menu");
                                slidemenu.forEach((e) => {
                                    if (
                                        e.classList.contains(
                                            "double-menu-active"
                                        )
                                    ) {
                                        e.classList.remove(
                                            "double-menu-active"
                                        );
                                    }
                                });
                                let sidemenuActive = document.querySelector(
                                    ".side-menu__item.active"
                                );
                                if (sidemenuActive?.nextElementSibling) {
                                    let submenu =
                                        sidemenuActive.nextElementSibling;
                                    submenu.classList.add("double-menu-active");
                                    document.body.classList.remove(
                                        "sidenav-toggled"
                                    );
                                } else {
                                    document.body.classList.add(
                                        "sidenav-toggled"
                                    );
                                }
                            }
                        }
                    } else {
                        $(".app").addClass("sidenav-toggled");
                        if (innerWidth >= 992) {
                            if (
                                (document.body.classList.contains(
                                    "double-menu"
                                ) ||
                                    document.body.classList.contains(
                                        "double-menu-tabs"
                                    )) &&
                                !document.body.classList.contains("horizontal")
                            ) {
                                if (document.querySelector(".slide-menu")) {
                                    let slidemenu =
                                        document.querySelectorAll(
                                            ".slide-menu"
                                        );
                                    slidemenu.forEach((e) => {
                                        if (
                                            e.classList.contains(
                                                "double-menu-active"
                                            )
                                        ) {
                                            e.classList.remove(
                                                "double-menu-active"
                                            );
                                        }
                                    });
                                }
                            }
                        }
                    }
                }
            );

            this.responsive();
            this.toggleSidebar();
            $(window).resize(this.toggleSidebar());

            $(window).on("scroll", function (e) {
                if ($(window).scrollTop() >= 70) {
                    $(".app-header").addClass("fixed-header");
                    $(".app-header").addClass("visible-title");
                } else {
                    $(".app-header").removeClass("fixed-header");
                    $(".app-header").removeClass("visible-title");
                }
            });

            $(window).on("scroll", function (e) {
                if ($(window).scrollTop() >= 70) {
                    $(".horizontal-main").addClass("fixed-header");
                    $(".horizontal-main").addClass("visible-title");
                } else {
                    $(".horizontal-main").removeClass("fixed-header");
                    $(".horizontal-main").removeClass("visible-title");
                }
            });

            //sticky-header
            $(window).on("scroll", function (e) {
                if ($(window).scrollTop() >= 70) {
                    $(".app-header").addClass("fixed-header");
                    $(".app-header").addClass("visible-title");
                } else {
                    $(".app-header").removeClass("fixed-header");
                    $(".app-header").removeClass("visible-title");
                }
            });

            this.HorizontalHovermenu();
            this.hovermenu();
            this.ActiveSubmenu();
        },

        toggleSidebar() {
            var w = $(window);
            if (w.outerWidth() <= 1024) {
                $("body").addClass("sidebar-gone");
                $(document)
                    .off("click", "body")
                    .on("click", "body", function (e) {
                        if (
                            $(e.target).hasClass("sidebar-show") ||
                            $(e.target).hasClass("search-show")
                        ) {
                            $("body").removeClass("sidebar-show");
                            $("body").addClass("sidebar-gone");
                            $("body").removeClass("search-show");
                        }
                    });
            } else {
                $("body").removeClass("sidebar-gone");
            }
        },

        responsive() {
            let currentWidth;
            currentWidth = [window.innerWidth];
            const mediaQuery = window.innerWidth;
            currentWidth.push(mediaQuery);
            if (currentWidth.length > 2) {
                currentWidth.shift();
            }
            if (currentWidth.length > 1) {
                if (
                    currentWidth[currentWidth.length - 1] < 992 &&
                    currentWidth[currentWidth.length - 2] >= 992
                ) {
                    // less than 992
                }

                if (
                    currentWidth[currentWidth.length - 1] >= 992 &&
                    currentWidth[currentWidth.length - 2] < 992
                ) {
                    // greater than 992

                    if (
                        document.body.classList.contains("double-menu") ||
                        document.body.classList.contains("double-menu-tabs")
                    ) {
                        document.body.classList.remove("sidenav-toggled");
                    }
                }
            }
        },

        HorizontalHovermenu() {
            let value = document
                .querySelector("body")
                .classList.contains("horizontal-hover");
            if (value && window.innerWidth >= 992) {
                $("[data-bs-toggle='slide']").off("click");
                $("[data-bs-toggle='sub-slide']").off("click");
                $("[data-bs-toggle='sub-slide2']").off("click");
                slideClick();
            } else {
                this.menuClick();
            }
        },

        hovermenu() {
            $(".app-sidebar").hover(
                function () {
                    if ($(".app").hasClass("sidenav-toggled")) {
                        $(".app").addClass("sidenav-toggled-open");
                    }
                },
                function () {
                    if ($(".app").hasClass("sidenav-toggled")) {
                        $(".app").removeClass("sidenav-toggled-open");
                    }
                }
            );
        },

        ActiveSubmenu() {
            var position = window.location.pathname.split("/");
            position = position[position.length - 1];
            $(".app-sidebar li a").each(function () {
                var $this = $(this);
                var pageUrl = window.location.href.split(/[?#]/)[0];
                let prevValue = [window.innerWidth];
                if (prevValue.length > 1) {
                    prevValue = prevWidth[prevWidth.length - 2];
                }

                if (this.href == pageUrl) {
                    setTimeout(() => {
                        if ($this.closest(".sub-slide-menu2")) {
                            $this.closest(".sub-slide-menu2").addClass("open");
                            if (
                                !document
                                    .querySelector("body")
                                    .classList.contains("horizontal") ||
                                window.innerWidth < 992
                            ) {
                                $this.closest(".sub-slide-menu2").slideDown();
                            }
                            $this
                                .closest(".sub-slide-menu2")
                                .prev()
                                .addClass("active");
                            $this
                                .closest(".sub-slide-menu2")
                                .parent()
                                .addClass("is-expanded");
                        }
                        if ($this.closest(".sub-slide-menu")) {
                            $this.closest(".sub-slide-menu").addClass("open");
                            if (
                                !document
                                    .querySelector("body")
                                    .classList.contains("horizontal") ||
                                window.innerWidth < 992
                            ) {
                                $this.closest(".sub-slide-menu").slideDown();
                            }
                            $this
                                .closest(".sub-slide-menu")
                                .parent()
                                .addClass("is-expanded");
                            $this
                                .closest(".sub-slide-menu")
                                .prev()
                                .addClass("active");
                        }
                        if ($this.closest(".slide-menu")) {
                            $this.closest(".slide-menu").addClass("open");
                            if (
                                !document
                                    .querySelector("body")
                                    .classList.contains("horizontal") ||
                                window.innerWidth < 992
                            ) {
                                $this.closest(".slide-menu").slideDown();
                            }
                            $this
                                .closest(".slide-menu")
                                .parent()
                                .addClass("is-expanded");
                            $this
                                .closest(".slide-menu")
                                .prev()
                                .addClass("active");
                        }
                        $this.addClass("active");
                        $this.parent().addClass("active");

                        if (
                            document.body.classList.contains(
                                "double-menu-tabs"
                            ) ||
                            document.body.classList.contains("double-menu")
                        ) {
                            if ($this.closest(".slide-menu").length) {
                                $this
                                    .closest(".slide-menu")
                                    .addClass("double-menu-active");
                            } else {
                                let slideMenu =
                                        document.querySelectorAll(
                                            ".slide-menu"
                                        ),
                                    slideNavStatus = false;
                                slideMenu.forEach((e) => {
                                    if (
                                        e.classList.contains(
                                            "double-menu-active"
                                        )
                                    ) {
                                        slideNavStatus = true;
                                    }
                                });
                                if (!slideNavStatus) {
                                    document.body.classList.add(
                                        "sidenav-toggled"
                                    );
                                }
                            }
                        }
                    }, 200);
                }
            });
        },

        menuClick() {
            $("[data-bs-toggle='slide']").off("click");
            $("[data-bs-toggle='sub-slide']").off("click");
            $("[data-bs-toggle='sub-slide2']").off("click");
            $("[data-bs-toggle='slide']").on("click", function (e) {
                var $this = $(this);
                var checkElement = $this.next();
                var animationSpeed = 300,
                    slideMenuSelector = ".slide-menu";
                if (
                    checkElement.is(slideMenuSelector) &&
                    checkElement.is(":visible")
                ) {
                    checkElement.slideUp(animationSpeed, function () {
                        checkElement.removeClass("open");
                    });
                    checkElement.parent("li").removeClass("is-expanded");
                } else if (
                    checkElement.is(slideMenuSelector) &&
                    !checkElement.is(":visible")
                ) {
                    var parent = $this.parents("ul").first();
                    var ul = parent
                        .find('ul[class^="slide-menu"]:visible')
                        .slideUp(animationSpeed);
                    ul.removeClass("open");
                    var parent_li = $this.parent("li");
                    checkElement.slideDown(animationSpeed, function () {
                        checkElement.addClass("open");
                        parent
                            .find("li.is-expanded")
                            .removeClass("is-expanded");
                        parent_li.addClass("is-expanded");
                    });
                }
                if (checkElement.is(slideMenuSelector)) {
                    e.preventDefault();
                }

                if (window.innerWidth >= 992) {
                    if (
                        !checkElement.hasClass("double-menu-active") &&
                        !document.body.classList.contains("horizontal") &&
                        (document.body.classList.contains("double-menu") ||
                            document.body.classList.contains(
                                "double-menu-tabs"
                            ))
                    ) {
                        if (document.querySelector(".slide-menu")) {
                            let slidemenu =
                                document.querySelectorAll(".slide-menu");
                            slidemenu.forEach((e) => {
                                if (
                                    e.classList.contains("double-menu-active")
                                ) {
                                    e.classList.remove("double-menu-active");
                                }
                            });
                        }

                        checkElement.addClass("double-menu-active");
                        document.body.classList.remove("sidenav-toggled");
                    }
                }
            });
            // Activate sidebar slide toggle
            $("[data-bs-toggle='sub-slide']").on("click", function (e) {
                var $this = $(this);
                var checkElement = $this.next();
                var animationSpeed = 300,
                    slideMenuSelector = ".sub-slide-menu";
                if (
                    checkElement.is(slideMenuSelector) &&
                    checkElement.is(":visible")
                ) {
                    checkElement.slideUp(animationSpeed, function () {
                        checkElement.removeClass("open");
                    });
                    checkElement.parent("li").removeClass("is-expanded");
                } else if (
                    checkElement.is(slideMenuSelector) &&
                    !checkElement.is(":visible")
                ) {
                    var parent = $this.parents("ul").first();
                    var ul = parent
                        .find('ul[class^="sub-slide-menu"]:visible')
                        .slideUp(animationSpeed);
                    ul.removeClass("open");
                    var parent_li = $this.parent("li");
                    checkElement.slideDown(animationSpeed, function () {
                        checkElement.addClass("open");
                        parent
                            .find("li.is-expanded")
                            .removeClass("is-expanded");
                        parent_li.addClass("is-expanded");
                    });
                }
                if (checkElement.is(slideMenuSelector)) {
                    e.preventDefault();
                }
            });
            // Activate sidebar slide toggle
            $("[data-bs-toggle='sub-slide2']").on("click", function (e) {
                var $this = $(this);
                var checkElement = $this.next();
                var animationSpeed = 300,
                    slideMenuSelector = ".sub-slide-menu2";
                if (
                    checkElement.is(slideMenuSelector) &&
                    checkElement.is(":visible")
                ) {
                    checkElement.slideUp(animationSpeed, function () {
                        checkElement.removeClass("open");
                    });
                    checkElement.parent("li").removeClass("is-expanded");
                } else if (
                    checkElement.is(slideMenuSelector) &&
                    !checkElement.is(":visible")
                ) {
                    var parent = $this.parents("ul").first();
                    var ul = parent
                        .find('ul[class^="sub-slide-menu"]:visible')
                        .slideUp(animationSpeed);
                    ul.removeClass("open");
                    var parent_li = $this.parent("li");
                    checkElement.slideDown(animationSpeed, function () {
                        checkElement.addClass("open");
                        parent
                            .find("li.is-expanded")
                            .removeClass("is-expanded");
                        parent_li.addClass("is-expanded");
                    });
                }
                if (checkElement.is(slideMenuSelector)) {
                    e.preventDefault();
                }
            });
        },

        resetData() {
            "use strict";
            localStorage.clear();
            document.querySelector("html").style = "";

            $("#myonoffswitch3").prop("checked", true);
            $("#myonoffswitch6").prop("checked", true);
            $("#myonoffswitch1").prop("checked", true);
            $("#myonoffswitch9").prop("checked", true);
            $("#myonoffswitch11").prop("checked", true);
            $("#myonoffswitch13").prop("checked", true);
            $("#myonoffswitch34").prop("checked", true);
            $("#myonoffswitch23").prop("checked", true);
            $("body")?.removeClass("dark-mode");
            $("body")?.removeClass("light-menu");
            $("body")?.removeClass("dark-menu");
            $("body")?.removeClass("color-menu");
            $("body")?.removeClass("header-light");
            $("body")?.removeClass("dark-header");
            $("body")?.removeClass("color-header");
            $("body")?.removeClass("layout-boxed");
            $("body")?.removeClass("icontext-menu");
            $("body")?.removeClass("icon-overlay");
            $("body")?.removeClass("closed-leftmenu");
            $("body")?.removeClass("hover-submenu");
            $("body")?.removeClass("double-menu");
            $("body")?.removeClass("double-menu-tabs");
            $("body")?.removeClass("hover-submenu1");
            $("body")?.removeClass("sidenav-toggled");
            $("body")?.removeClass("scrollable-layout");
            $("body")?.removeClass("rtl");
            $("body")?.addClass("ltr");
            // names();

            document.querySelector("html").setAttribute("dir", "ltr");

            // resetting horizontal to vertical
            $("body").removeClass("horizontal");
            $("body").removeClass("horizontal-hover");
            $(".main-content").removeClass("hor-content");
            $(".main-content").addClass("app-content");
            $(".main-container").removeClass("container");
            $(".main-container").addClass("container-fluid");
            $(".app-header").removeClass("hor-header");
            $(".hor-header").addClass("app-header");
            $(".app-sidebar").removeClass("horizontal-main");
            $(".app-sidebar").removeClass("fixed-header");
            $(".main-sidemenu").removeClass("container");
            $(".slide-menu").removeClass("ps");
            $(".slide-menu").removeClass("ps--active-y");
            $("#slide-left").removeClass("d-none");
            $("#slide-right").removeClass("d-none");
            $("body").addClass("sidebar-mini");

            if (
                !document.querySelector("body").classList.contains("login-img")
            ) {
                this.responsive();
                this.ActiveSubmenu();
            }

            localStorage.clear();
            if (!document.body.classList.contains("login-img")) {
                let mainHeader = document.querySelector(".app-header");
                mainHeader.style = "";
                let appSidebar = document.querySelector(".app-sidebar");
                appSidebar.style = "";

                //
                this.checkOptions();
                this.menuClick();
            }
        },

        checkOptions() {
            "use strict";
            // rtl
            if (document.querySelector("body").classList.contains("rtl")) {
                $("#myonoffswitch24").prop("checked", true);
            }
            // horizontal
            if (
                document.querySelector("body").classList.contains("horizontal")
            ) {
                $("#myonoffswitch35").prop("checked", true);
            }
            // horizontal-hover
            if (
                document
                    .querySelector("body")
                    .classList.contains("horizontal-hover")
            ) {
                $("#myonoffswitch111").prop("checked", true);
            }

            // light header
            if (
                document
                    .querySelector("body")
                    .classList.contains("header-light")
            ) {
                $("#myonoffswitch6").prop("checked", true);
            }
            // color header
            if (
                document
                    .querySelector("body")
                    .classList.contains("color-header")
            ) {
                $("#myonoffswitch7").prop("checked", true);
            }
            // dark header
            if (
                document.querySelector("body").classList.contains("dark-header")
            ) {
                $("#myonoffswitch8").prop("checked", true);
            }

            // light menu
            if (
                document.querySelector("body").classList.contains("light-menu")
            ) {
                $("#myonoffswitch3").prop("checked", true);
            }
            // color menu
            if (
                document.querySelector("body").classList.contains("color-menu")
            ) {
                $("#myonoffswitch4").prop("checked", true);
            }
            // dark menu
            if (
                document.querySelector("body").classList.contains("dark-menu")
            ) {
                $("#myonoffswitch5").prop("checked", true);
            }
            // dark-mode
            if (
                document.querySelector("body").classList.contains("dark-mode")
            ) {
                $("#myonoffswitch2").prop("checked", true);
            }
            // icontext-menu
            if (
                document
                    .querySelector("body")
                    .classList.contains("icontext-menu")
            ) {
                $("#myonoffswitch14").prop("checked", true);
            }
            // icon-overlay
            if (
                document
                    .querySelector("body")
                    .classList.contains("icon-overlay")
            ) {
                $("#myonoffswitch15").prop("checked", true);
            }
            // closed-leftmenu
            if (
                document
                    .querySelector("body")
                    .classList.contains("closed-leftmenu")
            ) {
                $("#myonoffswitch16").prop("checked", true);
            }
            // closed-leftmenu
            if (
                document
                    .querySelector("body")
                    .classList.contains("closed-leftmenu")
            ) {
                $("#myonoffswitch16").prop("checked", true);
            }
            // hover-submenu
            if (
                document
                    .querySelector("body")
                    .classList.contains("hover-submenu")
            ) {
                $("#myonoffswitch17").prop("checked", true);
            }
            // hover-submenu1
            if (
                document
                    .querySelector("body")
                    .classList.contains("hover-submenu1")
            ) {
                $("#myonoffswitch18").prop("checked", true);
            }
            // hover-submenu1
            if (
                document.querySelector("body").classList.contains("double-menu")
            ) {
                $("#myonoffswitch19").prop("checked", true);
            }
            // hover-submenu1
            if (
                document
                    .querySelector("body")
                    .classList.contains("double-menu-tabs")
            ) {
                $("#myonoffswitch20").prop("checked", true);
            }
            // layout-boxed
            if (
                document
                    .querySelector("body")
                    .classList.contains("layout-boxed")
            ) {
                $("#myonoffswitch10").prop("checked", true);
            }
            // scrollable-layout
            if (
                document
                    .querySelector("body")
                    .classList.contains("scrollable-layout")
            ) {
                $("#myonoffswitch12").prop("checked", true);
            }
            // center-logo
            if (
                document.querySelector("body").classList.contains("center-logo")
            ) {
                $("#center-logo").prop("checked", true);
            }
            // default-logo
            if (
                document
                    .querySelector("body")
                    .classList.contains("default-logo")
            ) {
                $("#default-logo").prop("checked", true);
            }
        },
    },

    mounted() {
        this.onloadSidebar();

        $(document).on("click", ".horizontal-content", function () {
            $(".app-sidebar li a").each(function () {
                $(this)
                    .next()
                    .slideUp(300, function () {
                        $(this).next().removeClass("open");
                    });
                $(this).parent("li").removeClass("is-expanded");
            });

            setTimeout(() => {
                if ($(".slide-item").hasClass("active")) {
                    $(".app-sidebar").animate(
                        {
                            scrollTop:
                                $("a.slide-item.active").offset().top - 600,
                        },
                        600
                    );
                }
                if ($(".sub-side-menu__item").hasClass("active")) {
                    $(".app-sidebar").animate(
                        {
                            scrollTop:
                                $("a.sub-side-menu__item.active").offset().top -
                                600,
                        },
                        600
                    );
                }
            }, 200);
        });

        document.querySelector(".main-content").addEventListener(
            "click",
            () => {
                if (
                    document
                        .querySelector("body")
                        .classList.contains("horizontal")
                ) {
                    let li = document.querySelectorAll(".side-menu li");
                    li.forEach((e, i) => {
                        e.classList.remove("is-expanded");
                    });
                    var animationSpeed = 300;
                    // first level
                    var parent = $("[data-bs-toggle='sub-slide']").parents(
                        "ul"
                    );
                    var ul = parent.find("ul:visible").slideUp(animationSpeed);
                    ul.removeClass("open");
                    var parent1 = $("[data-bs-toggle='sub-slide2']").parents(
                        "ul"
                    );
                    var ul1 = parent1
                        .find("ul:visible")
                        .slideUp(animationSpeed);
                    ul1.removeClass("open");
                }
            },
            true
        );

        $(document).on("click", "#resetAll", () => {
            this.resetData();
        });
    },

    watch: {
        $route(to, from) {},
    },
};
</script>
