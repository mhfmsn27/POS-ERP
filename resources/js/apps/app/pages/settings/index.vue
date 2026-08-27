<template>
    <div class="row mt-6">
        <div class="col-lg-3 col-md-12 col-sm-12">
            <div class="card p-3">
                <div class="list-group list-group-transparent mb-0 mail-inbox">
                    <div class="mb-4 text-center border-bottom">
                        <h4>Menu Preferensi</h4>
                    </div>
                    <router-link
                        :to="{ name: 'setting_account_crm' }"
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'setting_account_crm' ? 'active' : ''
                        "
                    >
                        <span class="icon me-3"><i class="fe fe-user"></i></span
                        >Akun CRM
                    </router-link>
                    <router-link
                        :to="{ name: 'setting_account_product' }" 
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'setting_account_product'
                                ? 'active'
                                : ''
                        "
                    >
                        <span class="icon me-3"><i class="fe fe-box"></i></span
                        >Akun Produk
                    </router-link>
                    <router-link
                        :to="{ name: 'setting_account_transaction' }" 
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'setting_account_transaction'
                                ? 'active'
                                : ''
                        "
                    >
                        <span class="icon me-3"><i class="fe fe-list"></i></span
                        >Akun Transaksi
                    </router-link>
                    <router-link
                        v-if="with_tax"
                        :to="{ name: 'setting_account_taxrate' }"
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="
                            $route.name == 'setting_account_taxrate'
                                ? 'active'
                                : ''
                        "
                    >
                        <span class="icon me-3"
                            ><i class="fe fe-percent"></i></span
                        >Akun Pajak
                    </router-link>
                    <router-link
                        :to="{ name: 'setting_key' }"
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        :class="$route.name == 'setting_key' ? 'active' : ''"
                    >
                        <span class="icon me-3"><i class="fa fa-key"></i></span
                        >Key Transaksi
                    </router-link>
                    <router-link
                        :to="{ name: 'setting_hrm' }"
                        :class="$route.name == 'setting_hrm' ? 'active' : ''"
                        class="list-group-item list-group-item-action d-flex align-items-center"
                    >
                        <span class="icon me-3"
                            ><i class="fa fa-address-card"></i></span
                        >Hrm
                    </router-link>
                    <router-link
                        :to="{ name: 'setting_notification' }"
                        :class="
                            $route.name == 'setting_notification'
                                ? 'active'
                                : ''
                        "
                        class="list-group-item list-group-item-action d-flex align-items-center"
                    >
                        <span class="icon me-3"><i class="fe fe-bell"></i></span
                        >Pemberitahuan
                    </router-link>
                    <router-link
                        :to="{ name: 'setting_store' }"
                        :class="$route.name == 'setting_store' ? 'active' : ''"
                        class="list-group-item list-group-item-action d-flex align-items-center"
                    >
                        <span class="icon me-3"
                            ><i class="fa fa-building"></i></span
                        >Toko / Cabang
                    </router-link>
                </div>
            </div>
        </div>
        <router-view></router-view>
    </div>
</template>
<script>
import { ApiData } from "@/api/server";
export default {
    computed: {
        currentRouteName() {
            return this.$route.name;
        },
    },
    data() {
        return {
            with_tax: false,
        };
    },
    created() {
        this.settup();
    },
    methods: {
        async settup() {
            try {
                const response = await ApiData.get(`app/master/tax/sett`);
                var data = response.data;
                this.with_tax = data.with_tax;
            } catch (error) {
                console.log(error);
            }
        },
    },
};
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
