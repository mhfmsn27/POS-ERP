<template>
    <!-- Commission Reports -->
    <a
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'commission_reports',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Komisi Penjualan</h4>
                        <h6 class="">Laporan Komisi penjualan Sales</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Commission Reports -->

    <!-- Customer Due -->
    <a
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'piutang_customer',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Piutang Pelanggan</h4>
                        <h6 class="">Laporan Piutang Pelanggan</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Customer Due -->

    <!-- Customer Saldo -->
    <a
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'saldo_customer',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Saldo Pelanggan</h4>
                        <h6 class="">Laporan Saldo Pelanggan</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Customer Saldo -->

    <!-- Supplier Due -->
    <a
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'hutang_supplier',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Hutang Supplier</h4>
                        <h6 class="">Laporan Hutang Ke Supplier</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Supplier Due -->

    <!-- Supplier Saldo -->
    <a
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'saldo_supplier',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Saldo Supplier</h4>
                        <h6 class="">Laporan Penyimpanan Saldo Di Supplier</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Supplier Saldo -->

    <!-- Neraca -->
    <a
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'neraca',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Neraca Standart</h4>
                        <h6 class="">Laporan Neraca Standart</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Neraca -->

    <!-- Laporan Produk -->
    <a
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'report_products',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Laporan Produk</h4>
                        <h6 class="">Laporan Produk</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Product Reports -->

    <!-- Product Minus -->
    <a
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'product_minus',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Laporan Produk Minus</h4>
                        <h6 class="">Laporan Produk dengan Qty Minus</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Product Minus -->

    <!-- Laba Rugi -->
    <a
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'profit',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Laporan Laba Rugi</h4>
                        <h6 class="">Laporan Laba Rugi Transaksi</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- end Laba Rugi -->

    <!-- Laba Rugi Reports -->
    <a
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'profit_priode',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Laporan Laba Rugi Per Priode</h4>
                        <h6 class="">Laporan Laba Rugi Transaksi Per Priode</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Laba Rugi Reports -->

    <!-- Pajak Penjualan -->
    <a
        v-if="with_accountant"
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'taxrate_sales',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Pajak Penjualan</h4>
                        <h6 class="">Laporan Pajak Penjualan</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Pajak Penjualan -->

    <!-- Pajak Pembelian -->
    <a
        v-if="with_accountant"
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'taxrate_purchase',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Pajak Pembelian</h4>
                        <h6 class="">Laporan Pajak Pembelian</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Pajak Pembelian -->

    <!-- Pajak Retur Penjualan -->
    <a
        v-if="with_accountant"
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'taxrate_r_sales',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Pajak Retur Penjualan</h4>
                        <h6 class="">Laporan Pajak Retur Penjualan</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Pajak Retur Penjualan -->

    <!-- Pajak Retur Pembelian -->
    <a
        v-if="with_accountant"
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'taxrate_r_purchase',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Pajak Retur Pembelian</h4>
                        <h6 class="">Laporan Pajak Retur Pembelian</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Pajak Retur Pembelian -->

    <!-- SPt -->
    <a
        v-if="with_accountant"
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'spt',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">SPT</h4>
                        <h6 class="">Laporan dan Pembuatan SPT Pajak</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End SPT -->

    <!-- Activity -->
    <a
        href="javascript:void(0)"
        @click="
            $goTo({
                name: 'activities',
            })
        "
        class="col-lg-4 col-sm-6"
    >
        <div class="card card-block card-stretch card-height">
            <div class="card-body relative-background">
                <div class="d-flex align-items-center">
                    <div class="text-left">
                        <h4 class="">Log Aktivitas</h4>
                        <h6 class="">Laporan Log Aktivitas Pengguna</h6>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <!-- End Activity -->
</template>

<script>
import { ApiData } from "@/api/server";
export default {
    data() {
        return {
            with_accountant: true,
        };
    },
    computed: {},
    created() {
        this.settup();
    },
    methods: {
        async settup() {
            try {
                const response = await ApiData.get(`app/master/tax/sett`);
                var data = response.data;

                if (data.accountant_use == "no") {
                    this.with_accountant = false;
                }
            } catch (error) {
                console.log(error);
            }
        },
    },
    computed: {
        currentRouteName() {
            return this.$route.name;
        },
    },
};
</script>
