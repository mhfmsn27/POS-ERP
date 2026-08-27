<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header p-4 d-flex justify-content-between">
                <div>
                    <button
                        class="btn btn-info"
                        type="button"
                        @click="modal.filter = true"
                    >
                        <i class="fa fa-filter mr-2"></i> Filter Data
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
                        <Column field="name" header="Pengguna"></Column>
                        <Column field="percentase" header="Persentase Komisi">
                            <template #body="{ data }">
                                {{ data.percentase }}
                            </template>
                        </Column>
                        <Column field="percentase" header="Total Penjualan">
                            <template #body="{ data }">
                                {{ formatNumber(data.transaction) }}
                            </template>
                        </Column>
                        <Column field="percentase" header="Total Komisi">
                            <template #body="{ data }">
                                {{ formatNumber(data.commission) }}
                            </template>
                        </Column>
                        <Column field="action" header="Aksi">
                            <template #body="{ data }">
                                <a
                                    href="javascript:void(0)"
                                    @click="
                                        $goTo({
                                            name: 'commission_detail',
                                            query: {
                                                tab: true,
                                                start_date:
                                                    filter.date.start ?? '',
                                                end_date: filter.date.end ?? '',
                                            },
                                            params: {
                                                id: data.id,
                                            },
                                        })
                                    "
                                    v-tooltip.left="'Detail Komisi'"
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
                <label class="form-label">Pilih Pengguna</label>
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
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "list_purchase",
    components: {},
    data() {
        return {
            transactions: [],
            users: [],
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
            },
        };
    },
    computed: {},
    created() {},
    methods: {
        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;
            try {
                const response = await ApiData.get(
                    `app/reports/commission/list?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&start_date=${this.filter.date.start}&end_date=${this.filter.date.end}&user=${this.filter.user.id}`
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
