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
        <div class="container-login100" v-if="stage.first">
            <div class="wrap-login100 p-6">
                <Form
                    @submit="sendResetCode()"
                    ref="ValidationCodeReset"
                    class="login100-form validate-form"
                >
                    <span class="login100-form-title">
                        Minta Reset Password
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
                            :name="'Alamat Email'"
                            v-model="user.email"
                        >
                            <input
                                class="input100"
                                type="email"
                                v-model="user.email"
                                placeholder="Masukkan Alamat Email"
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
                                    : "Kirim Permintaan"
                            }}
                        </button>
                    </div>
                    <div class="text-center pt-3">
                        <p class="text-dark mb-0">
                            Kembali ke halaman
                            <router-link
                                :to="{ name: 'login' }"
                                class="text-primary mx-1"
                                >Login</router-link
                            >
                        </p>
                    </div>
                </Form>
            </div>
        </div>

        <div class="container-login100" v-if="stage.second">
            <div class="wrap-login100 p-6">
                <Form
                    @submit="sendVerifyEmail()"
                    ref="ValidationEmailCode"
                    class="login100-form validate-form"
                >
                    <span class="login100-form-title">
                        Verifikasi Kode Email
                    </span>
                    <div class="wrap-input100 validate-input mb-4">
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Masukkan Kode'"
                            v-model="verify_email.two_factor_code"
                        >
                            <input
                                class="form-control"
                                type="text"
                                v-model="verify_email.two_factor_code"
                                placeholder="Masukkan Kode Verifikasi"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
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
                        <p class="text-dark mb-0">
                            Kembali ke halaman
                            <router-link
                                :to="{ name: 'login' }"
                                class="text-primary mx-1"
                                >Login</router-link
                            >
                        </p>
                    </div>
                </Form>
            </div>
        </div>

        <div class="container-login100" v-if="stage.finish">
            <div class="wrap-login100 p-6">
                <Form
                    @submit="resetPassword()"
                    ref="ValidationResetPassword"
                    class="login100-form validate-form"
                >
                    <span class="login100-form-title">
                        Masukkan Password Baru
                    </span>
                    <div class="wrap-input100 validate-input mb-4">
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Password Baru'"
                            v-model="reset_password.password"
                        >
                            <input
                                class="form-control"
                                type="password"
                                v-model="reset_password.password"
                                placeholder="Password Baru"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div class="wrap-input100 validate-input mb-4">
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Konfirmasi Password'"
                            v-model="reset_password.password_confirmation"
                        >
                            <input
                                class="form-control"
                                type="password"
                                v-model="reset_password.password_confirmation"
                                placeholder="Konfirmasi Password Baru"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
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
                                    : "Reset Password"
                            }}
                        </button>
                    </div>
                </Form>
            </div>
        </div>
        <!-- CONTAINER CLOSED -->
    </div>
</template>

<script>
import { ApiData } from "@/api/server";
import NProgress from "nprogress";
import appLogo from "@/assets/images/logo.webp";
import { TokenService } from "@/services";
export default {
    name: "login",
    data() {
        return {
            stage: {
                first: true,
                second: false,
                finish: false,
            },
            verify_email: {
                two_factor_code: "",
            },
            reset_password: {
                email: "",
                password_confirmation: "",
                password: "",
                verify_email: {
                    two_factor_code: "",
                },
            },
            loader: {
                submit: false,
            },
            asset: {
                logo: appLogo,
            },
            user: {
                email: "",
            },
        };
    },
    methods: {
        sendResetCode() {
            this.$refs.ValidationCodeReset.validate().then((success) => {
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
                    ApiData.post(`authentication/forget-pass/ask`, this.user)
                        .then((response) => {
                            NProgress.done();
                            this.loader.submit = false;
                            this.$handleSuccessResponse(response.message);
                            setTimeout(() => {
                                this.reset_password.email = this.user.email;
                                this.stage.first = false;
                                this.stage.second = true;
                            }, 500);
                        })
                        .catch((error) => {
                            NProgress.done();
                            this.$handleErrorResponse(error);
                            this.loader.submit = false;
                        });
                }
            });
        },

        sendVerifyEmail() {
            this.$refs.ValidationEmailCode.validate().then((success) => {
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
                    ApiData.post(
                        `authentication/forget-pass/verify`,
                        this.verify_email
                    )
                        .then((response) => {
                            NProgress.done();
                            this.loader.submit = false;
                            this.$handleSuccessResponse(response.message);
                            setTimeout(() => {
                                this.reset_password.verify_email =
                                    this.verify_email;
                                this.stage.second = false;
                                this.stage.finish = true;
                            }, 500);
                        })
                        .catch((error) => {
                            NProgress.done();
                            var data = error.response.data;
                            this.$handleErrorResponse(error);
                            this.loader.submit = false;
                        });
                }
            });
        },

        resetPassword() {
            this.$refs.ValidationResetPassword.validate().then((success) => {
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
                    ApiData.post(
                        `authentication/forget-pass/reset`,
                        this.reset_password
                    )
                        .then((response) => {
                            NProgress.done();
                            this.loader.submit = false;
                            var detail = response.data;
                            

                            TokenService.saveToken(detail.token);
                            TokenService.saveProfile(detail.data);
                            var verify = detail.data.verify == true ? 1 : null;
                            var merchant = detail.data.merchant == true ? 1 : null;
                            TokenService.saveVerify(verify);
                            TokenService.saveMerchant(merchant);

                            this.$handleSuccessResponse(response.message);

                            if (verify == null) {
                                return this.$router.push({
                                    name: "verify",
                                });
                            }

                            if (merchant == null) {
                                return this.$router.push({
                                    name: "business_register",
                                });
                            }

                            return (window.location = "/starter");
                        })
                        .catch((error) => {
                            NProgress.done();
                            this.$handleErrorResponse(error);
                            this.loader.submit = false;
                        });
                }
            });
        },
    },
};
</script>
