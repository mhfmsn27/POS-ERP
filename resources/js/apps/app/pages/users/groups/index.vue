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
                        v-tooltip.top="'Klik, Untuk Membatalkan Edit Data'"
                        class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light"
                    >
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <Form @submit="ValidationGroupUsers()" ref="GroupValidation">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Nama Group Pengguna</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors, field }"
                                v-model="role.name"
                                name="Nama Group"
                                ref="rolename"
                            >
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="role.name"
                                    placeholder="Masukkan Nama Group Pengguna"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
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
            <div class="card-header d-flex justify-content-between p-3">
                <div>
                    <label class="control-label">Nama Group Pengguna</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="filter.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Group Pengguna...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <DataTable
                        :value="roles"
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
                        <Column header="Total Pengguna">
                            <template #body="{ data }">
                                {{ formatNumber(data.users) }}
                            </template>
                        </Column>

                        <Column header="Aksi">
                            <template #body="{ data }">
                                <div class="btn-list">
                                    <a
                                        href="javascript:void(0)"
                                        @click="
                                            $goTo({
                                                name: 'permissions',
                                                params: {
                                                    id: data.id,
                                                    name: data.name,
                                                },
                                            })
                                        " 
                                        class="btn btn-blue"
                                    >
                                        <i class="fe fe-lock me-2"></i
                                        >Permission
                                    </a>
                                    <button
                                        class="btn btn-orange"
                                        type="button"
                                        @click="editData(data)"
                                    >
                                        <i class="fe fe-edit me-2"></i>Edit
                                    </button>
                                    <button
                                        class="btn btn-red"
                                        type="button"
                                        @click="removeData(data.id)"
                                    >
                                        <i class="fe fe-trash me-2"></i>Delete
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
            editmode: false,
            roles: [],
            role: {
                name: "",
            },
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
                    `app/settings/roles?limit=${this.limit}&page=${this.page}&name=${this.filter.name}`
                );
                var data = response.data;
                this.roles = data.roles;
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
                    ApiData.delete("app/settings/roles/delete/" + id)
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();

                            if (id == this.role.id) {
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
            this.role = data;
            this.editmode = true;
        },

        ValidationGroupUsers() {
            this.$refs.GroupValidation.validate().then((success) => {
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
            ApiData.post("app/settings/roles/create", {
                name: this.role.name,
            })
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
            ApiData.post("app/settings/roles/update/" + this.role.id, {
                name: this.role.name,
            })
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
            this.role = {
                name: "",
            };

            this.editmode = false;
            this.$refs.rolename.reset();
        },
    },
    mounted: function () {
        this.getData();
    },
};
</script>
