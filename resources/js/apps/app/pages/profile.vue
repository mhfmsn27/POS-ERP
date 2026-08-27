<template>
    <div class="page-header">
        <h1 class="page-title">Akun Saya</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <router-link :to="{ name: $route.meta.parent_menu }"
                    >Akun Saya</router-link
                >
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $route.meta.title }}
            </li>
        </ol>
    </div>
    <div class="row">
        <div class="col-lg-8 col-sm-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Edit Profil</h4>
                </div>
                <Form @submit="ValidationProfile()" ref="ProfileValidation">
                    <div class="card-body">
                        <div class="row">
                            <!-- Form -->
                            <div class="col-12 mb-4">
                                <label for="category-product"
                                    >Nama Lengkap</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="user.name"
                                    name="Nama Lengkap"
                                >
                                    <InputText
                                        v-model="user.name"
                                        style="width: 100%"
                                        type="text"
                                        class="form-control"
                                        placeholder="Masukkan Nama Lengkap"
                                    />
                                    <div class="fs-sm text-danger">
                                        {{ errors[0] }}
                                    </div>
                                </Field>
                            </div>
                            <!-- End Form -->

                            <!-- Form -->
                            <div class="col-12 mb-4">
                                <label for="category-product"
                                    >Alamat Email</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="user.email"
                                    name="Alamat Email"
                                >
                                    <InputText
                                        v-model="user.email"
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
                            <div class="col-12 mb-4">
                                <label for="category-product"
                                    >Gambar Profile</label
                                >
                                <FileUpload
                                    mode="basic"
                                    accept="image/*"
                                    @select="handlePhotoChange"
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

        <div class="col-lg-4 col-sm-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Ubah Password</h4>
                </div>
                <Form @submit="ValidationPassword()" ref="PasswordValidation">
                    <div class="card-body">
                        <div class="row">
                            <!-- Form -->
                            <div class="col-12 mb-4">
                                <label for="category-product"
                                    >Password Saat ini</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="password.old"
                                    name="Password Saat ini"
                                >
                                    <InputText
                                        v-model="password.old"
                                        style="width: 100%"
                                        type="password"
                                        class="form-control"
                                    />
                                    <div class="fs-sm text-danger">
                                        {{ errors[0] }}
                                    </div>
                                </Field>
                            </div>
                            <!-- End Form -->

                            <div class="col-12 mb-4">
                                <label for="category-product"
                                    >Password Baru</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="password.new"
                                    name="Password Baru"
                                >
                                    <InputText
                                        v-model="password.new"
                                        style="width: 100%"
                                        type="password"
                                        class="form-control"
                                    />
                                    <div class="fs-sm text-danger">
                                        {{ errors[0] }}
                                    </div>
                                </Field>
                            </div>

                            <div class="col-12 mb-4">
                                <label for="category-product"
                                    >Konfirmasi Password Baru</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="password.confirm"
                                    name="Konfirmasi Password baru"
                                >
                                    <InputText
                                        v-model="password.confirm"
                                        style="width: 100%"
                                        type="password"
                                        class="form-control"
                                    />
                                    <div class="fs-sm text-danger">
                                        {{ errors[0] }}
                                    </div>
                                </Field>
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
    </div>
</template>

<script>
import { ApiData } from "@/api/server";
import NProgress from "nprogress";
import { TokenService } from "@/services";
var _ = require("lodash");

export default {
    name: "KeySetting",
    data() {
        return {
            user: {
                name: "",
                email: "",
                avatar: "",
                phone: "",
            },
            password: {
                old: "",
                new: "",
                confirm: "",
            },
            loader: {
                submit: false,
            },
        };
    },
    methods: {
        async handlePhotoChange(e) {
            if (e.files[0] != undefined) {
                this.convertFileToBase64(e.files[0]);
            } else {
                this.user.image = null;
            }
        },

        convertFileToBase64(file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                this.user.image = reader.result;
            };
            reader.onerror = (error) => {
                console.error("Error converting file to base64:", error);
            };
        },

        async getData() {
            try {
                const response = await ApiData.get(`app/profile`);
                var data = response.data;
                TokenService.saveProfile(data);
            } catch (error) {
                console.log(error);
            }
        },

        ValidationPassword() {
            this.$refs.PasswordValidation.validate().then((success) => {
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
                    this.updatePassword();
                }
            });
        },

        ValidationProfile() {
            this.$refs.ProfileValidation.validate().then((success) => {
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

        updatePassword() {
            ApiData.post("app/profile/password", this.password)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.password = {
                        old: "",
                        new: "",
                        confirm: "",
                    };
                    this.loader.submit = false;
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        updateData() {
            ApiData.post("app/profile/update", this.user)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.getData();
                    this.loader.submit = false;
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        setProfile() {
            this.user = {
                name: TokenService.getProfile().name,
                email: TokenService.getProfile().email,
                phone: TokenService.getProfile().phone,
                image: TokenService.getProfile().photo,
            };
        },
    },
    mounted: function () {
        this.getData();
    },
    created: function () {
        this.setProfile();
    },
    watch: {},
};
</script>
