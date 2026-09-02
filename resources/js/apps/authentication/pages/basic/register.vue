<template>
    <div class="auth-page-wrapper">
        <div class="wrap-login100 auth-card-wide">
            <!-- Brand Header -->
            <div class="auth-brand-header">
                <router-link :to="{ name: 'login' }" class="d-inline-block">
                    <img
                        v-lazy="asset.logo"
                        class="auth-brand-logo"
                        alt="POSHUB Enterprise"
                    />
                </router-link>
                <div>
                    <span class="auth-brand-badge">Pendaftaran Akun Baru</span>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="auth-form-title">Daftar Akun Enterprise</h1>
            <p class="auth-form-subtitle">Buat akun staf atau pemilik baru untuk mengelola transaksi POS dan laporan akuntansi.</p>

            <Form
                @submit="ValidationSignup()"
                ref="ValidationSignup"
                class="validate-form row g-3"
            >
                <div class="col-lg-6 col-sm-12">
                    <div class="form-group mb-0">
                        <label for="fullName" class="auth-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Nama Lengkap'"
                            v-model="user.name"
                        >
                            <input
                                id="fullName"
                                class="form-control no-icon"
                                type="text"
                                v-model="user.name"
                                placeholder="Masukkan nama lengkap"
                            />
                            <div class="fs-sm text-danger mt-1">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                </div>

                <div class="col-sm-12 col-lg-6">
                    <div class="form-group mb-0">
                        <label for="emailAddr" class="auth-label">Alamat Email <span class="text-danger">*</span></label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Alamat Email'"
                            v-model="user.email"
                        >
                            <input
                                id="emailAddr"
                                class="form-control no-icon"
                                type="email"
                                v-model="user.email"
                                placeholder="contoh: user@perusahaan.com"
                            />
                            <div class="fs-sm text-danger mt-1">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                </div>

                <div class="col-sm-12 col-lg-6">
                    <div class="form-group mb-0">
                        <label for="phoneNumber" class="auth-label">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Nomor WhatsApp'"
                            v-model="user.phone"
                        >
                            <input
                                id="phoneNumber"
                                class="form-control no-icon"
                                type="tel"
                                v-model="user.phone"
                                placeholder="contoh: 081234567890"
                            />
                            <div class="fs-sm text-danger mt-1">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                </div>

                <div class="col-sm-12 col-lg-6">
                    <div class="form-group mb-0">
                        <label for="gender" class="auth-label">Jenis Kelamin</label>
                        <select
                            class="form-control no-icon"
                            name="jk"
                            id="gender"
                            v-model="user.jk"
                        >
                            <option value="pria">Laki-laki (Pria)</option>
                            <option value="wanita">Perempuan (Wanita)</option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-12 col-lg-6">
                    <div class="form-group mb-0">
                        <label for="regPassword" class="auth-label">Kata Sandi <span class="text-danger">*</span></label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Password'"
                            v-model="user.password"
                        >
                            <input
                                id="regPassword"
                                class="form-control no-icon"
                                type="password"
                                v-model="user.password"
                                placeholder="Minimal 8 karakter"
                            />
                            <div class="fs-sm text-danger mt-1">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                </div>

                <div class="col-sm-12 col-lg-6">
                    <div class="form-group mb-0">
                        <label for="confirmPassword" class="auth-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Konfirmasi Password'"
                            v-model="user.password_confirmation"
                        >
                            <input
                                id="confirmPassword"
                                class="form-control no-icon"
                                type="password"
                                v-model="user.password_confirmation"
                                placeholder="Ulangi kata sandi"
                            />
                            <div class="fs-sm text-danger mt-1">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <button
                        type="submit"
                        :disabled="loader.submit"
                        class="btn-auth-primary"
                    >
                        <i v-if="loader.submit" class="fa fa-spinner fa-spin me-2"></i>
                        <i v-else class="fa fa-user-plus me-2"></i>
                        {{
                            loader.submit
                                ? "Mendaftarkan Akun..."
                                : "Daftar Akun Baru"
                        }}
                    </button>
                </div>

                <div class="col-12 auth-footer-text">
                    <p class="mb-0">
                        Sudah memiliki akun?
                        <router-link :to="{ name: 'login' }" class="auth-link"
                            >Masuk ke Akun</router-link
                        >
                    </p>
                    <small class="text-muted d-block mt-2" style="font-size: 11px;">
                        &copy; POSHUB ENTERPRISE. Hak Cipta Dilindungi.
                    </small>
                </div>
            </Form>
        </div>
    </div>
</template>

<script>
import NProgress from "nprogress";
import appLogo from "@/assets/images/logo.webp";

import { TokenService } from "@/services";
import { ApiData } from "@/api/server";
export default {
    name: "register",
    data() {
        return {
            loader: {
                submit: false,
            },
            asset: {
                logo: appLogo,
            },
            user: {
                name: "",
                jk: "pria",
                phone: "",
                email: "",
                password: "",
                password_confirmation: "",
            },
        };
    },
    methods: {
        ValidationSignup() {
            this.$refs.ValidationSignup.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Terjadi kesalahan",
                        detail: "Silahkan Check kembali form inputan anda",
                        life: 3000,
                    });
                } else {
                    this.CreateAccount();
                }
            });
        },

        CreateAccount() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            ApiData.post("authentication/register", this.user)
                .then((response) => {
                    setTimeout(() => {
                        NProgress.done();
                        var detail = response.data;
                        this.loginAccount(detail);
                    }, 500);
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
        },

        loginAccount(detail) {
            TokenService.saveToken(detail.token);
            TokenService.saveProfile(detail.data);
           
            var verify = detail.data.verify == true ? 1 : null;
            TokenService.saveVerify(verify);

            setTimeout(() => {
                return this.$router.push({
                    name: "verify",
                });
            }, 1000);
        },
    },
};
</script>
