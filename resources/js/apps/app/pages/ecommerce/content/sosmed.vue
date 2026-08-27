<template>
    <!-- List Data -->
    <div class="col-lg-9 col-sm-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-3">
                <h4 class="card-title">Tentang Kami / About Us Page</h4>
            </div>
            <Form
                @submit="Validationblogs()"
                ref="aboutValidation"
                class="card-body p-4"
            >
                <div class="row"> 
                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Instagram Url</label>
                        <InputText
                            v-model="about.instagram_url"
                            style="width: 100%"
                            type="url"
                            class="form-control"
                        />
                    </div>  

                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Facebook Url</label>
                        <InputText
                            v-model="about.facebook_url"
                            style="width: 100%"
                            type="url"
                            class="form-control"
                        />
                    </div>  

                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Twitter Url</label>
                        <InputText
                            v-model="about.twitter_url"
                            style="width: 100%"
                            type="url"
                            class="form-control"
                        />
                    </div>  

                    <div class="col-lg-6 col-sm-12 mt-4">
                        <label for="category-product">Youtube Url</label>
                        <InputText
                            v-model="about.youtube_url"
                            style="width: 100%"
                            type="url"
                            class="form-control"
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
                facebook_url: "",
                instagram_url: "",
                twitter_url: "",
                youtube_url: "",
            },
        };
    },
    methods: {
        async getData() {
            try {
                const response = await ApiData.get(
                    `app/ecommerce/blogs/abouts/social`
                );
                var data = response.data;
                this.about = data;
            } catch (error) {
                console.log(error);
            }
        },

        Validationblogs() {
            this.$refs.aboutValidation.validate().then((success) => {
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
            ApiData.post(`app/ecommerce/blogs/abouts/social-store`, this.about)
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
