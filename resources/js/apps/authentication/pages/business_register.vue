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
                    <span class="auth-brand-badge">Pendaftaran Profil Usaha</span>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="auth-form-title">Registrasi Profil Bisnis &amp; Toko</h1>
            <p class="auth-form-subtitle">Lengkapi identitas badan usaha dan preferensi sistem akuntansi untuk toko baru Anda.</p>

            <Form
                @submit="ValidationSignupBusiness()"
                ref="ValidationSignupBusiness"
                class="validate-form row g-3"
            >
                <div class="col-lg-6 col-sm-12">
                    <div class="form-group mb-0">
                        <label for="bizName" class="auth-label">Nama Bisnis / Usaha <span class="text-danger">*</span></label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Nama Bisnis'"
                            v-model="bisnis.name"
                        >
                            <input
                                id="bizName"
                                class="form-control no-icon"
                                type="text"
                                v-model="bisnis.name"
                                placeholder="contoh: PT Maju Bersama"
                            />
                            <div class="fs-sm text-danger mt-1">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                </div>

                <div class="col-sm-12 col-lg-6">
                    <div class="form-group mb-0">
                        <label for="bizEmail" class="auth-label">Alamat Email Bisnis <span class="text-danger">*</span></label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Alamat Email Bisnis'"
                            v-model="bisnis.email"
                        >
                            <input
                                id="bizEmail"
                                class="form-control no-icon"
                                type="email"
                                v-model="bisnis.email"
                                placeholder="contoh: info@perusahaan.com"
                            />
                            <div class="fs-sm text-danger mt-1">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                </div>

                <div class="col-sm-12 col-lg-6">
                    <div class="form-group mb-0">
                        <label for="bizPhone" class="auth-label">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Nomor Bisnis'"
                            v-model="bisnis.phone"
                        >
                            <input
                                id="bizPhone"
                                class="form-control no-icon"
                                type="tel"
                                v-model="bisnis.phone"
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
                        <label for="accUse" class="auth-label">Modul Akuntansi &amp; Buku Besar</label>
                        <select
                            class="form-control no-icon"
                            name="accountant_use"
                            id="accUse"
                            v-model="bisnis.accountant_use"
                        >
                            <option value="yes">Aktifkan Modul Akuntansi</option>
                            <option value="no">Non-Aktifkan (Mode Kasir Saja)</option>
                        </select>
                    </div>
                </div>

                <div
                    class="col-sm-12 col-lg-6"
                    v-if="bisnis.accountant_use == 'yes'"
                >
                    <div class="form-group mb-0">
                        <label for="taxOpt" class="auth-label">Pengaturan Pajak (PPN)</label>
                        <select
                            class="form-control no-icon"
                            name="tax_option"
                            id="taxOpt"
                            v-model="bisnis.tax_option"
                        >
                            <option value="yes">Gunakan Pajak Standar</option>
                            <option value="no">Tidak Menggunakan Pajak</option>
                        </select>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group mb-0">
                        <label for="bizAddress" class="auth-label">Alamat Kantor / Outlet Utama <span class="text-danger">*</span></label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Alamat Bisnis'"
                            v-model="bisnis.address"
                        >
                            <textarea
                                id="bizAddress"
                                class="form-control no-icon"
                                rows="3"
                                placeholder="Masukkan alamat lengkap kantor atau lokasi outlet utama"
                                v-model="bisnis.address"
                            ></textarea>
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
                        <i v-else class="fa fa-building me-2"></i>
                        {{
                            loader.submit
                                ? "Menyimpan Profil Usaha..."
                                : "Simpan &amp; Daftarkan Bisnis"
                        }}
                    </button>
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
    name: "business_register",
    data() {
        return {
            loader: {
                submit: false,
            },
            asset: {
                logo: appLogo,
            },
            bisnis: {
                name: "",
                tax_option: "no",
                accountant_use: "yes",
                address: "",
                phone: "",
                email: "",
            },
        };
    },
    methods: {
        ValidationSignupBusiness() {
            this.$refs.ValidationSignupBusiness.validate().then((success) => {
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
            ApiData.post("authentication/business-register/store", this.bisnis)
                .then((response) => {
                    setTimeout(() => {
                        NProgress.done(); 
                        TokenService.saveMerchant(1);
                        return (window.location = "/starter"); 
                    }, 500);
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
        }, 
    },
};
</script>
