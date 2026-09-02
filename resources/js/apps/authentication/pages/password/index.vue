<template>
    <div class="auth-page-wrapper">
        <!-- Stage 1: Request Password Reset -->
        <div class="wrap-login100" v-if="stage.first">
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
                    <span class="auth-brand-badge">Pemulihan Akun</span>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="auth-form-title">Lupa Password?</h1>
            <p class="auth-form-subtitle">Masukkan alamat email akun POSHUB Anda untuk menerima kode verifikasi pemulihan sandi.</p>

            <Form
                @submit="sendResetCode()"
                ref="ValidationCodeReset"
                class="validate-form"
            >
                <div class="form-group mb-4">
                    <label for="resetEmail" class="auth-label">Alamat Email Terdaftar</label>
                    <div class="wrap-input100">
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Alamat Email'"
                            v-model="user.email"
                        >
                            <input
                                id="resetEmail"
                                class="input100"
                                type="email"
                                v-model="user.email"
                                placeholder="name@company.com"
                            />
                            <div class="fs-sm text-danger mt-1">
                                {{ errors[0] }}
                            </div>
                        </Field>
                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
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
                        <i v-else class="fa fa-paper-plane me-2"></i>
                        {{
                            loader.submit
                                ? "Mengirim Kode..."
                                : "Kirim Kode Verifikasi"
                        }}
                    </button>
                </div>

                <div class="text-center pt-4">
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">
                        Ingat kata sandi Anda?
                        <router-link
                            :to="{ name: 'login' }"
                            class="text-primary fw-bold text-decoration-none ms-1"
                        >
                            Kembali ke Login
                        </router-link>
                    </p>
                </div>
            </Form>
        </div>

        <!-- Stage 2: Verify Code -->
        <div class="wrap-login100" v-if="stage.second">
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
                    <span class="auth-brand-badge">Verifikasi Token</span>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="auth-form-title">Verifikasi Kode Email</h1>
            <p class="auth-form-subtitle">Masukkan kode otentikasi pemulihan yang dikirimkan ke <strong>{{ user.email }}</strong>.</p>

            <Form
                @submit="sendVerifyEmail()"
                ref="ValidationEmailCode"
                class="validate-form"
            >
                <div class="form-group mb-4">
                    <label for="verifyResetCode" class="auth-label">Kode Verifikasi</label>
                    <div class="wrap-input100">
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Masukkan Kode'"
                            v-model="verify_email.two_factor_code"
                        >
                            <input
                                id="verifyResetCode"
                                class="input100 text-center fw-bold"
                                style="letter-spacing: 4px; font-size: 1.25rem;"
                                type="text"
                                maxlength="8"
                                v-model="verify_email.two_factor_code"
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
                                : "Validasi Kode"
                        }}
                    </button>
                </div>

                <div class="text-center pt-4">
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">
                        Kembali ke
                        <router-link
                            :to="{ name: 'login' }"
                            class="text-primary fw-bold text-decoration-none ms-1"
                        >
                            Halaman Login
                        </router-link>
                    </p>
                </div>
            </Form>
        </div>

        <!-- Stage 3: New Password -->
        <div class="wrap-login100" v-if="stage.finish">
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
                    <span class="auth-brand-badge">Sandi Baru</span>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="auth-form-title">Buat Password Baru</h1>
            <p class="auth-form-subtitle">Buat kombinasi kata sandi baru yang aman untuk akun POSHUB Anda.</p>

            <Form
                @submit="resetPassword()"
                ref="ValidationResetPassword"
                class="validate-form"
            >
                <div class="form-group mb-3">
                    <label for="newPassword" class="auth-label">Password Baru</label>
                    <div class="wrap-input100">
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Password Baru'"
                            v-model="reset_password.password"
                        >
                            <input
                                id="newPassword"
                                class="input100"
                                type="password"
                                v-model="reset_password.password"
                                placeholder="Minimal 8 karakter kombinasi"
                            />
                            <div class="fs-sm text-danger mt-1">
                                {{ errors[0] }}
                            </div>
                        </Field>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="newPasswordConfirm" class="auth-label">Konfirmasi Password Baru</label>
                    <div class="wrap-input100">
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Konfirmasi Password'"
                            v-model="reset_password.password_confirmation"
                        >
                            <input
                                id="newPasswordConfirm"
                                class="input100"
                                type="password"
                                v-model="reset_password.password_confirmation"
                                placeholder="Ulangi password baru"
                            />
                            <div class="fs-sm text-danger mt-1">
                                {{ errors[0] }}
                            </div>
                        </Field>
                        <span class="symbol-input100">
                            <i class="fa fa-shield-alt" aria-hidden="true"></i>
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
                        <i v-else class="fa fa-save me-2"></i>
                        {{
                            loader.submit
                                ? "Menyimpan Sandi..."
                                : "Simpan &amp; Masuk ke Sistem"
                        }}
                    </button>
                </div>
            </Form>
        </div>
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
