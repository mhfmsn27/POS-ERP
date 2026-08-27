<template>
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header">
                <h4 class="card-title">Tambah Data Pemasok</h4>
            </div>
            <Form @submit="ValidationSupplier()" ref="supplierValidation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Nama Pemasok</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                v-model="supplier.name"
                                name="Nama Pemasok"
                                ref="suppliername"
                            >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="supplier.name"
                                    placeholder="Masukkan Nama "
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Email Pemasok</label
                            >
                            <input
                                type="email"
                                class="form-control"
                                v-model="supplier.email"
                                placeholder="Email Pemasok "
                            />
                        </div>

                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Nomor Ponsel Pemasok</label
                            >
                            <InputMask
                                v-model="supplier.phone"
                                style="width: 100%"
                                mask="+62 999-9999?-99999999"
                                placeholder="(999) 999-9999? x99999"
                            />
                        </div>

                        <div
                            class="col-lg-6 col-sm-12 mb-4"
                            v-if="with_accountant"
                        >
                            <label for="Unit-name-add" class="form-label"
                                >Integrasi Akuntansi</label
                            >
                            <Dropdown
                                v-model="supplier.is_account"
                                :options="[
                                    {
                                        label: 'Tidak',
                                        value: false,
                                    },
                                    {
                                        label: 'Iya',
                                        value: true,
                                    },
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Pilih Opsi"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>

                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Kebijakan Pembayaran
                            </label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="supplier.term"
                                name="Kebijakan"
                            >
                                <Multiselect
                                    v-model="supplier.term"
                                    :options="terms"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :preserve-search="true"
                                    :searchable="true"
                                    :loading="loader.term"
                                    :internal-search="true"
                                    :options-limit="50"
                                    placeholder="Pilih Kebijakan"
                                    open-direction="bottom"
                                    label="name"
                                    id="id"
                                    track-by="name"
                                    @search-change="getTerm"
                                ></Multiselect>
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div
                            class="col-lg-6 col-sm-12 mb-4"
                            v-if="supplier.is_account"
                        >
                            <label for="category-product">Akun Utang</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="supplier.debt"
                                name="Akun Utang"
                            >
                                <Multiselect
                                    v-model="supplier.debt"
                                    :options="accounts"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :preserve-search="true"
                                    :searchable="true"
                                    :loading="loader.account"
                                    :internal-search="true"
                                    :options-limit="50"
                                    placeholder="Pilih Akun"
                                    open-direction="bottom"
                                    label="name"
                                    id="id"
                                    track-by="name"
                                    @search-change="getAccounts"
                                ></Multiselect>
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div
                            class="col-lg-6 col-sm-12 mb-4"
                            v-if="supplier.is_account"
                        >
                            <label for="category-product"
                                >Akun Utang DP ( Optional)
                            </label>

                            <Multiselect
                                v-model="supplier.debt_imprest"
                                :options="accounts"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :loading="loader.account"
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

                        <div class="col-lg-6 col-sm-12 mb-4" v-if="with_tax">
                            <label for="Unit-name-add" class="form-label"
                                >Opsi Penggunaan Pajak</label
                            >
                            <Dropdown
                                v-model="supplier.tax_option"
                                :options="[
                                    {
                                        label: 'Tidak',
                                        value: 'no',
                                    },
                                    {
                                        label: 'Termasuk',
                                        value: 'yes',
                                    },
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Pilih Opsi"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>

                        <div class="col-lg-6 col-sm-12 mb-4" v-if="with_tax">
                            <label for="Unit-name-add" class="form-label"
                                >NPWP</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                v-model="supplier.npwp"
                                placeholder="Masukkan NPWP Pemasok "
                            />
                        </div>

                        <div class="col-lg-6 col-sm-12 mb-4" v-if="with_tax">
                            <label for="Unit-name-add" class="form-label"
                                >Default Harga Pembelian</label
                            >
                            <Dropdown
                                v-model="supplier.tax_default"
                                :options="[
                                    {
                                        label: 'Sebelum PPN',
                                        value: 'no',
                                    },
                                    {
                                        label: 'Setelah PPN',
                                        value: 'yes',
                                    },
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Pilih Opsi"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>

                        <div class="col-12 mb-4">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Alamat Supplier</label
                            >
                            <textarea
                                class="form-control"
                                v-model="supplier.address"
                            ></textarea>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Catatan
                            </label>
                            <textarea
                                class="form-control"
                                v-model="supplier.detail"
                            ></textarea>
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
                                : "Tambahkan Data"
                        }}
                        <i class="ti ti-plus label-btn-icon ms-2"></i>
                    </button>
                </div>
            </Form>
        </div>
    </div>
</template>

<script>
import InputMask from "primevue/inputmask";
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "supplier_create",
    components: {
        InputMask,
    },
    data() {
        return {
            with_tax: false,
            with_accountant: true,
            accounts: [],
            accounts_deposit: [],
            terms: [],
            taxrates: [],
            account_default: {
                supplier_debt: {
                    id: "",
                    name: "",
                },
                supplier_debt_imprest: {
                    id: "",
                    name: "",
                },
            },
            supplier: {
                name: "",
                email: "",
                phone: "",
                address: "",
                detail: "",
                tax_default: 0,
                npwp: "",
                tax_option: "no",
                is_account: true,
                term: {
                    id: "",
                    name: "",
                },
                debt: {
                    id: "",
                    name: "",
                },
                debt_imprest: {
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
        };
    },
    computed: {},
    created() {
        this.settup();
        this.getTerm("");
    },
    methods: {
        async settup() {
            try {
                const response = await ApiData.get(`app/master/tax/sett`);
                var data = response.data;
                this.with_tax = data.with_tax;

                if (data.accountant_use == "no") {
                    this.with_accountant = false;
                    this.supplier.is_account = !true;
                } else {
                    this.getAccounts("");
                    this.getSetting();
                }
            } catch (error) {
                console.log(error);
            }
        },

        async getSetting() {
            try {
                const response = await ApiData.get(
                    `app/account/components/setting`
                );
                var data = response.data;
                this.account_default.supplier_debt = data.supplier_debt;
                this.account_default.supplier_debt_imprest =
                    data.supplier_debt_imprest;

                this.supplier.debt = this.account_default.supplier_debt;
                this.supplier.debt_imprest =
                    this.account_default.supplier_debt_imprest;
            } catch (error) {
                console.log(error);
            }
        },

        async getAccounts(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&only_parent=yes`
                );
                var data = response.data;
                this.accounts = data.accounts;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getTerm(query) {
            this.loader.term = true;
            try {
                const response = await ApiData.get(
                    `app/master/term?name=${query}`
                );
                var data = response.data;
                this.terms = data.terms;
                this.loader.term = false;

                this.setDefaultTerm();
            } catch (error) {
                console.log(error);
            }
        },

        setDefaultTerm() {
            const filteredDefault = this.terms.filter(
                (item) => item.default == true
            );

            if (filteredDefault.length > 0) {
                this.supplier.term = filteredDefault[0];
            }
        },

        ValidationSupplier() {
            this.$refs.supplierValidation.validate().then((success) => {
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
                    this.createData();
                }
            });
        },

        createData() {
            var supplierData = this.supplier;
            supplierData.phone = this.supplier.phone
                .replace("+", "")
                .replace(/\s|-/g, "");
            ApiData.post("app/crm/suppliers/create", supplierData)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    window.parent.postMessage({
                        action: "closeActiveMenu",
                        data: "",
                    });
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },
    },
};
</script>
