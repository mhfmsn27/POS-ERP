<template>
    <div class="col-12" v-if="!loader.data">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between">
                <div class="h5 mb-0">
                    <div class="ms-sm-2 ms-0 mt-sm-0 mt-2">
                        <div class="h6 fw-semibold mb-0">
                            NO REFERENSI :
                            <span class="text-primary"
                                >#{{ transaction.ref_no }}</span
                            >
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button
                        class="btn btn-secondary me-2"
                        type="button"
                        @click="printLabel()"
                    >
                        Print<i
                            class="fe fe-printer ms-1 align-middle d-inline-flex"
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
                    <div class="col-lg-3 mt-3 mb-3">
                        <p class="fw-semibold text-muted mb-1">
                            Referensi Supplier :
                        </p>
                        <p class="fs-15 mb-1">
                            #{{ transaction.supplier_ref }}
                        </p>
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
                        <p class="fw-semibold text-muted mb-1">Status :</p>
                        <p class="fs-16 mb-1 fw-semibold">
                            {{
                                transaction.status == "po_not_use"
                                    ? "Belum Di Faktur"
                                    : "Sudah Di Faktur"
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
                                        <th>Nama Produk</th>
                                        <th>Quantity</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            detail, index
                                        ) in transaction.items"
                                        :key="index"
                                    >
                                        <td>
                                            {{ detail.name }}
                                        </td>
                                        <td>
                                            {{ formatNumber(detail.qty) }}
                                            {{ detail.unit.name }}
                                        </td>
                                        <td>
                                            Rp
                                            {{
                                                formatNumber(
                                                    detail.purchase_price_inc_tax
                                                )
                                            }}
                                        </td>
                                        <td>
                                            <p>
                                                Rp
                                                {{
                                                    formatNumber(
                                                        detail.subtotal
                                                    )
                                                }}
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th
                                            scope="row"
                                            colspan="3"
                                            class="text-right"
                                        >
                                            Subtotal
                                        </th>
                                        <th scope="row" class="text-left">
                                            Rp
                                            {{
                                                formatNumber(
                                                    transaction.summary.subtotal
                                                )
                                            }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div>
                            <label for="invoice-note" class="form-label"
                                >Catatan:</label
                            >
                            <textarea
                                class="form-control form-control-light"
                                :disabled="true"
                                id="invoice-note"
                                rows="3"
                                >{{ transaction.note }}</textarea
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 d-flex justify-content-center p-4 mt-4" v-else>
        <ProgressSpinner />
    </div>
</template>

<script>
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "detail_purchase",
    components: {},
    data() {
        return {
            loader: {
                submit: false,
                data: false,
            },
            transaction: {
                supplier: {
                    id: "",
                    name: "",
                },
                store: {
                    id: "",
                    name: "",
                },
                date: "",
                ref_no: "",
                supplier_ref: "",
                note: "",
                items: [],
                summary: {
                    subtotal: 0,
                    discount: 0,
                    tax: 0,
                    total: 0,
                },
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
                    `app/transactions/purchases/po/detail/${this.$route.params.id}`
                );
                this.transaction = response.data.details;
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

        printLabel() {
            const receiptHTML = `<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima Penerimaan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: top; 
            background-color: #f0f0f0;
        }

        .container {
            background: #ffffff;
            padding: 10px;
            width: 88mm;
            border: 1px solid #000;
        }

        header {
            text-align: center;
            margin-bottom: 10px;
        }

        header h1 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        .recipient-info p,
        .remarks p,
        .signature p {
            margin: 5px 0;
            font-size: 12px;

        }

        .item-table {
            margin-bottom: 10px;
        }

        .item-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .item-table th,
        .item-table td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 5px;
        }

        .item-table th {
            background-color: #f2f2f2;
        }

        .signature-box {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .signature-box p {
            margin: 0;
            padding: 10px;
            border: 1px solid #000;
            width: 40%;
            text-align: center;
            font-size: 12px;
        }
    </style>
 
</head>

<body><div class="container">
            <header style="border-top: 1px solid black; border-bottom: 1px solid black; padding: 5px;">
            <h1>PENERIMAAN PEMBELIAN</h1>
            </header>
            
            <section class="recipient-info">
            <p><strong>Kepada:</strong> <br /> ${
                this.transaction.supplier.name
            }</p>
            <p><strong>Alamat:</strong> <br /> ${
                this.transaction.supplier.address
            }</p>
            <p><strong>No. Form:</strong> ${this.transaction.ref_no}</p>
            <p><strong>Tanggal:</strong> ${this.transaction.date}</p>
            </section>
            
            <section class="item-table">
            <table>
                <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                </tr>
                </thead>
                <tbody>
                     ${this.transaction.items
                         .map(
                             (sell) => `
                        <tr>
                        <td>${sell.name || ""}</td>
                        <td>${this.formatNumber(sell.qty)}</td>
                        </tr>
                        `
                         )
                         .join("")}
                </tbody>
            </table>
            </section>
            
            <section class="signature" style="margin-bottom: 100px;">
            <p><strong>Keterangan:</strong></p>
            </section>
        </div></body></html>`;

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
