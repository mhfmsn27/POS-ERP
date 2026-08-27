<template>
    <div class="col-lg-9 col-sm-12 row">
        <!-- Create Data -->
        <div class="col-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-header">
                    <h4 class="card-title">Pilih Kategori</h4>
                </div>
                <Form @submit="validationCategory()" ref="categoryValidation">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <label
                                    for="category-name-add"
                                    class="form-label"
                                    >Pilih Kategori</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="category.categories"
                                    name="Pilih Kategori"
                                >
                                    <Multiselect
                                        v-model="category.categories"
                                        :options="categories_choose"
                                        :multiple="true"
                                        :close-on-select="false"
                                        :clear-on-select="true"
                                        :preserve-search="true"
                                        :searchable="true"
                                        :loading="loader.category"
                                        :internal-search="true"
                                        :options-limit="50"
                                        placeholder="Pilih Kategori"
                                        open-direction="bottom"
                                        label="name"
                                        id="id"
                                        track-by="name"
                                        tagPlaceholder=""
                                        selectLabel=""
                                        @search-change="getCategories"
                                    ></Multiselect>
                                    <div class="fs-sm text-danger">
                                        {{ errors[0] }}
                                    </div>
                                </Field>
                            </div>
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
                                    : "Tambahkan Kategori"
                            }}
                            <i class="ti ti-plus label-btn-icon ms-2"></i>
                        </button>
                    </div>
                </Form>
            </div>
        </div>
        <!-- End Create Data -->

        <!-- List Data -->
        <div class="col-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between p-2">
                    <div>
                        <label class="control-label">Nama Kategori</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"
                                ><i class="fa fa-search"></i>
                            </span>
                            <input
                                type="text"
                                v-model="filter.name"
                                @keyup="searchData()"
                                class="form-control"
                                placeholder="Cari Kategori...."
                                aria-describedby="basic-addon1"
                            />
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <TreeTable
                            :value="categories"
                            :paginator="true"
                            :loading="loader.data"
                            :lazy="true"
                            :rows="limit"
                            :rowsPerPageOptions="[10, 25]"
                            paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                            currentPageReportTemplate="{first} to {last} of {totalRecords}"
                            :totalRecords="totalRows"
                            class="table text-nowrap"
                            @page="onPageChange($event)"
                        >
                            <Column
                                field="name"
                                header="Nama"
                                expander
                            ></Column>
                            <Column header="Icon">
                                <template #body="{ node }">
                                    <div class="avatar avatar-md mr-2 lh-1">
                                        <img
                                            class="rounded-circle img-fluid avatar-40"
                                            :src="node.data.image"
                                        />
                                    </div>
                                </template>
                            </Column>
                            <Column header="Unggulan">
                                <template #body="{ node }">
                                    <InputSwitch
                                        @change="chooseFeatured(node.data.id)"
                                        v-model="node.data.featured_category"
                                    />
                                </template>
                            </Column>
                            <Column header="Aksi">
                                <template #body="{ node }">
                                    <button
                                        type="button"
                                        @click="removeCategory(node.data.id)"
                                        v-tooltip.top="'Hapus Kategori'"
                                        class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </template>
                            </Column>
                        </TreeTable>
                    </div>
                </div>
            </div>
        </div>
        <!-- End List Data -->
    </div>
</template>

<script>
import Swal from "sweetalert2";
import NProgress from "nprogress";
import TreeTable from "primevue/treetable";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "product_list",
    components: {
        TreeTable,
    },
    data() {
        return {
            categories: [],
            categories_choose: [],
            category: {
                categories: "",
            },
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
                submit: false,
                category: false,
            },
            filter: {
                name: "",
            },
        };
    },
    computed: {},
    methods: {
        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;

            try {
                const response = await ApiData.get(
                    `app/ecommerce/media-content/categories?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&show_ecommerce=yes`
                );
                var data = response.data;
                this.categories = data.categories;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getCategories(query) {
            this.loader.category = true;
            try {
                const response = await ApiData.get(
                    `app/inventory/components/categories?name=${query}&show_ecommerce=no`
                );
                var data = response.data;
                this.categories_choose = data.categories;
                this.loader.category = false;
            } catch (error) {
                console.log(error);
            }
        },

        searchData() {
            this.doSearch(this);
        },

        doSearch: _.debounce((rootInstance) => {
            rootInstance.getData();
        }, 300),

        onPageChange(e) {
            this.limit = e.rows;
            this.page = e.page += 1;
            this.getData(this.page);
        },

        removeCategory(id) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.delete(
                        "app/ecommerce/media-content/categories/delete/" + id
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.getData();
                        })
                        .catch((error) => {
                            NProgress.done();
                            this.$handleErrorResponse(error);
                        });
                } else {
                    Swal.fire("Membatalkan Proses Hapus Data");
                }
            });
        },

        validationCategory() {
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

        chooseFeatured(id) {
            ApiData.post(`app/ecommerce/media-content/categories/change/${id}`)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                })
                .catch((error) => {
                    this.$handleErrorResponse(error);
                });
        },

        createData() {
            ApiData.post(
                "app/ecommerce/media-content/categories/create",
                this.category
            )
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    this.formatCategory();
                    this.getData();
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
        },

        formatCategory() {
            this.category = {
                categories: "",
            };
        },
    },
    mounted: function () {
        this.getData();
        this.getCategories("");
    },
    watch: {},
};
</script>
