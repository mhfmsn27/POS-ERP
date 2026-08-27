<template>
    <div class="page-header">
        <h1 class="page-title">E-Commerce</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <router-link :to="{ name: $route.meta.parent_menu }"
                    >Konfigurasi</router-link
                >
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $route.meta.title }}
            </li>
        </ol>
    </div>

    <!-- List Data -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-3">
                <h4 class="card-title">Pengaturan General</h4>
            </div>
            <Form
                @submit="ValidationSetting()"
                ref="SettingValidation"
                class="card-body p-4"
            >
                <div class="row">
                    <!-- Title -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Metode Pembayaran</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.payment_method"
                            name="Metode Pembayaran"
                        >
                            <Dropdown
                                v-model="setting.payment_method"
                                :options="[
                                    {
                                        name: 'Midtrans',
                                        value: 'midtrans',
                                    },
                                    {
                                        name: 'Bank Transfer',
                                        value: 'manual',
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
                    <!-- End Title -->

                    <!-- MerchantId -->
                    <div
                        class="col-lg-6 col-sm-12 mt-4"
                        v-if="setting.payment_method == 'midtrans'"
                    >
                        <label for="category-product">MerchantID</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.merchant_id"
                            name="MerchantID"
                        >
                            <InputText
                                v-model="setting.merchant_id"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End MerchantId -->

                    <!-- Client Key -->
                    <div
                        class="col-lg-6 col-sm-12 mt-4"
                        v-if="setting.payment_method == 'midtrans'"
                    >
                        <label for="category-product">Client Key</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.client_key"
                            name="Client Key"
                        >
                            <InputText
                                v-model="setting.client_key"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Client Key -->

                    <!-- Server Key -->
                    <div
                        class="col-lg-6 col-sm-12 mt-4"
                        v-if="setting.payment_method == 'midtrans'"
                    >
                        <label for="category-product">Server Key</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.server_key"
                            name="Server Key"
                        >
                            <InputText
                                v-model="setting.server_key"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Server Key -->

                    <!-- Raja Ongkir -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">RajaOngkir</label>
                        <InputText
                            v-model="setting.rajaongkir"
                            style="width: 100%"
                            type="text"
                            class="form-control"
                        />
                    </div>
                    <!-- End Raja Ongkir -->

                    <!-- Kurir -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product"
                            >Opsi Kurir Internal</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.kurir_manual"
                            name="Kurir Internal"
                        >
                            <Dropdown
                                v-model="setting.kurir_manual"
                                :options="[
                                    {
                                        name: 'Gunakan',
                                        value: 'yes',
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
                    <!-- End Kurir -->

                    <div
                        class="col-lg-6 col-sm-12 mt-4"
                        v-if="setting.kurir_manual == 'yes'"
                    >
                        <label for="category-product"
                            >Harga Kurir Internal PerKm</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.price_per_km"
                            name="Harga Kurir Internal"
                        >
                            <InputNumber
                                style="width: 100%"
                                v-model="setting.price_per_km"
                                prefix="Rp "
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <!-- Domain -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Domain Site</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.domain_site"
                            name="Domain"
                        >
                            <InputText
                                v-model="setting.domain_site"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Domain -->

                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Tampilkan Stok</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.show_stock"
                            name="Tampilkan Stok"
                        >
                            <Dropdown
                                v-model="setting.show_stock"
                                :options="[
                                    {
                                        name: 'Tampilkan',
                                        value: 'yes',
                                    },
                                    {
                                        name: 'Jangan Tampilkan',
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

                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product"
                            >Aktifkan E-Commerce</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.status"
                            name="Status Website"
                        >
                            <Dropdown
                                v-model="setting.status"
                                :options="[
                                    {
                                        name: 'Aktifkan',
                                        value: 'yes',
                                    },
                                    {
                                        name: 'Non Aktif',
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

                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product"
                            >Tampilkan Produk yang tidak memiliki stok</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.with_stock"
                            name="Tampilkan Produk tanpa Stok"
                        >
                            <Dropdown
                                v-model="setting.with_stock"
                                :options="[
                                    {
                                        name: 'Tampilkan',
                                        value: 'yes',
                                    },
                                    {
                                        name: 'Jangan Tampilkan',
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

                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Provinsi</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.store.province"
                            name="Pilih Provinsi"
                        >
                            <Multiselect
                                v-model="setting.store.province"
                                :options="provinces"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :options-limit="50"
                                placeholder="Pilih Provinsi"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @select="getCities('')"
                                @search-change="getProvince"
                            ></Multiselect>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Kota</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.store.city"
                            name="Pilih Kota"
                        >
                            <Multiselect
                                v-model="setting.store.city"
                                :options="cities"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :options-limit="50"
                                placeholder="Pilih Kota"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @select="getDistricts('')"
                                @search-change="getCities"
                            ></Multiselect>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Kecamatan</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="setting.store.district"
                            name="Pilih Kecamatan"
                        >
                            <Multiselect
                                v-model="setting.store.district"
                                :options="districts"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :options-limit="50"
                                placeholder="Pilih Kecamatan"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getDistricts"
                            ></Multiselect>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div class="col-12 d-flex justify-content-end mt-4">
                        <button
                            class="btn btn-primary"
                            type="submit"
                            :disabled="loader.submit"
                        >
                            {{
                                loader.submit
                                    ? "Mohon Tunggu"
                                    : "Simpan Perubahan"
                            }}
                        </button>
                    </div>
                </div>
            </Form>
        </div>
    </div>
    <!-- End List Data -->
</template>

<script>
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
import Editor from "primevue/editor";
var _ = require("lodash");

export default {
    name: "create_blog",
    components: {
        Editor,
    },
    data() {
        return {
            loader: {
                submit: false,
                data: false,
            },
            provinces: [],
            cities: [],
            districts: [],
            setting: {
                payment_method: "",
                rajaongkir: "",
                status: "",
                merchant_id: "",
                client_key: "",
                server_key: "",
                kurir_manual: "",
                price_per_km: "",
                domain_site: "",
                show_stock: "",
                with_stock: "",
                store: {
                    district: {
                        id: "",
                        name: "",
                    },
                    city: {
                        id: "",
                        name: "",
                    },
                    province: {
                        id: "",
                        name: "",
                    },
                },
            },
        };
    },
    methods: {
        async getProvince(query = "") {
            try {
                const response = await ApiData.get(
                    `app/ecommerce/location/provinces?term=${query}`
                );
                var data = response.data;
                this.provinces = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getCities(query = "") {
            try {
                const response = await ApiData.get(
                    `app/ecommerce/location/cities?term=${query}&province=${this.setting.store.province?.id}`
                );
                var data = response.data;
                this.cities = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getDistricts(query = "") {
            try {
                const response = await ApiData.get(
                    `app/ecommerce/location/district?term=${query}&city=${this.setting.store.city?.id}`
                );
                var data = response.data;
                this.districts = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getData() {
            try {
                const response = await ApiData.get(
                    `app/ecommerce/settings/integrations`
                );
                var data = response.data;
                this.setting = data;
            } catch (error) {
                console.log(error);
            }
        },

        async handlePhotoChange(e) {
            if (e.files[0] != undefined) {
                this.convertFileToBase64(e.files[0]);
            } else {
                this.setting.image = null;
            }
        },

        convertFileToBase64(file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                this.setting.image = reader.result;
            };
            reader.onerror = (error) => {
                console.error("Error converting file to base64:", error);
            };
        },

        ValidationSetting() {
            this.$refs.SettingValidation.validate().then((success) => {
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
            ApiData.post(
                `app/ecommerce/settings/integrations/store`,
                this.setting
            )
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
    mounted() {
        this.getData();
        this.getProvince("");
    },
};
</script>
