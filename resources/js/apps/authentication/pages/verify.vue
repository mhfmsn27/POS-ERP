<template>
    <div class="auth-page-wrapper">
        <div class="wrap-login100">
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
                    <span class="auth-brand-badge">Keamanan Akun</span>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="auth-form-title">Verifikasi Alamat Email</h1>
            <p class="auth-form-subtitle">Masukkan 6 digit kode verifikasi yang telah kami kirimkan ke alamat email Anda.</p>

            <Form
                @submit="ValidationVerfy()"
                ref="ValidationVerfy"
                class="validate-form"
            >
                <div class="form-group mb-4">
                    <label for="twoFactorCode" class="auth-label">Kode Verifikasi</label>
                    <div class="wrap-input100">
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Kode Verifikasi'"
                            v-model="user.two_factor_code"
                        >
                            <input
                                id="twoFactorCode"
                                class="input100 text-center fw-bold"
                                style="letter-spacing: 4px; font-size: 1.25rem;"
                                type="text"
                                maxlength="8"
                                v-model="user.two_factor_code"
                                placeholder="• • • • • •"
                            />
                            <div class="fs-sm text-danger mt-1">
                                {{ errors[0] }}
                            </div>
                        </Field>
                        <span class="symbol-input100">
                            <i class="fa fa-key" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>

                <div class="mt-4">
                    <button
                        type="submit"
                        :disabled="loader.submit"
                        class="btn-auth-primary"
                    >
                        <i v-if="loader.submit" class="fa fa-spinner fa-spin me-2"></i>
                        <i v-else class="fa fa-check-circle me-2"></i>
                        {{
                            loader.submit
                                ? "Memverifikasi..."
                                : "Verifikasi Email Sekarang"
                        }}
                    </button>
                </div>

                <div class="text-center pt-4">
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">
                        Tidak menerima kode email?
                        <a
                            class="text-primary fw-bold text-decoration-none ms-1"
                            href="javascript:void(0)"
                            @click="reSend"
                            :disabled="loader.resend"
                        >
                            <i v-if="loader.resend" class="fa fa-spinner fa-spin me-1"></i>
                            Kirim Ulang Kode
                        </a>
                    </p>
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
    name: "verify",
    data() {
        return {
            loader: {
                submit: false,
                resend: false,
            },
            asset: {
                logo: appLogo,
            },
            user: {
                two_factor_code: "",
            },
        };
    },
    methods: {
        ValidationVerfy() {
            this.$refs.ValidationVerfy.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Terjadi kesalahan",
                        detail: "Silahkan Check kembali form inputan anda",
                        life: 3000,
                    });
                } else {
                    this.verifyEmail();
                }
            });
        },

        verifyEmail() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            ApiData.post(`authentication/verify/store`, this.user)
                .then((response) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleSuccessResponse(response.message);
                    this.nextStage();
                })
                .catch((error) => {
                    NProgress.done();
                    this.$handleErrorResponse(error);
                    this.loader.submit = false;
                });
        },

        reSend() {
            this.loader.resend = true;
            NProgress.start();
            NProgress.set(0.1);
            ApiData.post(`authentication/verify/re-send`)
                .then((response) => {
                    NProgress.done();
                    this.loader.resend = false;
                    this.$handleSuccessResponse(response.message);
                })
                .catch((error) => {
                    NProgress.done();
                    var data = error.response.data;
                    this.$handleErrorResponse(error);
                    this.loader.resend = false;
                });
        },

        nextStage() {
            TokenService.saveVerify(1);
            
            setTimeout(() => {
                return this.$router.push({
                    name: "business_register",
                });
            }, 1000);
        },
    },
};
</script>
