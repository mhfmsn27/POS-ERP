<template>
    <!-- List Data -->
    <div class="col-lg-12">
        <div class="row">
            <div class="col-12" v-if="account.name">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="text-left ml-3">
                                <h6>{{ account.name }} ({{ account.code }})</h6>
                                <p class="mb-0">
                                    Saldo : Rp
                                    {{ account.saldo }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body d-flex justify-content-start p-2">
                        <div class="me-2">
                            <label class="form-label"
                                >Tanggal Priode Mutasi</label
                            >
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
                        <div>
                            <label class="form-label">Bank</label>
                            <div class="input-group">
                                <Multiselect
                                    v-model="filter.account"
                                    :options="accounts"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :preserve-search="true"
                                    :searchable="true"
                                    :internal-search="false"
                                    :options-limit="50"
                                    :loading="loader.account"
                                    placeholder="Pilih Akun"
                                    open-direction="bottom"
                                    style="width: 100%"
                                    label="name"
                                    id="id"
                                    track-by="name"
                                    @search-change="getAccount"
                                ></Multiselect>
                            </div>
                        </div>
                        <button
                        type="button"
                        v-tooltip.top="'Refresh'"
                        @click="getData()"
                        class="btn btn-outline-info btn-wave waves-effect mt-6 waves-light ms-2"
                    >
                        <i class="fa fa-refresh"></i>
                    </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-sm-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body relative-background">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle card-icon iq-bg-primary mr-3"
                            >
                                <i class="ri-exchange-dollar-line"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="">
                                    Rp {{ formatNumber(account.debit) }}
                                </h4>
                                <h5 class="">Total Debit</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-sm-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body relative-background">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle card-icon iq-bg-primary mr-3"
                            >
                                <i class="ri-exchange-dollar-line"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="">
                                    Rp {{ formatNumber(account.credit) }}
                                </h4>
                                <h5 class="">Total Credit</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card card-block card-stretch card-height">
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
                                <Column field="ref_no" header="Sumber"></Column>
                                <Column header="Debit">
                                    <template #body="{ data }">
                                        {{ formatNumber(data.debit) }}
                                    </template>
                                </Column>
                                <Column header="Credit">
                                    <template #body="{ data }">
                                        {{ formatNumber(data.credit) }}
                                    </template>
                                </Column>
                                <Column header="Saldo Akhir">
                                    <template #body="{ data }">
                                        {{ formatNumber(data.logs) }}
                                    </template>
                                </Column>
                                <Column header="Rekonsiliasi">
                                    <template #body="{ data }">
                                        <button
                                            type="button"
                                            v-if="data.rekon"
                                            @click="checkList(data)"
                                            v-tooltip.top="'Batalkan'"
                                            class="btn btn-icon btn-outline-success rounded-pill btn-wave waves-effect waves-light mr-2"
                                        >
                                            <i class="fa fa-check-circle"></i>
                                        </button>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End List Data -->

    <Dialog
        v-model:visible="filter.modal"
        header="Filter Data"
        class="filter-data"
        position="top"
        :style="{ width: '40rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row p-2"></div>
        <template #footer>
            <button
                type="button"
                @click="resetFilter()"
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
</template>

<script>
import Swal from "sweetalert2";
import NProgress from "nprogress";
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
            limit: 20,
            accounts: [],
            account: {
                name: "",
                code: "",
                saldo: 0,
                first_saldo: 0,
                last_saldo: 0,
                credit: 0,
                debit: 0,
            },
            loader: {
                data: false,
                account: false,
            },
            filter: {
                modal: false,
                name: "",
                account: {
                    id: "",
                    name: "",
                },
                date: {
                    start: "",
                    end: "",
                },
            },
        };
    },
    computed: {},
    created() {
        this.getAccount("");
    },
    methods: {
        async getData(page = 1) {
            if (
                this.filter.account.id != "" &&
                this.filter.account.id != null
            ) {
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
                        `app/reports/commission/bank-mutation?limit=${this.limit}&page=${this.page}&account=${this.filter.account.id}&start=${startdate}&end=${enddate}&after_rekonsiliasi=`
                    );
                    var data = response.data;
                    this.transactions = data.transactions;
                    this.totalRows = data.totalRows;
                    this.account = data.account;
                    this.loader.data = false;
                } catch (error) {
                    console.log(error);
                }
            }
        },

        checkList(data) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Anda perlu melakukan rekonsiliasi ulang setelah membatalkan aksi ini",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.post("app/account/rekonsiliasi/rejected/" + data.id)
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
                    Swal.fire("Membatalkan Proses Rekonsiliasi");
                }
            });
        },

        searchData() {
            this.doSearch(this);
            this.filter.modal = false;
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

        resetFilter() {
            this.filter = {
                modal: false,
                name: "",
                account: {
                    id: "",
                    name: "",
                },
                date: {
                    start: "",
                    end: "",
                },
            };

            this.transactions = [];
            this.totalRows = 0;
            this.page = 1;
        },

        async getAccount(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&price=bank_cash&only_parent=yes`
                );
                var data = response.data;
                this.accounts = data.accounts;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },
    },
    mounted: function () {},
    watch: {
        filter: {
            handler: function (newVal, oldVal) {
                this.getData();
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>
