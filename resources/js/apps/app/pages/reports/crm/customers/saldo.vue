<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-4">
                <div>
                    <button
                        class="btn btn-sm btn-info"
                        type="button"
                        @click="filter.modal = true"
                    >
                        <i class="fa fa-filter"></i> Filter Data
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <TreeTable
                        :value="customers"
                        :paginator="true"
                        :loading="loader.data"
                        :lazy="true"
                        :rows="limit"
                        :rowsPerPageOptions="[25, 50]"
                        paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                        currentPageReportTemplate="{first} to {last} of {totalRecords}"
                        :totalRecords="totalRows"
                        responsiveLayout="scroll"
                        @page="onPageChange($event)"
                    >
                        <Column header="Customer" expander>
                            <template #body="{ node }">
                                {{ node.name }}
                            </template>
                        </Column>

                        <Column header="Tanggal">
                            <template #body="{ node }">
                                {{ node.date }}
                            </template>
                        </Column>
                       
                        <Column header="Nominal">
                            <template #body="{ node }">
                                {{ formatNumber(node.amount) }}
                            </template>
                        </Column>
                        <Column header="Saldo">
                            <template #body="{ node }">
                                {{ formatNumber(node.total_due) }}
                            </template>
                        </Column>
                        <Column header="Umur">
                            <template #body="{ node }">
                                {{ node.umur }}
                            </template>
                        </Column>
                    </TreeTable>
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
        <div class="row p-2">
            <div class="col-lg-6 mt-2">
                <label class="form-label">Nama Pelanggan</label>
                <div class="input-group">
                    <InputText
                        v-model="filter.name"
                        style="width: 100%"
                        type="text"
                        class="form-control"
                    />
                </div>
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label">Umur Piutang</label>
                <div class="input-group">
                    <InputText
                        v-model="filter.umur"
                        style="width: 100%"
                        type="number"
                        class="form-control"
                    />
                </div>
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
</template>

<script>
import TreeTable from "primevue/treetable";
import ScrollPanel from "primevue/scrollpanel";
import ColumnGroup from "primevue/columngroup";
import Row from "primevue/row";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "type_list",
    components: {
        TreeTable,
        ScrollPanel,
        ColumnGroup,
        Row,
    },
    data() {
        return {
            customers: [],
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
            },
            filter: {
                name: "",
                umur: 0,
                modal: false,
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
                    `app/reports/crm/customers/saldo?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&umur_saldo=${this.filter.umur}`
                );
                var data = response.data;
                this.customers = data.customers;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        resetFilter() {
            this.filter = {
                name: "",
                umur: 0,
                modal: false,
            };
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
    },
    mounted: function () {},
    watch: {},
};
</script>
