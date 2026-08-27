<template>
    <!-- Create Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Pengaturan Pemberitahuan</h4>
            </div>
            <Form @submit="validationSettings()" ref="settingValidation">
                <div class="card-body">
                    <div class="row">
                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Opsi Penggunaan Device WhatsApp</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.type"
                                name="Opsi Penggunaan Device"
                            >
                                <Dropdown
                                    v-model="form.type"
                                    :options="[
                                        {
                                            name: 'Gunakan Opsi Bawaan',
                                            value: 'general',
                                        },
                                        {
                                            name: 'Gunakan Device Sendiri',
                                            value: 'personal',
                                        },
                                    ]"
                                    optionLabel="name"
                                    optionValue="value"
                                    placeholder="Pilih Opsi"
                                    style="width: 100%"
                                    class="w-full md:w-14rem"
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
                                >Nomor Penerima Notifikasi</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.phone"
                                name="Penerima Notifikasi"
                            >
                                <InputText
                                    v-model="form.phone"
                                    style="width: 100%"
                                    type="number"
                                    class="form-control"
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
                                >Penambahan Pengguna</label
                            >
                            <Dropdown
                                v-model="form.add"
                                :options="templates"
                                optionLabel="name"
                                optionValue="value"
                                placeholder="Pilih Template"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Pemesanan E-Commerce</label
                            >
                            <Dropdown
                                v-model="form.ecommerce_order"
                                :options="templates"
                                optionLabel="name"
                                optionValue="value"
                                placeholder="Pilih Template"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Pembayaran E-Commerce</label
                            >
                            <Dropdown
                                v-model="form.ecommerce_payment"
                                :options="templates"
                                optionLabel="name"
                                optionValue="value"
                                placeholder="Pilih Template"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Pengiriman E-Commerce</label
                            >
                            <Dropdown
                                v-model="form.ecommerce_shipping"
                                :options="templates"
                                optionLabel="name"
                                optionValue="value"
                                placeholder="Pilih Template"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Barang Diterima ( E-Commerce )</label
                            >
                            <Dropdown
                                v-model="form.ecommerce_received"
                                :options="templates"
                                optionLabel="name"
                                optionValue="value"
                                placeholder="Pilih Template"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>
                        <!-- End Form -->

                         <!-- Form -->
                         <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Penambahan Rma</label
                            >
                            <Dropdown
                                v-model="form.rma_add"
                                :options="templates"
                                optionLabel="name"
                                optionValue="value"
                                placeholder="Pilih Template"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>
                        <!-- End Form -->

                         <!-- Form -->
                         <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Progress Status Rma</label
                            >
                            <Dropdown
                                v-model="form.rma_progress"
                                :options="templates"
                                optionLabel="name"
                                optionValue="value"
                                placeholder="Pilih Template"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
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
                type: "general",
                phone: "",
                add: "",
                ecommerce_order: "",
                ecommerce_payment: "",
                ecommerce_shipping: "",
                ecommerce_received: "",
                rma_add: "",
                rma_progress: "",
            },
            templates: [],
            loader: {
                submit: false,
            },
        };
    },
    methods: {
        async getData() {
            try {
                const response = await ApiData.get(
                    `app/settings/notifications`
                );
                var data = response.data;
                this.form = data.settings;
                this.templates = data.templates;
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
            ApiData.post("app/settings/notifications/store", this.form)
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
