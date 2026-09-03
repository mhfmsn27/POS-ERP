<template>
    <div class="auth-page-wrapper">
        <!-- Top Navigation Link -->
        <div class="auth-nav-top">
            <router-link :to="{ name: 'login' }" class="auth-link-back">
                <i class="fa fa-arrow-left"></i> Kembali ke Login
            </router-link>
        </div>

        <div class="auth-split-container">
            <!-- Left Side: Form Panel -->
            <div class="auth-form-side">
                <!-- Stage 1: Request Password Reset -->
                <div v-if="stage.first">
                    <!-- Brand Header -->
                    <div class="auth-brand-header text-start mb-3">
                        <router-link :to="{ name: 'login' }" class="d-inline-block text-decoration-none">
                            <img
                                :src="asset.logo"
                                class="auth-brand-logo"
                                alt="POSHUB Enterprise"
                            />
                        </router-link>
                        <div>
                            <span class="auth-brand-badge">Pemulihan Akun &bull; Langkah 1 dari 3</span>
                        </div>
                    </div>

                    <!-- Title & Subtitle -->
                    <div class="mb-4">
                        <h1 class="auth-form-title text-start mb-1">Lupa Kata Sandi?</h1>
                        <p class="auth-form-subtitle text-start mb-0">Masukkan alamat email akun Anda untuk menerima kode verifikasi pemulihan sandi.</p>
                    </div>

                    <Form
                        @submit="sendResetCode()"
                        ref="ValidationCodeReset"
                        class="validate-form"
                    >
                        <div class="form-group mb-3">
                            <label for="resetEmail" class="auth-label">Alamat Email Terdaftar <span class="text-danger">*</span></label>
                            <div class="position-relative">
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
                                        class="form-control auth-input-with-icon"
                                        type="email"
                                        v-model="user.email"
                                        placeholder="contoh: admin@poshub.id"
                                        autofocus
                                    />
                                    <div class="fs-sm text-danger mt-1" v-if="errors && errors.length">
                                        <i class="fa fa-exclamation-circle me-1"></i>{{ errors[0] }}
                                    </div>
                                </Field>
                                <span class="auth-field-icon">
                                    <i class="fa fa-envelope"></i>
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
                                        ? "Mengirim Kode Verifikasi..."
                                        : "Kirim Kode Verifikasi"
                                }}
                            </button>
                        </div>

                        <div class="auth-footer-text text-start mt-4 pt-2 border-top">
                            <p class="mb-1 text-muted" style="font-size: 13.5px;">
                                Ingat kata sandi Anda?
                                <router-link
                                    :to="{ name: 'login' }"
                                    class="auth-link fw-bold ms-1"
                                >
                                    &larr; Kembali ke Login
                                </router-link>
                            </p>
                            <small class="text-muted d-block mt-2" style="font-size: 11.5px;">
                                &copy; POSHUB ENTERPRISE. Hak Cipta Dilindungi.
                            </small>
                        </div>
                    </Form>
                </div>

                <!-- Stage 2: Verify Code -->
                <div v-if="stage.second">
                    <!-- Brand Header -->
                    <div class="auth-brand-header text-start mb-3">
                        <router-link :to="{ name: 'login' }" class="d-inline-block text-decoration-none">
                            <img
                                :src="asset.logo"
                                class="auth-brand-logo"
                                alt="POSHUB Enterprise"
                            />
                        </router-link>
                        <div>
                            <span class="auth-brand-badge">Verifikasi Token &bull; Langkah 2 dari 3</span>
                        </div>
                    </div>

                    <!-- Title & Subtitle -->
                    <div class="mb-4">
                        <h1 class="auth-form-title text-start mb-1">Verifikasi Kode Email</h1>
                        <p class="auth-form-subtitle text-start mb-0">
                            Masukkan kode verifikasi 6-digit yang telah dikirim ke <strong>{{ user.email }}</strong>.
                        </p>
                    </div>

                    <Form
                        @submit="sendVerifyEmail()"
                        ref="ValidationEmailCode"
                        class="validate-form"
                    >
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="verifyResetCode" class="auth-label mb-0">Kode Verifikasi Email <span class="text-danger">*</span></label>
                                <a
                                    href="javascript:void(0)"
                                    @click="backToEmailStep"
                                    class="auth-link"
                                    style="font-size: 12px;"
                                >
                                    Ubah Email
                                </a>
                            </div>
                            <div class="position-relative">
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors, field }"
                                    :name="'Kode Verifikasi'"
                                    v-model="verify_email.two_factor_code"
                                >
                                    <input
                                        id="verifyResetCode"
                                        class="form-control auth-input-with-icon text-center fw-bold"
                                        style="letter-spacing: 4px; font-size: 1.15rem;"
                                        type="text"
                                        maxlength="8"
                                        v-model="verify_email.two_factor_code"
                                        placeholder="• • • • • •"
                                        autofocus
                                    />
                                    <div class="fs-sm text-danger mt-1" v-if="errors && errors.length">
                                        <i class="fa fa-exclamation-circle me-1"></i>{{ errors[0] }}
                                    </div>
                                </Field>
                                <span class="auth-field-icon">
                                    <i class="fa fa-key"></i>
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
                                        ? "Memverifikasi Kode..."
                                        : "Validasi Kode &amp; Lanjutkan"
                                }}
                            </button>
                        </div>

                        <div class="auth-footer-text text-start mt-4 pt-2 border-top">
                            <p class="mb-1 text-muted" style="font-size: 13.5px;">
                                Belum menerima kode?
                                <a
                                    href="javascript:void(0)"
                                    @click="resendResetCode"
                                    class="auth-link fw-bold ms-1"
                                >
                                    Kirim Ulang Kode
                                </a>
                            </p>
                            <p class="mb-0 text-muted" style="font-size: 12.5px;">
                                <router-link
                                    :to="{ name: 'login' }"
                                    class="auth-link"
                                >
                                    &larr; Batalkan &amp; Kembali ke Login
                                </router-link>
                            </p>
                        </div>
                    </Form>
                </div>

                <!-- Stage 3: New Password -->
                <div v-if="stage.finish">
                    <!-- Brand Header -->
                    <div class="auth-brand-header text-start mb-3">
                        <router-link :to="{ name: 'login' }" class="d-inline-block text-decoration-none">
                            <img
                                :src="asset.logo"
                                class="auth-brand-logo"
                                alt="POSHUB Enterprise"
                            />
                        </router-link>
                        <div>
                            <span class="auth-brand-badge">Sandi Baru &bull; Langkah 3 dari 3</span>
                        </div>
                    </div>

                    <!-- Title & Subtitle -->
                    <div class="mb-4">
                        <h1 class="auth-form-title text-start mb-1">Buat Kata Sandi Baru</h1>
                        <p class="auth-form-subtitle text-start mb-0">Buat kombinasi kata sandi baru yang kuat untuk akun POSHUB Anda.</p>
                    </div>

                    <Form
                        @submit="resetPassword()"
                        ref="ValidationResetPassword"
                        class="validate-form"
                    >
                        <!-- Password Baru -->
                        <div class="form-group mb-3">
                            <label for="newPassword" class="auth-label">Kata Sandi Baru <span class="text-danger">*</span></label>
                            <div class="position-relative">
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
                                        class="form-control auth-input-with-icon auth-input-with-toggle"
                                        :type="showNewPassword ? 'text' : 'password'"
                                        v-model="reset_password.password"
                                        placeholder="Minimal 8 karakter kombinasi"
                                        autofocus
                                    />
                                    <div class="fs-sm text-danger mt-1" v-if="errors && errors.length">
                                        <i class="fa fa-exclamation-circle me-1"></i>{{ errors[0] }}
                                    </div>
                                </Field>
                                <span class="auth-field-icon">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <button
                                    type="button"
                                    class="btn-toggle-password"
                                    @click="showNewPassword = !showNewPassword"
                                    tabindex="-1"
                                    :title="showNewPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                                >
                                    <i :class="showNewPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="form-group mb-3">
                            <label for="newPasswordConfirm" class="auth-label">Konfirmasi Kata Sandi Baru <span class="text-danger">*</span></label>
                            <div class="position-relative">
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
                                        class="form-control auth-input-with-icon auth-input-with-toggle"
                                        :type="showConfirmPassword ? 'text' : 'password'"
                                        v-model="reset_password.password_confirmation"
                                        placeholder="Ulangi kata sandi baru"
                                    />
                                    <div class="fs-sm text-danger mt-1" v-if="errors && errors.length">
                                        <i class="fa fa-exclamation-circle me-1"></i>{{ errors[0] }}
                                    </div>
                                </Field>
                                <span class="auth-field-icon">
                                    <i class="fa fa-shield"></i>
                                </span>
                                <button
                                    type="button"
                                    class="btn-toggle-password"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    tabindex="-1"
                                    :title="showConfirmPassword ? 'Sembunyikan konfirmasi' : 'Tampilkan konfirmasi'"
                                >
                                    <i :class="showConfirmPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                                </button>
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
                                        ? "Menyimpan Kata Sandi..."
                                        : "Simpan &amp; Masuk ke Sistem"
                                }}
                            </button>
                        </div>

                        <div class="auth-footer-text text-start mt-4 pt-2 border-top">
                            <p class="mb-1 text-muted" style="font-size: 13.5px;">
                                <router-link
                                    :to="{ name: 'login' }"
                                    class="auth-link fw-bold"
                                >
                                    &larr; Kembali ke Login
                                </router-link>
                            </p>
                        </div>
                    </Form>
                </div>
            </div>

            <!-- Right Side: Feature Showcase Panel -->
            <div class="auth-showcase-side">
                <div>
                    <div>
                        <span class="auth-showcase-badge">
                            <i class="fa fa-shield me-1 text-info"></i> KEAMANAN AKUN TERJAMIN
                        </span>
                    </div>
                    <h2 class="auth-showcase-title">Pemulihan Akun Aman &amp; Terverifikasi</h2>
                    <p class="auth-showcase-subtitle">POSHUB menerapkan otentikasi bertingkat untuk memastikan perlindungan penuh terhadap data bisnis dan aset finansial Anda.</p>

                    <!-- Feature Grid -->
                    <div class="auth-feature-grid">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-envelope-open"></i>
                            </div>
                            <div class="auth-feature-heading">Verifikasi Email</div>
                            <p class="auth-feature-desc">Kode unik sekali pakai (OTP) dikirim langsung ke email resmi terdaftar.</p>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-lock"></i>
                            </div>
                            <div class="auth-feature-heading">Enkripsi Password</div>
                            <p class="auth-feature-desc">Password diamankan dengan algoritma hashing bcrypt berstandar industri.</p>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-history"></i>
                            </div>
                            <div class="auth-feature-heading">Audit Trail</div>
                            <p class="auth-feature-desc">Setiap aktivitas pemulihan kata sandi tercatat otomatis di log sistem.</p>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-life-ring"></i>
                            </div>
                            <div class="auth-feature-heading">Bantuan 24/7</div>
                            <p class="auth-feature-desc">Tim dukungan teknis siap membantu jika ada kendala akses akun.</p>
                        </div>
                    </div>
                </div>

                <!-- Showcase Footer -->
                <div class="auth-showcase-footer">
                    <span class="auth-security-pill">
                        <i class="fa fa-lock me-1"></i> Bank-Grade 256-Bit Data Security
                    </span>
                    <span class="text-white-50">High-Availability Cloud Architecture</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ApiData } from "@/api/server";
import NProgress from "nprogress";
import appLogo from "@/assets/images/logo.webp";
import { TokenService } from "@/services";

export default {
    name: "forgetpass",
    data() {
        return {
            showNewPassword: false,
            showConfirmPassword: false,
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
        backToEmailStep() {
            this.stage.second = false;
            this.stage.first = true;
        },

        resendResetCode() {
            this.sendResetCode();
        },

        sendResetCode() {
            this.$refs.ValidationCodeReset.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Terjadi kesalahan",
                        detail: "Silahkan periksa kembali form inputan Anda",
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
                        detail: "Silahkan periksa kembali form inputan Anda",
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
                        detail: "Silahkan periksa kembali form inputan Anda",
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
