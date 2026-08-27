<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-4">
                <div>
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control"
                            v-model="filter.name"
                            @input="searchData()"
                            placeholder="Cari Data...."
                            aria-describedby="search-team-member"
                        />
                        <button
                            class="btn btn-light btn-primary"
                            type="button"
                            id="search-team-member"
                        >
                            <i class="fe fe-search"></i>
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-start">
                    <button
                        @click="addData()"
                        v-tooltip.top="'Tambah Data'"
                        class="btn btn-outline-primary rounded-pill btn-wave waves-effect waves-light me-2"
                    >
                        <i class="fa fa-plus mr-2"></i> Tambah Data
                    </button>
                    <button
                        @click="modal.import = true"
                        class="btn btn-outline-primary rounded-pill btn-wave waves-effect waves-light"
                    >
                        <i class="fa fa-plus mr-2"></i> Import Data
                    </button>
                    <button
                        type="button"
                        v-tooltip.top="'Refresh'"
                        @click="getData()"
                        class="btn btn-outline-info btn-wave waves-effect waves-light ms-2"
                    >
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
            </div>
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
                    >
                        <Column header="Tanggal" field="date"> </Column>
                        <Column header="No Ref" field="ref_no"> </Column>
                        <Column header="Transaksi">
                            <template #body="{ data }">
                                <p>
                                    {{ data.transaction.ref_no }}
                                </p>
                            </template>
                        </Column>
                        <Column header="Total Saldo">
                            <template #body="{ data }">
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>
                        <Column header="Penggunaan">
                            <template #body="{ data }">
                                {{ formatNumber(data.total_pay) }}
                            </template>
                        </Column>
                        <Column header="Sisa Saldo">
                            <template #body="{ data }">
                                {{ formatNumber(data.total_due) }}
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
                                        <li v-if="data.total_due > 0">
                                            <a
                                                href="javascript:void(0);"
                                                @click="addPayment(data)"
                                                ><i
                                                    class="fa fa-money mr-2"
                                                ></i>
                                                Tambah Penggunaan</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="javascript:void(0);"
                                                @click="historyPayment(data)"
                                                ><i class="fa fa-list mr-2"></i>
                                                Histori Penggunaan</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="javascript:void(0);"
                                                @click="removeData(data)"
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

    <!-- Deposit Account -->
    <Dialog
        v-model:visible="modal.create"
        class="filter-data"
        :header="'Tambah Data'"
        :style="{ width: '35rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <Form @submit="createtransactions()" ref="createValidationTransaction">
            <div class="row p-3">
                <div class="col-lg-6 mb-2">
                    <label for="user-ref" class="form-label"
                        >Tanggal Transaksi</label
                    >
                    <Calendar v-model="due.date" style="width: 100%" />
                </div>

                <div class="col-lg-6 mb-2">
                    <label for="user-date" class="form-label"
                        >Bank / Kas
                    </label>
                    <Field
                        :rules="{
                            required: true,
                        }"
                        v-slot="{ errors }"
                        v-model="due.account"
                        :name="'Bank or Cash'"
                    >
                        <Multiselect
                            v-model="due.account"
                            :options="accounts"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            :searchable="true"
                            :internal-search="false"
                            :options-limit="50"
                            :loading="loader.account"
                            placeholder="Pilih Akun "
                            open-direction="bottom"
                            label="name"
                            id="id"
                            track-by="name"
                            @search-change="getAccount"
                        ></Multiselect>
                        <div class="fs-sm text-danger">
                            {{ errors[0] }}
                        </div>
                    </Field>
                </div>

                <div class="col-12 mb-2">
                    <label for="user-ref" class="form-label">Jumlah</label>
                    <InputNumber
                        style="width: 100%"
                        v-model="due.amount"
                        prefix="Rp "
                    />
                </div>
                <div class="col-12">
                    <label for="regular-form-1" class="form-label"
                        >Catatan
                    </label>
                    <textarea
                        v-model="due.note"
                        class="form-control"
                    ></textarea>
                </div>
            </div>
        </Form>
        <template #footer>
            <button
                type="button"
                @click="cancelDue()"
                :disabled="loader.submit"
                class="btn btn-outline-danger btn-wave waves-effect waves-light mr-2"
            >
                Batal
            </button>

            <button
                type="button"
                @click="createtransactions()"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                Tambah Saldo
            </button>
        </template>
    </Dialog>
    <!-- End Deposit -->

    <!-- payment -->
    <Dialog
        v-model:visible="modal.payment"
        class="filter-data"
        :header="editmode ? 'Edit Penggunaan' : 'Tambah Penggunaan Saldo'"
        :style="{ width: '35rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <Form @submit="createPayment()" ref="ValidationCreatePayment">
            <div class="row p-3">
                <div class="col-lg-6 mb-2">
                    <label for="user-ref" class="form-label">Tanggal </label>
                    <Calendar v-model="payment.date" style="width: 100%" />
                </div>

                <div class="col-lg-6 mb-2">
                    <label for="user-ref" class="form-label">Nominal </label>
                    <InputNumber
                        style="width: 100%"
                        :max="payment.max"
                        v-model="payment.amount"
                        prefix="Rp "
                    />
                </div>

                <div class="col-lg-6 mb-2">
                    <label for="user-date" class="form-label">Metode </label>
                    <Field
                        :rules="{
                            required: true,
                        }"
                        v-slot="{ errors }"
                        v-model="payment.method"
                        :name="'Akun Induk'"
                    >
                        <Multiselect
                            v-model="payment.method"
                            :options="methods"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            :searchable="true"
                            :internal-search="false"
                            :options-limit="50"
                            :loading="loader.method"
                            placeholder="Pilih Metode "
                            open-direction="bottom"
                            label="name"
                            id="id"
                            track-by="name"
                            @search-change="getMethods"
                        ></Multiselect>
                        <div class="fs-sm text-danger">
                            {{ errors[0] }}
                        </div>
                    </Field>
                </div>

                <div class="col-12">
                    <Divider />
                </div>
                <div class="col-12">
                    <label for="regular-form-1" class="form-label"
                        >Catatan
                    </label>
                    <textarea
                        v-model="payment.note"
                        class="form-control"
                    ></textarea>
                </div>
            </div>
        </Form>
        <template #footer>
            <button
                type="button"
                @click="cancelPayment()"
                :disabled="loader.submit"
                class="btn btn-outline-danger btn-wave waves-effect waves-light mr-2"
            >
                Batalkan
            </button>

            <button
                type="button"
                @click="createPayment()"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                {{ editmode ? "Simpan Perubahan" : "Tambahkan Penggunaan" }}
            </button>
        </template>
    </Dialog>
    <!-- End payment -->

    <!-- History Payment -->
    <Dialog
        v-model:visible="modal.history"
        class="filter-data"
        :header="'Riwayat Penggunaaan'"
        :style="{ width: '45rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row p-3">
            <div class="col-12 table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nominal</th>
                            <th>Metode Pembayaran</th>
                            <th>Dilakukan Oleh</th>
                            <th v-if="transactionId == null">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(detail, index) in payments" :key="index">
                            <td>
                                {{ detail.date }}
                            </td>
                            <td>
                                {{ formatNumber(detail.amount) }}
                            </td>
                            <td>
                                {{ detail.method_name }}
                            </td>
                            <td>
                                {{ detail.created }}
                            </td>
                            <td v-if="transactionId == ''">
                                <button
                                    class="btn btn-sm btn-warning mr-2"
                                    type="button"
                                    @click="editPayment(detail)"
                                >
                                    <i class="fa fa-pencil"></i>
                                </button>

                                <button
                                    class="btn btn-sm btn-danger"
                                    type="button"
                                    @click="deletePayment(detail, index)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </Dialog>
    <!-- End History Payment -->

    <!-- Import Data Modal -->
    <Dialog v-model:visible="modal.import" header="" :style="{ width: '60vh' }">
        <div class="card-body ps-5 pe-5 pt-2 pb-5 rectangle3">
            <p class="h4 fw-semibold mb-2 text-center">Import Data Saldo</p>
            <p class="mb-4 text-muted op-7 fw-normal text-center">
                Silahkan upload file xlsx di bawah ini untuk melakukan proses
                import data Saldo
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
            editmode: false,
            transactions: [],
            types: [],
            methods: [],
            payments: [],
            accounts: [],
            transactionId: null,
            due: {
                date: "",
                amount: 0,
                note: "",
                account: {
                    id: "",
                    name: "",
                },
                type: "saldo",
            },
            payment: {
                transaction_id: "",
                amount: 0,
                type: "supplier",
                method: {
                    id: "",
                    name: "",
                },
                date: "",
                note: "",
                max: 0,
            },
            modal: {
                create: false,
                deposit: false,
                payment: false,
                history: false,
            },
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
                method: false,
                submit: false,
                type: false,
                account: false,
            },
            filter: {
                name: "",
                type: {
                    id: "",
                    name: "",
                },
                store: {
                    id: "",
                    name: "",
                },
            },
        };
    },
    computed: {},
    created() {
        this.getData();
        const today = new Date().toISOString().substr(0, 10);
        this.due.date = today;
        this.payment.date = today;
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
            ApiData.post(
                "app/crm/suppliers/import/saldo/" + this.$route.params.id,
                this.data
            )
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
                    `app/crm/suppliers/import/download-saldo`,
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

                link.setAttribute(
                    "download",
                    "sample_export_saldo_supplier.xlsx"
                );
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
                    `app/transactions/transaction-due?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&supplier=${this.$route.params.id}&type=saldo`
                );
                var data = response.data;
                this.transactions = data.transactions;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async historyPayment(datapay) {
            try {
                const response = await ApiData.get(
                    `app/transactions/transaction-due/history?limit=100&page=1&due=${datapay.id}`
                );
                var data = response.data;
                this.payments = data.payments;
                this.modal.history = true;
                this.transactionId = datapay.transaction.id;
            } catch (error) {
                console.log(error);
            }
        },

        editPayment(data) {
            this.payment = {
                id: data.id,
                transaction_id: data.due_id,
                amount: data.amount,
                type: "supplier",
                method: data.method,
                date: data.date,
                note: data.note,
                max: 999999999999,
            };
            this.modal.history = false;
            this.editmode = true;
            this.modal.payment = true;
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

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
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

        async getMethods(query) {
            this.loader.method = true;
            try {
                const response = await ApiData.get(
                    `app/master/payment-method?name=${query}&`
                );
                var data = response.data;
                this.methods = data.methods;
                this.loader.method = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getpaymentAccount(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&price=bank_cash&only_parent=yes&without_data=${this.payment.id}`
                );
                var data = response.data;
                this.account_payment = data.transactions;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },

        removeData(data) {
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
                    ApiData.delete("app/crm/suppliers/due/delete/" + data.id)
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

        deletePayment(data, index) {
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
                    ApiData.delete(
                        "app/transactions/transaction-due/delete-payment/" +
                            data.id
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.payments.splice(index, 1);
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

        createtransactions() {
            this.$refs.createValidationTransaction
                .validate()
                .then((success) => {
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
                        ApiData.post(
                            "app/crm/suppliers/due/add/" +
                                this.$route.params.id,
                            this.due
                        )
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                NProgress.done();
                                this.resetDue();
                                this.modal.create = false;
                                this.getData();
                            })
                            .catch((err) => {
                                NProgress.done();
                                this.loader.submit = false;
                                this.$handleErrorResponse(err);
                            });
                    }
                });
        },

        addData() {
            this.resetDue();
            this.editmode = false;
            this.modal.create = true;
        },

        editData(data) {
            this.account = data;
            this.editmode = true;
            this.modal.create = true;
        },

        addPayment(data) {
            this.resetPayment();
            this.payment.transaction_id = data.id;
            this.payment.max = data.total_due;
            this.modal.payment = true;
        },

        cancelDue() {
            this.resetDue();
            this.modal.create = false;
        },

        cancelPayment() {
            this.resetPayment();
            this.modal.payment = false;
        },

        resetDue() {
            const today = new Date().toISOString().substr(0, 10);
            this.due = {
                date: today,
                amount: 0,
                note: "",
                type: "saldo",
                account: {
                    id: "",
                    name: "",
                },
            };
        },

        resetPayment() {
            const today = new Date().toISOString().substr(0, 10);
            this.payment = {
                transaction_id: "",
                amount: 0,
                type: "supplier",
                method: {
                    id: "",
                    name: "",
                },
                date: today,
                note: "",
            };
        },

        createPayment() {
            this.$refs.ValidationCreatePayment.validate().then((success) => {
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

                    if (this.editmode) {
                        ApiData.post(
                            "app/transactions/transaction-due/update-payment/" +
                                this.payment.id,
                            this.payment
                        )
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                NProgress.done();

                                this.cancelPayment();
                                this.getData();
                            })
                            .catch((err) => {
                                NProgress.done();
                                this.loader.submit = false;
                                this.$handleErrorResponse(err);
                            });
                    } else {
                        ApiData.post(
                            "app/transactions/transaction-due/payment/" +
                                this.payment.transaction_id,
                            this.payment
                        )
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                NProgress.done();
                                this.cancelPayment();
                                this.getData();
                            })
                            .catch((err) => {
                                NProgress.done();
                                this.loader.submit = false;
                                this.$handleErrorResponse(err);
                            });
                    }
                }
            });
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
