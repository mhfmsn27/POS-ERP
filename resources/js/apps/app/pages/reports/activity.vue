<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header p-4 d-flex justify-content-between">
                <div>
                    <label class="form-label">Cari Log Aktivitas</label>
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
                            placeholder="Cari Log...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
                <div>
                    <label class="form-label">Tipe Log</label>
                    <Dropdown
                        v-model="filter.event"
                        @change="searchData"
                        :options="[
                            {
                                name: 'Semua',
                                value: '',
                            },
                            {
                                name: 'Edit data',
                                value: 'updated',
                            },
                            {
                                name: 'Tambah Data',
                                value: 'created',
                            },
                            {
                                name: 'Hapus data',
                                value: 'deleted',
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
            <div class="card-body p-0">
                <div class="table-responsive">
                    <DataTable
                        :value="activities"
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
                        <Column header="Aktivitas" field="description"></Column>
                        <Column   header="Tipe Aktivitas">
                            <template #body="{data}">
                                {{ data.event == 'created' ? 'Tambah data' : (data.event == 'updated' ? 'Edit Data' : 'Hapus Data') }}
                            </template>
                        </Column>
                        <Column field="user.name" header="Pengguna"> </Column>
                        <Column header="Tanggal" field="date"> </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </div>
    <!-- End List Data -->
</template>

<script>
var _ = require("lodash");
import { ApiData } from "@/api/server";
export default {
    name: "list_purchase",
    components: {},
    data() {
        return {
            activities: [],
            totalRows: 0,
            page: 1,
            limit: 20,
            loader: {
                data: false,
            },
            filter: {
                name: "",
                event: "",
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
                    `app/reports/activities?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&event=${this.filter.event}`
                );
                var data = response.data;
                this.activities = data.activities;
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

        resetFilter() {
            this.filter = {
                name: "",
                event: "",
            };
            this.searchData();
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
