<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-3">
                <h4 class="card-title">Tambah Pengguna</h4>
            </div>
            <Form
                @submit="ValidationUsers()"
                ref="UserValidation"
                class="card-body p-4"
            >
                <div class="row">
                    <!-- Toko -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Pilih Akses Toko</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="user.stores"
                            name="Akses Toko"
                        >
                            <Multiselect
                                v-model="user.stores"
                                :options="stores"
                                :multiple="true"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :options-limit="50"
                                placeholder="Pilih Toko"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getStores"
                            ></Multiselect>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Toko -->

                    <!-- Role Pengguna -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Role Pengguna</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="user.role"
                            name="Role"
                        >
                            <Dropdown
                                v-model="user.role"
                                :options="roles"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Pilih Role"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Role -->

                    <!-- Name -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Nama Pengguna</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="user.name"
                            name="Nama Pengguna"
                        >
                            <InputText
                                v-model="user.name"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                                placeholder="Masukkan Nama Lengkap"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Name -->

                    <!-- Email -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Email Pengguna</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="user.email"
                            name="Email Pengguna"
                        >
                            <InputText
                                v-model="user.email"
                                style="width: 100%"
                                type="email"
                                class="form-control"
                                placeholder="Masukkan Alamat Email"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Email -->

                    <!-- Wa -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product"
                            >No.Whatsapp Pengguna</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="user.phone"
                            name="No.Whatsapp"
                        >
                            <InputText
                                v-model="user.phone"
                                style="width: 100%"
                                type="number"
                                class="form-control"
                                placeholder="Masukkan No Whatsapp"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Wa -->

                    <!-- Password -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Password Pengguna</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="user.password"
                            name="Password Pengguna"
                        >
                            <InputText
                                v-model="user.password"
                                style="width: 100%"
                                type="password"
                                class="form-control"
                                placeholder="Masukkan Password"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Password -->

                    <!-- Gender -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Jenis Kelamin</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="user.jk"
                            name="Jenis Kelamin"
                        >
                            <Dropdown
                                v-model="user.jk"
                                :options="[
                                    {
                                        name: 'Laki-Laki',
                                        value: 'pria',
                                    },
                                    {
                                        name: 'Perempuan',
                                        value: 'wanita',
                                    },
                                ]"
                                optionLabel="name"
                                optionValue="value"
                                placeholder="Pilih Opsi"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Gender -->

                    <!-- Commission -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="Pajak-name-add" class="form-label"
                            >Persentase Komisi</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="user.commission"
                            name="Persentase Komisi"
                        >
                            <InputNumber
                                style="width: 100%"
                                v-model="user.commission"
                                :max="100"
                                suffix=" %"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Commission -->

                    <!-- Max Commission -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="Pajak-name-add" class="form-label"
                            >Maximal Komisi</label
                        >
                        <InputNumber
                            style="width: 100%"
                            v-model="user.max_commission"
                            prefix="Rp "
                        />
                    </div>
                    <!-- End Max -->

                    <div class="col-12 d-flex justify-content-end mt-4">
                        <button
                            class="btn btn-primary"
                            type="submit"
                            :disabled="loader.submit"
                        >
                            {{
                                loader.submit
                                    ? "Mohon Tunggu"
                                    : "Tambahkan Pengguna"
                            }}
                        </button>
                    </div>
                </div>
            </Form>
        </div>
    </div>
    <!-- End List Data -->
</template>

<script>
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "type_list",
    data() {
        return {
            stores: [],
            roles: [],
            loader: {
                submit: false,
                data: false,
            },
            user: {
                stores: null,
                commission: 0,
                max_commission: 0,
                name: "",
                email: "",
                phone: "",
                password: "",
                jk: "pria",
                role: "",
            },
        };
    },
    methods: {
        async getStores(query) {
            this.loader.data = true;

            try {
                const response = await ApiData.get(`app/stores?name=${query}`);
                var data = response.data;
                this.stores = data.stores;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getRoles(query) {
            try {
                const response = await ApiData.get(
                    `app/settings/roles?name=${query}`
                );
                var data = response.data;
                this.roles = data.roles;
            } catch (error) {
                console.log(error);
            }
        },

        ValidationUsers() {
            this.$refs.UserValidation.validate().then((success) => {
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
                    this.createData();
                }
            });
        },

        createData() {
            ApiData.post("app/settings/users/create", this.user)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.redirectData();
                    this.loader.submit = false;
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        redirectData() {
            window.parent.postMessage({
                action: "closeActiveMenu",
                data: "",
            });
        },
    },
    mounted() {
        this.getRoles("");
        this.getStores("");
    },
};
</script>
