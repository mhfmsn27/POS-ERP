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
                        @click="formatCategory()"
                        v-tooltip="'Klik, Untuk Membatalkan Edit Data'"
                        class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <Form @submit="validationCategory()" ref="categoryValidation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="category-name-add" class="form-label"
                                >Nama Kategori</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="category.name"
                                ref="categoryname"
                                name="Nama Kategori"
                            >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="category.name"
                                    placeholder="Masukkan Nama Kategori"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12 mt-3">
                            <label for="category-name-add" class="form-label"
                                >Tipe Kategori</label
                            >
                            <Dropdown
                                v-model="category.is_root_parent"
                                :options="[
                                    {
                                        label: 'Induk Kategori',
                                        value: false,
                                    },
                                    {
                                        label: 'Sub Kategori',
                                        value: true,
                                    },
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Pilih Tipe"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>

                        <div class="col-12 mt-3" v-if="category.is_root_parent">
                            <label for="category-name-add" class="form-label"
                                >Pilih Induk Kategori</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="category.parent"
                                name="Induk Kategori"
                            >
                                <Multiselect
                                    v-model="category.parent"
                                    :options="categories_choose"
                                    :multiple="false"
                                    :close-on-select="true"
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

                        <div class="col-12 mt-3">
                            <label for="category-detail-add" class="form-label"
                                >Detail Kategori</label
                            >
                            <textarea
                                class="form-control"
                                v-model="category.detail"
                            ></textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="profile-img-edit">
                                <img
                                    class="profile-pic"
                                    :src="category.image"
                                    :alt="category.name"
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
    <div class="col-lg-8">
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
                <button
                    type="button"
                    @click="modal.import = true"
                    class="btn btn-info label-btn label-end"
                >
                    Import Data
                    <i class="ti ti-upload label-btn-icon ms-2"></i>
                </button>
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
                        <Column field="name" header="Nama" expander></Column>
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
                        <Column header="Aksi">
                            <template #body="{ node }">
                                <button
                                    type="button"
                                    @click="editCategory(node.data)"
                                    v-tooltip.top="'Edit Kategori'"
                                    class="btn btn-icon btn-outline-warning rounded-pill btn-wave waves-effect waves-light mr-2"
                                >
                                    <i class="fa fa-pencil"></i>
                                </button>

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

    <!-- Modal Import -->
    <Dialog
        v-model:visible="modal.import"
        modal
        header=""
        :style="{ width: '60vh' }"
    >
        <div class="card-body ps-5 pe-5 pt-2 pb-5 rectangle3">
            <div class="d-flex justify-content-center">
                <img src="@/assets/images/import_data.png" style="width: 75%" />
            </div>

            <p class="h4 fw-semibold mb-2 text-center">Import Data Kategori</p>
            <p class="mb-4 text-muted op-7 fw-normal text-center">
                Upload file Excel (.xlsx) yang berisikan data kategori di bawah
                ini, lalu klik tekan Import data untuk memulai proses import
            </p>
            <div class="row gy-3">
                <div class="col-xl-12 d-flex justify-content-center mt-3 mb-3">
                    <FileUpload
                        mode="basic"
                        v-model="import_data.model"
                        @select="onFileSelected"
                        v-tooltip="'Upload File Disini'"
                        accept=".xlsx"
                        :maxFileSize="1000000"
                        @upload="onUpload"
                    />
                </div>
                <!-- End Code Form -->

                <div
                    class="col-xl-12 d-grid mt-4 d-flex justify-content-center"
                >
                    <button
                        type="button"
                        @click="downloadExample"
                        :disabled="loader.submit"
                        class="btn btn-info label-btn me-3"
                    >
                        <i class="ti ti-download label-btn-icon mr-2"></i>
                        Unduh Sample
                    </button>
                    <button
                        type="button"
                        @click="importData"
                        v-tooltip="
                            'Sebelum Import Data, Pastikan File Telah di unggah'
                        "
                        :disabled="loader.submit"
                        class="btn btn-primary label-btn label-end"
                    >
                        {{ loader.submit ? "Mohon Tunggu...." : "Import Data" }}
                        <i class="ti ti-upload label-btn-icon ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </Dialog>
    <!-- End Import Data -->
</template>

<script>
import Swal from "sweetalert2";
import NProgress from "nprogress";
import TreeTable from "primevue/treetable";
import DefaultPhoto from "@/assets/images/10.jpg";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "product_list",
    components: {
        TreeTable,
    },
    data() {
        return {
            data: new FormData(),
            editmode: false,
            image_file: null,
            categories: [],
            categories_choose: [],

            category: {
                name: "",
                image: DefaultPhoto,
                detail: "",
                is_root_parent: false,
                parent: {
                    id: null,
                    name: "",
                },
            },
            modal: {
                import: false,
            },
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
                submit: false,
                category: false,
            },
            import_data: {
                file: null,
                model: null,
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
                    `app/inventory/categories?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&only_parent=0`
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
                    `app/inventory/components/categories?name=${query}`
                );
                var data = response.data;
                this.categories_choose = data.categories;
                this.loader.category = false;
            } catch (error) {
                console.log(error);
            }
        },

        async handlePhotoChange(event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = () => {
                this.category.image = reader.result;
            };
            reader.readAsDataURL(file);
        },

        async downloadExample() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);

            try {
                const response = await ApiData.get(
                    `app/inventory/categories/download-sample`,
                    {
                        responseType: "blob",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                    }
                );

                const url = window.URL.createObjectURL(
                    new Blob([response.data])
                );
                const link = document.createElement("a");
                link.href = url;

                link.setAttribute("download", "sample_import_category.xlsx");
                document.body.appendChild(link);
                link.click();

                this.loader.submit = false;
                NProgress.done();
            } catch (error) {
                this.loader.submit = false;
                NProgress.done();
                console.log(error);
            }
        },

        async onFileSelected(e) {
            if (e.files[0] != undefined) {
                this.import_data.file = e.files[0];
            } else {
                this.import_data.file = null;
            }
        },

        importData() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            this.data.append("file", this.import_data.file);
            ApiData.post("app/inventory/categories/import", this.data)
                .then((response) => {
                    this.loader.submit = false;
                    this.$handleSuccessResponse(response.data.message);
                    this.modal.import = false;
                    NProgress.done();
                    this.getData();
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
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
                    ApiData.delete("app/inventory/categories/delete/" + id)
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

        editCategory(data) {
            this.category = {
                id: data.id,
                name: data.name,
                image: data.image,
                detail: data.detail,
                is_root_parent: data.is_root_parent,
                parent: data.parent,
            };
            this.editmode = true;
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
                    if (!this.editmode) {
                        this.createData();
                    } else {
                        this.updateData();
                    }
                }
            });
        },

        createData() {
            ApiData.post("app/inventory/categories/create", this.category)
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

        updateData() {
            ApiData.post(
                "app/inventory/categories/update/" + this.category.id,
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
                name: "",
                image: DefaultPhoto,
                detail: "",
                is_root_parent: false,
                parent: {
                    id: null,
                    name: "",
                },
            };

            this.editmode = false;
            this.$refs.categoryname.reset();
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
