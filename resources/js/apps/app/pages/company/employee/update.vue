<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-3">
                <h4 class="card-title">Edit Pegawai</h4>
            </div>
            <Form
                @submit="Validationemployees()"
                ref="employeeValidation"
                class="card-body p-4"
            >
                <div class="row">
                    <!-- Toko -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Pilih Pengguna</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="employee.user"
                            name="Pengguna"
                        >
                            <Multiselect
                                v-model="employee.user"
                                :options="users"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :allowEmpty="false"
                                :options-limit="50"
                                placeholder="Pilih Pengguna"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getUsers"
                            ></Multiselect>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Toko -->

                    <!-- Role Pengguna -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Pilih Devisi</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="employee.department"
                            name="Devisi"
                        >
                            <Multiselect
                                v-model="employee.department"
                                :options="departments"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :allowEmpty="false"
                                :options-limit="50"
                                placeholder="Pilih Devisi"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @select="getDesignation('')"
                                @search-change="getDepartments"
                            ></Multiselect>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Role -->

                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Pilih Jabatan</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="employee.designation"
                            name="Jabatan"
                        >
                            <Multiselect
                                v-model="employee.designation"
                                :options="designations"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="true"
                                :allowEmpty="false"
                                :options-limit="50"
                                placeholder="Pilih Jabatan"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getDesignation"
                            ></Multiselect>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <!-- Name -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Gaji Pegawai</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="employee.salary"
                            name="Gaji Pegawai"
                        >
                            <InputNumber
                                style="width: 100%"
                                v-model="employee.salary"
                                prefix="Rp "
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Name -->

                    <!-- Email -->
                    <div class="col-12 mt-4">
                        <label for="category-product">Alamat Pegawai</label>
                        <textarea
                            class="form-control"
                            v-model="employee.address"
                        ></textarea>
                    </div>
                    <!-- End Email -->

                    <div class="col-12 d-flex justify-content-end mt-4">
                        <button
                            class="btn btn-primary"
                            type="submit"
                            :disabled="loader.submit"
                        >
                            {{
                                loader.submit
                                    ? "Mohon Tunggu"
                                    : "Tambahkan Pegawai"
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
            users: [],
            departments: [],
            designations: [],
            loader: {
                submit: false,
                data: false,
            },
            employee: {
                user: {
                    id: "",
                    name: "",
                },
                department: {
                    id: "",
                    name: "",
                },
                designation: {
                    id: "",
                    name: "",
                },
                salary: 0,
                address: "",
            },
        };
    },
    methods: {
        async getData() {
            try {
                const response = await ApiData.get(
                    `app/master/employees/detail/${this.$route.params.id}`
                );
                var data = response.data;
                this.employee = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getUsers(query) {
            this.loader.data = true;

            try {
                const response = await ApiData.get(
                    `app/settings/users?name=${query}`
                );
                var data = response.data;
                this.users = data.users;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getDepartments(query) {
            try {
                const response = await ApiData.get(
                    `app/master/departments?name=${query}`
                );
                var data = response.data;
                this.departments = data.departments;
            } catch (error) {
                console.log(error);
            }
        },

        async getDesignation(query) {
            try {
                const response = await ApiData.get(
                    `app/master/designations?name=${query}&department=${this.employee.department?.id}`
                );
                var data = response.data;
                this.designations = data.designations;
            } catch (error) {
                console.log(error);
            }
        },

        Validationemployees() {
            this.$refs.employeeValidation.validate().then((success) => {
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
            ApiData.post(
                `app/master/employees/update/${this.$route.params.id}`,
                this.employee
            )
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
        this.getData();
        this.getDepartments("");
        this.getUsers("");
    },
};
</script>
