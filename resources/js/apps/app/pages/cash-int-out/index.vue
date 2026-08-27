<template>
    <div class="row">
        <div class="col-lg-12 tabheader mb-4">
            <ul class="iq-edit-profile d-flex nav nav-pills">
                <!-- Type Account -->
                <li class="col item-nav">
                    <a
                        class="nav-link item-tab"
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'expense',
                            })
                        "
                        :class="
                            $route.name == 'expense' ||
                            $route.name == 'create_expense' ||
                            $route.name == 'update_expense'
                                ? 'active'
                                : ''
                        "
                    >
                        <i class="ri-shirt-line"></i> Pembayaran
                    </a>
                </li>
                <!-- End Type Account -->

                <!-- Account -->
                <li class="col item-nav">
                    <a
                        class="nav-link item-tab"
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'cash_int',
                            })
                        "
                        :class="
                            $route.name == 'cash_int' ||
                            $route.name == 'create_cash_int' ||
                            $route.name == 'update_cash_int'
                                ? 'active'
                                : ''
                        "
                    >
                        <i class="ri-shirt-line"></i> Penerimaan
                    </a>
                </li>
                <!-- End Account -->

                <li class="col item-nav">
                    <a
                        class="nav-link item-tab"
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'expense_category',
                            })
                        "
                        :class="
                            $route.name == 'expense_category' ? 'active' : ''
                        "
                    >
                        <i class="ri-shirt-line"></i> Kategori
                    </a>
                </li>
            </ul>
        </div>
        <router-view></router-view>
    </div>
</template>
<script>
export default {
    computed: {
        currentRouteName() {
            return this.$route.name;
        },
    },
};
</script>
<style scoped>
.tabheader {
    background-color: #fff;
    margin-top: 0;
    margin: 0 !important;
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
