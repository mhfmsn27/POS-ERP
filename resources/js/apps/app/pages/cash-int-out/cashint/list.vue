<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header p-4 d-flex justify-content-between">
                <div>
                    <label class="form-label">Cari Penerimaan</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="filter.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Penerimaan...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
                <div class="d-flex justify-content-start">
                    <a
                        class="btn btn-info me-2"
                        href="javascript:void(0);"
                        @click="modal.filter = true"
                        ><i class="fa fa-filter"></i> Filter Data</a
                    >
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'create_cash_int',
                            })
                        "
                        class="btn btn-primary"
                        ><i class="fa fa-plus-circle"></i>
                        Buat Transaksi
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
                        :value="transactions"
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
                        <Column field="ref_no" header="Nomor"></Column>
                        <Column field="date" header="Tanggal"> </Column>
                        <Column field="method.name" header="Kas/Bank"></Column>
                        <Column field="detail" header="Keterangan"></Column>
                        <Column header="Nilai">
                            <template #body="{ data }">
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>
                        <Column field="action" header="Aksi">
                            <template #body="{ data }">
                                <a
                                    class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light me-2"
                                    @click="deleteTransaction(data.id)"
                                    ><i class="fa fa-trash"></i>
                                </a>
                                <a
                                    href="javascript:void(0)"
                                    @click="
                                        $goTo({
                                            name: 'update_cash_int',
                                            params: { id: data.id },
                                        })
                                    "
                                    class="btn btn-icon btn-outline-warning rounded-pill btn-wave waves-effect waves-light me-2"
                                    ><i class="fa fa-pencil"></i>
                                </a>
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
        header="Filter Data"
        class="filter-data"
        position="top"
        :style="{ width: '40rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row p-2">
            <div class="col-lg-12 mt-2">
                <label class="form-label">Tanggal Transaksi</label>
                <div class="input-group">
                    <VueCtkDateTimePicker
                        label="Filter Tanggal"
                        locale="Asia/Jakarta"
                        class="form-control"
                        v-model="filter.date"
                        @validate="filterDate"
                        :range="true"
                    />
                </div>
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
</template>

<script>
import NProgress from "nprogress";
import Swal from "sweetalert2";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "cash_int",
    components: {},
    data() {
        return {
            transactions: [],
            totalRows: 0,
            page: 1,
            limit: 20,
            loader: {
                data: false,
                submit: false,
            },
            modal: {
                filter: false,
            },
            filter: {
                name: "",
                date: {
                    start: "",
                    end: "",
                },
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
                    `app/expenses?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&start_date=${this.filter.date.start}&end_date=${this.filter.date.end}&type=cash_int`
                );
                var data = response.data;
                this.transactions = data.transactions;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
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
                date: {
                    start: "",
                    end: "",
                },
            };
            this.searchData();
        },

        formatNumber(number) {
            if (parseFloat(number) >= 0) {
                return number.toLocaleString();
            } else {
                return "-" + (-number).toLocaleString();
            }
        },

        deleteTransaction(id) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Draft Transaksi yang telah di hapus tidak dapat dikembalikan lagi",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.delete("app/expenses/delete/" + id)
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
    },
};
</script>
