<template>
    <div class="row mt-4">
        <div class="col-12">
            <div class="container-login100 register">
                <div class="wrap-login100 p-6">
                    <span class="login100-form-title"> Tambahkan Toko </span>

                    <Form
                        @submit="ValidationAddStore()"
                        ref="ValidationAddStore"
                        class="validate-form row"
                    >
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label for="emailAddress"
                                    >Nama Toko atau Cabang</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors, field }"
                                    :name="'Nama Toko'"
                                    v-model="bisnis.name"
                                >
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="bisnis.name"
                                        placeholder="Masukkan Nama Toko atau Cabang"
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
                                    >Alamat Email Toko</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors, field }"
                                    :name="'Alamat Email Toko'"
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
                                <label for="phoneNumber">Nomor Hp Toko</label>
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors, field }"
                                    :name="'Nomor Toko'"
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
                                    <option value="no">
                                        Tidak Menggunakan
                                    </option>
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
                                    @change="changeTaxOption"
                                    v-model="bisnis.tax_option"
                                >
                                    <option value="yes">Gunakan</option>
                                    <option value="no">
                                        Tidak Menggunakan
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="password"
                                    >Alamat Toko atau Cabang</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors, field }"
                                    :name="'Alamat Toko atau Cabang'"
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
                                        : "Tambahkan Toko"
                                }}
                            </button>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NProgress from "nprogress";
import { ApiData } from "@/api/server"; 
export default {
    name: "create_store",
    components: {},
    data() {
        return {
            loader: {
                submit: false,
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
    mounted() {},
    methods: {
        ValidationAddStore() {
            this.$refs.ValidationAddStore.validate().then((success) => {
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

        changeTaxOption() {
            if(this.bisnis.tax_option == 'yes' && this.bisnis.accountant_use == 'no') {
                this.bisnis.accountant_use = 'yes';
            }
        },

        CreateAccount() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1); 
            ApiData.post("app/stores/store", this.bisnis, false)
                .then((response) => {
                    setTimeout(() => {
                        NProgress.done();
                        return this.$router.push({
                            name: "choose_store",
                        });
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
