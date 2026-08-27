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
                    class="btn btn-outline-info btn-wave waves-effect waves-light ms-2"
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
                                    >Filter Supplier</label
                                >
                                <SelectOption
                                    v-model="filter.supplier"
                                    :options="suppliers"
                                    filter
                                    :loading="loader.supplier"
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Pilih Supplier"
                                    filterPlaceholder="Cari Supplier"
                                    style="width: 100%; max-width: 100%"
                                    :maxSelectedLabels="2"
                                    @filter="onFilterSuppliers"
                                />
                            </div>
                            <div class="col">
                                <label class="form-label">Status</label>
                                <SelectOption
                                    v-model="filter.status"
                                    :options="[
                                        {
                                            name: 'Belum di Bayar',
                                            value: 'due',
                                        },
                                        {
                                            name: 'Lunas',
                                            value: 'paid',
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
                        header="Nomor Ref"
                        sortable
                        v-if="isColumnVisible('number')"
                    >
                        <template #body="{ data }">
                            {{ data.ref_no }}
                        </template>
                    </Column>
                    <Column
                        field="supplier.name"
                        header="Supplier"
                        sortable
                        v-if="isColumnVisible('supplier')"
                    ></Column>
                    <Column
                        header="Total"
                        v-if="isColumnVisible('price')"
                        field="final_total"
                        sortable
                    >
                        <template #body="{ data }">
                            {{ formatNumber(data.final_total) }}
                        </template>
                    </Column>
                    <Column
                        field="created.name"
                        header="Ditambahkan"
                        v-if="isColumnVisible('created')"
                    ></Column>
                    <Column header="Status" v-if="isColumnVisible('status')">
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
                                        name: 'purchase_return_detail',
                                        params: { id: data.id },
                                    })
                                "
                                v-tooltip.left="'Detail Return Pembelian'"
                                class="btn btn-icon btn-outline-info rounded-pill btn-wave waves-effect waves-light mr-2"
                               
                            >
                                <i class="fa fa-eye"></i>
                            </a>
                            <button
                                v-tooltip.left="'Hapus Return Pembelian'"
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
var _ = require("lodash");
import { ApiData } from "@/api/server";
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
                        name: "Supplier",
                        value: "supplier",
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
                    "supplier",
                    "price",
                    "created",
                    "status",
                    "action",
                ],
            },
            transactions: [],
            users: [],
            suppliers: [],
            totalRows: 0,
            page: 1,
            limit: 20,
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
                date: {
                    start: "",
                    end: "",
                },
                user: [],
                status: ["due", "paid"],
                supplier: [],
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
                    `app/settings/table-view?table=purchase_retur`
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
                table: "purchase_retur",
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

        onFilterSuppliers(event) {
            const query = event.value;
            this.getSuppliers(query);
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
                    `app/transactions/purchases/returns?limit=${this.limit}&page=${this.page}&ref=${this.filter.name}&start_date=${startdate}&end_date=${enddate}&createdby=${this.filter.user}&supplier=${this.filter.supplier}&payment=${this.filter.status}&sort=${this.sort.field}&sortby=${this.sort.order}`
                );
                var data = response.data;
                this.transactions = data.transactions;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
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

        async getSuppliers(query) {
            this.loader.supplier = true;
            try {
                const response = await ApiData.get(
                    `app/crm/components/suppliers?name=${query}`
                );
                var data = response.data;
                this.suppliers = data.suppliers;
                this.loader.supplier = false;
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
                        "app/transactions/purchases/returns/delete/" + id
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
        this.getSuppliers("");
    },
    watch: {
        filter: {
            handler: function () {
                this.searchData();
            },
            deep: true,
            immediate: true,
        },
        $route(to, from) {
            this.type = this.$route.query.type == "draft" ? "true" : "false";
            this.getData();
        },
    },
};
</script>
