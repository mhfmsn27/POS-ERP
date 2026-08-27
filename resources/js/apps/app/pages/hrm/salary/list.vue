<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <label class="form-label">Cari Data Pegawai</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fe fe-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="filter.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Pegawai...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
                <div class="d-flex justify-content-start">
                    <a
                        class="btn btn-info me-2"
                        href="javascript:void(0);"
                        @click="modal.filter = true"
                        ><i class="fa fa-filter me-2"></i>Filter Data</a
                    >
                    <a
                        href="javascript:void(0);"
                        class="btn btn-primary"
                        @click="modal.create = true"
                        ><i class="fa fa-generate mr-2"></i>
                        Generate Gaji
                    </a>
                    <button
                        type="button"
                        v-tooltip.top="'Refresh'"
                        @click="getData()"
                        class="btn btn-outline-info btn-wave waves-effect waves-light ms-2"
                    >
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <DataTable
                        :value="salaries"
                        :paginator="true"
                        :rows="limit"
                        :rowsPerPageOptions="[20, 50, 100]"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :lazy="true"
                        :totalRecords="totalRows"
                        @page="onPageChange($event)"
                        class="table"
                        :loading="loader.data"
                        responsiveLayout="scroll"
                        sortField="dynamicSortField"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                    >
                        <Column field="date" :header="'Tanggal'">
                            <template #body="{ data }">
                                {{ data.date.substring(0, 10) }}
                            </template>
                        </Column>
                        <Column field="name" :header="'Pegawai'"> </Column>
                        <Column field="store" :header="'Toko'"></Column>
                        <Column field="salary" :header="'Total Gaji'">
                            <template #body="{ data }">
                                Rp {{ formatNumber(data.total) }}
                            </template>
                        </Column>
                        <Column :header="'Status'">
                            <template #body="{ data }">
                                <span
                                    class="badge bg-warning text-black"
                                    v-if="data.status == 'due'"
                                    >Piutang</span
                                >
                                <span
                                    class="badge bg-success"
                                    v-if="data.status == 'paid'"
                                    >Lunas</span
                                >
                            </template>
                        </Column>

                        <Column :header="'Tindakan'">
                            <template #body="{ data }">
                                <div
                                    class="btn-group"
                                    role="group"
                                    aria-label="Button group with nested dropdown"
                                >
                                    <div class="btn-group" role="group">
                                        <button
                                            id="btnGroupDrop1"
                                            type="button"
                                            class="btn btn-primary dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                        >
                                            Aksi
                                        </button>
                                        <div
                                            class="dropdown-menu"
                                            aria-labelledby="btnGroupDrop1"
                                            style=""
                                        >
                                            <a
                                                href="javascript:void(0)"
                                                @click="
                                                    $goTo({
                                                        name: 'detail_salary',
                                                        params: { id: data.id },
                                                    })
                                                "
                                                class="dropdown-item"
                                                ><i class="fa fa-eye mr-2"></i>
                                                Detail</a
                                            >
                                            <a
                                                v-if="data.status == 'due'"
                                                href="javascript:void(0);"
                                                @click="
                                                    editStatus(data, 'paid')
                                                "
                                                class="dropdown-item"
                                                ><i
                                                    class="fa fa-money mr-2"
                                                ></i>
                                                Bayar
                                            </a>
                                            <a
                                                href="javascript:void(0);"
                                                @click="deleteData(data.id)"
                                                class="dropdown-item"
                                                ><i
                                                    class="fa fa-trash mr-2"
                                                ></i>
                                                Hapus
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </div>
    <!-- End List Data -->

    <!-- Filter Modal -->
    <Dialog
        v-model:visible="modal.filter"
        :header="'Filter Data'"
        class="filter-data"
        position="top"
        :style="{ width: '40rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row p-2">
            <div class="col-lg-6 mb-3">
                <label class="form-label">Tanggal</label>
                <div class="input-group">
                    <VueCtkDateTimePicker
                        :label="'Filter Tanggal'"
                        locale="Asia/Jakarta"
                        class="form-control"
                        v-model="filter.date"
                        @validate="filterDate"
                        :range="true"
                    />
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <label class="form-label">Pilih Devisi</label>
                <Multiselect
                    v-model="filter.department"
                    :options="departments"
                    :multiple="false"
                    :close-on-select="true"
                    :clear-on-select="true"
                    :preserve-search="true"
                    :searchable="true"
                    :internal-search="true"
                    :hide-selected="true"
                    :options-limit="50"
                    :placeholder="'Pilih Devisi'"
                    open-direction="bottom"
                    label="name"
                    id="id"
                    track-by="name"
                    @search-change="getDepartments"
                ></Multiselect>
            </div>

            <div class="col-lg-6 mb-3">
                <label class="form-label">Filter Status</label>
                <Dropdown
                    v-model="filter.status"
                    :options="[
                        {
                            label: 'Piutang',
                            value: 'due',
                        },
                        {
                            label: 'Lunas',
                            value: 'paid',
                        },
                    ]"
                    optionLabel="label"
                    optionValue="value"
                    style="width: 100%"
                    :placeholder="'Pilih Status'"
                />
            </div>
        </div>
        <template #footer>
            <button
                type="button"
                @click="resetFilter()"
                :disabled="loader.submit"
                class="btn btn-outline-danger btn-wave waves-effect waves-light"
            >
                Reset Filter
            </button>

            <button
                type="button"
                @click="searchData()"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                Filter Data
            </button>
        </template>
    </Dialog>
    <!-- End Filter Modal -->

    <!-- Create Transaction -->
    <Dialog
        v-model:visible="modal.create"
        :header="'Generate Gaji Pegawai'"
        class="filter-data"
        :style="{ width: '80vh' }"
        :modal="true"
        :draggable="false"
    >
        <Form @submit="generateSalarySlip()" ref="ValidateGenerateSlip">
            <div class="row p-2">
                <div class="col-lg-6 mb-3">
                    <Field
                        :rules="{
                            required: true,
                        }"
                        v-slot="{ errors }"
                        v-model="salary.date"
                        :name="'Tanggal'"
                    >
                        <label class="form-label">Tanggal</label>
                        <InputText
                            v-model="salary.date"
                            type="date"
                            style="width: 100%"
                            :placeholder="'Masukkan Tanggal'"
                        />
                        <div class="fs-sm text-danger">
                            {{ errors[0] }}
                        </div>
                    </Field>
                </div>

                <div class="col-lg-6 mb-3">
                    <label class="form-label">Devisi</label>
                    <Field
                        :rules="{
                            required: true,
                        }"
                        v-slot="{ errors }"
                        v-model="salary.department"
                        :name="'Devisi'"
                    >
                        <Multiselect
                            v-model="salary.department"
                            :options="departments"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            :searchable="true"
                            :internal-search="true"
                            :hide-selected="true"
                            :options-limit="50"
                            :placeholder="'Pilih Devisi'"
                            open-direction="bottom"
                            label="name"
                            id="id"
                            track-by="name"
                            @search-change="getDepartments"
                        ></Multiselect>
                        <div class="fs-sm text-danger">
                            {{ errors[0] }}
                        </div>
                    </Field>
                </div>
            </div>
        </Form>
        <template #footer>
            <button
                type="button"
                @click="generateSalarySlip()"
                :disabled="loader.submit"
                class="btn label-btn label-end btn-primary"
            >
                {{ loader.submit ? "Loading" : "Generate Gaji" }}
                <i class="ti ti-plus label-btn-icon ms-2"></i>
            </button>
        </template>
    </Dialog>
    <!-- End -->

    <Dialog
        v-model:visible="modal.status"
        :header="'Tambah Pembayaran'"
        class="filter-data"
        :style="{ width: '80vh' }"
        :modal="true"
        :draggable="false"
    >
        <div class="row p-2">
            <div class="col-lg-6 mb-3">
                <label class="form-label">Pilih Metode Pembayaran</label>
                <Multiselect
                    v-model="status.method"
                    :options="methods"
                    :multiple="false"
                    :close-on-select="true"
                    :clear-on-select="true"
                    :preserve-search="true"
                    :searchable="true"
                    :internal-search="false"
                    :options-limit="50"
                    :loading="loader.method"
                    placeholder="Pilih Metode Pembayaran"
                    open-direction="bottom"
                    label="name"
                    id="id"
                    track-by="name"
                    @search-change="getComponents"
                ></Multiselect>
            </div>
            <div class="col-lg-6 mb-3">
                <label class="form-label">Tanggal Pembayaran</label>
                <InputText
                    v-model="status.date"
                    type="date"
                    style="width: 100%"
                    :placeholder="'Masukkan Tanggal'"
                />
            </div>
        </div>
        <template #footer>
            <button
                type="button"
                v-if="status.method.id != '' && status.method.id != null"
                @click="approvalOrRejected(status.id, status.status)"
                :disabled="loader.submit"
                class="btn label-btn label-end btn-primary"
            >
                {{ loader.submit ? "Loading...." : "Lakukan Pembayaran" }}
                <i class="ti ti-plus label-btn-icon ms-2"></i>
            </button>
        </template>
    </Dialog>
</template>

<script>
import NProgress from "nprogress";
import Swal from "sweetalert2";
var _ = require("lodash");
import { ApiData } from "@/api/server";
export default {
    components: {},
    data() {
        return {
            salaries: [],
            departments: [],
            methods: [],
            totalRows: 0,
            page: 1,
            limit: 20,
            loader: {
                data: false,
                submit: false,
                user: false,
                method: false,
                department: false,
            },
            modal: {
                filter: false,
                create: false,
                status: false,
            },
            salary: {
                department: {
                    id: "",
                    name: "",
                },
                date: "",
            },
            filter: {
                name: "",
                store: {
                    id: "",
                    name: "",
                },

                department: {
                    id: "",
                    name: "",
                },
                status: "",
                date: {
                    start: "",
                    end: "",
                },
            },
            status: {
                id: "",
                status: "",
                date: "",
                method: {
                    id: "",
                    name: "",
                },
            },
        };
    },
    computed: {},
    created() {
        this.getData();
        this.getDepartments("");
        this.getComponents("");
    },
    methods: {
        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;
            try {
                const response = await ApiData.get(
                    `app/hrm/salaries?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&start_date=${this.filter.date.start}&end_date=${this.filter.date.end}&department=${this.filter.department.id}&status=${this.filter.status}&store=${this.filter.store.id}`
                );
                var data = response.data;
                this.salaries = data.salaries;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getDepartments(query) {
            try {
                const response = await ApiData.get(
                    "app/hrm/employees/departments?name=" + query
                );
                this.departments = response.data.departments;
            } catch (error) {
                this.$toast.add({
                    severity: "error",
                    summary: "Peringatan",
                    detail: "Silahkan Refresh Halaman untuk mencoba kembali",
                    life: 3000,
                });
            }
        },

        async getComponents(query) {
            this.loader.method = true;
            try {
                const response = await ApiData.get(
                    `app/master/payment-method?name=${query}&`
                );
                var data = response.data;
                this.methods = data.methods;
                this.loader.method = false;
            } catch (error) {
                console.log(error);
            }
        },

        editStatus(data, type) {
            this.status = {
                id: data.id,
                status: type,
                date: "",
                method: {
                    id: "",
                    name: "",
                },
            };
            this.modal.status = true;
        },

        approvalOrRejected(id, type) {
            this.modal.status = false;
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Data yang di hapus tidak dapat di kembalikan lagi",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.post("app/hrm/salaries/update/" + id, {
                        method: this.status.method,
                        date: this.status.date,
                    })
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.modal.status = false;
                            this.getData();
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.loader.submit = false;
                            this.$handleErrorResponse(err);
                        });
                } else {
                    Swal.fire("Membatalkan Proses Hapus Data");
                }
            });
        },

        searchData() {
            this.doSearch(this);
            this.modal.filter = false;
        },

        doSearch: _.debounce((rootInstance) => {
            rootInstance.getData();
        }, 300),

        onPageChange(e) {
            this.limit = e.rows;
            this.page = e.page += 1;
            this.getData(this.page);
        },

        filterDate() {
            var date = this.filter.date;
            if (date != null) {
                this.filter.date = {
                    start:
                        date.start != null ? date.start.substring(0, 10) : "",
                    end: date.end != null ? date.end.substring(0, 10) : "",
                };
            }
        },

        resetFilter() {
            this.filter = {
                name: "",
                store: {
                    id: "",
                    name: "",
                },

                department: {
                    id: "",
                    name: "",
                },
                status: "",
                date: {
                    start: "",
                    end: "",
                },
            };
            this.searchData();
        },

        formatData() {
            this.salary = {
                department: {
                    id: "",
                    name: "",
                },
                date: "",
            };
            this.modal.create = false;
        },

        generateSalarySlip() {
            this.$refs.ValidateGenerateSlip.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Peringatan!",
                        detail: "Silahkan Check kembali form inputan anda",
                        life: 3000,
                    });
                } else {
                    this.loader.submit = true;
                    setTimeout(() => {
                        return this.$router.push({
                            name: "generate_salary",
                            params: {
                                date: this.salary.date,
                                department: this.salary.department.id,
                            },
                        });
                    }, 1000);
                }
            });
        },

        deleteData(id) {
            Swal.fire({
                title: "Peringatan!",
                text: "Data yang telah di hapus tidak dapat di kembalikan lagi",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.delete("app/hrm/salaries/delete/" + id)
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
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

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },
    },
    mounted: function () {},
    watch: {
        "filter.date": function (newDate, oldDate) {
            if (newDate === null) {
                this.filter.date = {
                    start: "",
                    end: "",
                };
                this.getData();
            }
        },
        $route(to, from) {
            this.type = this.$route.query.type == "draft" ? "true" : "false";
            this.getData();
        },
        totalRows() {
            if (this.totalRows < 2) {
                this.$nextTick(() => {
                    document
                        .querySelector(".p-datatable-wrapper")
                        .classList.add("overflow-visible");
                    document.querySelector(".table-responsive").style =
                        "overflow-x: visible";
                });
            }
        },
    },
};
</script>
