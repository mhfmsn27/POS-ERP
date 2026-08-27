<template>
    <!-- Create Data -->
    <div class="col-lg-4">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">
                    {{ editmode ? "Edit Data" : "Tambah Data" }}
                </h4>

                <div>
                    <button
                        v-if="editmode"
                        type="button"
                        @click="formatType()"
                        v-tooltip="'Klik, Untuk Membatalkan Edit Data'"
                        class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <Form @submit="validationUnit()" ref="unitValidation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="Unit-name-add" class="form-label"
                                >Jenis Internet Banking</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                v-model="smartlink.type"
                                name="Jenis Internet Banking"
                                ref="type"
                            >
                                <Dropdown
                                    v-model="smartlink.type"
                                    :options="[
                                        {
                                            label: 'KlikBCA Personal',
                                        },
                                        {
                                            label: 'KlikBCA Bisnis',
                                        },
                                        {
                                            label: 'Mandiri Internet Bisnis',
                                        },
                                        {
                                            label: 'BRI IB (Bri Internet Banking)',
                                        },
                                    ]"
                                    optionLabel="label"
                                    optionValue="label"
                                    placeholder="Pilih Opsi"
                                    style="width: 100%"
                                    class="w-full md:w-14rem"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12">
                            <label for="Unit-name-add" class="form-label"
                                >No.Rekening</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                v-model="smartlink.rekening"
                                placeholder="Masukkan No.Rekening "
                            />
                        </div>

                        <div class="col-12 mt-3">
                            <label for="Unit-name-add" class="form-label"
                                >Relasi Akun bank</label
                            >
                            <Multiselect
                                v-model="smartlink.account"
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
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getAccount"
                            ></Multiselect>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button
                        type="submit"
                        :disabled="loader.submit"
                        class="btn label-btn label-end"
                        :class="editmode ? 'btn-warning' : 'btn-primary'"
                    >
                        {{
                            loader.submit
                                ? "Mohon Tunggu...."
                                : editmode
                                ? "Simpan Perubahan"
                                : "Tambahkan Data"
                        }}
                        <i class="ti ti-plus label-btn-icon ms-2"></i>
                    </button>
                </div>
            </Form>
        </div>
    </div>
    <!-- End Create Data -->

    <!-- List Data -->
    <div class="col-lg-8">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-2">
                <div>
                    <label class="control-label">No.Rekening</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="filter.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Data...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <DataTable
                        :value="smartlinks"
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
                        <Column field="rekening" header="Rekening"></Column>
                        <Column header="Jenis" field="type"></Column>
                        <Column header="Akun" field="account.name"> </Column>

                        <Column header="Aksi">
                            <template #body="{ data }">
                                <button
                                    type="button"
                                    @click="editData(data)"
                                    v-tooltip="'Edit Data'"
                                    class="btn btn-icon btn-outline-warning rounded-pill btn-wave waves-effect waves-light mr-2"
                                >
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button
                                    type="button"
                                    @click="removeData(data.id)"
                                    v-tooltip="'Hapus Data'"
                                    class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
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
import Swal from "sweetalert2";
import NProgress from "nprogress";
import TreeTable from "primevue/treetable"; 
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "type_list",
    components: {
        TreeTable,
    },
    data() {
        return {
            editmode: false,
            smartlinks: [],
            accounts: [],
            smartlink: {
                type: "",
                rekening: "",
                account: {
                    id: "",
                    name: "",
                },
            },
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
                account: false,
                submit: false,
            },
            filter: {
                name: "",
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
                    `app/master/smartlinks?limit=${this.limit}&page=${this.page}&name=${this.filter.name}`
                );
                var data = response.data;
                this.smartlinks = data.smartlinks;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
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
                    ApiData.delete("app/master/smartlinks/delete/" + id)
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();

                            if (id == this.smartlink.id) {
                                this.formatType();
                            }
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

        editData(data) {
            this.smartlink = data;
            this.editmode = true;
        },

        validationUnit() {
            this.$refs.unitValidation.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Terjadi kesalahan",
                        detail: "Silahkan Check kembali form inputan anda",
                        life: 3000,
                    });
                } else {
                    this.loader.submit = true;
                    NProgress.start();
                    NProgress.set(0.1);
                    if (!this.editmode) {
                        this.createData();
                    } else {
                        this.updateData();
                    }
                }
            });
        },

        createData() {
            ApiData.post("app/master/smartlinks/create", this.smartlink)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    this.formatType();
                    this.getData();
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        updateData() {
            ApiData.post(
                "app/master/smartlinks/update/" + this.smartlink.id,
                this.smartlink
            )
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    this.formatType();
                    this.getData();
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        formatType() {
            this.smartlink = {
                type: "",
                rekening: "",
                account: {
                    id: "",
                    name: "",
                },
            };
            this.$refs.type.reset();
            this.editmode = false;
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
