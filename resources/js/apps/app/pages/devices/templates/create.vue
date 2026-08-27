<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-3">
                <h4 class="card-title">Tambah Template</h4>
            </div>
            <Form
                @submit="ValidationTemplate()"
                ref="TemplateValidation"
                class="card-body p-4"
            >
                <div class="row">
                    <!-- Name -->
                    <div class="col-12 mt-4">
                        <label for="category-product">Nama Template</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="template.name"
                            name="Nama Template"
                        >
                            <InputText
                                v-model="template.name"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                                placeholder="Masukkan Nama Template"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Name -->

                    <!-- Email -->
                    <div class="col-12 mt-4">
                        <label for="category-product">Isi Template Pesan</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="template.message"
                            name="Isi Template"
                        >
                            <textarea
                                class="form-control"
                                v-model="template.message"
                                style="height: 300px"
                            ></textarea>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
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
                                    : "Tambahkan Template"
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
            loader: {
                submit: false,
                data: false,
            },
            template: {
                name: "",
                message: "",
            },
        };
    },
    methods: {
        ValidationTemplate() {
            this.$refs.TemplateValidation.validate().then((success) => {
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
            ApiData.post("app/settings/templates/create", this.template)
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
    mounted() {},
};
</script>
