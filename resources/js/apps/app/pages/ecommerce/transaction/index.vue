<template>
    <div class="page-header">
        <h1 class="page-title">E-Commerce</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <router-link :to="{ name: $route.meta.parent_menu }"
                    >Transaksi</router-link
                >
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $route.meta.title }}
            </li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-12 col-sm-12">
            <div class="card p-3">
                <div class="list-group list-group-transparent mb-0 mail-inbox">
                    <div class="mb-4 text-center border-bottom">
                        <h4>Menu Transaksi</h4>
                    </div>
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'e_commerce_order_process',
                                query: { tab: true, status: 'hold' },
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'e_commerce_order_pending'
                                ? 'active'
                                : ''
                        "
                    >
                        <span class="icon me-3"
                            ><i class="fa fa-money"></i></span
                        >Pending Transaksi
                    </a>
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'e_commerce_order_process',
                                query: { tab: true, status: 'transit' },
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'e_commerce_order_process'
                                ? 'active'
                                : ''
                        "
                    >
                        <span class="icon me-3"
                            ><i class="fe fe-check-circle"></i></span
                        >Dalam Pengemasan
                    </a>
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'e_commerce_order_process',
                                query: { tab: true, status: 'transit' },
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'e_commerce_order_shipping'
                                ? 'active'
                                : ''
                        "
                    >
                        <span class="icon me-3"
                            ><i class="fa fa-truck"></i></span
                        >Dalam Pengiriman
                    </a>

                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'e_commerce_order_process',
                                query: { tab: true, status: 'final' },
                            })
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'e_commerce_order_received'
                                ? 'active'
                                : ''
                        "
                    >
                        <span class="icon me-3"><i class="fe fe-box"></i></span
                        >Pesanan di Terima
                    </a>
                </div>
            </div>
        </div>
        <router-view></router-view>
    </div>
</template>
<script>
export default {};
</script>
<style scoped>
.tabheader {
    background-color: #fff;
    margin-top: 0;
    margin: 0 !important;
    padding: 0 !important;
}

.item-nav {
    margin: 0 !important;
    padding: 0 !important;
}

.item-tab {
    width: 100% !important;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    color: black;
}

.item_tab {
    text-align: center !important;
}
</style>
