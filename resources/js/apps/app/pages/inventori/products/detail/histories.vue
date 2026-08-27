<template>
    <div class="p-4">
        <div class="row">
            <div class="col-12 d-flex justify-content-between">
                <div>
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
                <div>
                    <label class="form-label">Tipe Transaksi</label>
                    <div class="input-group">
                        <Dropdown
                            v-model="filter.type"
                            @change="searchData"
                            :options="[
                                {
                                    name: 'Semua',
                                    value: '',
                                },
                                {
                                    name: 'Stok Opname ( Pengurangan)',
                                    value: 'adjustment',
                                },
                                {
                                    name: 'Stok Opname ( Penambahan )',
                                    value: 'adjustment_add',
                                },
                                {
                                    name: 'Penerimaan Barang',
                                    value: 'received_product',
                                },
                                {
                                    name: 'Retur Penjualan',
                                    value: 'return_sell',
                                },
                                {
                                    name: 'Pembelian',
                                    value: 'purchase',
                                },
                                {
                                    name: 'Retur Pembelian',
                                    value: 'return',
                                },
                                {
                                    name: 'Penjualan',
                                    value: 'sell',
                                },
                                {
                                    name: 'Pengiriman Barang',
                                    value: 'shipping_product',
                                },
                            ]"
                            optionLabel="name"
                            optionValue="value"
                            placeholder="Pilih Tipe"
                            style="width: 100%"
                            class="w-full md:w-14rem"
                        />
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <DataTable
                    :value="histories"
                    :paginator="true"
                    :rows="limit"
                    :rowsPerPageOptions="[20, 50, 100]"
                    paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                    :lazy="true"
                    :totalRecords="totalRows"
                    @page="onPageChange($event)"
                    class="table text-nowrap"
                    :loading="loader.data"
                    responsiveLayout="scroll"
                    sortField="dynamicSortField"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                >
                    <Column header="Nama Produk" field="name"></Column>
                    <Column header="Tanggal">
                        <template #body="{ data }">
                            {{ data.date.substring(0, 10) }} {{ data.time }}
                        </template>
                    </Column>
                    <Column header="Asal Transaksi">
                        <template #body="{ data }">
                            <a
                                style="text-decoration: none"
                                href="javascript:void(0)"
                                @click="
                                    $goTo({
                                        name: 'stock_opname_detail',
                                        params: {
                                            id: data.transaction.id,
                                        },
                                    })
                                "
                                v-if="
                                    data.type == 'adjustment' ||
                                    data.type == 'adjustment_add'
                                "
                                >{{ data.transaction.ref_no }}
                            </a>

                            <a
                                style="text-decoration: none"
                                href="javascript:void(0)"
                                @click="
                                    $goTo({
                                        name: 'purchase_received_detail',
                                        params: {
                                            id: data.transaction.id,
                                        },
                                    })
                                "
                                v-if="data.type == 'received_product'"
                                >{{ data.transaction.ref_no }}
                            </a>

                            <a
                                style="text-decoration: none"
                                href="javascript:void(0)"
                                @click="
                                    $goTo({
                                        name: 'purchase_detail',
                                        params: {
                                            id: data.transaction.id,
                                        },
                                    })
                                "
                                v-if="data.type == 'purchase'"
                                >{{ data.transaction.ref_no }}
                            </a>

                            <a
                                style="text-decoration: none"
                                href="javascript:void(0)"
                                @click="
                                    $goTo({
                                        name: 'purchase_return_detail',
                                        params: {
                                            id: data.transaction.id,
                                        },
                                    })
                                "
                                v-if="data.type == 'return'"
                                >{{ data.transaction.ref_no }}
                            </a>

                            <a
                                style="text-decoration: none"
                                href="javascript:void(0)"
                                @click="
                                    $goTo({
                                        name: 'sales_shipping_detail',
                                        params: {
                                            id: data.transaction.id,
                                        },
                                    })
                                "
                                v-if="data.type == 'shipping_product'"
                                >{{ data.transaction.ref_no }}
                            </a>

                            <a
                                style="text-decoration: none"
                                href="javascript:void(0)"
                                @click="
                                    $goTo({
                                        name: 'sales_detail',
                                        params: {
                                            id: data.transaction.id,
                                        },
                                    })
                                "
                                v-if="data.type == 'sell'"
                                >{{ data.transaction.ref_no }}
                            </a>

                            <a
                                style="text-decoration: none"
                                href="javascript:void(0)"
                                @click="
                                    $goTo({
                                        name: 'sales_return_detail',
                                        params: {
                                            id: data.transaction.id,
                                        },
                                    })
                                "
                                v-if="data.type == 'return_sell'"
                                >{{ data.transaction.ref_no }}
                            </a>
                        </template>
                    </Column>
                    <Column header="Keterangan">
                        <template #body="{ data }">
                            <div class="d-flex justify-content-start">
                                <Badge
                                    v-if="data.type == 'received_product'"
                                    severity="info"
                                    value="Penerimaan Barang"
                                />
                                <Badge
                                    v-if="data.type == 'return_sell'"
                                    severity="danger"
                                    value="Return Penjualan Produk"
                                />
                                <Badge
                                    v-if="data.type == 'return_transfer'"
                                    severity="danger"
                                    value="Return Produk Transfer Stok"
                                />
                                <Badge
                                    v-if="
                                        data.type == 'return_transfer_received'
                                    "
                                    severity="info"
                                    value="Penerimaan Return Produk Transfer Stok"
                                />
                                <Badge
                                    v-if="data.type == 'adjustment'"
                                    severity="danger"
                                    value="Stok Opname ( Pengurangan Stok )"
                                />
                                <Badge
                                    v-if="data.type == 'adjustment_add'"
                                    severity="info"
                                    value="Stok Opname ( Penambahan Stok )"
                                />
                                <Badge
                                    v-if="data.type == 'purchase'"
                                    severity="info"
                                    value="Pembelian"
                                />
                                <Badge
                                    v-if="data.type == 'return'"
                                    value="Return Pembelian "
                                    severity="danger"
                                />
                                <Badge
                                    v-if="data.type == 'transfer_int'"
                                    value="Transfer Masuk "
                                    severity="primary"
                                />
                                <Badge
                                    v-if="data.type == 'transfer_out'"
                                    value="Transfer Keluar "
                                    severity="info"
                                />
                                <Badge
                                    v-if="data.type == 'sell'"
                                    value="Terjual "
                                    severity="success"
                                />
                                <Badge
                                    v-if="data.type == 'expire'"
                                    value="Expire "
                                    severity="danger"
                                />
                                <Badge
                                    v-if="data.type == 'void_sale'"
                                    value="Void Penjualan "
                                    severity="primary"
                                />
                                <Badge
                                    v-if="data.type == 'void_purchase'"
                                    value="Void Pembelian "
                                    severity="danger"
                                />
                                <Badge
                                    v-if="data.type == 'shipping_product'"
                                    value="Pengiriman Barang "
                                    severity="info"
                                />
                                <p
                                    class="ms-2"
                                    v-if="
                                        data.type == 'shipping_product' ||
                                        data.type == 'return_sale' ||
                                        data.type == 'sell'
                                    "
                                >
                                    {{ data.customer.name }}
                                </p>
                                <p
                                    class="ms-2"
                                    v-if="
                                        data.type == 'received_product' ||
                                        data.type == 'purchase' ||
                                        data.type == 'return'
                                    "
                                >
                                    {{ data.supplier.name }}
                                </p>
                            </div>
                        </template>
                    </Column>
                    <Column header="Qty">
                        <template #body="slotProps">
                            <Badge
                                :value="
                                    slotProps.data.qty +
                                    '/' +
                                    slotProps.data.unit
                                "
                                severity="primary"
                            />
                        </template>
                    </Column>

                    <Column header="Record">
                        <template #body="slotProps">
                            {{ slotProps.data.from }} ->
                            {{ slotProps.data.to }}
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script>
var _ = require("lodash");
import { ApiData } from "@/api/server";
export default {
    name: "histories",
    components: {},
    data() {
        return {
            histories: [],
            loader: {
                data: false,
            },
            limit: 20,
            totalRows: 0,
            page: 1,
            filter: {
                name: "",
                type: "",
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
                    `app/reports/stocks/histories?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&start_date=${this.filter.date.start}&end_date=${this.filter.date.end}&product=${this.$route.params.id}&type=${this.filter.type}`
                );
                var data = response.data;
                this.histories = data.histories;
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

<style>
.form-check-input {
    inset-block-start: 0.65rem !important;
}

.datepicker {
    right: 0px !important;
}
</style>
