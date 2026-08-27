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
                    <button
                        class="btn btn-info"
                        type="button"
                        @click="downloadKeluaran"
                    >
                        <i class="fa fa-download"></i> Download Pajak Keluaran
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
                        <Column
                            field="customer.name"
                            header="Pelanggan"
                        ></Column>
                        <Column field="customer.npwp" header="NPWP"></Column>
                        <Column
                            field="ref_no"
                            header="Faktur Penjualan"
                        ></Column>
                        <Column field="tax_ref" header="Faktur Pajak"></Column>
                        <Column header="PPN">
                            <template #body="{ data }">
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>
                        <Column header="Gunggung">
                            <template #body="{ data }">
                                {{
                                    data.tax_gunggung == true ? "Iya" : "Tidak"
                                }}
                            </template>
                        </Column>
                        <Column field="tax_type" header="Tipe Pajak"></Column>
                        <Column header="Di Terima">
                            <template #body="{ data }">
                                <InputSwitch
                                    @change="changeData(data)"
                                    v-model="data.tax_paid"
                                />
                            </template>
                        </Column>
                        <Column header="Status SPT">
                            <template #body="{ data }">
                                <span
                                    class="badge bg-warning text-black"
                                    v-if="data.tax_status == 'pending'"
                                    >Belum</span
                                >
                                <span
                                    class="badge  bg-success"
                                    v-if="data.tax_status == 'complete'"
                                    >Selesai</span
                                >
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

            <div class="col-lg-6 mt-2">
                <label class="form-label">Status Diterima</label>
                <div class="input-group">
                    <Dropdown
                        v-model="filter.status"
                        :options="[
                            {
                                name: 'Iya',
                                value: 'paid',
                            },
                            {
                                name: 'Tidak',
                                value: 'due',
                            },
                        ]"
                        optionLabel="name"
                        optionValue="value"
                        placeholder="Pilih Opsi"
                        style="width: 100%"
                        class="w-full md:w-14rem"
                    />
                </div>
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label">Status SPT</label>
                <div class="input-group">
                    <Dropdown
                        v-model="filter.payment_status"
                        :options="[
                            {
                                name: 'Iya',
                                value: 'complete',
                            },
                            {
                                name: 'Tidak',
                                value: 'pending',
                            },
                        ]"
                        optionLabel="name"
                        optionValue="value"
                        placeholder="Pilih Opsi"
                        style="width: 100%"
                        class="w-full md:w-14rem"
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
import { ApiData } from "@/api/server";
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
        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;
            try {
                const response = await ApiData.get(
                    `app/taxs?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&start_date=${this.filter.date.start}&end_date=${this.filter.date.end}&sub_type=tax_output&status_payment=${this.filter.payment_status}&status=${this.filter.status}`
                );
                var data = response.data;
                this.transactions = data.transactions;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async changeData(data) {
            NProgress.start();
            NProgress.set(0.1);
            try {
                const response = await ApiData.post(
                    `app/taxs/change-status/${data.id}`
                );
                this.$handleSuccessResponse(response.data.message);
                NProgress.done();
                this.getData();
            } catch (error) {
                NProgress.done();
                this.getData();
                this.$handleErrorResponse(error);
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

        async downloadKeluaran() {
            NProgress.start();
            NProgress.set(0.1);

            try {
                const response = await ApiData.get(
                    `app/taxs/download?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&start_date=${this.filter.date.start}&end_date=${this.filter.date.end}&sub_type=tax_output&status_payment=${this.filter.payment_status}&status=${this.filter.status}`,
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

                link.setAttribute("download", "pajak_keluaran.csv");
                document.body.appendChild(link);
                link.click();
                NProgress.done();
            } catch (error) {
                NProgress.done();
                console.log(error);
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
