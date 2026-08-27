<template>
    <!-- Create Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Pengaturan Akun Default Transaksi</h4>
            </div>
            <Form @submit="validationSettings()" ref="settingValidation">
                <div class="card-body">
                    <div class="row">
                        <!-- Beban -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Beban Pengiriman</label
                            >
                            <Multiselect
                                v-model="form.cost_shipping_transaction"
                                :options="accounts"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :options-limit="50"
                                placeholder="Pilih Akun"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getAccounts"
                            ></Multiselect>
                        </div>
                        <!-- End Beban -->

                        <!-- Gaji Pegawai -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product">Gaji Pegawai</label>
                            <Multiselect
                                v-model="form.salaries"
                                :options="accounts"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :options-limit="50"
                                placeholder="Pilih Akun"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getAccounts"
                            ></Multiselect>
                        </div>
                        <!-- End Gaji Pegawai -->

                        <!-- Kasbon -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product">Kasbon Pegawai</label>
                            <Multiselect
                                v-model="form.kasbon"
                                :options="accounts"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :options-limit="50"
                                placeholder="Pilih Akun"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getAccounts"
                            ></Multiselect>
                        </div>
                        <!-- End Kasbon -->

                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Diskon Penjualan</label
                            >
                            <Multiselect
                                v-model="form.discount_account"
                                :options="accounts"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :options-limit="50"
                                placeholder="Pilih Akun"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getAccounts"
                            ></Multiselect>
                        </div>

                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Beban Komisi Pegawai</label
                            >
                            <Multiselect
                                v-model="form.commission_account"
                                :options="accounts"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :options-limit="50"
                                placeholder="Pilih Akun"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getAccounts"
                            ></Multiselect>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button
                        type="submit"
                        :disabled="loader.submit"
                        class="btn label-btn label-end btn-primary"
                    >
                        {{
                            loader.submit
                                ? "Mohon Tunggu...."
                                : "Simpan Perubahan"
                        }}
                        <i class="fe fe-save label-btn-icon ms-2"></i>
                    </button>
                </div>
            </Form>
        </div>
    </div>
    <!-- End Create Data -->
</template>

<script>
import { ApiData } from "@/api/server";
import NProgress from "nprogress";

var _ = require("lodash");

export default {
    name: "type_list",
    components: {},
    data() {
        return {
            editmode: false,
            accounts: [],
            form: {
                cost_shipping_transaction: {
                    id: "",
                    name: "",
                },
                salaries: {
                    id: "",
                    name: "",
                },
                kasbon: {
                    id: "",
                    name: "",
                },
                discount_account: {
                    id: "",
                    name: "",
                },
                commission_account: {
                    id: "",
                    name: "",
                },
            },
            loader: {
                data: false,
                submit: false,
                type: false,
                debt: false,
                term: false,
                deposit: false,
                debt_imprest: false,
            },
            filter: {
                name: "",
            },
        };
    },
    methods: {
        async getData() {
            try {
                const response = await ApiData.get(`app/settings/account/data`);
                var data = response.data;
                this.form = data.detail;
            } catch (error) {
                console.log(error);
            }
        },

        async getAccounts(query) {
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&only_parent=yes`
                );
                var data = response.data;
                this.accounts = data.accounts;
            } catch (error) {
                console.log(error);
            }
        },

        validationSettings() {
            this.$refs.settingValidation.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Terjadi kesalahan",
                        detail: "Silahkan Check kembali form inputan anda",
                        life: 3000,
                    });
                } else {
                    this.loader.submit = true;
                    NProgress.start();
                    NProgress.set(0.1);
                    this.updateData();
                }
            });
        },

        updateData() {
            ApiData.post("app/settings/account/transaction", this.form)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },
    },
    mounted: function () {
        this.getData();
        this.getAccounts("");
    },
    watch: {},
};
</script>
