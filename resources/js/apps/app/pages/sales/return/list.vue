<template>
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
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
                <button
                    type="button"
                    v-tooltip.top="'Refresh'"
                    @click="getData()"
                    class="btn btn-outline-info btn-wave waves-effect waves-light"
                >
                    <i class="fa fa-refresh"></i>
                </button>
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
                                    >Ditambahkan Oleh ?</label
                                >
                                <SelectOption
                                    v-model="filter.user"
                                    :options="users"
                                    filter
                                    :loading="loader.user"
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Pilih Pengguna"
                                    filterPlaceholder="Cari Pengguna"
                                    style="width: 100%; max-width: 100%"
                                    :maxSelectedLabels="2"
                                    @filter="onFilterUsers"
                                />
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
                        header="No. Faktur"
                        sortable
                        v-if="isColumnVisible('number')"
                    >
                        <template #body="{ data }">
                            {{ data.ref_no }}
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
                        v-if="isColumnVisible('final_total')"
                    >
                        <template #body="{ data }">
                            {{ formatNumber(data.final_total) }}
                        </template>
                    </Column>
                    <Column
                        field="created.name"
                        header="Ditambahkan"
                        sortable
                        v-if="isColumnVisible('created')"
                    ></Column>
                    <Column
                        field="action"
                        header="Aksi"
                        v-if="isColumnVisible('action')"
                    >
                        <template #body="{ data }">
                            <a
                                href="javascript:void(0)"
                                @click="
                                    $goTo({
                                        name: 'sales_return_detail',
                                        params: { id: data.id },
                                    })
                                "
                                v-tooltip.left="'Detail Return Penjualan'"
                                class="btn btn-icon btn-outline-info rounded-pill btn-wave waves-effect waves-light mr-2"
                                :to="{}"
                            >
                                <i class="fa fa-eye"></i>
                            </a>
                            <button
                                v-tooltip.left="'Hapus Return Penjualan'"
                                class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                                @click="deleteData(data.id)"
                            >
                                <i class="fa fa-trash"></i>
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
    name: "list_sale",
    components: {
        SelectOption,
        SelectButton,
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
                        name: "No.Ref",
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
                        name: "Di Tambahkan",
                        value: "created",
                    },
                    {
                        name: "Aksi",
                        value: "action",
                    },
                ],
                shows: [
                    "date",
                    "number",
                    "customer",
                    "price",
                    "created",
                    "action",
                ],
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
                date: {
                    start: "",
                    end: "",
                },
                user: [],
                status: "",
                customer: [],
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
                    `app/settings/table-view?table=sale_retur`
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
                table: "sale_retur",
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
                    `app/transactions/sales/returns?limit=${this.limit}&page=${this.page}&ref=${this.filter.name}&start_date=${startdate}&end_date=${enddate}&createdby=${this.filter.user}&customer=${this.filter.customer}&sort=${this.sort.field}&sortby=${this.sort.order}`
                );
                var data = response.data;
                this.transactions = data.transactions;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        deleteData(id) {
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
                    ApiData.delete(
                        "app/transactions/sales/returns/delete/" + id
                    )
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

        async getUsers(query) {
            this.loader.user = true;
            try {
                const response = await ApiData.get(
                    `app/master/components/users?name=${query}`
                );
                var data = response.data;
                this.users = data.users;
                this.loader.user = false;
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
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },
    },
    mounted: function () {
        this.getData();
        this.getUsers("");
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
