<template>
    <!-- List Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-3">
                <h4 class="card-title">Edit Kategori</h4>
            </div>
            <Form
                @submit="Validationcategorys()"
                ref="categoryValidation"
                class="card-body p-4"
            >
                <div class="row">
                    <!-- Title -->
                    <div class="col-12 mt-4">
                        <label for="category-product">Nama Kategori</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="category.name"
                            name="Nama Kategori"
                        >
                            <InputText
                                v-model="category.name"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                                placeholder="Masukkan Nama Kategori"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Title -->

                    <div class="col-12 mt-4">
                        <label for="courier-name-add" class="form-label"
                            >Upload Image</label
                        >
                        <FileUpload
                            mode="basic"
                            accept="image/*"
                            @select="handlePhotoChange"
                        />
                    </div>

                    <div class="col-12 d-flex justify-content-end mt-4">
                        <button
                            class="btn btn-primary"
                            type="submit"
                            :disabled="loader.submit"
                        >
                            {{
                                loader.submit
                                    ? "Mohon Tunggu"
                                    : "Simpan Perubahan"
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
    name: "create_category",
    data() {
        return {
            loader: {
                submit: false,
                data: false,
            },
            category: {
                name: "",
                image: "",
            },
        };
    },
    methods: {
        async getData() {
            try {
                const response = await ApiData.get(
                    `app/ecommerce/blogs/categories/detail/${this.$route.params.id}`
                );
                var data = response.data;
                this.category = data;
            } catch (error) {
                console.log(error);
            }
        },

        async handlePhotoChange(e) {
            if (e.files[0] != undefined) {
                this.convertFileToBase64(e.files[0]);
            } else {
                this.category.image = null;
            }
        },

        convertFileToBase64(file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                this.category.image = reader.result;
            };
            reader.onerror = (error) => {
                console.error("Error converting file to base64:", error);
            };
        },

        Validationcategorys() {
            this.$refs.categoryValidation.validate().then((success) => {
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
                `app/ecommerce/blogs/categories/update/${this.$route.params.id}`,
                this.category
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
    },
};
</script>
