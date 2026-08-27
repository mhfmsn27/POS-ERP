<template>
    <!-- Create Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Pengaturan Prefix Transaksi</h4>
            </div>
            <Form @submit="validationSettings()" ref="settingValidation">
                <div class="card-body">
                    <div class="row">
                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Pembelian</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.purchase"
                                name="Pembelian"
                            >
                                <InputText
                                    v-model="form.purchase"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Prefix Key"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                         <!-- Form -->
                         <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Return Pembelian</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.purchase_return"
                                name="Return Pembelian"
                            >
                                <InputText
                                    v-model="form.purchase_return"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Prefix Key"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                         <!-- Form -->
                         <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Pembayaran Pembelian</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.purchase_payment"
                                name="Pembayaran Pembelian"
                            >
                                <InputText
                                    v-model="form.purchase_payment"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Prefix Key"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                         <!-- Form -->
                         <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Penjualan</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.sell"
                                name="Penjualan"
                            >
                                <InputText
                                    v-model="form.sell"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Prefix Key"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                         <!-- Form -->
                         <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Retur Penjualan</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.sell_return"
                                name="Retur Penjualan"
                            >
                                <InputText
                                    v-model="form.sell_return"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Prefix Key"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                         <!-- Form -->
                         <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Pembayaran Faktur Penjualan</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.sell_payment"
                                name="Pembayaran Faktur Penjualan"
                            >
                                <InputText
                                    v-model="form.sell_payment"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Prefix Key"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                         <!-- Form -->
                         <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Stok Opname</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.adjustment"
                                name="Stok Opname"
                            >
                                <InputText
                                    v-model="form.adjustment"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Prefix Key"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                         <!-- Form -->
                         <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Stok Transfer</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.stock_transfer"
                                name="Stok Transfer"
                            >
                                <InputText
                                    v-model="form.stock_transfer"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Prefix Key"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                         <!-- Form -->
                         <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Uang Keluar / Pembayaran</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.expense"
                                name="Pembayaran"
                            >
                                <InputText
                                    v-model="form.expense"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Prefix Key"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->
 
 
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
    name: "KeySetting",
    data() {
        return {
            form: {
                purchase: "",
                purchase_return: "",
                sell: "",
                sell_return: "",
                adjustment: "",
                stock_transfer: "",
                expense: "",
                purchase_payment: "",
                sell_payment: "",
            },
            loader: {
                submit: false,
            },
        };
    },
    methods: {
        async getData() {
            try {
                const response = await ApiData.get(`app/settings/keys`);
                var data = response.data;
                this.form = data;
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
            ApiData.post("app/settings/keys/store", this.form)
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
    },
    watch: {},
};
</script>
