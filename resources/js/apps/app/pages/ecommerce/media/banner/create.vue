<template>
    <!-- List Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-3">
                <h4 class="card-title">Tambah banner</h4>
            </div>
            <Form
                @submit="Validationbanners()"
                ref="bannerValidation"
                class="card-body p-4"
            >
                <div class="row">
                    <!-- Title -->
                    <div class="col-lg-6 col-sm-6 mt-4">
                        <label for="category-product">Title banner</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="banner.title"
                            name="Title banner"
                        >
                            <InputText
                                v-model="banner.title"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                                placeholder="Masukkan Title banner"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Title -->

                    <!-- Title -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Opsi Penempatan</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="banner.position"
                            name="Posisi banner"
                        >
                            <Dropdown
                                v-model="banner.position"
                                :options="[
                                    {
                                        id: 'home',
                                        name: 'Home Page',
                                    },
                                    {
                                        id: 'shop',
                                        name: 'Shop Page',
                                    },
                                    {
                                        id: 'blog',
                                        name: 'Blog Page',
                                    },
                                    {
                                        id: 'mobile',
                                        name: 'Mobile Page',
                                    },
                                ]"
                                optionLabel="name"
                                optionValue="id"
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

                    <!-- Pengunaan Button -->
                    <div class="col-lg-4 col-sm-6 mt-4">
                        <label for="category-product">Pengunaan Button</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="banner.button"
                            name="Button banner"
                        >
                            <Dropdown
                                v-model="banner.button"
                                :options="[
                                    {
                                        id: 'yes',
                                        name: 'Gunakan',
                                    },
                                    {
                                        id: 'no',
                                        name: 'Jangan Gunakan',
                                    },
                                ]"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Pilih Opsi"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Button -->

                    <div
                        class="col-lg-4 col-sm-6 mt-4"
                        v-if="banner.button == 'yes'"
                    >
                        <label for="category-product">Nama Button</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="banner.button_name"
                            name="Nama Button"
                        >
                            <InputText
                                v-model="banner.button_name"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                                placeholder="Masukkan Nama Button"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div
                        class="col-lg-4 col-sm-6 mt-4"
                        v-if="banner.button == 'yes'"
                    >
                        <label for="category-product">Url Button</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="banner.button_url"
                            name="Url Button"
                        >
                            <InputText
                                v-model="banner.button_url"
                                style="width: 100%"
                                type="url"
                                class="form-control"
                                placeholder="Masukkan Url Button"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div class="col-12 mt-4">
                        <label for="courier-name-add" class="form-label"
                            >Upload Image</label
                        >
                        <FileUpload
                            mode="basic"
                            accept="image/*"
                            @select="handlePhotoChange"
                        />
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
                                    : "Tambahkan banner"
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
var _ = require("lodash");

export default {
    name: "create_banner",
    data() {
        return {
            loader: {
                submit: false,
                data: false,
            },
            banner: {
                title: "",
                position: "",
                button: "no",
                button_name: "",
                button_url: "",
                image: "",
            },
        };
    },
    methods: {
        async handlePhotoChange(e) {
            if (e.files[0] != undefined) {
                this.convertFileToBase64(e.files[0]);
            } else {
                this.banner.image = null;
            }
        },

        convertFileToBase64(file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                this.banner.image = reader.result;
            };
            reader.onerror = (error) => {
                console.error("Error converting file to base64:", error);
            };
        },

        Validationbanners() {
            this.$refs.bannerValidation.validate().then((success) => {
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
                "app/ecommerce/media-content/banners/create",
                this.banner
            )
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.redirectData();
                    this.loader.submit = false;
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        redirectData() {
            window.parent.postMessage({
                action: "closeActiveMenu",
                data: "",
            });
        },
    },
    mounted() {},
};
</script>
