<template>
    <!-- List Data -->
    <div class="col-lg-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-3">
                <div>
                    <label class="control-label">Nama Module</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="filter.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Module Fitur...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <DataTable
                        :value="modules"
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
                        <Column field="name" header="Nama"></Column>
                        <Column header="Total Permission">
                            <template #body="{ data }">
                                {{ formatNumber(data.permissions) }}
                            </template>
                        </Column>

                        <Column header="Aksi">
                            <template #body="{ data }">
                                <div class="btn-list">
                                    <button
                                        class="btn btn-info"
                                        type="button"
                                        @click="showAccess(data.id)"
                                    >
                                        <i class="fe fe-eye me-2"></i>Akses
                                    </button>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </div>
    <!-- End List Data -->

    <!-- Modal For Permission -->
    <Dialog
        v-model:visible="modal"
        class="filter-data"
        modal
        maximizable
        header="Permission Data"
        :style="{ width: '50vw' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row">
            <div
                class="col-lg-4"
                v-for="(permission, index) in permissions"
                :key="index"
            >
                <label for="brand-product">{{ permission.name }}</label>
                <br />
                <InputSwitch
                    @click="updateData(permission.permission_id)"
                    v-model="permission.status"
                />
            </div>
        </div>
    </Dialog>
    <!-- End modal -->
</template>

<script>
import Swal from "sweetalert2";
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "type_list",
    components: {},
    data() {
        return {
            modal: false,
            modules: [],
            permissions: [],
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
                submit: false,
            },
            filter: {
                name: "",
            },
        };
    },
    methods: {
        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;

            try {
                const response = await ApiData.get(
                    `app/settings/roles/modules?limit=${this.limit}&page=${this.page}&name=${this.filter.name}`
                );
                var data = response.data;
                this.modules = data.modules;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
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

        async showAccess(id) {
            try {
                const response = await ApiData.get(
                    `app/settings/roles/permissions/${this.$route.params.id}?module=${id}`
                );
                var data = response.data;
                this.permissions = data.permissions;
                this.modal = true;
            } catch (error) {
                console.log(error);
            }
        },

        updateData(id) {
            ApiData.post(
                `app/settings/roles/change-permission/${this.$route.params.id}/${id}`
            )
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                })
                .catch((err) => {
                    this.$handleErrorResponse(err);
                });
        },
    },
    mounted: function () {
        this.getData();
    },
};
</script>
