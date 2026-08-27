<template>
    <!-- List Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-3">
                <h4 class="card-title">Tentang Kami / About Us Page</h4>
            </div>
            <Form
                @submit="ValidationAbout()"
                ref="AboutValidation"
                class="card-body p-4"
            >
                <div class="row">
                    <!-- Title -->
                    <div class="col-12">
                        <label for="category-product">About Title</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="about.about_title"
                            name="About Title"
                        >
                            <InputText
                                v-model="about.about_title"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <!-- End Title -->

                    <div class="col-12 mt-4">
                        <label for="category-product">About Copyright</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="about.copyright"
                            name="Copyright"
                        >
                            <InputText
                                v-model="about.copyright"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div class="col-12 mt-4">
                        <label for="category-product">About Text</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="about.about_text"
                            name="Isi blog"
                        >
                            <Editor
                                v-model="about.about_text"
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
            about: {
                about_title: "",
                about_text: "",
                image: "",
            },
        };
    },
    methods: {
        async getData() {
            try {
                const response = await ApiData.get(`app/ecommerce/blogs/abouts/`);
                var data = response.data;
                this.about = data;
            } catch (error) {
                console.log(error);
            }
        },

        async handlePhotoChange(e) {
            if (e.files[0] != undefined) {
                this.convertFileToBase64(e.files[0]);
            } else {
                this.about.image = null;
            }
        },

        convertFileToBase64(file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                this.about.image = reader.result;
            };
            reader.onerror = (error) => {
                console.error("Error converting file to base64:", error);
            };
        },

        ValidationAbout() {
            this.$refs.AboutValidation.validate().then((success) => {
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
            ApiData.post(`app/ecommerce/blogs/abouts/update`, this.about)
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
    mounted() { 
        this.getData();
    },
};
</script>
