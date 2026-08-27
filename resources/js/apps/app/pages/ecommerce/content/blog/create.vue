<template>
    <!-- List Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-3">
                <h4 class="card-title">Tambah blog</h4>
            </div>
            <Form
                @submit="Validationblogs()"
                ref="blogValidation"
                class="card-body p-4"
            >
                <div class="row">
                    <!-- Title -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Judul Blog</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="blog.title"
                            name="Judul Blog"
                        >
                            <InputText
                                v-model="blog.title"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                                placeholder="Masukkan Judul"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Title -->

                    <!-- Title -->
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Kategori Blog</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="blog.category"
                            name="Kategori blog"
                        >
                            <Dropdown
                                v-model="blog.category"
                                :options="categories"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Pilih Kategori"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Title -->

                    <div class="col-12 mt-4">
                        <label for="category-product">Short Deskripsi</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="blog.short_description"
                            name="Deskripsi Singkat"
                        >
                            <textarea
                                class="form-control"
                                v-model="blog.short_description"
                            ></textarea>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div class="col-12 mt-4">
                        <label for="category-product">Isi Blog</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="blog.description"
                            name="Isi blog"
                        >
                            <Editor
                                v-model="blog.description"
                                editorStyle="height: 320px"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

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
                                    : "Tambahkan blog"
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
import Editor from "primevue/editor";
var _ = require("lodash");

export default {
    name: "create_blog",
    components: {
        Editor,
    },
    data() {
        return {
            loader: {
                submit: false,
                data: false,
            },
            blog: {
                title: "",
                category: "",
                short_description: "",
                description: "",
                image: "",
            },
        };
    },
    methods: {
        async getCategories() {
            try {
                const response = await ApiData.get(
                    `app/ecommerce/blogs/categories?limit=30&page=1&name=`
                );
                var data = response.data;
                this.categories = data.categories;
            } catch (error) {
                console.log(error);
            }
        },

        async handlePhotoChange(e) {
            if (e.files[0] != undefined) {
                this.convertFileToBase64(e.files[0]);
            } else {
                this.blog.image = null;
            }
        },

        convertFileToBase64(file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                this.blog.image = reader.result;
            };
            reader.onerror = (error) => {
                console.error("Error converting file to base64:", error);
            };
        },

        Validationblogs() {
            this.$refs.blogValidation.validate().then((success) => {
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
            ApiData.post("app/ecommerce/blogs/article/create", this.blog)
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
        this.getCategories();
    },
};
</script>
