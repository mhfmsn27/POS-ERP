<template>
    <div class="col-lg-4 mt-4" v-if="loader.data">
        <div class="card custom-card">
            <div class="card-header">
                <h5>Form SPT</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th colspan="2">
                            <label class="form-label">Tanggal Transaksi</label>
                            <div class="input-group">
                                <VueCtkDateTimePicker
                                    label="Filter Tanggal"
                                    locale="Asia/Jakarta"
                                    class="form-control"
                                    v-model="spt.date"
                                    @validate="filterDate"
                                    :range="true"
                                />
                            </div>
                        </th>
                    </tr>

                    <tr class="text-center">
                        <th colspan="2">
                            <button
                                class="btn btn-md btn-primary"
                                type="button"
                                @click="savingAccount()"
                                :disabled="loader.submit"
                            >
                                {{
                                    loader.submit
                                        ? "Loading...."
                                        : "Posting SPT"
                                }}
                            </button>
                        </th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mt-4" v-if="loader.data">
        <div class="col-12 mb-4">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-between mt-3">
                                <div>
                                    <p class="text-muted mb-2">
                                        Toko / Cabang :
                                    </p>
                                    <p class="fw-bold mb-1">
                                        {{ spt.store.name }}
                                    </p>
                                    <p class="mb-1 text-muted">
                                        {{ spt.store.address }}
                                    </p>
                                    <p class="mb-1 text-muted">
                                        {{ spt.store.email }}
                                    </p>
                                    <p class="text-muted">
                                        {{ spt.store.phone }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-striped table-bordered table-sale"
                                >
                                    <thead>
                                        <tr>
                                            <th colspan="4" class="text-center">
                                                RINGKASAN PPN
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Informasi</th>
                                            <th>Tipe</th>
                                            <th>Di Creditkan</th>
                                            <th>PPN Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>Pembelian</th>
                                            <th>Pajak Masukan</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        spt.purchase.credit
                                                    )
                                                }}
                                            </th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        spt.purchase.ppn
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Retur Pembelian</th>
                                            <th>Pajak Masukan</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        spt.return_purchase
                                                            .credit
                                                    )
                                                }}
                                            </th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        spt.return_purchase.ppn
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-start">
                                                Total Pajak Masukan
                                            </th>
                                            <th colspan="2">
                                                {{ formatNumber(spt.masukan) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Penjualan</th>
                                            <th>Pajak Keluaran</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        spt.sales.credit
                                                    )
                                                }}
                                            </th>
                                            <th>
                                                {{
                                                    formatNumber(spt.sales.ppn)
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Penjualan ( Di Gung-gung)</th>
                                            <th>Pajak Keluaran</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        spt.sales.cgunggung
                                                    )
                                                }}
                                            </th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        spt.sales.gunggung
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Return Penjualan</th>
                                            <th>Pajak Keluaran</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        spt.return_sales.credit
                                                    )
                                                }}
                                            </th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        spt.return_sales.ppn
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-start">
                                                Total Pajak Keluaran
                                            </th>
                                            <th colspan="2">
                                                {{ formatNumber(spt.keluaran) }}
                                            </th>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr v-if="spt.lebih_bayar > 0">
                                            <th colspan="2" class="text-start">
                                                Lebih Bayar ( Bulan Sebelumnya )
                                            </th>
                                            <th colspan="2">
                                                {{
                                                    formatNumber(
                                                        spt.lebih_bayar
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr v-if="spt.kurang_bayar > 0">
                                            <th colspan="2" class="text-start">
                                                Kurang Bayar ( Bulan Sebelumnya
                                                )
                                            </th>
                                            <th colspan="2">
                                                {{
                                                    formatNumber(
                                                        spt.kurang_bayar
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-start">
                                                {{
                                                    spt.kurang < 1
                                                        ? "Lebih Bayar"
                                                        : "Kurang Bayar"
                                                }}
                                            </th>
                                            <th colspan="2">
                                                {{
                                                    formatNumber(
                                                        spt.kurang < 1
                                                            ? spt.lebih
                                                            : spt.kurang
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-start">
                                                <Calendar
                                                    :showButtonBar="true"
                                                    inputId="calendarPopup"
                                                    :hideOnDateTimeSelect="true"
                                                    style="width: 100%"
                                                    v-model="spt.payment.date"
                                                    dateFormat="dd-mm-yy"
                                                />
                                            </th>
                                            <th colspan="2">
                                                <InputText
                                                    v-model="spt.payment.ntpt"
                                                    style="width: 100%"
                                                    type="text"
                                                    class="form-control"
                                                    placeholder="Masukan NTPT"
                                                />
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="4" class="text-start">
                                                <textarea
                                                    class="form-control"
                                                    v-model="spt.note"
                                                ></textarea>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-striped table-bordered table-sale"
                                >
                                    <thead>
                                        <tr>
                                            <th colspan="4" class="text-center">
                                                INFORMASI PAJAK LAINNYA
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Informasi</th>
                                            <th>Tipe</th>
                                            <th>PPN Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>Penjualan</th>
                                            <th>PPH23</th>
                                            <th colspan="2">
                                                {{
                                                    formatNumber(
                                                        spt.sales.service
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Retur Penjualan</th>
                                            <th>PPH23</th>
                                            <th colspan="2">
                                                {{
                                                    formatNumber(
                                                        spt.return_sales.service
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-start">
                                                Total Pph 23
                                            </th>
                                            <th colspan="2">
                                                {{ formatNumber(spt.service) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Penjualan</th>
                                            <th>Pph 22</th>
                                            <th colspan="2">
                                                {{ formatNumber(spt.pph.int) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Return Penjualan</th>
                                            <th>Pph 22</th>
                                            <th>
                                                {{ formatNumber(spt.pph.out) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-start">
                                                Total Pph 22
                                            </th>
                                            <th>
                                                {{
                                                    formatNumber(spt.pph.total)
                                                }}
                                            </th>
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

    <div class="col-12 d-flex justify-content-center p-4" v-else>
        <ProgressSpinner />
    </div>
</template>

<script>
import NProgress from "nprogress";
import Swal from "sweetalert2";
var _ = require("lodash");
import { ApiData } from "@/api/server";
export default {
    components: {},
    data() {
        return {
            salaries: [],
            spt: {
                store: {
                    name: "",
                    address: "",
                    email: "",
                    phone: "",
                },
                purchase: {
                    credit: 0,
                    ppn: 0,
                },
                return_purchase: {
                    credit: 0,
                    ppn: 0,
                },
                sales: {
                    credit: 0,
                    service: 0,
                    gunggung: 0,
                    cgunggung: 0,
                    ppn: 0,
                },
                return_sales: {
                    credit: 0,
                    service: 0,

                    ppn: 0,
                },
                start_date: "",
                end_date: "",
                date: {
                    start: "",
                    end: "",
                },
                terutang: {
                    masukan: 0,
                    keluaran: 0,
                },
                pph: {
                    int: 0,
                    out: 0,
                    total: 0,
                },
                service: 0,
                masukan: 0,
                keluaran: 0,
                kurang: 0,
                lebih: 0,
                note: "",
                payment: {
                    date: "",
                    ntpt: "",
                    amount: 0,
                    type: "",
                },
                lebih_bayar: 0,
                kurang_bayar: 0,
            },

            loader: {
                submit: false,
                data: true,
            },
        };
    },
    computed: {},
    created() {},
    methods: {
        async getData() {
            this.loader.data = false;
            try {
                const response = await ApiData.post(`app/taxs/summary`, {
                    start_date: this.spt.date.start,
                    end_date: this.spt.date.end,
                });
                var data = response.data;
                this.spt = data.detail;
                this.loader.data = true;
            } catch (error) {
                console.log(error);
            }
        },

        updateSalaries(index) {
            var detailsalary = this.salaries[index];
            var salary = detailsalary.salary;
            var kasbon = detailsalary.kasbon;
            var commission = detailsalary.commission;
            var totaltunjangan = 0;
            var totalpotongan = 0;
            var totaltax = 0;

            // Tunjangan
            for (var i in detailsalary.tunjangan) {
                var detailtunjangan = detailsalary.tunjangan[i];
                var qtytunjangan = detailtunjangan.hari;
                var amounttunjangan = detailtunjangan.jumlah;
                var subtotaltunjangan = amounttunjangan * qtytunjangan;
                detailtunjangan.total = subtotaltunjangan;
                totaltunjangan += subtotaltunjangan;
            }

            // Potongan Pegawai
            for (var i in detailsalary.potongan) {
                var detailpotongan = detailsalary.potongan[i];
                var qtypotongan = detailpotongan.hari;
                var amountpotongan = detailpotongan.jumlah;
                var subtotalpotongan = amountpotongan * qtypotongan;
                detailpotongan.total = subtotalpotongan;
                totalpotongan += subtotalpotongan;
            }

            detailsalary.total_tunjangan = totaltunjangan;
            detailsalary.total_potongan = totalpotongan;
            detailsalary.subtotal =
                salary + totaltunjangan + commission - (kasbon + totalpotongan);

            totaltax =
                detailsalary.pajak > 0
                    ? (detailsalary.pajak / 100) * salary
                    : 0;
            detailsalary.after_bonus =
                detailsalary.subtotal + detailsalary.bonus - totaltax;

            this.totalCalculate();
        },

        removeSalary(index) {
            this.salaries.splice(index, 1);
            this.totalCalculate();
        },

        totalCalculate() {
            var totalSalary = 0;
            for (var i in this.salaries) {
                var detail = this.salaries[i];
                totalSalary += detail.after_bonus;
            }

            this.summary.total_salary = totalSalary;
        },

        savingAccount() {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Data yang telah di posting tidak dapat di ulang kembali",
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
                    ApiData.post("app/taxs/store", this.spt)
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            window.parent.postMessage({
                                action: "closeActiveMenu",
                                data: "",
                            });
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.loader.submit = false;
                            this.$handleErrorResponse(err);
                        });
                } else {
                    Swal.fire("Aksi di batalkan");
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

        filterDate() {
            var date = this.spt.date;
            if (date != null) {
                this.spt.date = {
                    start:
                        date.start != null ? date.start.substring(0, 10) : "",
                    end: date.end != null ? date.end.substring(0, 10) : "",
                };

                this.getData();
            }
        },
    },
    mounted: function () {},
    watch: {
        "spt.date": function (newDate, oldDate) {
            if (newDate === null) {
                this.spt.date = {
                    start: "",
                    end: "",
                };
                this.getData();
            }
        },
        // salaries: {
        //     handler: function (newVal, oldVal) {
        //         newVal.forEach((item, index) => {
        //             this.updateSalaries(index);
        //         });
        //     },
        //     deep: true,
        //     immediate: true,
        // },
    },
};
</script>
