<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-4">
                <div>
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control"
                            v-model="filter.name"
                            @input="searchData()"
                            placeholder="Cari Data...."
                            aria-describedby="search-team-member"
                        />
                        <button
                            class="btn btn-light btn-primary"
                            type="button"
                            id="search-team-member"
                        >
                            <i class="fe fe-search"></i>
                        </button>
                    </div>
                </div>
                <button
                    type="button"
                    v-tooltip.top="'Refresh'"
                    @click="getData()"
                    class="btn btn-outline-info btn-wave waves-effect waves-light ms-2"
                >
                    <i class="fa fa-refresh"></i>
                </button>
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
                        <Column field="store.name" header="Toko"></Column>
                        <Column
                            field="supplier.name"
                            header="Supplier"
                        ></Column>
                        <Column header="Penerimaan">
                            <template #body="{ data }">
                                <span
                                    class="badge bg-warning"
                                    v-if="data.status == 'draft'"
                                    >Draft</span
                                >

                                <span
                                    class="badge bg-success"
                                    v-if="data.status == 'received'"
                                    >Di Terima</span
                                >

                                <br v-if="data.qty_return > 0" />
                                <span
                                    v-if="data.qty_return > 0"
                                    class="badge rounded-pill bg-outline-secondary mt-1"
                                    >{{
                                        "(" +
                                        data.qty_return +
                                        ") Di Kembalikan"
                                    }}</span
                                >
                            </template>
                        </Column>
                        <Column header="Pembayaran">
                            <template #body="{ data }">
                                <span
                                    class="badge bg-warning text-black"
                                    v-if="data.status_payment == 'due'"
                                    >Piutang</span
                                >
                                <span
                                    class="badge bg-success"
                                    v-if="data.status_payment == 'paid'"
                                    >Lunas</span
                                >
                                <br v-if="data.status_payment == 'due'" />
                                <span
                                    class="badge bg-warning text-black mt-1"
                                    v-if="data.status_payment == 'due'"
                                    >Rp {{ formatNumber(data.due_total) }}</span
                                >
                            </template>
                        </Column>
                        <Column header="Total">
                            <template #body="{ data }">
                                {{ formatNumber(data.final_total) }}
                            </template>
                        </Column>
                        <Column
                            field="created.name"
                            header="Ditambahkan"
                        ></Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </div>
    <!-- End List Data -->
</template>

<script>
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "type_list",
    components: {},
    data() {
        return {
            editmode: false,
            transactions: [],
            types: [],
            account_choose: [],
            account_transfer: [],
            due: {
                date: "",
                amount: 0,
                note: "",
            },
            payment: {
                id: null,
                amount: 0,
                account: {
                    id: "",
                    name: "",
                },
                date: "",
                note: "",
                name: "",
            },
            modal: {
                create: false,
                deposit: false,
                transfer: false,
            },
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
                account: false,
                submit: false,
                type: false,
            },
            filter: {
                name: "",
                type: {
                    id: "",
                    name: "",
                },
                store: {
                    id: "",
                    name: "",
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
                    `app/transactions/purchases?limit=${this.limit}&page=${this.page}&ref=${this.filter.name}&supplier=${this.$route.params.id}&status=received`
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
