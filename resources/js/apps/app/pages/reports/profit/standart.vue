<template>
    <div class="col-lg-4 mt-4" v-if="loader.data">
        <div class="card custom-card">
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th colspan="2">
                            <label class="form-label">Filter Tanggal</label>
                            <div class="input-group">
                                <VueCtkDateTimePicker
                                    label="Filter Tanggal"
                                    locale="Asia/Jakarta"
                                    class="form-control"
                                    v-model="date"
                                    @validate="filterDate"
                                    :range="true"
                                />
                            </div>
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
                            <div class="table-responsive">
                                <table
                                    class="table table-striped table-bordered table-sale"
                                >
                                    <tr>
                                        <th>Deskripsi</th>
                                        <th>Nominal</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-center">
                                            Pendapatan
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Pendapatan</th>
                                        <th>
                                            {{
                                                reports.pendapatan.pendapatan
                                                    .pendapatan
                                            }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>Penjualan</td>
                                        <td>
                                            {{
                                                reports.pendapatan.pendapatan
                                                    .penjualan
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pendapatan Jasa</td>
                                        <td>
                                            {{
                                                reports.pendapatan.pendapatan
                                                    .jasa
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Retur Penjualan</td>
                                        <td>
                                            {{
                                                reports.pendapatan.pendapatan
                                                    .return_penjualan
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Diskon Penjualan</td>
                                        <td>
                                            {{
                                                reports.pendapatan.pendapatan
                                                    .diskon_penjualan
                                            }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Jumlah Pendapatan</th>
                                        <th>
                                            {{ reports.jumlah_pendapatan }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-center">
                                            Harga Pokok Penjualan
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>Cogs</td>
                                        <td>
                                            {{ reports.harga_pokok.cogs }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Jumlah Harga Pokok Penjualan</th>
                                        <th>
                                            {{
                                                reports.jumlah_harga_pokok_penjualan
                                            }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Laba Kotor</th>
                                        <th>
                                            {{ reports.laba_kotor }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-center">
                                            Beban Operasional
                                        </th>
                                    </tr>
                                    <tr
                                        v-for="(
                                            beban, b
                                        ) in reports.beban_operasional"
                                        :key="b"
                                    >
                                        <td>{{ beban.name }}</td>
                                        <td>
                                            {{ beban.amount }}
                                        </td>
                                    </tr>
                                    <tr v-if="!with_accountant">
                                        <th>Kasbon Pegawai</th>
                                        <th>
                                            {{ reports.kasbon }}
                                        </th>
                                    </tr>
                                    <tr v-if="!with_accountant">
                                        <th>Gaji Pegawai</th>
                                        <th>
                                            {{ reports.salary }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Beban Operasional</th>
                                        <th>
                                            {{ reports.jumlah_beban }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Pendapatan Operasional</th>
                                        <th>
                                            {{ reports.pendapatan_operasi }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-center">
                                            Pendapatan & Beban Lain
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-center">
                                            Pendapatan Lainnya
                                        </th>
                                    </tr>
                                    <tr
                                        v-for="(pendapatan, p) in reports
                                            .pendapatan_dan_beban_lainnya
                                            .pendapatan_lainnya"
                                        :key="p"
                                    >
                                        <td>{{ pendapatan.name }}</td>
                                        <td>
                                            {{ pendapatan.amount }}
                                        </td>
                                    </tr>
                                    <tr v-if="!with_accountant">
                                        <th>Kas Masuk</th>
                                        <th>
                                            {{
                                                reports
                                                    .pendapatan_dan_beban_lainnya
                                                    .cash_int
                                            }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Pendapatan</th>
                                        <th>
                                            {{
                                                reports
                                                    .pendapatan_dan_beban_lainnya
                                                    .jumlah_pendapatan
                                            }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-center">
                                            Beban Lainnya
                                        </th>
                                    </tr>
                                    <tr
                                        v-for="(blain, bl) in reports
                                            .pendapatan_dan_beban_lainnya
                                            .beban_lainnya"
                                        :key="bl"
                                    >
                                        <td>{{ blain.name }}</td>
                                        <td>
                                            {{ blain.amount }}
                                        </td>
                                    </tr>
                                    <tr v-if="!with_accountant">
                                        <th>Kas Keluar ( Pengeluaran )</th>
                                        <th>
                                            {{
                                                reports
                                                    .pendapatan_dan_beban_lainnya
                                                    .cash_out
                                            }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Beban</th>
                                        <th>
                                            {{
                                                reports
                                                    .pendapatan_dan_beban_lainnya
                                                    .jumlah_beban
                                            }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Laba Rugi Sebelum Pajak</th>
                                        <th>
                                            {{ reports.laba_rugi_before_tax }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Laba Rugi Setelah Pajak</th>
                                        <th>
                                            {{ reports.laba_rugi_after_tax }}
                                        </th>
                                    </tr>
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
var _ = require("lodash");
import { ApiData } from "@/api/server";
export default {
    components: {},
    data() {
        return {
            salaries: [],
            date: {
                start: "",
                end: "",
            },
            with_accountant: true,
            reports: {
                store: {
                    name: "",
                    address: "",
                    email: "",
                    phone: "",
                },
                pendapatan: {
                    pendapatan: {
                        pendapatan: 0,
                        penjualan: 0,
                        return_penjualan: 0,
                        jasa: 0,
                        diskon_penjualan: 0,
                    },
                    pendapatan_tetap: 0,
                },
                jumlah_pendapatan: 0,
                harga_pokok: {
                    cogs: 0,
                    cogs_tetap: 0,
                },
                jumlah_harga_pokok_penjualan: 0,
                laba_kotor: 0,
                beban_operasional: [],
                salary: 0,
                kasbon: 0,
                jumlah_beban: 0,
                pendapatan_operasi: 0,
                pendapatan_dan_beban_lainnya: {
                    pendapatan_lainnya: [],
                    cash_int: 0,
                    cash_out: 0,
                    jumlah_pendapatan: 0,
                    beban_lainnya: [],
                    jumlah_beban: 0,
                },
                laba_rugi_before_tax: 0,
                laba_rugi_after_tax: 0,
            },

            loader: {
                submit: false,
                data: true,
            },
        };
    },
    computed: {},
    created() {
        this.settup();
    },
    methods: {
        async settup() {
            try {
                const response = await ApiData.get(`app/master/tax/sett`);
                var data = response.data;

                if (data.accountant_use == "no") {
                    this.with_accountant = false;
                }
            } catch (error) {
                console.log(error);
            }
        },

        async getData() {
            this.loader.data = false;
            var url = this.with_accountant
                ? "app/reports/profits/standart"
                : "app/reports/profits/non-standart";
            try {
                const response = await ApiData.post(url, {
                    start_date: this.date.start,
                    end_date: this.date.end,
                });
                var data = response.data;
                this.reports = data;
                this.loader.data = true;
            } catch (error) {
                console.log(error);
            }
        },

        formatNumber(number) {
            if (parseFloat(number) >= 0) {
                return number.toLocaleString();
            } else {
                return "-" + (-number).toLocaleString();
            }
        },

        filterDate() {
            var date = this.date;
            if (date != null) {
                this.date = {
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
        date: function (newDate, oldDate) {
            if (newDate === null) {
                this.date = {
                    start: "",
                    end: "",
                };
                this.getData();
            }
        },
    },
};
</script>
