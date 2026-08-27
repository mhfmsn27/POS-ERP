<template>
    <!-- Create Data -->
    <div class="col-12 mb-4">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Tambah Pelanggan</h4>
            </div>
            <Form @submit="ValidationCustomers()" ref="customerValidation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Nama Customer</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                v-model="customer.name"
                                name="Nama Customer"
                                ref="customername"
                            >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="customer.name"
                                    placeholder="Masukkan Nama "
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Email Customer</label
                            >
                            <input
                                type="email"
                                class="form-control"
                                v-model="customer.email"
                                placeholder="Email Customer "
                            />
                        </div>

                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Nomor Ponsel Customer
                            </label>
                            <InputMask
                                v-model="customer.phone"
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
                                v-model="customer.is_account"
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
                                v-model="customer.term"
                                name="Kebijakan"
                            >
                                <Multiselect
                                    v-model="customer.term"
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
                            v-if="customer.is_account && with_accountant"
                        >
                            <label for="category-product">Akun Utang</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="customer.debt"
                                name="Akun Utang"
                            >
                                <Multiselect
                                    v-model="customer.debt"
                                    :options="accounts"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :preserve-search="true"
                                    :searchable="true"
                                    :loading="loader.debt"
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
                            v-if="customer.is_account && with_accountant"
                        >
                            <label for="category-product"
                                >Akun Utang DP ( Optional)
                            </label>

                            <Multiselect
                                v-model="customer.debt_imprest"
                                :options="accounts"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :loading="loader.deposit"
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
                                >Tipe Pelanggan</label
                            >
                            <Dropdown
                                v-model="customer.type"
                                :options="[
                                    {
                                        label: 'Pemungut Pajak',
                                        value: 'bumn',
                                    },
                                    {
                                        label: 'Bukan Pemungut Pajak',
                                        value: 'general',
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
                                >Opsi Penggunaan Pajak</label
                            >
                            <Dropdown
                                v-model="customer.tax_option"
                                :options="[
                                    {
                                        label: 'Gunakan Pajak',
                                        value: 'yes',
                                    },
                                    {
                                        label: 'Tidak gunakan pajak',
                                        value: 'no',
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
                                v-model="customer.npwp"
                                placeholder="Masukkan NPWP Pelanggan "
                            />
                        </div>

                        <div class="col-lg-6 col-sm-12 mb-4" v-if="with_tax">
                            <label for="Unit-name-add" class="form-label"
                                >Default Harga Penjualan</label
                            >
                            <Dropdown
                                v-model="customer.tax_default"
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
                                >Alamat Customer</label
                            >
                            <textarea
                                class="form-control"
                                v-model="customer.address"
                            ></textarea>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Catatan
                            </label>
                            <textarea
                                class="form-control"
                                v-model="customer.detail"
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
                                : "Tambahkan Pelanggan"
                        }}
                        <i class="ti ti-plus label-btn-icon ms-2"></i>
                    </button>
                </div>
            </Form>
        </div>
    </div>
    <!-- End Create Data -->
</template>

<script>
import InputMask from "primevue/inputmask";
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "customer_create",
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
            account_default: {
                customer_debt: {
                    id: "",
                    name: "",
                },
                customer_debt_imprest: {
                    id: "",
                    name: "",
                },
            },
            customer: {
                name: "",
                email: "",
                phone: "",
                address: "",
                detail: "",
                npwp: "",
                type: "general",
                tax_option: "no",
                tax_default: "no",
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
                    this.customer.is_account = !true;
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
                this.account_default.customer_debt = data.customer_debt;
                this.account_default.customer_debt_imprest =
                    data.customer_debt_imprest;

                this.customer.is_account = this.with_accountant;
                this.customer.debt = this.account_default.customer_debt;
                this.customer.debt_imprest =
                    this.account_default.customer_debt_imprest;
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

                this.setDefaultTerm();

                this.loader.term = false;
            } catch (error) {
                console.log(error);
            }
        },

        setDefaultTerm() {
            const filteredDefault = this.terms.filter(
                (item) => item.default == true
            );

            if (filteredDefault.length > 0) {
                this.customer.term = filteredDefault[0];
            }
        },

        ValidationCustomers() {
            this.$refs.customerValidation.validate().then((success) => {
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
            var customerData = this.customer;
            customerData.phone = this.customer.phone
                .replace("+", "")
                .replace(/\s|-/g, "");
            ApiData.post("app/crm/customers/create", this.customer)
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
    mounted: function () {},
    watch: {},
};
</script>
