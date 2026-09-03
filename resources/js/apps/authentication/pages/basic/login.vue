<template>
    <div class="auth-page-wrapper">
        <!-- Top Navigation Link -->
        <div class="auth-nav-top">
            <a href="/" class="auth-link-back">
                <i class="fa fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>

        <div class="auth-split-container">
            <!-- Left Side: Form Panel -->
            <div class="auth-form-side">
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
                        <span class="auth-brand-badge">Sistem Kasir &amp; Akuntansi Enterprise</span>
                    </div>
                </div>

                <!-- Title & Subtitle -->
                <div class="mb-4">
                    <h1 class="auth-form-title text-start mb-1">Masuk ke Akun Anda</h1>
                    <p class="auth-form-subtitle text-start mb-0">Silakan masukkan kredensial resmi untuk mengakses panel operasional.</p>
                </div>

                <Form
                    @submit="loginValidation()"
                    ref="ValidationSignin"
                    class="validate-form"
                >
                    <!-- Email Input -->
                    <div class="form-group mb-3">
                        <label for="emailInput" class="auth-label">Alamat Email <span class="text-danger">*</span></label>
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
                                    id="emailInput"
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

                    <!-- Password Input -->
                    <div class="form-group mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="passwordInput" class="auth-label mb-0">Kata Sandi <span class="text-danger">*</span></label>
                            <router-link
                                :to="{ name: 'forgetpass' }"
                                class="auth-link fw-semibold"
                                style="font-size: 12.5px;"
                            >
                                Lupa Sandi?
                            </router-link>
                        </div>
                        <div class="position-relative">
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                :name="'Kata Sandi'"
                                v-model="user.password"
                            >
                                <input
                                    id="passwordInput"
                                    class="form-control auth-input-with-icon auth-input-with-toggle"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="user.password"
                                    placeholder="Masukkan kata sandi"
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
                                @click="showPassword = !showPassword"
                                tabindex="-1"
                                :title="showPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                            >
                                <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check d-flex align-items-center mb-0">
                            <input class="form-check-input me-2" type="checkbox" id="rememberMe" v-model="user.remember">
                            <label class="form-check-label" for="rememberMe">
                                Ingat Sesi Saya
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button
                            type="submit"
                            :disabled="loader.submit"
                            class="btn-auth-primary"
                        >
                            <i v-if="loader.submit" class="fa fa-spinner fa-spin me-2"></i>
                            <i v-else class="fa fa-sign-in me-2"></i>
                            {{
                                loader.submit
                                    ? "Memproses Autentikasi..."
                                    : "Masuk ke Sistem"
                            }}
                        </button>
                    </div>

                    <!-- Footer Link to Register -->
                    <div class="auth-footer-text text-start mt-4 pt-2 border-top">
                        <p class="mb-1 text-muted" style="font-size: 13.5px;">
                            Belum memiliki akun operasional?
                            <router-link
                                :to="{ name: 'register' }"
                                class="auth-link fw-bold ms-1"
                            >
                                Daftar Sekarang &rarr;
                            </router-link>
                        </p>
                        <small class="text-muted d-block mt-2" style="font-size: 11.5px;">
                            &copy; POSHUB ENTERPRISE. Hak Cipta Dilindungi.
                        </small>
                    </div>
                </Form>
            </div>

            <!-- Right Side: Feature Showcase Panel -->
            <div class="auth-showcase-side">
                <div>
                    <div>
                        <span class="auth-showcase-badge">
                            <i class="fa fa-shield me-1 text-info"></i> POSHUB ENTERPRISE ECOSYSTEM
                        </span>
                    </div>
                    <h2 class="auth-showcase-title">Solusi Terpadu Kasir (POS), Akuntansi &amp; Omnichannel</h2>
                    <p class="auth-showcase-subtitle">Platform all-in-one untuk efisiensi transaksi kasir, otomasi pembukuan finansial, sinkronisasi stok multi-gudang, dan e-commerce.</p>

                    <!-- Feature Grid -->
                    <div class="auth-feature-grid">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-shopping-bag"></i>
                            </div>
                            <div class="auth-feature-heading">Point of Sale Kasir</div>
                            <p class="auth-feature-desc">Kasir cepat, kitchen &amp; customer display, receipt thermal dan WhatsApp.</p>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-file-text-o"></i>
                            </div>
                            <div class="auth-feature-heading">Akuntansi Otomatis</div>
                            <p class="auth-feature-desc">Jurnal otomatis, neraca, laba rugi real-time, dan rekonsiliasi kas/bank.</p>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-cubes"></i>
                            </div>
                            <div class="auth-feature-heading">Multi-Gudang &amp; Cabang</div>
                            <p class="auth-feature-desc">Pantau stok real-time, transfer inventori, dan opname terpusat.</p>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-globe"></i>
                            </div>
                            <div class="auth-feature-heading">Omnichannel Storefront</div>
                            <p class="auth-feature-desc">Toko online e-commerce langsung tersinkron ke inventori POS &amp; payment.</p>
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
import NProgress from "nprogress";
import appLogo from "@/assets/images/logo.webp";

export default {
    name: "login",
    data() {
        return {
            showPassword: false,
            loader: {
                submit: false,
            },
            asset: {
                logo: appLogo,
            },
            user: {
                email: "",
                password: "",
            },
        };
    },
    methods: {
        loginValidation() {
            this.$refs.ValidationSignin.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Terjadi kesalahan",
                        detail: "Silahkan periksa kembali form inputan Anda",
                        life: 3000,
                    });
                } else {
                    this.signIntAccount();
                }
            });
        },

        async signIntAccount() {
            this.loader.submit = true;

            NProgress.start();
            NProgress.set(0.1);

            try {
                const response = await this.$store.dispatch(
                    "auth/signInt",
                    this.user
                );

                this.$handleSuccessResponse(response.message);

                NProgress.done();

                if (response.data.verify == false) {
                    return this.$router.push({
                        name: "verify",
                    });
                }

                if (response.data.merchant == false) {
                    return this.$router.push({
                        name: "business_register",
                    });
                }

                return (window.location = "/starter");
            } catch (error) {
                NProgress.done();
                this.loader.submit = false;
                this.$handleErrorResponse(error);
            }
        },
    },
};
</script>
