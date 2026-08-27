<template>
    <div class="col-12" v-if="loader.data">
        <div class="card custom-card">
            <div class="card-header d-md-flex d-block">
                <div class="h5 mb-0 d-sm-flex d-bllock align-items-center">
                    <div class="ms-sm-2 ms-0 mt-sm-0 mt-2">
                        <div class="h6 fw-semibold mb-0">
                            Gaji Pegawai :
                            <span class="text-primary">{{
                                detail.employee.name
                            }}</span>
                        </div>
                    </div>
                </div>
                <!-- <div class="ms-auto mt-md-0 mt-2" v-if="!readyDownload">
                    <button
                        class="btn btn-secondary me-1"
                        @click="handleClickPrint"
                    >
                        {{ $t("general.print")
                        }}<i
                            class="fe fe-printer ms-1 align-middle d-inline-flex"
                        ></i>
                    </button>
                    <button
                        class="btn btn-info me-1"
                        @click="handleClickExportPDF"
                    >
                        {{ $t("general.export_to_pdf")
                        }}<i
                            class="ri-file-pdf-line ms-1 align-middle d-inline-flex"
                        ></i>
                    </button>
                </div> -->
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <p class="text-muted mb-2">Informasi Toko :</p>
                                <p class="fw-bold mb-1">
                                    {{ detail.store.name }}
                                </p>
                                <p class="mb-1 text-muted">
                                    {{ detail.store.address }}
                                </p>
                                <p class="mb-1 text-muted">
                                    {{ detail.store.email }}
                                </p>
                                <p class="text-muted">
                                    {{ detail.store.phone }}
                                </p>
                            </div>
                            <div>
                                <p class="text-muted mb-2">
                                    Informasi Pegawai :
                                </p>
                                <p class="fw-bold mb-1">
                                    {{ detail.employee.name }}
                                </p>
                                <p class="text-muted mb-1">
                                    {{ detail.employee.address }}
                                </p>
                                <p class="text-muted mb-1">
                                    {{ detail.employee.email }}
                                </p>
                                <p class="text-muted">
                                    {{ detail.employee.phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <p class="fw-semibold text-muted mb-1">
                            Tanggal Gaji :
                        </p>
                        <p class="fs-15 mb-1">
                            {{ detail.date }}
                        </p>
                    </div>
                    <div class="col-lg-4">
                        <p class="fw-semibold text-muted mb-1">
                            Absensi Bulan ini :
                        </p>
                        <p class="fs-15 mb-1">
                            {{
                                formatNumber(
                                    detail.info_kinerja.absensi_bulan_ini
                                )
                            }}
                            <span class="text-muted fs-12">Hari</span>
                        </p>
                    </div>
                    <div class="col-lg-4">
                        <p class="fw-semibold text-muted mb-1">
                            Total Jam Kerja :
                        </p>
                        <p class="fs-15 mb-1">
                            {{ detail.info_kinerja.total_jam_kerja }}
                        </p>
                    </div>

                    <div class="col-xl-12">
                        <div class="table-responsive">
                            <table
                                class="table table-striped table-bordered table-sale"
                            >
                                <thead>
                                    <tr>
                                        <th>Informasi</th>
                                        <th>Nominal</th>
                                        <th>X</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th colspan="2">
                                            <h6>Gaji Pokok</h6>
                                        </th>
                                        <th colspan="2" class="text-end">
                                            Rp {{ formatNumber(detail.salary) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">
                                            <h6>Pembayaran Kasbon</h6>
                                        </th>
                                        <th colspan="2" class="text-end">
                                            Rp {{ formatNumber(detail.kasbon) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">
                                            <h6>Komisi Penjualan</h6>
                                        </th>
                                        <th colspan="2" class="text-end">
                                            Rp
                                            {{
                                                formatNumber(detail.commission)
                                            }}
                                        </th>
                                    </tr>
                                    <tr
                                        class="bg-light"
                                        v-if="detail.tunjangan_list.length > 0"
                                    >
                                        <th colspan="4">
                                            <h6>Tunjangan</h6>
                                        </th>
                                    </tr>
                                    <tr
                                        v-for="(
                                            detail_tunjangan, allowanceIndex
                                        ) in detail.tunjangan_list"
                                        :key="allowanceIndex"
                                    >
                                        <td>
                                            {{ detail_tunjangan.name }}
                                        </td>
                                        <td>
                                            Rp
                                            {{
                                                formatNumber(
                                                    detail_tunjangan.amount
                                                )
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                formatNumber(
                                                    detail_tunjangan.qty
                                                )
                                            }}
                                        </td>
                                        <td class="text-end">
                                            Rp
                                            {{
                                                formatNumber(
                                                    detail_tunjangan.subtotal
                                                )
                                            }}
                                        </td>
                                    </tr>
                                    <tr
                                        class="bg-light"
                                        v-if="detail.tunjangan_list.length > 0"
                                    >
                                        <th colspan="2">Total Tunjangan</th>
                                        <th colspan="2" class="text-end">
                                            Rp
                                            {{ formatNumber(detail.tunjangan) }}
                                        </th>
                                    </tr>

                                    <tr
                                        class="bg-light"
                                        v-if="detail.potongan_list.length > 0"
                                    >
                                        <th colspan="4">
                                            <h6>Potongan</h6>
                                        </th>
                                    </tr>
                                    <tr
                                        v-for="(
                                            detail_potongan, allowanceIndex
                                        ) in detail.potongan_list"
                                        :key="'deductionIndex' + allowanceIndex"
                                    >
                                        <td>
                                            <h6 class="m-0">
                                                {{ detail_potongan.name }}
                                            </h6>
                                        </td>
                                        <td>
                                            <h6 class="m-0">
                                                Rp
                                                {{
                                                    formatNumber(
                                                        detail_potongan.amount
                                                    )
                                                }}
                                            </h6>
                                        </td>
                                        <td>
                                            <h6 class="m-0">
                                                {{
                                                    formatNumber(
                                                        detail_potongan.qty
                                                    )
                                                }}
                                            </h6>
                                        </td>
                                        <td class="text-end">
                                            <h6 class="m-0">
                                                Rp
                                                {{
                                                    formatNumber(
                                                        detail_potongan.subtotal
                                                    )
                                                }}
                                            </h6>
                                        </td>
                                    </tr>

                                    <tr class="bg-light">
                                        <th colspan="2">Total Potongan</th>
                                        <th colspan="2" class="text-end">
                                            <h6 class="m-0">
                                                Rp
                                                {{
                                                    formatNumber(
                                                        detail.potongan
                                                    )
                                                }}
                                            </h6>
                                        </th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th
                                            scope="row"
                                            colspan="2"
                                            class="text-end"
                                        >
                                            Pajak
                                        </th>
                                        <th
                                            scope="row"
                                            colspan="2"
                                            class="text-end"
                                        >
                                            {{ detail.pajak }}%
                                        </th>
                                    </tr>
                                    <tr>
                                        <th
                                            scope="row"
                                            colspan="2"
                                            class="text-end"
                                        >
                                            Bonus
                                        </th>
                                        <th
                                            scope="row"
                                            colspan="2"
                                            class="text-end"
                                        >
                                            {{ formatNumber(detail.bonus) }}
                                        </th>
                                    </tr>

                                    <tr>
                                        <th
                                            class="text-end"
                                            colspan="2"
                                            scope="row"
                                        >
                                            <h6>Total Gaji Di Terima</h6>
                                        </th>
                                        <th class="text-end" colspan="2">
                                            Rp
                                            {{ formatNumber(detail.total) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div>
                            <label for="invoice-note" class="form-label">
                                Catatan :</label
                            >
                            <textarea
                                class="form-control form-control-light"
                                id="invoice-note"
                                :disabled="true"
                                v-model="detail.info_kinerja.note"
                                rows="3"
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 d-flex justify-content-center p-4" v-else>
        <ProgressSpinner />
    </div>
</template>

<script>
import NProgress from "nprogress";
var _ = require("lodash");
import { ApiData } from "@/api/server";
export default {
    components: {},
    data() {
        return {
            detail: {
                employee: {
                    id: "",
                    user_id: "",
                    name: "",
                    email: "",
                    phone: "",
                    address: "",
                },
                store: {
                    id: "",
                    name: "",
                    email: "",
                    phone: "",
                    address: "",
                },
                date: "",
                designation: {
                    id: "",
                    name: "",
                },
                commission: 0,
                salary: 0,
                tunjangan: 0,
                potongan: 0,
                kasbon: 0,
                pajak: 0,
                bonus: 0,
                total: 0,
                status: "due",
                potongan_by_late: 0,
                potongan_menit_amount: 0,
                tunjangan_list: [],
                potongan_list: [],
                info_kinerja: {
                    absensi_bulan_ini: "0",
                    total_jam_kerja: " 0",
                    total_keterlambatan: " 0",
                    note: null,
                },
            },

            loader: {
                submit: false,
                data: false,
            },

            pdfOptions: {
                margin: 0,
                image: {
                    type: "jpeg",
                    quality: 1,
                },
                html2canvas: { scale: 3 },
                jsPDF: {
                    unit: "mm",
                    format: "a4",
                    orientation: "l",
                },
            },
            exportFilename: "my-custom-file.pdf",
            readyDownload: false,
        };
    },
    computed: {},
    created() {
        this.getData();
    },
    methods: {
        async getData() {
            try {
                const response = await ApiData.get(
                    `app/hrm/salaries/detail/${this.$route.params.id}`
                );
                var data = response.data;
                this.detail = data.detail;
                this.loader.data = true;
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

        handleClickPrint() {
            this.readyDownload = true;
            NProgress.start();
            NProgress.set(0.1);
            this.$refs.vue3SimpleHtml2pdf.print();
            setTimeout(() => {
                this.readyDownload = false;
            }, 250);
        },

        handleClickExportPDF() {
            this.readyDownload = true;
            NProgress.start();
            NProgress.set(0.1);
            this.exportFilename = `gaji-pegawai-${this.detail.employee.name}.pdf`;
            setTimeout(() => {
                this.$refs.vue3SimpleHtml2pdf.download();
                this.readyDownload = false;
            }, 250);
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
