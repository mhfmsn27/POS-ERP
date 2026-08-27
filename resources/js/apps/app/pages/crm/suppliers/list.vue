<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-2">
                <div>
                    <label class="control-label">Nama Supplier / Email</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="filter.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Supplier...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
                <div class="d-flex justify-content-start">
                    <button
                        class="btn btn-info me-2"
                        type="button"
                        @click="modal.import = true"
                    >
                        <i class="fe fe-upload me-2"></i> Import Data
                    </button>
                    <a
                        href="javascript:void(0)"
                        @click="
                            $goTo({
                                name: 'supplier_create',
                            })
                        "
                        class="btn btn-info"
                    >
                        <i class="fe fe-plus-circle me-2"></i> Tambah Data
                    </a>
                    <button
                        type="button"
                        v-tooltip.top="'Refresh'"
                        @click="getData()"
                        class="btn btn-outline-info btn-wave waves-effect waves-light"
                    >
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <DataTable
                        :value="suppliers"
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
                        <Column field="email" header="Email"></Column>
                        <Column header="Total Utang">
                            <template #body="{ data }">
                                {{ formatNumber(data.total_due) }}
                            </template>
                        </Column>
                        <Column header="Total Saldo">
                            <template #body="{ data }">
                                {{ formatNumber(data.total_saldo) }}
                            </template>
                        </Column>

                        <Column header="Aksi">
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
                                                        name: 'supplier_update',
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
                                                href="javascript:void(0)"
                                                @click="
                                                    $goTo({
                                                        name: 'supplier_due',
                                                        params: { id: data.id },
                                                    })
                                                "
                                                ><i
                                                    class="fa fa-money mr-2"
                                                ></i>
                                                Saldo Utang</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="javascript:void(0)"
                                                @click="
                                                    $goTo({
                                                        name: 'supplier_saldo',
                                                        params: { id: data.id },
                                                    })
                                                "
                                                ><i
                                                    class="fa fa-money mr-2"
                                                ></i>
                                                Saldo Simpanan</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="javascript:void(0)"
                                                @click="
                                                    $goTo({
                                                        name: 'supplier_transaction',
                                                        params: { id: data.id },
                                                    })
                                                " 
                                                ><i class="fa fa-list mr-2"></i>
                                                Histori Transaksi</a
                                            >
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a
                                                href="javascript:void(0);"
                                                @click="removeData(data.id)"
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

    <!-- Import Data Modal -->
    <Dialog v-model:visible="modal.import" header="" :style="{ width: '60vh' }">
        <div class="card-body ps-5 pe-5 pt-2 pb-5 rectangle3">
            <p class="h4 fw-semibold mb-2 text-center">Import Data Supplier</p>
            <p class="mb-4 text-muted op-7 fw-normal text-center">
                Silahkan upload file xlsx di bawah ini untuk melakukan proses
                import data Supplier
            </p>
            <div class="row gy-3">
                <div class="col-xl-12 d-flex justify-content-center mt-3 mb-3">
                    <FileUpload
                        mode="basic"
                        v-model="import_data.model"
                        @select="onFileSelected"
                        v-tooltip="'Upload File Disini'"
                        accept=".xlsx"
                        :maxFileSize="1000000"
                    />
                </div>
                <!-- End Code Form -->

                <div
                    class="col-xl-12 d-grid mt-4 d-flex justify-content-center"
                >
                    <button
                        type="button"
                        @click="downloadExample"
                        :disabled="loader.submit"
                        class="btn btn-info label-btn me-3"
                    >
                        <i class="ti ti-download label-btn-icon mr-2"></i>
                        Download Sample
                    </button>
                    <button
                        type="button"
                        @click="importData"
                        :disabled="loader.submit"
                        class="btn btn-primary label-btn label-end"
                    >
                        {{ loader.submit ? "Mohon Tunggu" : "Import Data" }}
                        <i class="ti ti-upload label-btn-icon ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </Dialog>
    <!-- End Import Data Modal -->
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
            data: new FormData(),
            modal: {
                import: false,
            },
            import_data: {
                file: null,
                model: null,
            },

            suppliers: [],
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
                submit: false,
                type: false,
                debt: false,
                term: false,
                deposit: false,
                debt_imprest: false,
            },
            filter: {
                name: "",
            },
        };
    },
    computed: {},
    mounted() {
        this.getData();
    },
    methods: {
        async onFileSelected(e) {
            if (e.files[0] != undefined) {
                this.import_data.file = e.files[0];
            } else {
                this.import_data.file = null;
            }
        },

        importData() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            this.data.append("file", this.import_data.file);
            ApiData.post("app/crm/suppliers/import", this.data)
                .then((response) => {
                    this.loader.submit = false;
                    this.$handleSuccessResponse(response.data.message);
                    this.modal.import = false;
                    NProgress.done();

                    this.getData();
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
        },

        async downloadExample() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);

            try {
                const response = await ApiData.get(
                    `app/crm/suppliers/import/download-sample`,
                    {
                        responseType: "blob",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                    }
                );

                const url = window.URL.createObjectURL(
                    new Blob([response.data])
                );
                const link = document.createElement("a");
                link.href = url;

                link.setAttribute("download", "sample_import_supplier.xlsx");
                document.body.appendChild(link);
                link.click();

                this.loader.submit = false;
                NProgress.done();
            } catch (error) {
                this.loader.submit = false;
                NProgress.done();
                console.log(error);
            }
        },

        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;

            try {
                const response = await ApiData.get(
                    `app/crm/suppliers?limit=${this.limit}&page=${this.page}&name=${this.filter.name}`
                );
                var data = response.data;
                this.suppliers = data.suppliers;
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

        removeData(id) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "data yang telah di hapus tidak dapat dikembalikan lagi",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.delete("app/crm/suppliers/delete/" + id)
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
};
</script>
