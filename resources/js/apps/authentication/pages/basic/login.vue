<template>
    <div class="auth-page-wrapper">
        <div class="auth-split-container">
            <!-- Left Side: Form Panel -->
            <div class="auth-form-side">
                <!-- Brand Header -->
                <div class="auth-brand-header text-start mb-4">
                    <router-link :to="{ name: 'login' }" class="d-inline-block">
                        <img
                            v-lazy="asset.logo"
                            class="auth-brand-logo"
                            alt="POSHUB Enterprise"
                        />
                    </router-link>
                    <div>
                        <span class="auth-brand-badge">Sistem Kasir &amp; Akuntansi Enterprise</span>
                    </div>
                </div>

                <!-- Title & Subtitle -->
                <h1 class="auth-form-title text-start mb-1">Masuk ke Akun Anda</h1>
                <p class="auth-form-subtitle text-start mb-4">Silakan masukkan email dan kata sandi resmi untuk mengakses panel operasional.</p>

                <Form
                    @submit="loginValidation()"
                    ref="ValidationSignin"
                    class="validate-form"
                >
                    <div
                        class="wrap-input100 validate-input"
                        data-validate="Valid email is required: ex@abc.xyz"
                    >
                        <label for="emailInput" class="auth-label">Alamat Email</label>
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
                                    class="input100"
                                    type="email"
                                    v-model="user.email"
                                    placeholder="contoh: admin@poshub.id"
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

                    <div
                        class="wrap-input100 validate-input"
                        data-validate="Password is required"
                    >
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="passwordInput" class="auth-label mb-0">Kata Sandi</label>
                            <router-link
                                :to="{ name: 'forgetpass' }"
                                class="auth-link"
                                style="font-size: 12.5px;"
                                >Lupa Sandi?</router-link
                            >
                        </div>
                        <div class="position-relative">
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                :name="'Password'"
                                v-model="user.password"
                            >
                                <input
                                    id="passwordInput"
                                    class="input100"
                                    type="password"
                                    v-model="user.password"
                                    placeholder="Masukkan kata sandi"
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

                    <div class="container-login100-form-btn mt-2">
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

                    <div class="auth-footer-text text-start">
                        <p class="mb-0">
                            Belum memiliki akun operasional?
                            <router-link
                                :to="{ name: 'register' }"
                                class="auth-link"
                                >Daftar Sekarang</router-link
                            >
                        </p>
                        <small class="text-muted d-block mt-2" style="font-size: 11px;">
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
                        detail: "Silahkan Check kembali form inputan anda",
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
