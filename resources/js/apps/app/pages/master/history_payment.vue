<template>
    <!-- List Data -->
    <div class="col-lg-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-2">
                <div>
                    <label class="form-label">Tanggal </label>
                    <div class="btn-group btn-group2 w-100" role="group">
                        <VueCtkDateTimePicker
                            label="Filter Tanggal"
                            locale="Asia/Jakarta"
                            class="form-control"
                            v-model="filter.date"
                            @validate="filterDate"
                            :range="true"
                        />
                        <button
                            type="button"
                            v-tooltip.top="'Hapus Filter'"
                            @click="removeFilter('date')"
                            class="btn btn-outline-danger btn-wave waves-effect waves-light"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-start">
                    <button
                        type="button"
                        class="btn btn-info"
                        @click="depositAccount"
                    >
                        <i class="fa fa-plus-circle"></i> Tambah Saldo
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
                        dataKey="id"
                    >
                        <Column field="date" header="Tanggal"></Column>
                        <Column field="name" header="Keterangan"></Column>
                        <Column header="Tipe Transaksi">
                            <template #body="{ data }">
                                <p v-if="data.sub_type == 'deposit'">
                                    Deposit Saldo
                                </p>
                                <p v-if="data.sub_type == 'deposit_equitas'">
                                    Deposit Saldo
                                </p>
                                <p v-if="data.sub_type == 'transfer_dana'">
                                    Transfer Saldo
                                </p>
                                <p v-if="data.sub_type == 'received_dana'">
                                    Terima Saldo
                                </p>
                                <p
                                    v-if="
                                        data.sub_type == 'first_stock' ||
                                        data.sub_type == 'deposit_stock_product'
                                    "
                                >
                                    Stok Awal Barang
                                </p>

                                <p v-if="data.sub_type == 'due_supplier'">
                                    Utang Awal Supplier
                                </p>

                                <p
                                    v-if="
                                        data.sub_type ==
                                        'received_product_from_supplier'
                                    "
                                >
                                    Penerimaan Barang
                                </p>

                                <p
                                    v-if="
                                        data.sub_type == 'pay_supplier_faktur'
                                    "
                                >
                                    Pembayaran Faktur
                                </p>

                                <p v-if="data.sub_type == 'saldo_supplier'">
                                    Penyimpanan Saldo Supplier
                                </p>

                                <p v-if="data.sub_type == 'wd_supplier'">
                                    Penggunaan Saldo Supplier
                                </p>

                                <p v-if="data.sub_type == 'return_purchase'">
                                    Retur Pembelian
                                </p>

                                <p
                                    v-if="
                                        data.sub_type ==
                                        'sent_product_to_customer'
                                    "
                                >
                                    Pengiriman Barang
                                </p>

                                <p v-if="data.sub_type == 'due_customer'">
                                    Utang Pelanggan
                                </p>

                                <p v-if="data.sub_type == 'sale_faktur'">
                                    Faktur Penjualan
                                </p>

                                <p v-if="data.sub_type == 'saldo_customer'">
                                    Deposit Saldo Customer
                                </p>

                                <p
                                    v-if="
                                        data.sub_type == 'pay_customer_faktur'
                                    "
                                >
                                    Pembayaran Faktur Penjualan
                                </p>

                                <p v-if="data.sub_type == 'return_sell'">
                                    Retur Penjualan
                                </p>
                            </template>
                        </Column>
                        <Column header="Mutasi">
                            <template #body="{ data }">
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>
                        <Column header="Tipe">
                            <template #body="{ data }">
                                {{ data.type == "debit" ? "Debit" : "Kredit" }}
                            </template>
                        </Column>
                        <Column header="Saldo">
                            <template #body="{ data }">
                                {{ formatNumber(data.saldo) }}
                            </template>
                        </Column>
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
                                    @click="removeData(data)"
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

    <!-- Modal Deposit -->
    <Dialog
        v-model:visible="modal.deposit"
        class="filter-data"
        :header="editmode ? 'Ubah Deposit' : 'Deposit Akun'"
        :style="{ width: '55rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <Form @submit="submitDeposit()" ref="ValidationDepositAccount">
            <div class="row p-3">
                <div class="col-lg-6 mb-3">
                    <label for="user-ref" class="form-label"
                        >Nama Deposit</label
                    >
                    <input
                        type="text"
                        style="width: 100%"
                        v-model="deposit.name"
                        class="form-control"
                    />
                    <div class="fs-sm text-secondary">
                        Opsional, ( Di isi otomatis apabila di kosongkan)
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="user-ref" class="form-label"
                        >Tanggal Deposit</label
                    >
                    <input
                        type="date"
                        style="width: 100%"
                        v-model="deposit.date"
                        class="form-control"
                    />
                    <div class="fs-sm text-secondary">
                        Opsional, ( Di isi otomatis apabila di kosongkan)
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="user-ref" class="form-label"
                        >Nominal Deposit</label
                    >
                    <InputNumber
                        style="width: 100%"
                        v-model="deposit.amount"
                        prefix="Rp "
                    />
                    <div class="fs-sm text-gray mt-2">
                        Masukkan jumlah deposit Akun
                    </div>
                </div>

                <div class="col-12">
                    <Divider />
                </div>
                <div class="col-12">
                    <label for="regular-form-1" class="form-label"
                        >Catatan
                    </label>
                    <textarea
                        v-model="deposit.note"
                        class="form-control"
                    ></textarea>
                </div>
            </div>
        </Form>
        <template #footer>
            <button
                type="button"
                @click="cancelDeposit()"
                :disabled="loader.submit"
                class="btn btn-outline-danger btn-wave waves-effect waves-light mr-2"
            >
                Batalkan Deposit
            </button>

            <button
                type="button"
                @click="submitDeposit()"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                {{ editmode ? "Simpan Perubahan" : "Deposit Akun" }}
            </button>
        </template>
    </Dialog>
    <!-- End Modal Deposit -->
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
            modal: {
                deposit: false,
            },
            transactions: [],
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                data: false,
            },
            deposit: {
                amount: 0,
                note: "",
                date: "",
                name: "",
            },
            filter: {
                name: "",
                date: {
                    start: "",
                    end: "",
                },
            },
        };
    },
    computed: {},
    created() {
        this.getData();
    },
    methods: {
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
                    ApiData.delete(
                        "app/master/payment-method/delete-saldo/" + data.id
                    )
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

        editData(data) {
            this.deposit = {
                id: data.id,
                amount: data.amount,
                note: data.note,
                date: data.tanggal,
                name: data.name,
            };
            this.editmode = true;
            this.modal.deposit = true;
        },

        depositAccount() {
            this.resetDeposit();
            this.modal.deposit = true;
        },

        submitDeposit() {
            this.$refs.ValidationDepositAccount.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Terjadi kesalahan",
                        detail: "Silahkan Check kembali form inputan anda",
                        life: 3000,
                    });
                } else {
                    if (this.editmode) {
                        this.updateDeposit();
                    } else {
                        this.createDeposit();
                    }
                }
            });
        },

        createDeposit() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            ApiData.post(
                `app/master/payment-method/add-saldo/${this.$route.params.id}`,
                this.deposit
            )
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.cancelDeposit();
                    this.getData();
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        updateDeposit() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            ApiData.post(
                `app/master/payment-method/update-saldo/${this.deposit.id}`,
                this.deposit
            )
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.cancelDeposit();
                    this.getData();
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        cancelDeposit() {
            this.resetDeposit();
            this.modal.deposit = false;
        },

        resetDeposit() {
            this.editmode = false;
            this.deposit = {
                amount: 0,
                note: "",
                date: "",
                name: "",
            };
        },

        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;

            try {
                const response = await ApiData.get(
                    `app/master/payment-method/history/${this.$route.params.id}?limit=${this.limit}&page=${this.page}&start=${this.filter.date.start}&end=${this.filter.date.end}`
                );
                var data = response.data;
                this.transactions = data.transactions;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        filterDate() {
            var date = this.filter.date;
            if (date != null) {
                this.filter.date = {
                    start:
                        date.start != null ? date.start.substring(0, 10) : "",
                    end: date.end != null ? date.end.substring(0, 10) : "",
                };

                this.getData();
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

        formatNumber(number) {
            if (parseInt(number) != 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },

        removeFilter(type) {
            if (type == "date") {
                this.filter.date = {
                    start: "",
                    end: "",
                };
            }

            this.getData();
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
    },
};
</script>
