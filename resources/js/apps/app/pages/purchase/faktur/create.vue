<template>
    <Form ref="ValidationForOtherInformation" class="col-12">
        <div class="row">
            <div class="col-11">
                <div class="card custom-card">
                    <div class="card-body add-product p-0">
                        <div class="p-4">
                            <div class="row gx-5">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <!-- Supplier -->
                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Supplier
                                                {{
                                                    transaction.supplier.id !=
                                                    undefined
                                                        ? "Saldo Rp (" +
                                                          formatNumber(
                                                              transaction
                                                                  .supplier
                                                                  .total_saldo
                                                          ) +
                                                          ") "
                                                        : ""
                                                }}
                                            </label>
                                            <Field
                                                :rules="{
                                                    required: true,
                                                }"
                                                v-slot="{ errors }"
                                                v-model="transaction.supplier"
                                                name="Informasi Supplier"
                                            >
                                                <div class="p-inputgroup">
                                                    <Multiselect
                                                        v-model="
                                                            transaction.supplier
                                                        "
                                                        :options="suppliers"
                                                        :multiple="false"
                                                        :close-on-select="true"
                                                        :clear-on-select="true"
                                                        :preserve-search="true"
                                                        :searchable="true"
                                                        :loading="
                                                            loader.supplier
                                                        "
                                                        :internal-search="true"
                                                        :options-limit="50"
                                                        :disabled="
                                                            transaction.fakturs
                                                                .length > 0
                                                                ? true
                                                                : false
                                                        "
                                                        placeholder="Pilih Supplier"
                                                        open-direction="bottom"
                                                        label="name"
                                                        id="id"
                                                        track-by="name"
                                                        tagPlaceholder=""
                                                        selectLabel=""
                                                        @search-change="
                                                            getSuppliers
                                                        "
                                                    ></Multiselect>
                                                    <button
                                                        class="btn btn-sm btn-info"
                                                        type="button"
                                                        @click="
                                                            modalTransaction()
                                                        "
                                                        v-if="
                                                            transaction.supplier
                                                                .id
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-search"
                                                        ></i>
                                                    </button>
                                                </div>

                                                <div class="fs-sm text-danger">
                                                    {{ errors[0] }}
                                                </div>
                                            </Field>
                                        </div>
                                        <!-- End Supplier -->

                                        <!-- Date  -->
                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Tanggal Pembayaran</label
                                            >
                                            <Field
                                                :rules="{
                                                    required: true,
                                                }"
                                                v-slot="{ errors }"
                                                v-model="transaction.date"
                                                name="Tanggal Pembayaran"
                                            >
                                                <Calendar
                                                    :showButtonBar="true"
                                                    inputId="calendarPopup"
                                                    :hideOnDateTimeSelect="true"
                                                    style="width: 100%"
                                                    v-model="transaction.date"
                                                    dateFormat="dd-mm-yy"
                                                />
                                                <div class="fs-sm text-danger">
                                                    {{ errors[0] }}
                                                </div>
                                            </Field>
                                        </div>
                                        <!-- End Date -->

                                        <!-- Methods -->
                                        <div
                                            class="col-lg-6 mt-2"
                                            v-if="
                                                transaction.payment_method ==
                                                'cash'
                                            "
                                        >
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Metode Pembayaran</label
                                            >
                                            <Field
                                                :rules="{
                                                    required: true,
                                                }"
                                                v-slot="{ errors }"
                                                v-model="transaction.method"
                                                name="Metode Pembayaran"
                                            >
                                                <Multiselect
                                                    v-model="transaction.method"
                                                    :options="methods"
                                                    :multiple="false"
                                                    :close-on-select="true"
                                                    :clear-on-select="true"
                                                    :preserve-search="true"
                                                    :searchable="true"
                                                    :internal-search="false"
                                                    :options-limit="50"
                                                    :loading="loader.method"
                                                    placeholder="Pilih Metode Pembayaran"
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
                                        <!-- End Methods -->

                                        <!-- Payment -->
                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Nilai Di Bayarkan</label
                                            >

                                            <div class="p-inputgroup">
                                                <InputNumber
                                                    v-model="
                                                        transaction.total_payment
                                                    "
                                                    style="width: 100%"
                                                    placeholder="Masukkan Nominal Di Bayarkan"
                                                    prefix="Rp "
                                                />
                                                <button
                                                    class="btn btn-sm btn-info"
                                                    type="button"
                                                    @click="selectInput"
                                                >
                                                    <i
                                                        class="fa fa-check-circle"
                                                    ></i>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- End Payment -->

                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>No.Faktur</th>
                                                            <th>Tanggal</th>
                                                            <th>Total</th>
                                                            <th>Terutang</th>
                                                            <th>Di Bayar</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr
                                                            v-for="(
                                                                item, index
                                                            ) in transaction.fakturs"
                                                            :key="index"
                                                        >
                                                            <td>
                                                                <a
                                                                    href="javascript:void(0)"
                                                                    @click="
                                                                        $goTo({
                                                                            name: 'purchase_update',
                                                                            params: {
                                                                                id: item.transaction_id,
                                                                            },
                                                                        })
                                                                    "
                                                                >
                                                                    {{
                                                                        item.ref_no
                                                                    }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                {{ item.date }}
                                                            </td>
                                                            <td>
                                                                Rp
                                                                {{
                                                                    formatNumber(
                                                                        item.amount
                                                                    )
                                                                }}
                                                            </td>
                                                            <td>
                                                                Rp
                                                                {{
                                                                    formatNumber(
                                                                        item.total_due
                                                                    )
                                                                }}
                                                            </td>
                                                            <td>
                                                                Rp
                                                                {{
                                                                    formatNumber(
                                                                        item.total_pay
                                                                    )
                                                                }}
                                                            </td>

                                                            <td>
                                                                <button
                                                                    class="btn btn-danger btn-sm"
                                                                    type="button"
                                                                    v-tooltip.top="
                                                                        'Hapus Item'
                                                                    "
                                                                    @click="
                                                                        RemoveItem(
                                                                            index
                                                                        )
                                                                    "
                                                                >
                                                                    <i
                                                                        class="fa fa-trash"
                                                                    ></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <table
                                                class="table-centered border mb-lg-0 table mt-3"
                                            >
                                                <thead class="bg-light">
                                                    <tr>
                                                        <td colspan="2">
                                                            Keterangan
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            Jumlah Nominal Perlu
                                                            Di Bayarkan
                                                        </td>
                                                        <td class="text-right">
                                                            {{
                                                                formatNumber(
                                                                    this
                                                                        .transaction
                                                                        .total_due
                                                                )
                                                            }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Jumlah Nominal Akan
                                                            Di Bayarkan
                                                        </td>
                                                        <td class="text-right">
                                                            {{
                                                                formatNumber(
                                                                    this
                                                                        .transaction
                                                                        .total_payment
                                                                )
                                                            }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Sisa Hutang Perlu Di
                                                            Bayarkan
                                                        </td>
                                                        <td class="text-right">
                                                            {{
                                                                formatNumber(
                                                                    this
                                                                        .transaction
                                                                        .subtotal
                                                                )
                                                            }}
                                                        </td>
                                                    </tr>
                                                    <tr
                                                        v-if="
                                                            transaction.total_credit >
                                                            0
                                                        "
                                                    >
                                                        <td>
                                                            Nominal Akan Di
                                                            Kreditkan
                                                        </td>
                                                        <td class="text-right">
                                                            {{
                                                                formatNumber(
                                                                    this
                                                                        .transaction
                                                                        .total_credit
                                                                )
                                                            }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-1">
                <button
                    type="button"
                    @click="processPaymentTransaction('final')"
                    :disabled="loader.submit"
                    v-tooltip.top="'Proses Pembayaran'"
                    class="btn btn-success btn-block label-btn label-end mr-2"
                >
                    <i
                        class="fe fe-save label-btn-icon ms-2"
                        style="font-size: 30px"
                    ></i>
                </button>

                <button
                    type="button"
                    @click="processPaymentTransaction('final', 'print')"
                    :disabled="loader.submit"
                    v-tooltip.top="'Proses Pembayaran dan Print'"
                    class="btn btn-success btn-block label-btn label-end mr-2"
                >
                    <i
                        class="fa fa-print label-btn-icon ms-2"
                        style="font-size: 30px"
                    ></i>
                </button>
            </div>
        </div>
    </Form>

    <!-- Modal For Saving Transaction -->
    <Dialog
        v-model:visible="modal.transaction"
        header="Faktur Pembelian"
        :style="{ width: '70rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row p-2">
            <div class="col-12">
                <div>
                    <label class="form-label">Cari Transaksi</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="modal.search"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Transaksi...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <DataTable
                        :value="fakturs"
                        :paginator="true"
                        :rows="faktur.limit"
                        :rowsPerPageOptions="[20, 50, 100]"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :lazy="true"
                        :totalRecords="faktur.totalRows"
                        @page="onPageChange($event)"
                        class="table"
                        :loading="loader.faktur"
                        responsiveLayout="scroll"
                        sortField="dynamicSortField"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                    >
                        <Column field="date" header="Tanggal"></Column>
                        <Column field="ref_no" header="Nomor Faktur"> </Column>
                        <Column header="Tipe">
                            <template #body="{ data }">
                                {{
                                    data.type == "hutang"
                                        ? "Pembelian"
                                        : "Retur Pembelian"
                                }}
                            </template>
                        </Column>
                        <Column header="Nominal">
                            <template #body="{ data }">
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>
                        <Column header="Terbayar">
                            <template #body="{ data }">
                                {{ formatNumber(data.total_pay) }}
                            </template>
                        </Column>
                        <Column header="Sisa">
                            <template #body="{ data }">
                                {{ formatNumber(data.total_due) }}
                            </template>
                        </Column>

                        <Column field="action" header="Aksi">
                            <template #body="slotProps">
                                <button
                                    class="btn btn-sm btn-success"
                                    type="button"
                                    @click="
                                        selectedFakturs(
                                            slotProps.data,
                                            slotProps.index
                                        )
                                    "
                                >
                                    <i class="fa fa-check-circle"></i>
                                </button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </Dialog>
    <!-- End Modal For Saving Transaction -->
</template>

<script>
import NProgress from "nprogress";
import Editor from "primevue/editor";
import Fieldset from "primevue/fieldset";
import Swal from "sweetalert2";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "create_faktur",
    components: {
        Editor,
        Fieldset,
    },
    data() {
        return {
            suppliers: [],
            methods: [],
            dues: [],
            fakturs: [],
            selected_fakturs: [],
            loader: {
                supplier: false,
                faktur: false,
                method: false,
                data: true,
            },
            transaction: {
                status: "",
                id: null,
                method: {
                    id: "",
                    name: "",
                },
                payment_method: "cash",
                supplier: {
                    id: null,
                    name: "",
                    total_saldo: 0,
                },
                date: null,
                no_ref: null,
                fakturs: [],
                subtotal: 0,
                total_credit: 0,
                total_payment: 0,
                total_due: 0,
                subtotal: 0,
            },
            modal: {
                transaction: false,
                search: "",
            },
            faktur: {
                limit: 20,
                totalRows: 0,
            },
        };
    },
    computed: {},
    created() {
        this.getSuppliers("");
        this.getMethods("");
        const today = new Date().toISOString().substr(0, 10);
        this.transaction.date = today;
    },
    methods: {

        onPageChange(e) {
            this.faktur.limit = e.rows;
            this.faktur.page = e.page += 1;
            this.getFakturs(this.faktur.page);
        },
        
        modalTransaction() {
            this.getFakturs("");
            this.modal.transaction = true;
        },

        searchData() {
            this.doSearch(this);
        },

        doSearch: _.debounce((rootInstance) => {
            rootInstance.getFakturs();
        }, 300),

        selectInput() {
            this.transaction.total_payment = this.transaction.total_due;
        },

        async getFakturs() {
            if (
                this.transaction.supplier.id == null ||
                this.transaction.supplier.id == ""
            ) {
                this.$toast.add({
                    severity: "error",
                    summary: "Peringatan",
                    detail: "Silahkan Pilih Supplier Terlebih dahulu",
                    life: 3000,
                });
                return false;
            }
            this.loader.faktur = true;

            try {
                const response = await ApiData.get(
                    `app/transactions/transaction-due?limit=${this.faktur.limit}&name=${this.modal.search}&supplier=${this.transaction.supplier.id}&status=due&order=asc`
                );
                var data = response.data;
                this.faktur.totalRows = data.totalRows;

                const transactionFaktursIds = this.transaction.fakturs.map(
                    (faktur) => faktur.id
                );

                this.fakturs = data.transactions.filter(
                    (faktur) => !transactionFaktursIds.includes(faktur.id)
                );

                this.loader.faktur = false;
            } catch (error) {
                console.log(error);
            }
        },

        // Get Informasi Supplier
        async getSuppliers(query) {
            this.loader.supplier = true;
            try {
                const response = await ApiData.get(
                    `app/crm/suppliers?name=${query}`
                );
                var data = response.data;
                this.suppliers = data.suppliers;
                this.loader.supplier = false;
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

        selectedFakturs(data, index) {
            var idfaktur = false;
            var faktur = data;
            idfaktur = this.transaction.fakturs.filter((item) => {
                if (item.id == data.id) {
                    return true;
                }
            });

            if (idfaktur == false) {
                var amount = faktur.amount;
                var totaldue = faktur.total_due;

                if (faktur.type == "saldo") {
                    amount = faktur.amount - faktur.amount * 2;
                }

                if (faktur.amount == totaldue) {
                    totaldue = amount;
                }

                this.transaction.fakturs.push({
                    item_id: null,
                    id: faktur.id,
                    ref_no: faktur.ref_no,
                    date: faktur.date,
                    type: faktur.type,
                    amount: amount,
                    total_pay: faktur.total_pay,
                    transaction_id: faktur.transaction.id,
                    total_due: totaldue,
                });

                this.fakturs.splice(index, 1);
                this.calculateSummary();
            }
        },

        RemoveItem(index) {
            this.transaction.fakturs.splice(index, 1);
            setTimeout(() => {
                this.calculateSummary();
            }, 500);
        },

        updateTransaction() {
            var totalPay = this.transaction.total_payment;
            var totalSaldo = this.transaction.supplier.total_saldo;

            if (
                this.transaction.payment_method == "saldo" &&
                this.transaction.total_payment > totalSaldo
            ) {
                this.transaction.total_payment = totalSaldo;
            }

            if (this.transaction.total_payment > this.transaction.total_due) {
                this.transaction.total_credit =
                    this.transaction.total_payment - this.transaction.total_due;
            } else {
                this.transaction.total_credit = 0;
            }

            this.transaction.subtotal =
                totalPay > this.transaction.total_due
                    ? 0
                    : this.transaction.total_due - totalPay;
        },

        calculateSummary() {
            var subtotal = 0;

            for (var i in this.transaction.fakturs) {
                var detail = this.transaction.fakturs[i];
                subtotal += detail.total_due;
            }

            this.transaction.total_due = subtotal;
        },

        processPaymentTransaction(status, type = "") {
            this.transaction.status = status;
            this.$refs.ValidationForOtherInformation.validate().then(
                (success) => {
                    if (!success) {
                        this.$toast.add({
                            severity: "error",
                            summary: "Terjadi kesalahan",
                            detail: "Silahkan Check kembali form inputan anda",
                            life: 3000,
                        });
                    } else {
                        if (this.transaction.total_payment == 0) {
                            Swal.fire({
                                title: "Peringatan!",
                                text: "Nominal yang akan di bayarkan adalah nol ? klik ok jika ingin melanjutkan proses pembayaran",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Ok",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    this.loader.submit = true;
                                    NProgress.start();
                                    NProgress.set(0.1);
                                    ApiData.post(
                                        "app/transactions/purchases/faktur/create",
                                        this.transaction
                                    )
                                        .then((response) => {
                                            this.$handleSuccessResponse(
                                                response.data.message
                                            );
                                            NProgress.done();

                                            this.new_transaction =
                                                response.data.details;

                                            if (type == "print") {
                                                this.printLabel();
                                            }

                                            setTimeout(() => {
                                                window.parent.postMessage({
                                                    action: "closeActiveMenu",
                                                    data: "",
                                                });
                                            }, 1000);
                                        })
                                        .catch((err) => {
                                            NProgress.done();
                                            this.loader.submit = false;
                                            this.$handleErrorResponse(err);
                                        });
                                } else {
                                    Swal.fire("Membatalkan Proses");
                                }
                            });
                        } else {
                            this.loader.submit = true;
                            NProgress.start();
                            NProgress.set(0.1);
                            ApiData.post(
                                "app/transactions/purchases/faktur/create",
                                this.transaction
                            )
                                .then((response) => {
                                    this.$handleSuccessResponse(
                                        response.data.message
                                    );
                                    NProgress.done();

                                    this.new_transaction =
                                        response.data.details;

                                    if (type == "print") {
                                        this.printLabel();
                                    }

                                    setTimeout(() => {
                                        window.parent.postMessage({
                                            action: "closeActiveMenu",
                                            data: "",
                                        });
                                    }, 1000);
                                })
                                .catch((err) => {
                                    NProgress.done();
                                    this.loader.submit = false;
                                    this.$handleErrorResponse(err);
                                });
                        }
                    }
                }
            );
        },

        formatNumber(number) {
            if (parseFloat(number) >= 0) {
                return number.toLocaleString();
            } else {
                return "-" + (-number).toLocaleString();
            }
        },

        printLabel() {
            const receiptHTML = `<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pembelian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .container {
            margin: auto;
            padding: 15px;  
        }

        .header {
            display: flex;
            justify-content: space-between;
        }

        .title {
            font-size: 24px;
            margin: 0;
        }

        .kepada {
            text-align: right;
        }

        .kepada p {
            margin: 0;
            font-size: 16px;
        }


        .nomor p,
        .pembayaran p,
        .admin p {
            margin: 0;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .keterangan {
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }
    </style>
   
</head>

<body><div class="container">
    <div class="header">
      <div>
        <h1 style="margin-top: 0px; margin-bottom: 0px;">SAC</h1>
        <div>
          <b>Kepada :</b> <br />
          ${this.new_transaction.supplier.name} <br />
          ${this.new_transaction.supplier.address}
        </div>
      </div>
      <div>
        <h1 style="margin-top: 0px;">Pembayaran Pembelian</h1>
        <div>
          <table>
            <tr>
              <th>Nomor</th>
              <th>Tanggal</th>
              <th>Pembayaran</th>
              <th>Admin</th>
            </tr>
            <tr>
              <th>${this.new_transaction.ref_no}</th>
              <th>${this.new_transaction.date}</th>
              <th>${this.new_transaction.method.name}</th>
              <th>${this.new_transaction.created.name}</th>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>No. Faktur</th>
          <th>Tgl. Faktur</th>
          <th>Total Faktur</th>
          <th>Pembayaran</th>
        </tr>
      </thead>
      <tbody>
        ${this.new_transaction.fakturs
            .map(
                (faktur) => `
                         <tr>
          <td>${faktur.ref_no}</td>
          <td>${faktur.date}</td>
          <td>${this.formatNumber(faktur.amount)}</td>
          <td>${this.formatNumber(faktur.pay)}</td>
        </tr>
                        `
            )
            .join("")}
       
        <tr>
          <td colspan="3">
            <b>Total</b>
          </td>
          <td>
            <b>${this.formatNumber(this.new_transaction.subtotal)}</b>
          </td>
        </tr>
      </tbody>

      <tfoot>
        <tr>
          <td colspan="2" rowspan="3" style="vertical-align: top; text-align:left; height:100px;">
            Keterangan :
          </td>
          <td rowspan="3" style="vertical-align: top;">Pemberi</td>
          <td rowspan="3" style="vertical-align: top;">Penerima</td>
        </tr>
      </tfoot>
    </table>

    <div class="footer">
      <p>Halaman 1 dari 1</p>
    </div>
  </div>`;

            // Open a new window and write the HTML content
            const printWindow = window.open("", "_blank");
            printWindow.document.open();
            printWindow.document.write(receiptHTML);
            printWindow.document.close();

            // Wait for the content to load and then trigger print
            printWindow.onload = function () {
                printWindow.print();
                printWindow.onafterprint = function () {
                    printWindow.close();
                };
            };
        },
    },
    mounted: function () {},
    watch: {
        transaction: {
            handler: function (newVal, oldVal) {
                this.updateTransaction();
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>
