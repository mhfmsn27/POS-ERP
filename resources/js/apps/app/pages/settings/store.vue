<template>
    <!-- Create Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Pengaturan Toko / Cabang</h4>
                <button
                    type="button"
                    :disabled="loader.submit"
                    @click="modal = true"
                    class="btn label-btn label-end btn-danger"
                >
                    Hapus Toko
                </button>
            </div>
            <Form @submit="validationSettings()" ref="settingValidation">
                <div class="card-body">
                    <div class="row">
                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product">Pilih Printer</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.printer_id"
                                name="Printer"
                            >
                                <Dropdown
                                    v-model="form.printer_id"
                                    :options="printers"
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Pilih Printer"
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
                            <label for="category-product">Nama Toko</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.name"
                                name="Nama Toko"
                            >
                                <InputText
                                    v-model="form.name"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Nama Toko"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product">Email Toko</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.email"
                                name="Email"
                            >
                                <InputText
                                    v-model="form.email"
                                    style="width: 100%"
                                    type="email"
                                    class="form-control"
                                    placeholder="Masukkan Alamat Email"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product">No.Hp Toko</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.phone"
                                name="Hp Toko"
                            >
                                <InputText
                                    v-model="form.phone"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan No Hp"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product">Kode POS Toko</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.zip_code"
                                name="Kode POS Toko"
                            >
                                <InputText
                                    v-model="form.zip_code"
                                    style="width: 100%"
                                    type="text"
                                    class="form-control"
                                    placeholder="Masukkan Kode POS"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Opsi Penggunaan Akuntansi</label
                            >
                            <InputText
                                style="width: 100%"
                                type="text"
                                readonly
                                :value="
                                    form.accountant_use == 'yes'
                                        ? 'Mengunakan akuntansi'
                                        : 'Tidak Menggunakan'
                                "
                                class="form-control"
                                placeholder="Masukkan Kode POS"
                            />
                        </div>

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Opsi Penggunaan Pajak</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.tax_option"
                                name="Opsi Pajak"
                            >
                                <Dropdown
                                    v-model="form.tax_option"
                                    :options="[
                                        {
                                            name: 'Gunakan Pajak',
                                            value: 'active',
                                        },
                                        {
                                            name: 'Jangan Gunakan',
                                            value: 'no',
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
                        <div
                            class="col-lg-6 col-sm-12 mb-4"
                            v-if="form.tax_option == 'active'"
                        >
                            <label for="category-product">Pajak 1</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.tax_one"
                                name="Pajak Satu"
                            >
                                <Dropdown
                                    v-model="form.tax_one"
                                    :options="taxrate"
                                    optionLabel="name"
                                    optionValue="amount"
                                    placeholder="Pilih Pajak"
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
                        <div
                            class="col-lg-6 col-sm-12 mb-4"
                            v-if="form.tax_option == 'active'"
                        >
                            <label for="category-product">Pajak 2</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.tax_two"
                                name="Pajak Kedua"
                            >
                                <Dropdown
                                    v-model="form.tax_two"
                                    :options="taxrate"
                                    optionLabel="name"
                                    optionValue="amount"
                                    placeholder="Pilih Pajak"
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
                        <div
                            class="col-lg-6 col-sm-12 mb-4"
                            v-if="form.tax_option == 'active'"
                        >
                            <label for="category-product">Pajak 3</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.tax_tree"
                                name="Pajak Tiga"
                            >
                                <Dropdown
                                    v-model="form.tax_tree"
                                    :options="taxrate"
                                    optionLabel="name"
                                    optionValue="amount"
                                    placeholder="Pilih Pajak"
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
                        <div
                            class="col-lg-6 col-sm-12 mb-4"
                            v-if="form.tax_option == 'active'"
                        >
                            <label for="category-product">Default Gudang</label>
                            <Dropdown
                                v-model="form.warehouse_default_id"
                                :options="warehouses"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Gudang Utama"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-12 mb-4">
                            <label for="category-product">Alamat Toko</label>
                            <textarea
                                class="form-control"
                                v-model="form.address"
                            ></textarea>
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-12 mb-4">
                            <label for="category-product">Footer Text</label>
                            <textarea
                                class="form-control"
                                v-model="form.footer_text"
                            ></textarea>
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

    <!-- Modal For Delete -->
    <Dialog
        v-model:visible="modal"
        class="filter-data"
        modal
        maximizable
        header="Hapus Toko atau Cabang"
        :style="{ width: '50vw' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <Form @submit="deleteStore()" ref="ValidationDeleteStore">
            <div class="row p-3">
                <div class="col-12 text-center">
                    <img src="@/assets/images/delete-store.webp" class="w-50" />
                    <h3>Hapus Toko</h3>
                    <p>
                        Menghapus toko akan menyebabkan seluruh data mengenai
                        toko anda, baik produk, transaksi dan sebagainya
                        terhapus seluruhnya dan tidak akan pernah bisa di
                        kembalikan lagi. Jika anda telah mengerti akan hal ini,
                        silahkan klik tombol untuk meminta kode verifikasi di
                        bawah untuk melanjutkan langkah-langkah menghapus toko
                    </p>
                </div>
                <div class="col-12 text-center" v-if="!otp.status">
                    <button
                        class="btn btn-info btn-block"
                        @click="askCodeOtp"
                        type="button"
                        :disabled="loader.submit"
                    >
                        Minta Kode OTP
                    </button>
                </div>
                <div class="col-12" v-else>
                    <label for="category-product">Masukkan Kode OTP</label>
                    <Field
                        :rules="{
                            required: true,
                        }"
                        v-slot="{ errors }"
                        v-model="otp.code"
                        name="Masukkan Kode OTP"
                    >
                        <InputText
                            v-model="otp.code"
                            style="width: 100%"
                            type="text"
                            class="form-control"
                            placeholder="Masukkan Kode OTP"
                        />
                        <div class="fs-sm text-danger">
                            {{ errors[0] }}
                        </div>
                    </Field>
                </div>
            </div>
        </Form>

        <template #footer v-if="otp.status">
            <button
                type="button"
                @click="askCodeOtp()"
                :disabled="loader.submit"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                {{
                    loader.submit ? "Mohon Tunggu...." : "Minta Kode OTP Lagi!"
                }}
            </button>
            <button
                type="button"
                @click="deleteStore()"
                :disabled="loader.submit"
                class="btn btn-outline-danger btn-wave waves-effect waves-light"
            >
                {{ loader.submit ? "Mohon Tunggu...." : "Hapus Toko" }}
            </button>
        </template>
    </Dialog>
    <!-- End Modal -->
</template>

<script>
import { ApiData } from "@/api/server";
import NProgress from "nprogress";

var _ = require("lodash");

export default {
    name: "StoreSetting",
    data() {
        return {
            modal: false,
            taxrate: [],
            printers: [],
            warehouses: [],
            otp: {
                status: false,
                code: "",
            },
            form: {
                printer_id: "",
                name: "",
                email: "",
                phone: "",
                zip_code: "",
                tax_option: "",
                tax_one: "",
                tax_two: "",
                tax_tree: "",
                warehouse_default_id: "",
                shift_register: "",
                accountant_use: "",
                address: "",
                footer_text: "",
            },
            loader: {
                submit: false,
            },
        };
    },
    methods: {
        async getData() {
            try {
                const response = await ApiData.get(`app/settings/stores`);
                var data = response.data;
                this.form = data.store;
                this.printers = data.printers;
            } catch (error) {
                console.log(error);
            }
        },

        async getWarehouse() {
            try {
                const response = await ApiData.get(
                    `app/settings/warehouses/search`
                );
                var data = response.data;
                this.warehouses = data.warehouses;
            } catch (error) {
                console.log(error);
            }
        },

        async getTaxrate() {
            try {
                const response = await ApiData.get(`app/master/tax`);
                var data = response.data;
                this.taxrate = data.taxrates;
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
            ApiData.post("app/settings/stores/update", this.form)
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

        deleteStore() {
            this.$refs.ValidationDeleteStore.validate().then((success) => {
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
                    this.deleteToko();
                }
            });
        },

        deleteToko() {
            ApiData.post("app/settings/stores/delete", this.otp)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    setTimeout(() => {
                        return (window.location = "/starter");
                    }, 1000);
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        askCodeOtp() {
            this.loader.submit = true;
            ApiData.post("app/settings/stores/ask-otp")
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.otp = {
                        status: true,
                        code: "",
                    };
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
        this.getTaxrate();
        this.getWarehouse();
    },
    watch: {},
};
</script>
