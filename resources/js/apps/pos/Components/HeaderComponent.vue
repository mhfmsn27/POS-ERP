<template>
    <div class="row pos-top">
        <div class="table-responsive">
            <table class="table table-header-pos">
                <tr>
                    <th class="formsearch">
                        <div class="input-group" id="seacrhform">
                            <input
                                type="text"
                                class="form-control form-pencarian"
                                placeholder="Cari / Scan Produk (Tekan F2)"
                                id="searchProduct"
                                @input="searchProduct"
                                v-model="filter.product"
                                style="margin-top: 0px"
                            />
                            <span class="input-group-text bg-white border-start-0">
                                <span class="pos-kbd me-1">F2</span>
                                <i class="fas fa-barcode text-primary"></i>
                            </span>
                        </div>
                    </th>
                    <th class="d-flex justify-content-end align-items-center">
                        <button
                            type="button"
                            @click="modal.customer = true"
                            class="btn bg-white custom-border-secondary btn-sm d-flex align-items-center h-100 me-3"
                        >
                            <span> <i class="fa fa-user-circle"></i> </span
                            ><span class="custom-text-justify ms-2"
                                ><div class="fw-bold custom-text-secondary">
                                    {{
                                        customer.name == ""
                                            ? "Pilih Pelanggan"
                                            : customer.name
                                    }}
                                </div>
                                <div
                                    class="custom-text-secondary"
                                    style="margin-top: -3px"
                                >
                                    Pelanggan
                                </div></span
                            >
                        </button>
                        <button
                            type="button"
                            @click="modal.user = true"
                            class="btn bg-white custom-border-secondary btn-sm d-flex align-items-center h-100 me-3"
                        >
                            <span> <i class="fa fa-user"></i> </span
                            ><span class="custom-text-justify ms-2"
                                ><div class="fw-bold custom-text-secondary">
                                    {{
                                        user.name == ""
                                            ? "Pilih Penjual"
                                            : user.name
                                    }}
                                </div>
                                <div
                                    class="custom-text-secondary"
                                    style="margin-top: -3px"
                                >
                                    Kasir / Penjual
                                </div></span
                            >
                        </button>

                        <a
                            href="/app/home"
                            class="btn bg-white custom-border-secondary h-100"
                        >
                            <span> <i class="fa fa-power-off"></i> </span>
                        </a>
                    </th>
                </tr>
            </table>
        </div>
    </div>

    <!-- Modal For Customer -->
    <Dialog
        v-model:visible="modal.customer"
        class="filter-data"
        header="Pilih Data Pelanggan"
        :style="{ width: '35rem' }"
        modal
    >
        <div class="row p-2">
            <div class="col-12">
                <div class="position-relative">
                    <InputText
                        v-model="filter.customer"
                        @input="searchCustomer"
                        :placeholder="'Cari Pelanggan....'"
                        class="input-background"
                        style="width: 100%"
                    />
                    <div
                        class="position-absolute top-50 translate-middle-y"
                        style="right: 16px"
                    >
                        <i class="pi pi-search" style="color: #737373" />
                    </div>
                </div>
            </div>
            <div class="col-12">
                <ul class="dropdown-menu-customer">
                    <li v-for="(cus, c) in customers" :key="c">
                        <div
                            class="dropdown-item p-2 pointer"
                            @click="chooseCustomer(cus)"
                        >
                            <div class="dropdown-item-customer p-2">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <label for="radio-1" class="fw-bold">{{
                                            cus.name
                                        }}</label>
                                    </div>
                                </div>
                                <hr />
                                <div class="d-flex align-items-center">
                                    <div>
                                        <span
                                            class="ms-2 dropdown-customer-text"
                                        >
                                            {{ cus.address }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </Dialog>
    <!-- End Modal For Customer -->

    <!-- Modal For User -->
    <Dialog
        v-model:visible="modal.user"
        class="filter-data"
        header="Pilih Data Penjual"
        :style="{ width: '35rem' }"
        modal
    >
        <div class="row p-2">
            <div class="col-12">
                <div class="position-relative">
                    <InputText
                        v-model="filter.user"
                        @input="searchUser"
                        :placeholder="'Cari Pengguna....'"
                        class="input-background"
                        style="width: 100%"
                    />
                    <div
                        class="position-absolute top-50 translate-middle-y"
                        style="right: 16px"
                    >
                        <i class="pi pi-search" style="color: #737373" />
                    </div>
                </div>
            </div>
            <div class="col-12">
                <ul class="dropdown-menu-customer">
                    <li v-for="(pengguna, p) in users" :key="p">
                        <div
                            class="dropdown-item p-2 pointer"
                            @click="chooseUser(pengguna)"
                        >
                            <div class="dropdown-item-customer p-2">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <label for="radio-1" class="fw-bold">{{
                                            pengguna.name
                                        }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </Dialog>
    <!-- End Modal for User -->
</template>

<script>
var _ = require("lodash");
import ScrollPanel from "primevue/scrollpanel";
import { ApiData } from "@/api/server";
export default {
    name: "HeaderPOS",
    emits: ["search", "changecustomer", "changeuser"],
    data: function () {
        return {
            modal: {
                customer: false,
                user: false,
            },
            user: {
                id: "",
                name: "",
            },
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
            loader: {
                customer: false,
                user: false,
            },
            filter: {
                customer: "",
                user: "",
                product: "",
            },
            customers: [],
            users: [],
        };
    },
    components: {
        ScrollPanel,
    },
    computed: {},
    methods: {
        searchProduct() {
            this.doSearch(this);
        },

        doSearch: _.debounce((rootInstance) => {
            rootInstance.$emit("search", rootInstance.filter.product);
        }, 300),

        searchUser() {
            this.doSearchUser(this);
        },

        doSearchUser: _.debounce((rootInstance) => {
            rootInstance.getUsers();
        }, 300),

        async getUsers() {
            this.loader.user = true;
            try {
                const response = await ApiData.get(
                    `app/master/components/users?name=${this.filter.user}&type=yes`
                );
                var data = response.data;
                this.users = data.users;
                this.loader.user = false;
            } catch (error) {
                console.log(error);
            }
        },

        chooseUser(cus) {
            this.user = {
                id: cus.id,
                name: cus.name,
            };
            this.modal.user = false;
            this.$emit("changeuser", this.user);
        },

        searchCustomer() {
            this.doSearchCustomer(this);
        },

        doSearchCustomer: _.debounce((rootInstance) => {
            rootInstance.getCustomers();
        }, 300),

        // Get Informasi customer
        async getCustomers(type = '') {
            this.loader.customer = true;
            try {
                const response = await ApiData.get(
                    `app/crm/components/customers?name=${this.filter.customer}`
                );
                var data = response.data;
                this.customers = data.customers; 

                if(type == 'first' && data.customers.length > 0) {
                    this.chooseCustomer(data.customers[0]);
                }
                this.loader.customer = false;
            } catch (error) {
                console.log(error);
            }
        },

        chooseCustomer(cus) {
            this.customer = {
                id: cus.id,
                name: cus.name,
                type: cus.type,
                npwp: cus.npwp,
                address: cus.address,
                tax: {
                    default: cus.default,
                    tax_option: cus.tax_option,
                    due_date: cus.due_date,
                },
            };
            this.modal.customer = false;
            this.$emit("changecustomer", this.customer);
        },

        async getSign() {
            try {
                const response = await ApiData.get(
                    `app/master/components/sign`
                );
                var data = response.data;
                this.user = data;
                this.$emit("changeuser", this.user);
            } catch (error) {
                console.log(error);
            }
        },
    },
    created: async function () {},
    mounted: function () {
        this.getCustomers('first');
        this.getUsers();
        this.getSign();
    },
};
</script>
