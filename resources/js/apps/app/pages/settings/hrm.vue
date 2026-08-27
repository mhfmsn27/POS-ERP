<template>
    <!-- Create Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Pengaturan Hrm</h4>
            </div>
            <Form @submit="validationSettings()" ref="settingValidation">
                <div class="card-body">
                    <div class="row">
                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Jam Minimal Presensi</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.min_check_int"
                                name="Jam Minimal Masuk"
                            >
                                <InputText
                                    v-model="form.min_check_int"
                                    style="width: 100%"
                                    type="time"
                                    class="form-control"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Jam Maximal Presensi</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.max_check_int"
                                name="Jam Maximal Masuk"
                            >
                                <InputText
                                    v-model="form.max_check_int"
                                    style="width: 100%"
                                    type="time"
                                    class="form-control"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Jam Minimal Presensi Pulang</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.min_check_out"
                                name="Presensi Pulang"
                            >
                                <InputText
                                    v-model="form.min_check_out"
                                    style="width: 100%"
                                    type="time"
                                    class="form-control"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Biarkan Telat agar tetap dapat Presensi</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.attendance_in_late"
                                name="Telat Presensi"
                            >
                                <select
                                    class="form-control"
                                    v-model="form.attendance_in_late"
                                >
                                    <option value="yes">Iya</option>
                                    <option value="no">Tidak</option>
                                </select>
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Hubungkan Tunjangan Dengan Presensi</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.attendance_to_salary"
                                name="Tunjangan dengan Presensi"
                            >
                                <select
                                    class="form-control"
                                    v-model="form.attendance_to_salary"
                                >
                                    <option value="yes">Iya</option>
                                    <option value="no">Tidak</option>
                                </select>
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product"
                                >Hubungkan Potongan Gaji dengan Presensi</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.attendance_to_cutting"
                                name="Hubungkan Potongan Gaji"
                            >
                                <select
                                    class="form-control"
                                    v-model="form.attendance_to_cutting"
                                >
                                    <option value="yes">Iya</option>
                                    <option value="no">Tidak</option>
                                </select>
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form -->

                        <!-- Form -->
                        <div class="col-lg-6 col-sm-12 mb-4">
                            <label for="category-product">Pajak Gaji Karyawan</label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="form.salary_tax"
                                name="Pajak Gaji Karyawan"
                            >
                                <InputText
                                    v-model="form.salary_tax"
                                    style="width: 100%"
                                    type="number"
                                    :max="100"
                                    class="form-control" 
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Form --> 
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button
                        type="submit"
                        :disabled="loader.submit"
                        class="btn label-btn label-end btn-primary"
                    >
                        {{
                            loader.submit
                                ? "Mohon Tunggu...."
                                : "Simpan Perubahan"
                        }}
                        <i class="fe fe-save label-btn-icon ms-2"></i>
                    </button>
                </div>
            </Form>
        </div>
    </div>
    <!-- End Create Data -->
</template>

<script>
import { ApiData } from "@/api/server";
import NProgress from "nprogress";

var _ = require("lodash");

export default {
    name: "KeySetting",
    data() {
        return {
            form: {
                min_check_out: "00:00",
                max_check_int: "00:00",
                min_check_int: "",
                attendance_to_salary: "",
                attendance_in_late: "",
                attendance_to_cutting: "",
                salary_tax: 0,
            },
            loader: {
                submit: false,
            },
        };
    },
    methods: {
        async getData() {
            try {
                const response = await ApiData.get(`app/settings/hrm`);
                var data = response.data;
                this.form = data;
            } catch (error) {
                console.log(error);
            }
        },

        validationSettings() {
            this.$refs.settingValidation.validate().then((success) => {
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
                    this.updateData();
                }
            });
        },

        updateData() {
            ApiData.post("app/settings/hrm/store", this.form)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },
    },
    mounted: function () {
        this.getData();
    },
    watch: {},
};
</script>
