<template>
    <div class="col-lg-9 col-sm-12">
        <div class="card">
            <div class="card-header">
                <div>
                    <label class="form-label">Tanggal Transaksi</label>
                    <div class="input-group">
                        <VueCtkDateTimePicker
                            label="Filter Tanggal"
                            locale="Asia/Jakarta"
                            class="form-control"
                            v-model="filter.date"
                            :range="true"
                        />
                    </div>
                </div>
            </div>
            <div class="card-body p-0 table-responsive">
                <DataTable
                    :value="transactions"
                    :paginator="true"
                    :rows="limit"
                    :rowsPerPageOptions="[20, 50, 100]"
                    paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                    :lazy="true"
                    :totalRecords="totalRows"
                    @page="onPageChange($event)"
                    @sort="onSort($event)"
                    class="table"
                    :loading="loader.data"
                    responsiveLayout="scroll"
                    sortField="dynamicSortField"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                >
                    <template #header>
                        <div class="row">
                            <div class="col">
                                <label class="form-label">Cari Transaksi</label>
                                <span class="p-fluid">
                                    <div class="p-inputgroup">
                                        <span class="input-group-text"
                                            ><i class="fa fa-search"></i>
                                        </span>
                                        <InputText
                                            v-model="filter.name"
                                            class="form-control"
                                            placeholder="Cari Transaksi...."
                                        />
                                    </div>
                                </span>
                            </div>
                            <div class="col">
                                <label class="form-label"
                                    >Filter Pelanggan</label
                                >
                                <SelectOption
                                    v-model="filter.customer"
                                    :options="customers"
                                    filter
                                    :loading="loader.customer"
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Pilih Pelanggan"
                                    filterPlaceholder="Cari Pelanggan"
                                    style="width: 100%; max-width: 100%"
                                    :maxSelectedLabels="2"
                                    @filter="onFilterCustomers"
                                />
                            </div>
                            <div class="col">
                                <label class="form-label">Tampilkan Data</label>
                                <SelectOption
                                    v-model="column_show.shows"
                                    :options="column_show.data"
                                    optionLabel="name"
                                    optionValue="value"
                                    placeholder="Pilih Tampilan Data"
                                    style="width: 100%; max-width: 100%"
                                    :maxSelectedLabels="2"
                                    @hide="viewChangeData"
                                />
                            </div>
                        </div>
                    </template>
                    <Column
                        field="date"
                        header="Tanggal"
                        sortable
                        v-if="isColumnVisible('date')"
                    ></Column>
                    <Column
                        field="ref_no"
                        header="Nomor Faktur"
                        sortable
                        v-if="isColumnVisible('number')"
                    >
                        <template #body="{ data }">
                            <a
                                class="text-info"
                                href="javascript:void(0)"
                                @click="
                                    $goTo({
                                        name: 'e_commerce_order_detail',
                                        params: { id: data.id },
                                    })
                                " 
                            >
                                {{ data.ref_no }}
                            </a>
                        </template>
                    </Column>
                    <Column
                        field="customer.name"
                        header="Pelanggan"
                        sortable
                        v-if="isColumnVisible('customer')"
                    ></Column>
                    <Column
                        header="Total"
                        field="final_total"
                        sortable
                        v-if="isColumnVisible('price')"
                    >
                        <template #body="{ data }">
                            {{ formatNumber(data.final_total) }}
                        </template>
                    </Column>

                    <Column header="Status" v-if="isColumnVisible('status')">
                        <template #body="{ data }">
                            <button
                                v-if="data.status.payment"
                                type="button"
                                v-tooltip="'Ada Pembayaran Perlu di Check'"
                                class="btn btn-icon btn-outline-info rounded-pill btn-wave waves-effect waves-light"
                            >
                                <i class="fe fe-check-circle"></i>
                            </button>
                            <button
                                v-else
                                type="button"
                                v-tooltip="'Menunggu Pembayaran'"
                                class="btn btn-icon btn-outline-warning rounded-pill btn-wave waves-effect waves-light"
                            >
                                <i class="fe fe-x-circle"></i>
                            </button>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script>
import NProgress from "nprogress";
import Swal from "sweetalert2";
import SelectOption from "primevue/multiselect";
import SelectButton from "primevue/selectbutton";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "list_purchase",
    components: {
        SelectButton,
        SelectOption,
    },
    data() {
        return {
            sort: {
                field: "date",
                order: "desc",
            },
            column_show: {
                data: [
                    {
                        name: "Tanggal",
                        value: "date",
                    },
                    {
                        name: "No.Faktur",
                        value: "number",
                    },
                    {
                        name: "Pelanggan",
                        value: "customer",
                    },
                    {
                        name: "Total Harga",
                        value: "price",
                    },
                    {
                        name: "Aksi",
                        value: "action",
                    },
                ],
                shows: ["date", "number", "customer", "price", "action"],
            },
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
                status: ["due", "paid"],
                date: {
                    start: "",
                    end: "",
                },
                user: [],
                customer: [],
                name: "",
            },
        };
    },
    computed: {},
    created() {
        this.getOptions();
    },
    methods: {
        async getOptions() {
            try {
                const response = await ApiData.get(
                    `app/settings/table-view?table=ecommerce_pending`
                );
                var data = response.data;
                this.column_show.shows =
                    data.options == null
                        ? this.column_show.shows
                        : data.options;
            } catch (error) {
                console.log(error);
            }
        },

        viewChangeData() {
            ApiData.post("app/settings/table-view/store", {
                table: "ecommerce_pending",
                options: this.column_show.shows,
            }).then((response) => {});
        },

        onSort(event) {
            this.sort = {
                field: event.sortField,
                order: event.sortOrder > 0 ? "asc" : "desc",
            };
            this.getData(this.page);
        },

        isColumnVisible(columnName) {
            return this.column_show.shows.includes(columnName);
        },

        onFilterCustomers(event) {
            const query = event.value;
            this.getCustomers(query);
        },

        onFilterUsers(event) {
            const query = event.value;
            this.getUsers(query);
        },

        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;
            var startdate = "";
            var enddate = "";

            if (this.filter.date != null) {
                var date = this.filter.date;
                startdate = date.start.substring(0, 10);
                enddate = date.end.substring(0, 10);
            }

            try {
                const response = await ApiData.get(
                    `app/ecommerce/orders?limit=${this.limit}&page=${this.page}&ref=${this.filter.name}&start_date=${startdate}&end_date=${enddate}&createdby=${this.filter.user}&customer=${this.filter.customer}&status=final&sort=${this.sort.field}&sortby=${this.sort.order}`
                );
                var data = response.data;
                this.transactions = data.transactions;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getCustomers(query) {
            this.loader.customer = true;
            try {
                const response = await ApiData.get(
                    `app/crm/components/customers?name=${query}`
                );
                var data = response.data;
                this.customers = data.customers;
                this.loader.customer = false;
            } catch (error) {
                console.log(error);
            }
        },

        searchData() {
            if (this.filter.date == null) {
                this.filter.date = {
                    start: "",
                    end: "",
                };
            }

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
    mounted: function () {
        this.getData("");
        this.getCustomers("");
    },
    watch: {
        filter: {
            handler: function () {
                this.searchData();
            },
            deep: true,
            immediate: true,
        },
        // $route(to, from) {
        //     this.type = this.$route.query.type == "draft" ? "true" : "false";
        //     this.getData();
        // },
    },
};
</script>
