<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header p-4 d-flex justify-content-between">
                <div>
                    <label class="form-label">Cari Transaksi</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                        <input
                            type="text"
                            v-model="filter.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Transaksi...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
                <div class="mt-3 text-end d-none d-sm-block">
                    <button
                        class="btn btn-info me-2"
                        type="button"
                        @click="modal.filter = true"
                    >
                        <i class="fe fe-filter"></i> Filter Data
                    </button>
                    <a
                        class="btn btn-info"
                        :to="{ name: '' }"
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'create_spt',
                            })
                        "
                    >
                        <i class="fe fe-plus-circle"></i> Buat Spt
                    </a>
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
                        <Column header="Tanggal">
                            <template #body="{ data }">
                                {{ data.date.start }} - {{ data.date.end }}
                            </template>
                        </Column>
                        <Column field="ntpt" header="NTPT"></Column>
                        <Column header="Nominal">
                            <template #body="{ data }">
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>
                        <Column header="Tipe">
                            <template #body="{ data }">
                                {{ data.type == "lebih" ? "Lebih" : "Kurang" }}
                            </template>
                        </Column>
                        <Column
                            header="Tanggal Di Buat"
                            field="created"
                        ></Column>
                        <Column field="action" header="Aksi">
                            <template #body="{ data }">
                                <a
                                    href="javascript:void(0)"
                                    @click="
                                        $goTo({
                                            name: 'detail_spt',
                                            params: { id: data.id },
                                        })
                                    "
                                    tag="a"
                                    v-tooltip.left="'Detail SPT'"
                                    class="btn btn-icon btn-outline-info rounded-pill btn-wave waves-effect waves-light me-2"
                                >
                                    <i class="fa fa-eye"></i>
                                </a>
                                <button
                                    v-tooltip.left="'Hapus SPT'"
                                    class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                                    @click="deleteSpt(data.id)"
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
            <div class="col-lg-6 mt-2">
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
import { ApiData } from "@/api/server";
import Swal from "sweetalert2";
var _ = require("lodash");

export default {
    name: "list_purchase",
    components: {},
    data() {
        return {
            transactions: [],
            users: [],
            customers: [],
            totalRows: 0,
            page: 1,
            limit: 20,
            loader: {
                user: false,
                data: false,
                customer: false,
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
                status: "",
                payment_status: "",
            },
        };
    },
    computed: {},
    created() {
        this.getData();
    },
    methods: {
        deleteSpt(id) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "SPT yang telah di hapus tidak dapat dikembalikan lagi",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    ApiData.delete("app/taxs/spt/delete/" + id)
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            this.getData();
                        })
                        .catch((err) => {
                            this.$handleErrorResponse(err);
                        });
                } else {
                    Swal.fire("Membatalkan Proses Hapus Data");
                }
            });
        },

        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;
            try {
                const response = await ApiData.get(
                    `app/taxs/spt?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&start_date=${this.filter.date.start}&end_date=${this.filter.date.end}`
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
                status: "",
                payment_status: "",
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
    },
};
</script>
