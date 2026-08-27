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
                <span class="login100-form-title"> Daftar Akun </span>

                <Form
                    @submit="ValidationSignup()"
                    ref="ValidationSignup"
                    class="validate-form row"
                >
                    <div class="col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label for="emailAddress">Nama Lengkap</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                :name="'Nama Lengkap'"
                                v-model="user.name"
                            >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="user.name"
                                    placeholder="Masukkan Nama Anda"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-group">
                            <label for="emailAddress">Alamat Email</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                :name="'Alamat Email'"
                                v-model="user.email"
                            >
                                <input
                                    class="form-control"
                                    type="email"
                                    v-model="user.email"
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
                            <label for="phoneNumber">Nomor WhatsApp</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                :name="'Nomor WhatsApp'"
                                v-model="user.phone"
                            >
                                <input
                                    class="form-control"
                                    type="number"
                                    v-model="user.phone"
                                    placeholder="Masukkan Nomor Anda"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-group">
                            <label for="gender">Jenis Kelamin</label>
                            <select
                                class="form-control"
                                name="jk"
                                id="gender"
                                v-model="user.jk"
                            >
                                <option value="pria">Pria</option>
                                <option value="wanita">Wanita</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                :name="'Password'"
                                v-model="user.password"
                            >
                                <input
                                    class="form-control"
                                    type="password"
                                    v-model="user.password"
                                    placeholder="Masukkan Password"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-6">
                        <div class="form-group">
                            <label for="password">Konfirmasi Password</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                :name="'Konfirmasi Password'"
                                v-model="user.password_confirmation"
                            >
                                <input
                                    class="form-control"
                                    type="password"
                                    v-model="user.password_confirmation"
                                    placeholder="Konfirmasi Password"
                                />
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
                                    : "Daftar Sekarang"
                            }}
                        </button>
                    </div>

                    <div class="col-12 text-center mt-2">
                        <div class="sign-info">
                            <span
                                class="dark-color d-inline-block line-height-2"
                                >Kembali Ke Halaman
                                <router-link :to="{ name: 'login' }"
                                    >Login</router-link
                                ></span
                            >
                        </div>
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
    name: "register",
    data() {
        return {
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
