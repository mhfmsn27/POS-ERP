<template>
    <!-- List Data -->
    <div class="col-lg-12 mt-4">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-2">
                <div>
                    <label class="form-label">Tanggal </label>
                    <div class="btn-group btn-group2 w-100" role="group">
                        <VueCtkDateTimePicker
                            label="Filter Tanggal"
                            locale="Asia/Jakarta"
                            class="form-control"
                            v-model="filter.date"
                            @validate="filterDate"
                            :range="true"
                        />
                        <button
                            type="button"
                            v-tooltip.top="'Hapus Filter'"
                            @click="removeFilter('date')"
                            class="btn btn-outline-danger btn-wave waves-effect waves-light"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <button
                    type="button"
                    v-tooltip.top="'Refresh'"
                    @click="getData()"
                    class="btn btn-outline-info btn-wave waves-effect waves-light"
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
                        <Column field="date" header="Tanggal"></Column>
                        <Column field="ref_no" header="Sumber">
                            <template #body="{ data }">
                                <p v-if="data.transaction.route == null">
                                    {{ data.ref_no }}
                                </p>
                                <a
                                    v-else
                                    href="javascript:void(0);"
                                    class="text-info"
                                    @click="
                                        $goTo({
                                            name: data.transaction.route,
                                            params: { id: data.transaction.id },
                                        })
                                    "
                                >
                                    {{ data.ref_no }}
                                </a>
                            </template>
                        </Column>
                        <Column header="Transaksi">
                            <template #body="{ data }">
                                <p v-if="data.transaction.ref == null">
                                    {{ data.transaction_due.ref }}
                                </p>
                                <p v-else>
                                    {{ data.transaction.ref }}
                                </p>
                            </template>
                        </Column>
                        <Column header="Tipe Transaksi">
                            <template #body="{ data }">
                                <p v-if="data.sub_type == 'deposit'">
                                    Deposit Saldo
                                </p>
                                <p v-if="data.sub_type == 'deposit_equitas'">
                                    Deposit Saldo
                                </p>
                                <p v-if="data.sub_type == 'transfer_dana'">
                                    Transfer Saldo
                                </p>
                                <p v-if="data.sub_type == 'received_dana'">
                                    Terima Saldo
                                </p>
                                <p
                                    v-if="
                                        data.sub_type == 'first_stock' ||
                                        data.sub_type == 'deposit_stock_product'
                                    "
                                >
                                    Stok Awal Barang
                                </p>

                                <p v-if="data.sub_type == 'due_supplier'">
                                    Utang Awal Supplier
                                </p>

                                <p
                                    v-if="
                                        data.sub_type ==
                                        'received_product_from_supplier'
                                    "
                                >
                                    Penerimaan Barang
                                </p>

                                <p
                                    v-if="
                                        data.sub_type == 'pay_supplier_faktur'
                                    "
                                >
                                    Pembayaran Faktur
                                </p>

                                <p v-if="data.sub_type == 'saldo_supplier'">
                                    Penyimpanan Saldo Supplier
                                </p>

                                <p v-if="data.sub_type == 'wd_supplier'">
                                    Penggunaan Saldo Supplier
                                </p>

                                <p v-if="data.sub_type == 'return_purchase'">
                                    Retur Pembelian
                                </p>

                                <p
                                    v-if="
                                        data.sub_type ==
                                        'sent_product_to_customer'
                                    "
                                >
                                    Pengiriman Barang
                                </p>

                                <p v-if="data.sub_type == 'due_customer'">
                                    Utang Pelanggan
                                </p>

                                <p v-if="data.sub_type == 'sale_faktur'">
                                    Faktur Penjualan
                                </p>

                                <p v-if="data.sub_type == 'saldo_customer'">
                                    Deposit Saldo Customer
                                </p>

                                <p
                                    v-if="
                                        data.sub_type == 'pay_customer_faktur'
                                    "
                                >
                                    Pembayaran Faktur Penjualan
                                </p>

                                <p v-if="data.sub_type == 'return_sell'">
                                    Retur Penjualan
                                </p>
                            </template>
                        </Column>
                        <Column field="name" header="Keterangan"></Column>
                        <Column header="Mutasi">
                            <template #body="{ data }">
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>
                        <Column header="Tipe">
                            <template #body="{ data }">
                                {{ data.type == "debit" ? "Debit" : "Kredit" }}
                            </template>
                        </Column>
                        <Column header="Saldo">
                            <template #body="{ data }">
                                {{ formatNumber(data.saldo) }}
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
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    components: {},
    data() {
        return {
            editmode: false,
            transactions: [],
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
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
                    `app/account/history?limit=${this.limit}&page=${this.page}&account=${this.$route.params.id}&start=${this.filter.date.start}&end=${this.filter.date.end}`
                );
                var data = response.data;
                this.transactions = data.transactions;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        filterDate() {
            var date = this.filter.date;
            if (date != null) {
                this.filter.date = {
                    start:
                        date.start != null ? date.start.substring(0, 10) : "",
                    end: date.end != null ? date.end.substring(0, 10) : "",
                };

                this.getData();
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
            if (parseInt(number) != 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },

        removeFilter(type) {
            if (type == "date") {
                this.filter.date = {
                    start: "",
                    end: "",
                };
            }

            this.getData();
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
