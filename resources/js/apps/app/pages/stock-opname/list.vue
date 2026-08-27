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
                <div class="d-flex justify-content-center">
                    <a
                        class="btn btn-blue me-2"
                        href="javascript:void(0);"
                        @click="modal.filter = true"
                        ><i class="fa fa-filter mr-2"></i> Filter Data</a
                    >
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'stock_opname_create',
                            })
                        " 
                        class="btn btn-blue"
                        ><i class="fa fa-plus-circle mr-2"></i>
                        Buat Transaksi
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
                        <Column field="date" header="Tanggal"></Column>
                        <Column field="ref_no" header="Nomor Ref">
                            <template #body="{ data }">
                                {{ data.ref_no }}
                            </template>
                        </Column>
                        <Column field="store.name" header="Toko"></Column>
                        <Column
                            field="created.name"
                            header="Ditambahkan"
                        ></Column>
                        <Column field="note" header="Catatan"></Column>
                        <Column field="action" header="Aksi">
                            <template #body="{ data }">
                                <a
                                    href="javascript:void(0)"
                                    @click="
                                        $goTo({
                                            name: 'stock_opname_detail',
                                            params: { id: data.id },
                                        })
                                    "
                                    v-tooltip.left="'Detail Stok Opname'"
                                    class="btn btn-icon btn-outline-info rounded-pill btn-wave waves-effect waves-light"
                                >
                                    <i class="fa fa-eye"></i>
                                </a>
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
        :style="{ width: '70vh' }"
        :position="'top'"
        :modal="true"
        :draggable="false"
    >
        <div class="row p-2">
            <div class="col-xl-6">
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
            <div class="col-xl-6">
                <label class="form-label">Di Tambahkan Oleh ?</label>
                <Multiselect
                    v-model="filter.user"
                    :options="users"
                    :multiple="false"
                    :close-on-select="true"
                    :clear-on-select="true"
                    :preserve-search="true"
                    :searchable="true"
                    :loading="loader.user"
                    :internal-search="true"
                    :options-limit="50"
                    placeholder="Pilih Pengguna"
                    open-direction="bottom"
                    label="name"
                    id="id"
                    track-by="name"
                    tagPlaceholder=""
                    selectLabel=""
                    @search-change="getUsers"
                ></Multiselect>
            </div>
            <div class="col-12 d-flex justify-content-end mt-4">
                <button
                    type="button"
                    class="btn btn-md btn-danger mr-3 me-3"
                    @click="resetFilter()"
                >
                    Reset Filter
                </button>
                <button
                    type="button"
                    class="btn btn-md btn-success"
                    @click="searchData()"
                >
                    Filter Data
                </button>
            </div>
        </div>
    </Dialog>
    <!-- End Filter Modal -->
    <!-- End Filter Modal -->
</template>

<script>
import { ApiData } from "@/api/server";
var _ = require("lodash");

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
                user: {
                    id: "",
                    name: "",
                },
                status: "",
                supplier: {
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
                    `app/transactions/stock-opname?limit=${this.limit}&page=${this.page}&ref=${this.filter.name}&start_date=${this.filter.date.start}&end_date=${this.filter.date.end}&createdby=${this.filter.user.id}&supplier=${this.filter.supplier.id}&status=${this.filter.status}`
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
                user: {
                    id: "",
                    name: "",
                },
                status: "",
                supplier: {
                    id: "",
                    name: "",
                },
            };
            this.searchData();
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
