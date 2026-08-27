<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header p-4 d-flex justify-content-between">
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

                <div class="d-flex justify-content-start">
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'rma_create',
                            })
                        "
                        class="btn btn-primary ml-auto"
                    >
                        <i class="fa fa-plus-circle me-2"></i> Buat Transaksi
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
                                    <label class="form-label"
                                        >Cari Transaksi</label
                                    >
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
                                    <label class="form-label">Status</label>
                                    <SelectOption
                                        v-model="filter.status"
                                        :options="[
                                            {
                                                name: 'Pending',
                                                value: 'pending',
                                            },
                                            {
                                                name: 'Dalam Pengerjaan',
                                                value: 'process',
                                            },
                                            {
                                                name: 'Selesai',
                                                value: 'complete',
                                            },
                                            {
                                                name: 'Di Ambil',
                                                value: 'taken',
                                            },
                                        ]"
                                        optionLabel="name"
                                        optionValue="value"
                                        placeholder="Pilih Status"
                                        filterPlaceholder="Pilih Status"
                                        style="width: 100%; max-width: 100%"
                                        :maxSelectedLabels="2"
                                    />
                                </div>
                                <div class="col">
                                    <label class="form-label"
                                        >Tampilkan Data</label
                                    >
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
                            field="customer"
                            header="Pelanggan"
                            sortable
                            v-if="isColumnVisible('customer')"
                        ></Column>

                        <Column
                            field="note"
                            header="Catatan"
                            v-if="isColumnVisible('note')"
                        ></Column>
                        <Column
                            field="ref_no"
                            header="No.Ref"
                            sortable
                            v-if="isColumnVisible('number')"
                        ></Column>
                        <Column
                            field="date"
                            header="Tanggal"
                            sortable
                            v-if="isColumnVisible('date')"
                        ></Column>

                        <Column
                            header="Status"
                            v-if="isColumnVisible('status')"
                        >
                            <template #body="{ data }">
                                <span
                                    class="badge bg-warning text-black"
                                    v-if="data.status == 'pending'"
                                    >Pending</span
                                >
                                <span
                                    class="badge rounded-pill bg-info text-white"
                                    v-if="data.status == 'process'"
                                    >Dalam Pengerjaan</span
                                >

                                <span
                                    class="badge rounded-pill bg-primary text-white"
                                    v-if="data.status == 'complete'"
                                    >Selesai</span
                                >

                                <span
                                    class="badge bg-warning text-black"
                                    v-if="data.status == 'taken'"
                                    >Di Ambil</span
                                >
                            </template>
                        </Column>
                        <Column
                            field="action"
                            header="Aksi"
                            v-if="isColumnVisible('action')"
                        >
                            <template #body="{ data }">
                                <div class="btn-group mt-2 mb-2">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul
                                        class="dropdown-menu"
                                        role="menu"
                                        style=""
                                    >
                                        <li>
                                            <a
                                                href="javascript:void(0)"
                                                @click="
                                                    $goTo({
                                                        name: 'rma_detail',
                                                        params: { id: data.id },
                                                    })
                                                " 
                                                ><i class="fa fa-eye mr-2"></i>
                                                Detail Data</a
                                            >
                                        </li>

                                        <li class="divider"></li>
                                        <li>
                                            <a
                                                href="javascript:void(0)"
                                                @click="
                                                    $goTo({
                                                        name: 'rma_update',
                                                        params: { id: data.id },
                                                    })
                                                "
                                                ><i
                                                    class="fa fa-pencil mr-2"
                                                ></i>
                                                Edit Data</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="javascript:void(0);"
                                                @click="deleteData(data.id)"
                                                ><i
                                                    class="fa fa-trash mr-2"
                                                ></i>
                                                Hapus Data</a
                                            >
                                        </li>
                                    </ul>
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
import NProgress from "nprogress";
import Swal from "sweetalert2";
import SelectOption from "primevue/multiselect";
import SelectButton from "primevue/selectbutton";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "list_purchase",
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
                        name: "No.Faktur",
                        value: "number",
                    },
                    {
                        name: "Pelanggan",
                        value: "customer",
                    },
                    {
                        name: "Catatan",
                        value: "note",
                    },
                    {
                        name: "Status",
                        value: "status",
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
                    "note",
                    "status",
                    "action",
                ],
            },
            transactions: [],
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
                status: ["pending", "process", "complete"],
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
                    `app/settings/table-view?table=rma`
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
                table: "rma",
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
                    `app/transactions/rma?limit=${this.limit}&page=${this.page}&ref=${this.filter.name}&start_date=${startdate}&end_date=${enddate}&customer=${this.filter.customer}&sort=${this.sort.field}&sortby=${this.sort.order}&status=${this.filter.status}`
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
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
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
                    ApiData.delete("app/transactions/rma/delete/" + id)
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
    },
};
</script>
