<template>
    <!-- Create Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Pengaturan Akun Default Pajak</h4>
            </div>
            <Form @submit="validationSettings()" ref="settingValidation">
                <div class="card-body">
                    <div class="row">
                        <!-- Beban -->
                        <div class="col-lg-6 col-sm-12 mt-3">
                            <label for="category-product">Pajak Masukan</label>
                            <Multiselect
                                v-model="form.tax_input_account"
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
                        <div class="col-lg-6 col-sm-12 mt-3">
                            <label for="category-product">Pajak Keluaran</label>
                            <Multiselect
                                v-model="form.tax_output_account"
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

                        <!-- tax_over_account -->
                        <div class="col-lg-6 col-sm-12 mt-3">
                            <label for="category-product"
                                >Akun Lebih Bayar</label
                            >
                            <Multiselect
                                v-model="form.tax_over_account"
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

                        <div class="col-lg-6 col-sm-12 mt-3">
                            <label for="category-product"
                                >Akun Kurang Bayar</label
                            >
                            <Multiselect
                                v-model="form.tax_minus_account"
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

                        <!-- PPH 22 -->
                        <div class="col-lg-6 col-sm-12 mt-3">
                            <label for="category-product">PPH 22</label>
                            <Multiselect
                                v-model="form.tax_pph_account"
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

                        <!-- PPH 23 -->
                        <div class="col-lg-6 col-sm-12 mt-3">
                            <label for="category-product">PPH 23</label>
                            <Multiselect
                                v-model="form.tax_service_account"
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
            form: {
                tax_input_account: {
                    id: "",
                    name: "",
                },
                tax_output_account: {
                    id: "",
                    name: "",
                },
                tax_over_account: {
                    id: "",
                    name: "",
                },
                tax_minus_account: {
                    id: "",
                    name: "",
                },
                tax_pph_account: {
                    id: "",
                    name: "",
                },
                tax_service_account: {
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
    computed: {}, 
    methods: {
        async getData() {
            try {
                const response = await ApiData.get(
                    `app/settings/account/data`
                );
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
            ApiData
                .post("app/settings/account/taxrate", this.form)
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
