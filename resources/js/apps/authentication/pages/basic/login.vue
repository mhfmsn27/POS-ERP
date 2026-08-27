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
                    @submit="loginValidation()"
                    ref="ValidationSignin"
                    class="login100-form validate-form"
                >
                    <span class="login100-form-title"> Login </span>
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
                    <div
                        class="wrap-input100 validate-input"
                        data-validate="Password is required"
                    >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors, field }"
                            :name="'Password'"
                            v-model="user.password"
                        >
                            <input
                                class="input100"
                                type="password"
                                v-model="user.password"
                                placeholder="Masukkan Password"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>
                    <div class="text-end pt-1">
                        <p class="mb-0">
                            <router-link
                                :to="{ name: 'forgetpass' }"
                                class="text-primary ms-1"
                                >Lupa Password?</router-link
                            >
                        </p>
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
                                    : "Login Sekarang"
                            }}
                        </button>
                    </div>
                    <div class="text-center pt-3">
                        <p class="text-dark mb-0">
                            Belum Punya Akun ?<router-link
                                :to="{ name: 'register' }"
                                class="text-primary mx-1"
                                >Daftar Sekarang</router-link
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
