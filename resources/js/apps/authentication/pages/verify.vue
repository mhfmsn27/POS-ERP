<template>
    <div class="">
        <div class="col col-login mx-auto mt-7">
            <div class="text-center">
                <router-link :to="{ name: 'login' }">
                    <img
                        v-lazy="asset.logo"
                        class="header-brand-img"
                        alt="logo"
                    />
                </router-link>
            </div>
        </div>
        <div class="container-login100">
            <div class="wrap-login100 p-6">
                <Form
                    @submit="ValidationVerfy()"
                    ref="ValidationVerfy"
                    class="login100-form validate-form"
                >
                    <span class="login100-form-title">
                        Verifikasi Alamat Email
                    </span>

                    <div
                        class="wrap-input100 validate-input mb-4"
                        data-validate="Valid email is required: ex@abc.xyz"
                    >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Kode Verifikasi'"
                            v-model="user.two_factor_code"
                        >
                            <input
                                class="input100"
                                type="number"
                                v-model="user.two_factor_code"
                                placeholder="Masukkan Kode Verifikasi"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>

                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </span>
                    </div>
                    <div class="container-login100-form-btn">
                        <button
                            type="submit"
                            :disabled="loader.submit"
                            class="login100-form-btn btn-primary"
                        >
                            {{
                                loader.submit
                                    ? "Mohon Menunggu...."
                                    : "Verifikasi Email"
                            }}
                        </button>
                    </div>
                    <div class="text-center pt-3">
                        <p>
                            Tidak menerima email ?
                            <a
                                class="forgot-link"
                                href="javascript:void(0)"
                                @click="reSend"
                                :disabled="loader.resend"
                                >Minta Kembali</a
                            >
                        </p>
                    </div>
                </Form>
            </div>
        </div>
        <!-- CONTAINER CLOSED -->
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
