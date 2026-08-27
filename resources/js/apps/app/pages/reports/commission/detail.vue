<template>
    <div class="col-12">
        <!-- List Data -->
        <div class="row">
            <div class="col-lg-4">
                <div
                    class="card card-block card-stretch card-height"
                >
                    <div class="card-body relative-background">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle card-icon iq-bg-primary mr-3"
                            >
                                <i class="ri-exchange-dollar-line"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="">
                                    Rp {{ formatNumber(summary.total_faktur) }}
                                </h4>
                                <h5 class="">Total Faktur</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div
                    class="card card-block card-stretch card-height"
                >
                    <div class="card-body relative-background">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle card-icon iq-bg-primary mr-3"
                            >
                                <i class="ri-exchange-dollar-line"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="">
                                    {{
                                        formatNumber(summary.total_transaction)
                                    }}
                                </h4>
                                <h5 class="">Total Penjualan</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div
                    class="card card-block card-stretch card-height"
                >
                    <div class="card-body relative-background">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle card-icon iq-bg-primary mr-3"
                            >
                                <i class="ri-exchange-dollar-line"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="">
                                    Rp
                                    {{ formatNumber(summary.total_commission) }}
                                </h4>
                                <h5 class="">Total Komisi</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card custom-card">
                    <div
                        class="card-header p-4 d-flex justify-content-between"
                    >
                        <div>
                            <label class="form-label">Cari Transaksi</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"
                                    ><i class="fa fa-search"></i>
                                </span>
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
                                <Column field="date" header="Tanggal"></Column>
                                <Column field="ref_no" header="Nomor Ref">
                                    <template #body="{ data }">
                                        {{ data.ref_no }}
                                    </template>
                                </Column>
                                <Column
                                    field="customer"
                                    header="Pelanggan"
                                ></Column>
                                <Column header="Ditambahkan" field="created">
                                    <template #body="{ data }">
                                        {{ formatNumber(data.faktur) }}
                                    </template>
                                </Column>
                                <Column header="Nilai Komisi">
                                    <template #body="{ data }">
                                        {{ formatNumber(data.commission) }}
                                    </template>
                                </Column>
                                <Column header="Total Faktur">
                                    <template #body="{ data }">
                                        {{ formatNumber(data.faktur) }}
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- End List Data -->
    </div>
</template>

<script>
var _ = require("lodash");
import { ApiData } from "@/api/server";
export default {
    name: "list_purchase",
    components: {},
    data() {
        return {
            transactions: [],
            users: [],
            suppliers: [],
            totalRows: 0,
            page: 1,
            limit: 20,
            summary: {
                total_faktur: 0,
                total_commission: 0,
                total_transaction: 0,
            },
            loader: {
                user: false,
                data: false,
                supplier: false,
                submit: false,
            },
            modal: {
                filter: false,
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
                    `app/reports/commission/detail/${this.$route.params.id}?limit=${this.limit}&page=${this.page}&ref=${this.filter.name}&start_date=${this.$route.query.start_date ?? ''}&end_date=${this.$route.query.end_date ?? ''}&user=${this.$route.params.id}`
                );
                var data = response.data;
                this.transactions = data.transactions;
                this.summary = data.summary;
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

        formatNumber(number) {
            if (parseFloat(number) >= 0) {
                return number.toLocaleString();
            } else {
                return "-" + (-number).toLocaleString();
            }
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
