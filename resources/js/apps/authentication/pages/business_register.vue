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
        <div class="container-login100 register">
            <div class="wrap-login100 p-6">
                <span class="login100-form-title"> Daftarkan Bisnis </span>

                <Form
                    @submit="ValidationSignupBusiness()"
                    ref="ValidationSignupBusiness"
                    class="validate-form row"
                >
                    <div class="col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label for="emailAddress">Nama Bisnis</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                :name="'Nama Bisnis'"
                                v-model="bisnis.name"
                            >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="bisnis.name"
                                    placeholder="Masukkan Nama Bisnis Anda"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-group">
                            <label for="emailAddress"
                                >Alamat Email Bisnis</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                :name="'Alamat Email Bisnis'"
                                v-model="bisnis.email"
                            >
                                <input
                                    class="form-control"
                                    type="email"
                                    v-model="bisnis.email"
                                    placeholder="Masukkan Alamat Email"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-group">
                            <label for="phoneNumber">Nomor Bisnis</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                :name="'Nomor Bisnis'"
                                v-model="bisnis.phone"
                            >
                                <input
                                    class="form-control"
                                    type="number"
                                    v-model="bisnis.phone"
                                    placeholder="Masukkan Nomor"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-group">
                            <label for="gender">Penggunaan Akuntansi</label>
                            <select
                                class="form-control"
                                name="accountant_use"
                                id="gender"
                                v-model="bisnis.accountant_use"
                            >
                                <option value="yes">Gunakan</option>
                                <option value="no">Tidak Menggunakan</option>
                            </select>
                        </div>
                    </div>

                    <div
                        class="col-sm-12 col-lg-6"
                        v-if="bisnis.accountant_use"
                    >
                        <div class="form-group">
                            <label for="gender">Penggunaan Pajak</label>
                            <select
                                class="form-control"
                                name="accountant_use"
                                id="gender"
                                v-model="bisnis.tax_option"
                            >
                                <option value="yes">Gunakan</option>
                                <option value="no">Tidak Menggunakan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="password">Alamat Bisnis</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                :name="'Alamat Bisnis'"
                                v-model="bisnis.address"
                            >
                                <textarea
                                    class="form-control"
                                    v-model="bisnis.address"
                                ></textarea>
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-center mt-4">
                        <button
                            type="submit"
                            :disabled="loader.submit"
                            class="btn btn-primary btn-block float-right"
                        >
                            {{
                                loader.submit
                                    ? "Mohon Tunggu...."
                                    : "Daftarkan Bisnis"
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
