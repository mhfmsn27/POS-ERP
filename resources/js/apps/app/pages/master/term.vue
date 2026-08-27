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
                        @click="formatType()"
                        v-tooltip="'Klik, Untuk Membatalkan Edit Data'"
                        class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <Form @submit="validationTerm()" ref="termValidation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="Unit-name-add" class="form-label"
                                >Nama
                            </label>
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                v-model="term.name"
                                ref="termname"
                                name="Nama"
                            >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="term.name"
                                    placeholder="Masukkan Nama "
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <!-- Day -->
                        <div class="col-lg-12 mt-3">
                            <label for="user-ref" class="form-label"
                                >Jika Membayar Antara</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="term.day"
                                name="Nominal Biaya"
                            >
                                <InputNumber
                                    style="width: 100%"
                                    v-model="term.day"
                                    suffix=" Hari"
                                />
                                <div class="fs-sm text-gray mt-2">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Day -->

                        <!-- Discount -->
                        <div class="col-lg-12 mt-3">
                            <label for="user-ref" class="form-label"
                                >Akan Mendapat Diskon</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="term.discount"
                                name="Diskon"
                            >
                                <InputNumber
                                    style="width: 100%"
                                    v-model="term.discount"
                                    suffix=" %"
                                    :max="100"
                                />
                                <div class="fs-sm text-gray mt-2">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Discount -->

                        <!-- Due Date -->
                        <div class="col-lg-12 mt-3">
                            <label for="user-ref" class="form-label"
                                >Jatuh Tempo</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="term.due_date"
                                name="Hari"
                            >
                                <InputNumber
                                    style="width: 100%"
                                    v-model="term.due_date"
                                    suffix=" Hari"
                                    :max="100"
                                />
                                <div class="fs-sm text-gray mt-2">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>
                        <!-- End Due Date -->

                        <div class="col-lg-12 mt-3">
                            <label for="user-ref" class="form-label"
                                >Keterangan</label
                            >
                            <textarea
                                class="form-control"
                                v-model="term.note"
                            />
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
                                : "Tambahkan Data"
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
                    <label class="control-label">Nama </label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="filter.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Data...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <DataTable
                        :value="terms"
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
                        <Column field="discount" header="Diskon %"></Column>
                        <Column field="day" header="Masa Diskon"></Column>
                        <Column field="due_date" header="Jatuh Tempo"></Column> 
                        <Column header="Default">
                            <template #body="{ data }">
                                <InputSwitch
                                    @change="changeDefault(data)"
                                    v-model="data.default"
                                    v-tooltip.top="'Default Penggunaan'"
                                />
                            </template>
                        </Column>

                        <Column header="Aksi">
                            <template #body="{ data }">
                                <button
                                    type="button"
                                    @click="editData(data)"
                                    v-tooltip="'Edit Data'"
                                    class="btn btn-icon btn-outline-warning rounded-pill btn-wave waves-effect waves-light me-2"
                                >
                                    <i class="fa fa-pencil"></i>
                                </button>

                                <button
                                    type="button"
                                    @click="removeData(data.id)"
                                    v-tooltip="'Hapus Data'"
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
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "type_list",
    components: {},
    data() {
        return {
            editmode: false,
            terms: [],
            term: {
                name: "",
                discount: 0,
                day: 0,
                due_date: 0,
                note: "",
                default: false,
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
                    `app/master/term?limit=${this.limit}&page=${this.page}&name=${this.filter.name}`
                );
                var data = response.data;
                this.terms = data.terms;
                this.totalRows = data.totalRows;
                this.loader.data = false;
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
                    ApiData.delete("app/master/term/delete/" + id)
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();

                            if (id == this.term.id) {
                                this.formatType();
                            }
                            this.getData();
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.$handleErrorResponse(err);
                        });
                } else {
                    Swal.fire("Membatalkan Proses Hapus Data");
                }
            });
        },

        changeDefault(data) {
            NProgress.start();
            NProgress.set(0.1);
            ApiData.post("app/master/term/set/" + data.id)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.getData();
                })
                .catch((err) => {
                    NProgress.done();
                    this.$handleErrorResponse(err);
                });
        },

        editData(data) {
            this.term = data;
            this.editmode = true;
        },

        validationTerm() {
            this.$refs.termValidation.validate().then((success) => {
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
            ApiData.post("app/master/term/create", this.term)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    this.formatType();
                    this.getData();
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        updateData() {
            ApiData.post("app/master/term/update/" + this.term.id, this.term)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    this.formatType();
                    this.getData();
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        formatType() {
            this.term = {
                name: "",
                discount: 0,
                day: 0,
                due_date: 0,
                note: "",
                default: false,
            };

            this.editmode = false;
            this.$refs.termname.reset();
        },

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
