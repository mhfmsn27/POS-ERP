<template>
    <div class="col-12" v-if="!loader.data">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between d-block">
                <div class="h5 mb-0 d-sm-flex d-bllock align-items-center">
                    <div class="ms-sm-2 ms-0 mt-sm-0 mt-2">
                        <div class="h6 fw-semibold mb-0">
                            NO REFERENSI :
                            <span class="text-primary"
                                >#{{ transaction.ref_no }}</span
                            >
                        </div>
                    </div>
                </div>
                <div class="ms-auto mt-md-0 mt-2">
                    <button
                        class="btn btn-secondary me-1"
                        type="button"
                        @click="printLabel()"
                    >
                        Print<i
                            class="fe fe-printer ms-1 align-middle d-inline-flex"
                        ></i>
                    </button>
                    <button class="btn btn-info me-1">
                        Export ke PDF<i
                            class="ri-file-pdf-line ms-1 align-middle d-inline-flex"
                        ></i>
                    </button>

                    <button
                        class="btn btn-danger"
                        type="button"
                        @click="modal.void = true"
                        v-if="transaction.status == 'final'"
                    >
                        Void Transaksi<i
                            class="bx bx-x-circle ms-1 align-middle d-inline-flex"
                        ></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-2">Dari Toko :</p>
                                <p class="fw-bold mb-1">
                                    {{ transaction.store.name }}
                                </p>
                                <p class="mb-1 text-muted">
                                    {{ transaction.store.address }}
                                </p>
                                <p class="mb-1 text-muted">
                                    {{ transaction.store.email }}
                                </p>
                                <p class="text-muted">
                                    {{ transaction.store.phone }}
                                </p>
                            </div>
                            <div>
                                <p class="text-muted mb-2">Tujuan Supplier :</p>
                                <p class="fw-bold mb-1">
                                    {{ transaction.supplier.name }}
                                </p>
                                <p class="text-muted mb-1">
                                    {{ transaction.supplier.country }}
                                    {{ transaction.supplier.address }}
                                </p>
                                <p class="text-muted mb-1">
                                    {{ transaction.supplier.email }}
                                </p>
                                <p class="text-muted">
                                    {{ transaction.supplier.phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <p class="fw-semibold text-muted mb-1">
                            Referensi Supplier :
                        </p>
                        <p class="fs-15 mb-1">#{{ transaction.ref_no }}</p>
                    </div>
                    <div class="col-lg-2">
                        <p class="fw-semibold text-muted mb-1">
                            Tanggal Di Buat :
                        </p>
                        <p class="fs-15 mb-1">
                            {{ transaction.created_date.date }}
                            -
                            <span class="text-muted fs-12">{{
                                transaction.created_date.time
                            }}</span>
                        </p>
                    </div>
                    <div class="col-lg-2">
                        <p class="fw-semibold text-muted mb-1">
                            Tanggal Transaksi :
                        </p>
                        <p class="fs-15 mb-1">
                            {{ transaction.date }}
                        </p>
                    </div>
                    <div class="col-lg-3">
                        <p class="fw-semibold text-muted mb-1">
                            Status Transaksi :
                        </p>
                        <p class="fs-16 mb-1 fw-semibold">
                            {{
                                transaction.status == "received"
                                    ? "Diterima"
                                    : transaction.status == "ordered"
                                    ? "Proses Pemesanan"
                                    : ""
                            }}
                            {{
                                transaction.status == "void"
                                    ? "Di Batalkan"
                                    : ""
                            }}
                        </p>
                    </div>
                    <div class="col-lg-2">
                        <p class="fw-semibold text-muted mb-1">Dibuat Oleh :</p>
                        <p class="fs-16 mb-1 fw-semibold">
                            {{ transaction.created.name }}
                        </p>
                    </div>

                    <div class="col-xl-12">
                        <div class="table-responsive">
                            <table
                                class="table table-striped table-bordered table-sale"
                            >
                                <thead>
                                    <tr>
                                        <th>No.Faktur</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Di Bayarkan</th>
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
                                                v-if="
                                                    item.transaction_id != null
                                                "
                                            >
                                                {{ item.ref_no }}
                                            </a>

                                            <p v-else>
                                                {{ item.ref_no }}
                                            </p>
                                        </td>
                                        <td>
                                            {{ item.date }}
                                        </td>
                                        <td>
                                            Rp
                                            {{ formatNumber(item.amount) }}
                                        </td>
                                        <td>
                                            Rp
                                            {{ formatNumber(item.pay) }}
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
    <div class="col-12 d-flex justify-content-center p-4" v-else>
        <ProgressSpinner />
    </div>

    <!-- Void Request -->
    <Dialog
        v-model:visible="modal.void"
        modal
        header=""
        :style="{ width: '60vh' }"
    >
        <div class="card-body ps-5 pe-5 pt-2 pb-5 rectangle3">
            <div class="d-flex justify-content-center">
                <img
                    src="@/assets/images/void_transaction.png"
                    style="width: 55%"
                />
            </div>

            <p class="h4 fw-semibold mb-2 text-center">Void Transaksi</p>
            <p class="mb-4 text-muted op-7 fw-normal text-center">
                Pembatalan Transaksi Pembelian!
            </p>
            <Form @submit="sendAskVoid()" ref="ValidationAskCode">
                <div class="row" v-if="void_request.step == 1">
                    <div class="col-12 mt-2">
                        <label for="product-name-add" class="form-label"
                            >Masukkan Alasan Pembatalan Transaksi</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="void_request.reason"
                            name="Alasan Void"
                        >
                            <textarea
                                class="form-control"
                                v-model="void_request.reason"
                            ></textarea>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <div class="col-xl-12 d-grid mt-4">
                        <button
                            type="submit"
                            :disabled="loader.submit"
                            class="btn btn-warning label-btn label-end"
                        >
                            {{
                                loader.submit
                                    ? "Mohon Tunggu...."
                                    : "Minta Kode Void"
                            }}
                            <i
                                class="ri-send-plane-line label-btn-icon ms-2"
                            ></i>
                        </button>
                    </div>
                </div>
            </Form>
            <Form @submit="verificationCode()">
                <div class="row gy-3" v-if="void_request.step == 2">
                    <!-- Code Verifikasi Form -->
                    <div class="col-xl-12">
                        <!-- Code Verification -->
                        <div class="col-xl-12">
                            <label
                                for="signin-code"
                                class="form-label text-default"
                                >Kode Verifikasi</label
                            >
                            <div class="row">
                                <!-- 1 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_1"
                                        type="text"
                                        v-model="code.one"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_1')"
                                    />
                                </div>

                                <!-- 2 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_2"
                                        type="text"
                                        v-model="code.two"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_2')"
                                    />
                                </div>

                                <!-- 3 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_3"
                                        type="text"
                                        v-model="code.tree"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_3')"
                                    />
                                </div>

                                <!-- 4 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_4"
                                        type="text"
                                        v-model="code.for"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_4')"
                                    />
                                </div>

                                <!-- 5 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_5"
                                        type="text"
                                        v-model="code.five"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_5')"
                                    />
                                </div>

                                <!-- 6 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_6"
                                        type="text"
                                        v-model="code.six"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_6')"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- End Code Verification -->

                        <div class="d-flex justify-content-end mt-4">
                            <a
                                href="javascript:void(0);"
                                class="text-blue mb-0 pointer"
                                @click="sendAskVoid"
                                v-if="!resend"
                            >
                                Kirim Permintaan Kode Verifikasi!
                            </a>
                            <vue-countdown
                                v-if="resend"
                                :time="time_left"
                                :interval="100"
                                v-slot="{ seconds }"
                            >
                                <p class="mb-0" v-if="seconds > 1">
                                    Kirim Ulang Setelah ({{ seconds }})
                                </p>

                                <p class="text-dark-blue mb-0" v-else>
                                    Tidak menerima kode ?
                                    <a
                                        href="javascript:void(0);"
                                        class="text-blue text-decoration-none pointer"
                                        @click="sendAskVoid"
                                        >Kirim Ulang</a
                                    >
                                </p>
                            </vue-countdown>
                        </div>
                    </div>
                    <!-- End Code Form -->

                    <div class="col-xl-12 d-grid mt-4">
                        <button
                            type="submit"
                            :disabled="loader.submit"
                            class="btn btn-warning label-btn label-end"
                        >
                            {{
                                loader.submit
                                    ? "Mohon Tunggu...."
                                    : "Batalkan Transaksi Sekarang!"
                            }}
                            <i
                                class="ri-send-plane-line label-btn-icon ms-2"
                            ></i>
                        </button>
                    </div>
                </div>
            </Form>
        </div>
    </Dialog>
    <!-- End Void Request -->
</template>

<script>
import NProgress from "nprogress";
import Editor from "primevue/editor";
import Fieldset from "primevue/fieldset";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "detail_purchase",
    components: {
        Editor,
        Fieldset,
    },
    data() {
        return {
            editmode: false,
            modal: {
                void: false,
            },
            void_request: {
                step: 1,
                reason: "",
                code: "",
            },
            code: {
                one: "",
                two: "",
                tree: "",
                for: "",
                five: "",
                six: "",
            },
            resend: false,
            time_left: 0,

            loader: {
                submit: false,
                data: false,
            },
            transaction: {
                status: "",
                id: null,
                method: {
                    id: "",
                    name: "",
                },
                supplier: {
                    id: null,
                    name: "",
                },
                date: null,
                ref_no: null,
                fakturs: [],
                subtotal: 0,
                total_payment: 0,
                total_due: 0,
                subtotal: 0,
            },
        };
    },
    computed: {},
    created() {
        this.getDetails();
    },
    methods: {
        async getDetails() {
            this.loader.data = true;
            try {
                const response = await ApiData.get(
                    `app/transactions/purchases/faktur/detail/${this.$route.params.id}`
                );
                this.transaction = response.data.transaction;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        // Ask Void Code
        sendAskVoid() {
            this.$refs.ValidationAskCode.validate().then((success) => {
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
                    this.$store
                        .dispatch("purchases/voidRequest", {
                            request: {
                                reason: this.void_request.reason,
                            },
                            id: this.$route.params.id,
                        })
                        .then((response) => {
                            this.$handleSuccessResponse(response.message);
                            NProgress.done();
                            this.void_request.step = 2;
                            this.loader.submit = false;
                            this.resend = true;
                            this.time_left = this.time_left + 60000;
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.loader.submit = false;
                            this.$handleErrorResponse(err);
                        });
                }
            });
        },

        // Verification Void
        verificationCode() {
            if (
                this.code.one == "" ||
                this.code.two == "" ||
                this.code.tree == "" ||
                this.code.for == "" ||
                this.code.five == "" ||
                this.code.six == ""
            ) {
                this.$toast.add({
                    severity: "error",
                    summary: "Terjadi kesalahan",
                    detail: "Silahkan Check kembali form inputan anda",
                    life: 3000,
                });
            } else {
                this.loader.submit = true;
                var code =
                    this.code.one.toString() +
                    this.code.two.toString() +
                    this.code.tree.toString() +
                    this.code.for.toString() +
                    this.code.five.toString() +
                    this.code.six.toString();

                this.$store
                    .dispatch("purchases/voidTransaction", {
                        request: {
                            code: code,
                        },
                        id: this.$route.params.id,
                    })
                    .then((res) => {
                        NProgress.done();
                        this.$handleSuccessResponse(res.message);
                        setTimeout(() => {
                            return this.$router.push({ name: "void_purchase" });
                        }, 1000);
                    })
                    .catch((err) => {
                        NProgress.done();
                        this.loader.submit = false;
                        this.$handleErrorResponse(err);
                    });
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
          ${this.transaction.supplier.name} <br />
          ${this.transaction.supplier.address}
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
              <th>${this.transaction.ref_no}</th>
              <th>${this.transaction.date}</th>
              <th>${this.transaction.method.name}</th>
              <th>${this.transaction.created.name}</th>
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
        ${this.transaction.fakturs
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
            <b>${this.formatNumber(this.transaction.subtotal)}</b>
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

        formatNumber(number) {
            if (parseFloat(number) >= 0) {
                return number.toLocaleString();
            } else {
                return "-" + (-number).toLocaleString();
            }
        },

        nextField(e, nomor) {
            var inputname = nomor;
            var parts = inputname.split("_");
            var input = document.querySelector("[inputId=" + nomor + "]");

            var currentLength = input.value.length;

            if (parseInt(parts[1]) < 6) {
                var newField = parts[0] + "_" + (parseInt(parts[1]) + 1);
                input = document.querySelector("[inputId=" + newField + "]");
                input.focus();
            }

            if (e.key === "Backspace" && currentLength === 0) {
                if (parseInt(parts[1]) > 1) {
                    var newField = parts[0] + "_" + (parseInt(parts[1]) - 1);
                    input = document.querySelector(
                        "[inputId=" + newField + "]"
                    );
                    input.focus();
                }
            }

            if (e.key === "ArrowLeft") {
                if (parseInt(parts[1]) > 1) {
                    var newField = parts[0] + "_" + (parseInt(parts[1]) - 1);
                    input = document.querySelector(
                        "[inputId=" + newField + "]"
                    );
                    input.focus();
                }
            }

            if (e.key === "ArrowRight") {
                if (parseInt(parts[1]) < 6) {
                    var newField = parts[0] + "_" + (parseInt(parts[1]) + 1);
                    input = document.querySelector(
                        "[inputId=" + newField + "]"
                    );
                    input.focus();
                }
            }
        },
    },
    mounted: function () {},
    watch: {},
};
</script>

<style>
.form-check-input {
    inset-block-start: 0.65rem !important;
}

.verifycode {
    padding: 4px !important;
}
</style>
