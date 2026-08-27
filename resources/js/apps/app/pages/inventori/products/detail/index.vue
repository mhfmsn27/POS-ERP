<template>
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-header w-100">
                <ul class="nav nav-tabs nav-style-1 d-sm-flex d-block">
                    <li
                        class="nav-item"
                        :class="
                            $route.name == 'product_details' ? 'active' : ''
                        "
                    >
                        <router-link
                            :to="{
                                name: 'product_details',
                                params: { id: $route.params.id },
                            }"
                            class="nav-link"
                            :class="
                                $route.name == 'product_details' ? 'active' : ''
                            "
                            >Informasi Produk</router-link
                        >
                    </li>
                    <li class="nav-item">
                        <router-link
                            :class="
                                $route.name == 'stock_product' ? 'active' : ''
                            "
                            :to="{
                                name: 'stock_product',
                                params: { id: $route.params.id },
                            }"
                            class="nav-link"
                            >Informasi Stok</router-link
                        >
                    </li>
                    <li class="nav-item">
                        <router-link
                            :to="{
                                name: 'stock_histories_product',
                                params: { id: $route.params.id },
                            }"
                            class="nav-link"
                            :class="
                                $route.name == 'stock_histories_product'
                                    ? 'active'
                                    : ''
                            "
                            >Informasi Riwayat Arus Stok</router-link
                        >
                    </li>
                </ul>
            </div>
            <div class="card-body add-product p-0">
                <router-view />
            </div>
        </div>
    </div>
</template>
<script></script>
