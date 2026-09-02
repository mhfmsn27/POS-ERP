<template>
    <div class="auth-page-wrapper">
        <div class="auth-split-container auth-split-container-wide">
            <!-- Left Side: Form Panel -->
            <div class="auth-form-side">
                <!-- Brand Header -->
                <div class="auth-brand-header text-start mb-3">
                    <router-link :to="{ name: 'login' }" class="d-inline-block text-decoration-none">
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
                <div class="mb-3">
                    <h1 class="auth-form-title text-start mb-1">Daftar Akun Enterprise</h1>
                    <p class="auth-form-subtitle text-start mb-0">Buat akun staf atau pemilik baru untuk mengelola transaksi POS dan laporan keuangan.</p>
                </div>

                <Form
                    @submit="ValidationSignup()"
                    ref="ValidationSignup"
                    class="validate-form row g-3"
                >
                    <!-- Nama Lengkap -->
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group mb-0">
                            <label for="fullName" class="auth-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="position-relative">
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
                                        class="form-control auth-input-with-icon"
                                        type="text"
                                        v-model="user.name"
                                        placeholder="contoh: Budi Pratama"
                                        autofocus
                                    />
                                    <div class="fs-sm text-danger mt-1" v-if="errors && errors.length">
                                        <i class="fa fa-exclamation-circle me-1"></i>{{ errors[0] }}
                                    </div>
                                </Field>
                                <span class="auth-field-icon">
                                    <i class="fa fa-user"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Email -->
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group mb-0">
                            <label for="emailAddr" class="auth-label">Alamat Email <span class="text-danger">*</span></label>
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
                                        id="emailAddr"
                                        class="form-control auth-input-with-icon"
                                        type="email"
                                        v-model="user.email"
                                        placeholder="contoh: budi@poshub.id"
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
                    </div>

                    <!-- Nomor WhatsApp / HP -->
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group mb-0">
                            <label for="phoneNumber" class="auth-label">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                            <div class="position-relative">
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
                                        class="form-control auth-input-with-icon"
                                        type="tel"
                                        v-model="user.phone"
                                        placeholder="contoh: 081234567890"
                                    />
                                    <div class="fs-sm text-danger mt-1" v-if="errors && errors.length">
                                        <i class="fa fa-exclamation-circle me-1"></i>{{ errors[0] }}
                                    </div>
                                </Field>
                                <span class="auth-field-icon">
                                    <i class="fa fa-phone"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group mb-0">
                            <label for="gender" class="auth-label">Jenis Kelamin</label>
                            <div class="position-relative">
                                <select
                                    class="form-control auth-input-with-icon"
                                    name="jk"
                                    id="gender"
                                    v-model="user.jk"
                                >
                                    <option value="pria">Laki-laki (Pria)</option>
                                    <option value="wanita">Perempuan (Wanita)</option>
                                </select>
                                <span class="auth-field-icon">
                                    <i class="fa fa-venus-mars"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Kata Sandi -->
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group mb-0">
                            <label for="regPassword" class="auth-label">Kata Sandi <span class="text-danger">*</span></label>
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
                                        id="regPassword"
                                        class="form-control auth-input-with-icon auth-input-with-toggle"
                                        :type="showPassword ? 'text' : 'password'"
                                        v-model="user.password"
                                        placeholder="Minimal 8 karakter"
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
                    </div>

                    <!-- Konfirmasi Kata Sandi -->
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group mb-0">
                            <label for="confirmPassword" class="auth-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors, field }"
                                    :name="'Konfirmasi Kata Sandi'"
                                    v-model="user.password_confirmation"
                                >
                                    <input
                                        id="confirmPassword"
                                        class="form-control auth-input-with-icon auth-input-with-toggle"
                                        :type="showConfirmPassword ? 'text' : 'password'"
                                        v-model="user.password_confirmation"
                                        placeholder="Ulangi kata sandi"
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
                    </div>

                    <!-- Submit Button -->
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
                                    : "Daftar Akun Baru Sekarang"
                            }}
                        </button>
                    </div>

                    <!-- Footer Link to Login -->
                    <div class="col-12 auth-footer-text text-start mt-3 pt-2 border-top">
                        <p class="mb-1 text-muted" style="font-size: 13.5px;">
                            Sudah memiliki akun operasional?
                            <router-link :to="{ name: 'login' }" class="auth-link fw-bold ms-1">
                                Masuk ke Akun &rarr;
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
                            <i class="fa fa-check-circle me-1 text-success"></i> PENDAFTARAN CEPAT &amp; MUDAH
                        </span>
                    </div>
                    <h2 class="auth-showcase-title">Mulai Kelola Bisnis Anda dengan POSHUB Enterprise</h2>
                    <p class="auth-showcase-subtitle">Satu akun untuk mengontrol seluruh cabang toko, kelola kasir POS, sinkronisasi gudang stok, dan otomatisasi akuntansi.</p>

                    <!-- Feature Grid -->
                    <div class="auth-feature-grid">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-bolt"></i>
                            </div>
                            <div class="auth-feature-heading">Setup Instan</div>
                            <p class="auth-feature-desc">Langsung aktif dalam hitungan detik setelah verifikasi email.</p>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-users"></i>
                            </div>
                            <div class="auth-feature-heading">Multi-Staff &amp; Role</div>
                            <p class="auth-feature-desc">Atur hak akses kasir, manajer gudang, staf akuntan dan owner.</p>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-database"></i>
                            </div>
                            <div class="auth-feature-heading">Cloud Terintegrasi</div>
                            <p class="auth-feature-desc">Data transaksi &amp; persediaan tersinkron otomatis real-time.</p>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="fa fa-lock"></i>
                            </div>
                            <div class="auth-feature-heading">Keamanan Terjamin</div>
                            <p class="auth-feature-desc">Enkripsi data berlapis dan proteksi sesi multi-tenant aman.</p>
                        </div>
                    </div>
                </div>

                <!-- Showcase Footer -->
                <div class="auth-showcase-footer">
                    <span class="auth-security-pill">
                        <i class="fa fa-shield me-1"></i> Data Protection Guaranteed
                    </span>
                    <span class="text-white-50">Enterprise 99.9% Uptime SLA</span>
                </div>
            </div>
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
            showPassword: false,
            showConfirmPassword: false,
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
                        detail: "Silahkan periksa kembali form inputan Anda",
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
