<template>
    <!-- Create Data -->
    <div class="mt-4 col-lg-4">
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
                        v-tooltip.top="'Klik, Untuk Membatalkan Edit Data'"
                        class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <Form @submit="ValidationCustomers()" ref="customerValidation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Tanggal Kasbon</label
                            >
                            <Calendar
                                v-model="kasbon.date"
                                style="width: 100%"
                            />
                        </div>

                        <div class="col-12 mt-2">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Pilih Pegawai</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                v-model="kasbon.employee"
                                name="Pilih Pegawai"
                            >
                                <Multiselect
                                    v-model="kasbon.employee"
                                    :options="employees"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :preserve-search="true"
                                    :searchable="true"
                                    :loading="loader.employee"
                                    :internal-search="true"
                                    :options-limit="50"
                                    placeholder="Pilih Pegawai"
                                    open-direction="bottom"
                                    label="name"
                                    id="id"
                                    track-by="name"
                                    @search-change="getEmployees"
                                ></Multiselect>
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12 mt-2">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Metode Pembayaran</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                v-model="kasbon.method"
                                name="Metode Pembayaran"
                            >
                                <Multiselect
                                    v-model="kasbon.method"
                                    :options="methods"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :preserve-search="true"
                                    :searchable="true"
                                    :loading="loader.method"
                                    :internal-search="true"
                                    :options-limit="50"
                                    placeholder="Pilih Metode"
                                    open-direction="bottom"
                                    label="name"
                                    id="id"
                                    track-by="name"
                                    @search-change="getMethods"
                                ></Multiselect>
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12 mt-2">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Nominal Kasbon</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                v-model="kasbon.amount"
                                name="Nominal Kasbon"
                            >
                                <InputNumber
                                    style="width: 100%"
                                    v-model="kasbon.amount"
                                    prefix="Rp "
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12 mt-2">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Tipe</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                v-model="kasbon.type"
                                name="Tipe"
                            >
                                <Dropdown
                                    v-model="kasbon.type"
                                    :options="[
                                        {
                                            value: 'int',
                                            label: 'Pembayaran Kasbon',
                                        },
                                        {
                                            value: 'out',
                                            label: 'Permintaan Kasbon',
                                        },
                                    ]"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Pilih Tipe"
                                    style="width: 100%"
                                    class="w-full md:w-14rem"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Catatan</label
                            >
                            <textarea
                                class="form-control"
                                v-model="kasbon.note"
                            ></textarea>
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
    <div class="mt-4 col-lg-8">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-2">
                <div>
                    <label class="control-label">Nama Pegawai</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
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
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <DataTable
                        :value="kasbons"
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
                        <Column field="employee.name" header="Pegawai"></Column>
                        <Column field="method.name" header="Metode"></Column>
                        <Column header="Nominal">
                            <template #body="{ data }">
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>
                        <Column header="Catatan" field="note"> </Column>
                        <Column header="Tipe">
                            <template #body="{ data }">
                                {{
                                    data.type == "int"
                                        ? "Pembayaran"
                                        : "Permintaan"
                                }}
                            </template>
                        </Column>

                        <Column header="Aksi">
                            <template #body="{ data }">
                                <div class="btn-group mt-2 mb-2">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        Aksi <span class="caret"></span>
                                    </button>
                                    <ul
                                        class="dropdown-menu"
                                        role="menu"
                                        style=""
                                    >
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                @click="removeData(data.id)"
                                                ><i
                                                    class="fa fa-trash mr-2"
                                                ></i>
                                                Hapus Data</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                @click="editData(data)"
                                                ><i
                                                    class="fa fa-pencil mr-2"
                                                ></i>
                                                Edit Data</a
                                            >
                                        </li>
                                    </ul>
                                </div> 
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
            accounts: [],
            employees: [],
            kasbons: [],
            methods: [],
            kasbon: {
                method: {
                    id: "",
                    name: "",
                },
                employee: {
                    id: "",
                    name: "",
                },
                amount: 0,
                note: "",
                date: "",
                type: "out",
            },

            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
                submit: false,
                account: false,
                employee: false,
                method: false,
            },
            filter: {
                name: "",
            },
        };
    },
    computed: {},
    created() {
        this.getMethods("");
        this.getData();
        this.getEmployees("");
        const today = new Date().toISOString().substr(0, 10);
        this.kasbon.date = today;
    },
    methods: {
        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;

            try {
                const response = await ApiData.get(
                    `app/hrm/kasbon?limit=${this.limit}&page=${this.page}&name=${this.filter.name}`
                );
                var data = response.data;
                this.kasbons = data.kasbons;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getMethods(query) {
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

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },

        async getEmployees(query) {
            this.loader.employee = true;
            try {
                const response = await ApiData.get(
                    `app/hrm/employees?name=${query}`
                );
                var data = response.data;
                this.employees = data.employees;
                this.loader.employee = false;
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
                    ApiData.delete("app/hrm/kasbon/delete/" + id)
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();

                            if (id == this.kasbon.id) {
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

        editData(data) {
            this.kasbon = data;
            this.editmode = true;
        },

        ValidationCustomers() {
            this.$refs.customerValidation.validate().then((success) => {
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
            ApiData.post("app/hrm/kasbon/create", this.kasbon)
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
            ApiData.post("app/hrm/kasbon/update/" + this.kasbon.id, this.kasbon)
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
            this.kasbon = {
                method: {
                    id: "",
                    name: "",
                },
                employee: {
                    id: "",
                    name: "",
                },
                amount: 0,
                note: "",
                date: "",
                type: "out",
            };

            this.editmode = false;
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
