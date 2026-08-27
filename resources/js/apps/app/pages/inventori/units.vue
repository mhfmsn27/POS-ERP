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
                        @click="formatUnit()"
                        v-tooltip="'Klik, Untuk Membatalkan Edit Data'"
                        class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <Form @submit="validationUnit()" ref="unitValidation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="Unit-name-add" class="form-label"
                                >Nama Satuan</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                v-model="unit.name"
                                ref="unitname"
                                name="Nama Satuan"
                            >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="unit.name"
                                    placeholder="Masukkan Nama Satuan"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12 mt-3">
                            <label for="Unit-name-add" class="form-label"
                                >Kode Satuan</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="unit.code"
                                ref="unitcode"
                                name="Kode Satuan"
                            >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="unit.code"
                                    placeholder="Masukkan Kode Satuan"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12 mt-3">
                            <label for="Unit-name-add" class="form-label"
                                >Tipe Satuan</label
                            >
                            <Dropdown
                                v-model="unit.is_root_parent"
                                :options="[
                                    {
                                        label: 'Induk Satuan',
                                        value: false,
                                    },
                                    {
                                        label: 'Satuan Turunan',
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

                        <div class="col-12 mt-3" v-if="unit.is_root_parent">
                            <label for="Unit-name-add" class="form-label"
                                >Pilih Induk Satuan</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="unit.parent"
                                name="Tipe Satuan"
                            >
                                <Multiselect
                                    v-model="unit.parent"
                                    :options="units_choose"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :preserve-search="true"
                                    :searchable="true"
                                    :loading="loader.unit"
                                    :internal-search="true"
                                    :options-limit="50"
                                    placeholder="Pilih"
                                    open-direction="bottom"
                                    label="name"
                                    id="id"
                                    track-by="name"
                                    tagPlaceholder=""
                                    selectLabel=""
                                    @search-change="getUnits"
                                ></Multiselect>
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12 mt-3" v-if="unit.is_root_parent">
                            <label for="Unit-name-add" class="form-label"
                                >Operator</label
                            >
                            <Dropdown
                                v-model="unit.operator"
                                :options="[
                                    {
                                        label: 'Perkalian',
                                        value: '*',
                                    },
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Pilih Tipe"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>

                        <div class="col-12 mt-3" v-if="unit.is_root_parent">
                            <label for="Unit-name-add" class="form-label"
                                >Value Satuan</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="unit.value"
                                name="Value Satuan"
                            >
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="unit.value"
                                />
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
                        class="btn label-btn label-end"
                        :class="editmode ? 'btn-warning' : 'btn-primary'"
                    >
                        {{
                            loader.submit
                                ? "Mohon Tunggu...."
                                : editmode
                                ? "Simpan Perubahan"
                                : "Tambahkan Satuan"
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
                    <TreeTable
                        :value="units"
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
                        <Column field="code" header="Kode"></Column>
                        <Column field="value" header="Value"></Column>

                        <Column header="Aksi">
                            <template #body="{ node }">
                                <button
                                    type="button"
                                    @click="editUnit(node.data)" 
                                    v-tooltip="'Edit Satuan'"
                                    class="btn btn-icon btn-outline-warning rounded-pill btn-wave waves-effect waves-light mr-2"
                                >
                                    <i class="fa fa-pencil"></i>
                                </button>

                                <button
                                    type="button"
                                    @click="removeUnit(node.data.id)" 
                                    v-tooltip="'Hapus Satuan'"
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
            editmode: false,
            units: [],
            units_choose: [],
            unit: {
                name: "",
                is_root_parent: false,
                parent: {
                    id: "",
                    name: "",
                },
                value: 1,
                operator: "*",
                code: "",
            },

            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
                submit: false,
                unit: false,
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
                    `app/inventory/units?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&only_parent=0`
                );
                var data = response.data;
                this.units = data.units;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getUnits(query) {
            this.loader.unit = true;
            try {
                const response = await ApiData.get(
                    `app/inventory/components/units?name=${query}&only_parent=0`
                );
                var data = response.data;
                this.units_choose = data.units;
                this.loader.unit = false;
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

        removeUnit(id) {
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
                    ApiData
                        .delete("app/inventory/units/delete/" + id)
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

        editUnit(data) {
            this.unit = data;
            this.editmode = true;
        },

        validationUnit() {
            this.$refs.unitValidation.validate().then((success) => {
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
            ApiData
                .post("app/inventory/units/create", this.unit)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    this.formatUnit();
                    this.getData();
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
        },

        updateData() {
            ApiData
                .post("app/inventory/units/update/" + this.unit.id, this.unit)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    this.formatUnit();
                    this.getData();
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
        },

        formatUnit() {
            this.unit = {
                name: "",
                is_root_parent: false,
                parent: {
                    id: "",
                    name: "",
                },
                value: 1,
                operator: "*",
                code: "",
            };

            this.editmode = false;
            this.$refs.unitname.reset();
            this.$refs.unitcode.reset();
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
