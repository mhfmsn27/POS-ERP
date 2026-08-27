<template>
    <!-- Create Data -->
    <div class="col-lg-4">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">
                    {{ editmode ? "Edit Data" : "Tambah Data" }}
                </h4>
                <div>
                    <button
                        v-if="editmode"
                        type="button"
                        @click="formatBrand()"
                        v-tooltip="'Klik, Untuk Membatalkan Edit Data'"
                        class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <Form @submit="validationBrand()" ref="BrandValidation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="Brand-name-add" class="form-label"
                                >Nama Brand</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                v-model="brand.name"
                                ref="brandname"
                                name="Nama Brand"
                            >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="brand.name"
                                    placeholder="Masukkan Nama Brand"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12 mt-3">
                            <label for="Brand-detail-add" class="form-label"
                                >Detail Brand</label
                            >
                            <textarea
                                class="form-control"
                                v-model="brand.detail"
                            ></textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="profile-img-edit">
                                <img
                                    class="profile-pic"
                                    :src="brand.image"
                                    :alt="brand.name"
                                />
                                <div class="p-image bg-info">
                                    <label
                                        for="file-upload"
                                        class="upload-icon"
                                    >
                                        <i class="fe fe-edit text-white"></i>
                                    </label>
                                    <input
                                        id="file-upload"
                                        @change="handlePhotoChange"
                                        type="file"
                                        class="file-upload"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button
                        type="submit"
                        :disabled="loader.submit"
                        class="btn label-btn label-end"
                        :class="editmode ? 'btn-warning' : 'btn-primary'"
                    >
                        {{
                            loader.submit
                                ? "Mohon Tunggu...."
                                : editmode
                                ? "Simpan Perubahan"
                                : "Tambah Brand"
                        }}
                        <i class="ti ti-plus label-btn-icon ms-2"></i>
                    </button>
                </div>
            </Form>
        </div>
    </div>
    <!-- End Create Data -->

    <!-- List Data -->
    <div class="col-lg-8">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-2">
                <div>
                    <label class="control-label">Nama Satuan</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="filter.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Satuan...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <DataTable
                        :value="brands"
                        :paginator="true"
                        :rows="limit"
                        :rowsPerPageOptions="[10, 20, 50]"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :lazy="true"
                        :totalRecords="totalRows"
                        @page="onPageChange($event)"
                        class="table text-nowrap"
                        :loading="loader.data"
                        responsiveLayout="scroll"
                        sortField="dynamicSortField"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                        dataKey="id"
                    >
                        <Column field="name" header="Nama"></Column>
                        <Column header="Icon">
                            <template #body="{ data }">
                                <div class="avatar avatar-md mr-2 lh-1">
                                    <img
                                        class="rounded-circle img-fluid avatar-40"
                                        :src="data.image"
                                    />
                                </div>
                            </template>
                        </Column>
                        <Column header="Aksi">
                            <template #body="{ data }">
                                <button
                                    type="button"
                                    @click="editBrand(data)"
                                    v-tooltip="'Edit Brand'"
                                    class="btn btn-icon btn-outline-warning rounded-pill btn-wave waves-effect waves-light mr-2"
                                >
                                    <i class="fa fa-pencil"></i>
                                </button>

                                <button
                                    type="button"
                                    @click="removeBrand(data.id)"
                                    v-tooltip="'Hapus Brand'"
                                    class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </div>
    <!-- End List Data -->
</template>

<script>
import Swal from "sweetalert2";
import NProgress from "nprogress";
import DefaultPhoto from "@/assets/images/10.jpg";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "product_list",
    components: {},
    data() {
        return {
            data: new FormData(),
            editmode: false,
            image_file: null,
            brands: [],
            brand: {
                name: "",
                image: DefaultPhoto,
                detail: "",
            },

            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
                submit: false,
            },
            filter: {
                name: "",
            },
        };
    },
    computed: {},
    created() {
        this.getData();
    },
    methods: {
        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;

            try {
                const response = await ApiData.get(
                    `app/inventory/brands?limit=${this.limit}&page=${this.page}&name=${this.filter.name}`
                );
                var data = response.data;
                this.brands = data.brands;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async handlePhotoChange(event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = () => {
                this.brand.image = reader.result;
            };
            reader.readAsDataURL(file);
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

        removeBrand(id) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "data yang telah di hapus tidak dapat dikembalikan lagi",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.delete("app/inventory/brands/delete/" + id)
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

        editBrand(data) {
            this.brand = {
                id: data.id,
                name: data.name,
                image: data.image,
                detail: data.detail,
            };
            this.editmode = true;
        },

        validationBrand() {
            this.$refs.BrandValidation.validate().then((success) => {
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
                    if (!this.editmode) {
                        this.createData();
                    } else {
                        this.updateData();
                    }
                }
            });
        },

        createData() {
            ApiData.post("app/inventory/brands/create", this.brand)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    this.formatBrand();
                    this.getData();
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
        },

        updateData() {
            ApiData.post(
                "app/inventory/brands/update/" + this.brand.id,
                this.brand
            )
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    this.formatBrand();
                    this.getData();
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
        },

        formatBrand() {
            this.brand = {
                name: "",
                image: DefaultPhoto,
                detail: "",
            };

            this.editmode = false;
            this.$refs.brandname.reset();
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
