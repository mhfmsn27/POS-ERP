<template>
    <!-- Create Data -->
    <div class="mt-4 col-lg-4">
        <div class="card card-block card-stretch card-height">
            <div
                class="card-header d-flex justify-content-between border-bottom"
            >
                <h4 class="card-title">
                    {{ editmode ? "Edit Data" : "Tambah Data" }}
                </h4>
            </div>
            <Form @submit="ValidationFormData()" ref="FormValidation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="cutting-name-add" class="form-label"
                                >Nama Potongan</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="cutting.name"
                                ref="cuttingname"
                                name="Nama Potongan"
                            >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="cutting.name"
                                    placeholder="Masukkan Nama Potongan"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <div class="col-12 mt-4">
                            <label for="cutting-name-add" class="form-label"
                                >Devisi</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="cutting.department"
                                name="Devisi"
                            >
                                <Multiselect
                                    v-model="cutting.department"
                                    :options="departments"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :preserve-search="true"
                                    :searchable="true"
                                    :internal-search="true"
                                    :allowEmpty="false"
                                    :options-limit="50"
                                    placeholder="Pilih Devisi"
                                    open-direction="bottom"
                                    label="name"
                                    id="id"
                                    track-by="name"
                                    @select="getDesignation('')"
                                    @search-change="getDepartment"
                                ></Multiselect>
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <div class="col-12 mt-4">
                            <label for="cutting-name-add" class="form-label"
                                >Jabatan</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="cutting.designation"
                                name="Jabatan"
                            >
                                <Multiselect
                                    v-model="cutting.designation"
                                    :options="designations"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :preserve-search="true"
                                    :searchable="true"
                                    :internal-search="true"
                                    :allowEmpty="false"
                                    :options-limit="50"
                                    placeholder="Pilih Jabatan"
                                    open-direction="bottom"
                                    label="name"
                                    id="id"
                                    track-by="name"
                                    @search-change="getDesignation"
                                ></Multiselect>
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <div class="col-12 mt-4">
                            <label for="cutting-name-add" class="form-label"
                                >Priode</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="cutting.priode"
                                name="Priode"
                            >
                                <Dropdown
                                    v-model="cutting.priode"
                                    :options="[
                                        {
                                            id: 'day',
                                            name: 'Harian',
                                        },
                                        {
                                            id: 'month',
                                            name: 'Bulanan',
                                        },
                                    ]"
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Pilih Priode"
                                    style="width: 100%"
                                    class="w-full md:w-14rem"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <div class="col-12 mt-4">
                            <label for="cutting-name-add" class="form-label"
                                >Nominal Potongan</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="cutting.amount"
                                name="Nominal Potongan"
                            >
                                <InputNumber
                                    style="width: 100%"
                                    v-model="cutting.amount"
                                    prefix="Rp "
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
                                : "Tambahkan Potongan"
                        }}
                        <i class="ti ti-plus label-btn-icon ms-2"></i>
                    </button>
                </div>
            </Form>
        </div>
    </div>
    <!-- End Create Data -->

    <!-- List Data -->
    <div class="mt-4 col-lg-8">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <label class="control-label">Nama Potongan</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="filter.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Potongan...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <DataTable
                        :value="cuttings"
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
                        <Column
                            field="department.name"
                            header="Devisi"
                        ></Column>
                        <Column
                            field="designation.name"
                            header="Jabatan"
                        ></Column>
                        <Column header="Nominal">
                            <template #body="{ data }">
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>

                        <Column header="Aksi">
                            <template #body="{ data }">
                                <button
                                    type="button"
                                    @click="editData(data)"
                                    v-tooltip="'Edit Data'"
                                    class="btn btn-orange mr-2"
                                >
                                    <i class="fa fa-pencil"></i>
                                </button>

                                <button
                                    type="button"
                                    @click="removeData(data.id)"
                                    v-tooltip="'Hapus Data'"
                                    class="btn btn-red"
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
import { ApiData } from "@/api/server";

var _ = require("lodash");

export default {
    name: "product_list",
    components: {},
    data() {
        return {
            editmode: false,
            cuttings: [],
            departments: [],
            designations: [],
            cutting: {
                name: "",
                priode: "",
                amount: 0,
                designation: {
                    id: "",
                    name: "",
                },
                department: {
                    id: "",
                    name: "",
                },
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
    mounted() {
        this.getData();
        this.getDepartment("");
    },
    methods: {
        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;

            try {
                const response = await ApiData.get(
                    `app/master/cuttings?limit=${this.limit}&page=${this.page}&name=${this.filter.name}`
                );
                var data = response.data;
                this.cuttings = data.cuttings;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getDesignation(query) {
            try {
                const response = await ApiData.get(
                    `app/master/designations?name=${query}&department=${this.cutting.department?.id}`
                );
                var data = response.data;
                this.designations = data.designations;
            } catch (error) {
                console.log(error);
            }
        },

        async getDepartment(query) {
            try {
                const response = await ApiData.get(
                    `app/master/departments?name=${query}`
                );
                var data = response.data;
                this.departments = data.departments;
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

        removeData(id) {
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
                    ApiData.delete("app/master/cuttings/delete/" + id)
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

        editData(data) {
            this.cutting = data;
            this.editmode = true;
        },

        ValidationFormData() {
            this.$refs.FormValidation.validate().then((success) => {
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
            ApiData.post("app/master/cuttings/create", this.cutting)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    this.formatForm();
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
                "app/master/cuttings/update/" + this.cutting.id,
                this.cutting
            )
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    this.formatForm();
                    this.getData();
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
        },

        formatNumber(number) {
            if (parseFloat(number) >= 0) {
                return number.toLocaleString();
            } else {
                return "-" + (-number).toLocaleString();
            }
        },

        formatForm() {
            this.cutting = {
                name: "",
                department: {
                    id: "",
                    name: "",
                },
            };

            this.editmode = false;
            this.$refs.cuttingname.reset();
        },
    },
};
</script>
