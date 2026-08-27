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
                <div>
                    <a href="javascript:void(0);" @click="printLabel"  class="btn btn-secondary mr-1">
                        <i
                            class="fe fe-printer me-2 align-middle d-inline-flex"
                        ></i>
                        Print
                    </a>
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
                                <p class="text-muted mb-2">Tujuan customer :</p>
                                <p class="fw-bold mb-1">
                                    {{ transaction.customer.name }}
                                </p>
                                <p class="text-muted mb-1">
                                    {{ transaction.customer.address }}
                                </p>
                                <p class="text-muted mb-1">
                                    {{ transaction.customer.email }}
                                </p>
                                <p class="text-muted">
                                    {{ transaction.customer.phone }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
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
                    <div class="col-lg-3">
                        <p class="fw-semibold text-muted mb-1">
                            Estimasi Selesai :
                        </p>
                        <p class="fs-15 mb-1">
                            {{ transaction.estimate_date }}
                        </p>
                    </div>
                    <div class="col-lg-3">
                        <p class="fw-semibold text-muted mb-1">Status :</p>
                        <p class="fs-16 mb-1 fw-semibold">
                            <span
                                class="badge bg-warning text-black"
                                v-if="transaction.status == 'pending'"
                                >Pending</span
                            >
                            <span
                                class="badge rounded-pill bg-info text-black"
                                v-if="transaction.status == 'process'"
                                >Dalam Pengerjaan</span
                            >

                            <span
                                class="badge rounded-pill bg-outline-primary text-black"
                                v-if="transaction.status == 'complete'"
                                >Selesai</span
                            >

                            <span
                                class="badge bg-warning text-black"
                                v-if="transaction.status == 'taken'"
                                >Di Ambil</span
                            >
                        </p>
                    </div>
                    <div class="col-lg-3">
                        <p class="fw-semibold text-muted mb-1">
                            Estimasi Biaya :
                        </p>
                        <p class="fs-16 mb-1 fw-semibold">
                            {{ formatNumber(transaction.estimate_price) }}
                        </p>
                    </div>
                    <div class="col-xl-12">
                        <div class="table-responsive">
                            <DataTable
                                :value="transaction.items"
                                :paginator="true"
                                :rows="20"
                                :rowsPerPageOptions="[20, 50]"
                                paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                                :lazy="true"
                                :totalRecords="totalRows"
                                @page="onPageChange($event)"
                                stripedRows
                                responsiveLayout="scroll"
                                v-model:expandedRows="expandedlist"
                                :loading="loading"
                                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                            >
                                <Column
                                    :expander="true"
                                    headerStyle="width: 3rem"
                                />
                                <Column
                                    header="Nama Barang"
                                    field="name"
                                ></Column>
                                <Column
                                    header="Keluhan"
                                    field="complaint"
                                ></Column>
                                <Column
                                    header="Kelengkapan"
                                    field="completeness"
                                ></Column>
                                <Column header="Aksi">
                                    <template #body="{ data }">
                                        <button
                                        v-if="data.status != 'taken'"
                                            class="btn btn-sm btn-info"
                                            type="button"
                                            @click="changeStatus(data)"
                                        >
                                            <i class="fa fa-pencil"></i> Ubah
                                            Status
                                        </button>
                                    </template>
                                </Column>

                                <template #expansion="{ data }">
                                    <div class="p-2 bg-white">
                                        <ul class="iq-timeline">
                                            <li
                                                v-for="(
                                                    item, index
                                                ) in data.records"
                                                :key="index"
                                            >
                                                <div
                                                    class="timeline-dots"
                                                    :class="
                                                        item.type == 'note'
                                                            ? 'border-info'
                                                            : 'border-success'
                                                    "
                                                ></div>
                                                <h6 class="float-left mb-1">
                                                    {{ item.status }}
                                                </h6>
                                                <small
                                                    class="float-right mt-1"
                                                    >{{ item.date }}</small
                                                >
                                                <div
                                                    class="d-inline-block w-100"
                                                >
                                                    <p>
                                                        {{ item.subject }}
                                                    </p>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </template>
                            </DataTable>
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

    <Dialog
        v-model:visible="modal"
        class="filter-data"
        modal
        maximizable
        :header="'Ubah Status atau Tambah Catatan Progress'"
        :style="{ width: '50vw' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <ScrollPanel style="width: 100%; height: 70vh">
            <Form @submit="createRecords()" ref="recordValidation">
                <div class="row p-3">
                    <div class="col-12 mb-3">
                        <label for="user-date" class="form-label"
                            >Pilih Tipe</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="record.type"
                            :name="'Tipe Akun'"
                        >
                            <Dropdown
                                v-model="record.type"
                                :options="[
                                    {
                                        name: 'Catatan',
                                        value: 'note',
                                    },
                                    {
                                        name: 'Progress',
                                        value: 'process',
                                    },
                                    {
                                        name: 'Selesai',
                                        value: 'complete',
                                    },
                                    {
                                        name: 'Di Ambil',
                                        value: 'taken',
                                    },
                                ]"
                                optionLabel="name"
                                optionValue="value"
                                placeholder="Pilih Opsi"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="user-ref" class="form-label">Catatan</label>
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="record.subject"
                            :name="'Sub Akun'"
                        >
                            <input
                                type="text"
                                style="width: 100%"
                                v-model="record.subject"
                                class="form-control"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                </div>
            </Form>
        </ScrollPanel>

        <template #footer>
            <button
                type="button"
                @click="createRecords()"
                :disabled="loader.submit"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                Tambahkan Data
            </button>
        </template>
    </Dialog>
</template>

<script>
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "detail_sales",
    components: {},
    data() {
        return {
            qr:"",
            loader: {
                submit: false,
                data: false,
            },
            modal: false,
            record: {
                id: "",
                subject: "",
                type: "note",
            },
            expandedlist: [],
            transaction: {
                customer: {
                    id: "",
                    name: "",
                },
                complete_date: "",
                created_date: {
                    date: "",
                    time: "",
                },
                product: "",
                estimate_date: "",
                estimate_price: 0,
                complaint: "",
                note: "",
                completeness: "",
                items: [],
            },
        };
    },
    computed: {},
    created() {
        this.getDetails();
    },
    methods: {
        changeStatus(data) {
            this.record = {
                id: data.id,
                subject: "",
                type: "note",
            };

            this.modal = true;
        },

        async getDetails() {
            this.loader.data = true;
            try {
                const response = await ApiData.get(
                    `app/transactions/rma/detail/${this.$route.params.id}`
                );
                this.transaction = response.data.transactions;
                this.records = response.data.records;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        createRecords() {
            this.$refs.recordValidation.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Terjadi kesalahan",
                        detail: "Silahkan Check kembali form inputan anda",
                        life: 3000,
                    });
                } else {
                    this.loader.submit = true;
                    ApiData
                        .post(
                            `app/transactions/rma/records/add/${this.record.id}`,
                            this.record
                        )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            this.modal = false;
                            this.loader.submit = false;
                            this.getDetails();
                        })
                        .catch((err) => {
                            this.loader.submit = false;
                            this.$handleErrorResponse(err);
                        });
                }
            });
        },

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },

        printLabel() {
            const receiptHTML = `<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print RMA</title>
   <style>
        @font-face {
            font-family: SourceSansPro;
            src: url(SourceSansPro-Regular.ttf);
        }

        #logo {
           text-align: center;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table,
        .table th,
        .table td {
            border: 1px solid black;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            padding:10px;
            color: #555555;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-family: SourceSansPro;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
            margin-top: 8px;
        }

        #logo img {
            height: 70px;
        }

        #company {
            float: right;
            text-align: right;
        }


        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {
            float: right;
            text-align: right;
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 2.4em;
            line-height: 1em;
            font-weight: normal;
            margin: 0 0 10px 0;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 20px;
            background: #EEEEEE;
        }

        table td h3 {
            color: #57B223;
            font-size: 1.2em;
            font-weight: normal;
            margin: 0 0 0.2em 0;
        }

        table .no {
            color: #FFFFFF;
            font-size: 1.6em;
            background: #57B223;
        }


        table .total {
            background: #57B223;
            color: #FFFFFF;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }


        table tfoot td {
            padding: 10px 20px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-top: 1px solid #AAAAAA;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr:last-child td {
            color: #57B223;
            font-size: 1.4em;
            border-top: 1px solid #57B223;

        }

        table tfoot tr td:first-child {
            border: none;
        }

        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }
    </style>
 
</head>

<body>
    <header class="clearfix">
        <div id="logo">
            ${this.qr} <br />
            Scan kode berikut untuk melihat update
        </div>
        <div id="company">
            <br>
            <h2 class="name">${this.transaction.store?.name || ''}</h2>
            <div>+${this.transaction.store?.phone || ''}</div>
            <div>${this.transaction.store?.address || ''}</div>
        </div>
    </header>
    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <div class="to">No.Ref : ${this.transaction.ref_no}</div>
                <div class="name">Nama Pemilik : ${this.transaction.customer.name || ''}</div>
                <div class="address">Alamat : ${this.transaction.customer.address || ''}</div>
                <div class="email">No HP : ${this.transaction.customer.phone || ''}</div>
            </div>
            <div id="invoice">
                <div class="date">Tanggal : ${this.transaction.created_date.date}</div>
                <div class="date">Estimasi Selesai : ${this.transaction.estimate_date.substring(0, 10)}</div>
                <div class="date">Estimasi Biaya : Rp. ${Number(this.transaction.estimate_price).toLocaleString()}</div>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Keluhan</th>
                    <th>Kelengkapan</th>
                </tr>
            </thead>
            <tbody>
                ${this.transaction.items.map(item => `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.complaint}</td>
                    <td>${item.completeness}</td>
                </tr>
                `).join('')}
            </tbody>
        </table>
        <table border="1" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td>
                        <p><strong>Syarat dan Ketentuan RMA :</strong></p>
                        <p>1. Produk sudah di cek dan sesuai tanda terima<br>
                           2. Produk yang Tidak di ambil 2 Minggu setelah di konfirmasi Selesai akan dikenakan biaya penitipan sebesar Rp10.000 / Hari<br>
                           3. Produk yang tidak di ambil 1 Bulan setelah di konfirmasi Selesai di anggap TIDAK DIINGINKAN lagi oleh pemilik nya. Toko akan memusnahkan produk nya.<br>
                           4. Produk yang sudah di musnahkan tidak di Ganti atau di minta kembali.<br>
                           5. Pembeli / Customer dianggap telah Seteju dan Mengerti syarat dan ketentuan</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table border="0" cellspacing="0" cellpadding="0">
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">
                        <p align="center">Pelanggan <br> <br> <br> <br> </p>
                        <hr>
                    </td>
                    <td colspan="2">
                        <p align="center">Petugas <br> <br> <br> <br> </p>
                        <hr>
                    </td>
                </tr>
            </tfoot>
        </table>
    </main>
</body>
</html>`;

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
