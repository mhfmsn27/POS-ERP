<template>
    <HeaderComponent
        @search="handleSearch"
        @changecustomer="chooseCustomer"
        @changeuser="chooseUser"
    />
    <div id="main-content">
        <router-view
            :search_product="data.product"
            :choose_customer="customer"
            :choose_user="user"
        ></router-view>
    </div>
</template>

<script>
import HeaderComponent from "../Components/HeaderComponent.vue";

var _ = require("lodash");

export default {
    name: "type_list",
    components: {
        HeaderComponent,
    },
    data() {
        return {
            customer: {
                id: "",
                name: "",
                type: "",
                npwp: "",
                address: "",
                tax: {
                    default: "",
                    tax_option: "",
                    type: "",
                    due_date: "",
                },
            },
            user: {
                id: "",
                name: "",
            },
            data: {
                product: "",
            },
        };
    },
    computed: {},
    created() {},
    methods: {
        handleSearch(search) {
            this.data.product = search;
        },

        chooseUser(pengguna) {
            this.user = pengguna;
        },

        chooseCustomer(cus) {
            this.customer = {
                id: cus.id,
                name: cus.name,
                type: cus.type,
                npwp: cus.npwp,
                address: cus.address,
                tax: cus.tax,
            };
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
