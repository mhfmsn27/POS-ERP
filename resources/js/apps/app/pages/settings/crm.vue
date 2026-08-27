<template>
    <!-- Create Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Pengaturan Akun Default CRM</h4>
            </div>
            <Form @submit="validationSettings()" ref="settingValidation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 border-bottom">
                            <h4>Akun Default Pelanggan</h4>
                        </div>

                        <div class="col-lg-6 col-sm-12 mt-4">
                            <label for="category-product"
                                >Akun Utang Pelanggan</label
                            >
                            <Multiselect
                                v-model="form.customer_debt"
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

                        <div class="col-lg-6 col-sm-12 mt-4">
                            <label for="category-product"
                                >Akun Utang DP ( Optional)
                            </label>

                            <Multiselect
                                v-model="form.customer_debt_imprest"
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

                        <div class="col-12 mt-4 border-bottom">
                            <h4>Akun Default Supplier</h4>
                        </div>

                        <div class="col-lg-6 col-sm-12 mt-4">
                            <label for="category-product"
                                >Akun Utang Supplier</label
                            >
                            <Multiselect
                                v-model="form.supplier_debt"
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

                        <div class="col-lg-6 col-sm-12 mt-4">
                            <label for="category-product"
                                >Akun Utang DP ( Optional)
                            </label>

                            <Multiselect
                                v-model="form.supplier_debt_imprest"
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

                        <div class="col-12 mt-4 border-bottom">
                            <h4>Akun Lainnya</h4>
                        </div>

                        <div class="col-lg-6 col-sm-12 mt-4">
                            <label for="category-product"
                                >Akun Induk Beban Operasional
                            </label>

                            <Multiselect
                                v-model="form.beban_operasional_account"
                                :options="parents"
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
                                @search-change="getAccountParent"
                            ></Multiselect>
                        </div>

                        <div class="col-lg-6 col-sm-12 mt-4">
                            <label for="category-product"
                                >Akun Induk Beban Lainnya
                            </label>

                            <Multiselect
                                v-model="form.beban_lainnya_account"
                                :options="parents"
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
                                @search-change="getAccountParent"
                            ></Multiselect>
                        </div>

                        <div class="col-lg-6 col-sm-12 mt-4">
                            <label for="category-product"
                                >Akun Induk Pendapatan Lainnya
                            </label>

                            <Multiselect
                                v-model="form.pendapatan_lainnya_account"
                                :options="parents"
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
                                @search-change="getAccountParent"
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
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "type_list",
    components: {},
    data() {
        return {
            editmode: false,
            accounts: [],
            parents: [],
            form: {
                customer_debt: {
                    id: "",
                    name: "",
                },
                customer_debt_imprest: {
                    id: "",
                    name: "",
                },
                supplier_debt: {
                    id: "",
                    name: "",
                },
                supplier_debt_imprest: {
                    id: "",
                    name: "",
                },
                beban_operasional_account: {
                    id: "",
                    name: "",
                },
                beban_lainnya_account: {
                    id: "",
                    name: "",
                },
                pendapatan_lainnya_account: {
                    id: "",
                    name: "",
                },
            },
            loader: {
                data: false,
                submit: false,
                parent: false,
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
    computed: {},
    methods: {
        async getAccountParent(query) {
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&only_parent=no`
                );
                var data = response.data;
                this.parents = data.accounts;
            } catch (error) {
                console.log(error);
            }
        },

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
            ApiData.post("app/settings/account/crm", this.form)
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
        this.getAccountParent("");
    },
    watch: {},
};
</script>
