<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-3">
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
                <div class="d-flex justify-content-start">
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'employee_create',
                            })
                        "
                        class="btn btn-blue"
                    >
                        <i class="fe fe-plus-circle me-2"></i> Tambah Pegawai
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
                        :value="employees"
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
                        <Column field="designation" header="Jabatan"></Column>
                        <Column field="department" header="Devisi"></Column>
                        <Column field="salary" header="Gaji"></Column>

                        <Column header="Aksi">
                            <template #body="{ data }">
                                <div class="btn-list">
                                    <a
                                        href="javascript:void(0)"
                                        @click="
                                            $goTo({
                                                name: 'employee_create',
                                            })
                                        "
                                        :to="{
                                            name: 'employee_update',
                                            params: {
                                                id: data.id,
                                                name: data.name,
                                            },
                                            query: {
                                                tab: true,
                                            },
                                        }"
                                        class="btn btn-orange"
                                    >
                                        <i class="fe fe-edit me-2"></i>Edit
                                        Pegawai
                                    </a>

                                    <button
                                        class="btn btn-red"
                                        type="button"
                                        @click="removeData(data.id)"
                                    >
                                        <i class="fe fe-trash me-2"></i>Delete
                                    </button>
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
            employees: [],
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
            },
            filter: {
                name: "",
            },
        };
    },
    methods: {
        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;

            try {
                const response = await ApiData.get(
                    `app/master/employees?limit=${this.limit}&page=${this.page}&name=${this.filter.name}`
                );
                var data = response.data;
                this.employees = data.employees;
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
                    ApiData.delete("app/master/employees/delete/" + id)
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
    },
    mounted: function () {
        this.getData();
    },
};
</script>
