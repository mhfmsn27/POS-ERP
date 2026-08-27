<template>
    <div>
        <div class="col-lg-4 mt-4" v-if="loader.data">
            <div class="card custom-card">
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th colspan="2">
                                <label class="form-label">Pilih Tahun</label>
                                <div class="input-group">
                                    <Calendar
                                        :hideOnDateTimeSelect="true"
                                        style="width: 100%"
                                        v-model="date.year"
                                        dateFormat="yy"
                                    />
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <label class="form-label">Pilih Bulan</label>
                                <div class="input-group">
                                    <Multiselect
                                        v-model="date.month"
                                        :options="months"
                                        :multiple="true"
                                        :close-on-select="false"
                                        :clear-on-select="true"
                                        :preserve-search="true"
                                        :searchable="true"
                                        :internal-search="true"
                                        :options-limit="50"
                                        placeholder="Pilih Bulan"
                                        open-direction="bottom"
                                        label="name"
                                        id="id"
                                        track-by="name"
                                        :allowEmpty="false"
                                        tagPlaceholder=""
                                        selectLabel=""
                                    ></Multiselect>
                                </div>
                            </th>
                        </tr>
                        <tr class="text-center">
                            <th colspan="2">
                                <button
                                    class="btn btn-md btn-primary"
                                    type="button"
                                    @click="getData()"
                                    :disabled="loader.submit"
                                >
                                    {{
                                        loader.submit
                                            ? "Loading...."
                                            : "Ambil Data"
                                    }}
                                </button>
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-8 mt-4" v-if="loader.data">
            <div
                class="col-12 mb-4"
                v-for="(item, i) in listdata"
                :key="i + '-detail_salary'"
            >
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-lg-12">
                                <p class="text-muted mb-2">
                                    Tahun & Bulan : {{ item.year }}
                                    {{ item.month }}
                                </p>
                            </div>

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
                                                    formatNumber(
                                                        item.pendapatan
                                                            .pendapatan
                                                            .pendapatan
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <td>Penjualan</td>
                                            <td>
                                                {{
                                                    formatNumber(
                                                        item.pendapatan
                                                            .pendapatan
                                                            .penjualan
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Pendapatan Jasa</td>
                                            <td>
                                                {{
                                                    formatNumber(
                                                        item.pendapatan
                                                            .pendapatan.jasa
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Retur Penjualan</td>
                                            <td>
                                                {{
                                                    formatNumber(
                                                        item.pendapatan
                                                            .pendapatan
                                                            .return_penjualan
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Diskon Penjualan</td>
                                            <td>
                                                {{
                                                    formatNumber(
                                                        item.pendapatan
                                                            .pendapatan
                                                            .diskon_penjualan
                                                    )
                                                }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Jumlah Pendapatan</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        item.jumlah_pendapatan
                                                    )
                                                }}
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
                                                {{
                                                    formatNumber(
                                                        item.harga_pokok.cogs
                                                    )
                                                }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>
                                                Jumlah Harga Pokok Penjualan
                                            </th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        item.jumlah_harga_pokok_penjualan
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Laba Kotor</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        item.laba_kotor
                                                    )
                                                }}
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
                                            ) in item.beban_operasional"
                                            :key="b"
                                        >
                                            <td>{{ beban.name }}</td>
                                            <td>
                                                {{ formatNumber(beban.amount) }}
                                            </td>
                                        </tr>
                                        <tr v-if="!with_accountant">
                                            <th>Gaji Pegawai</th>
                                            <th>
                                                {{ formatNumber(item.salary) }}
                                            </th>
                                        </tr>
                                        <tr v-if="!with_accountant">
                                            <th>Kasbon Pegawai</th>
                                            <th>
                                                {{ formatNumber(item.kasbon) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Beban Operasional</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        item.jumlah_beban
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Pendapatan Operasional</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        item.pendapatan_operasi
                                                    )
                                                }}
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
                                            v-for="(pendapatan, p) in item
                                                .pendapatan_dan_beban_lainnya
                                                .pendapatan_lainnya"
                                            :key="p"
                                        >
                                            <td>{{ pendapatan.name }}</td>
                                            <td>
                                                {{
                                                    formatNumber(
                                                        pendapatan.amount
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                        <tr v-if="!with_accountant">
                                            <th>Kas Masuk</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        item
                                                            .pendapatan_dan_beban_lainnya
                                                            .cash_int
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Pendapatan</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        item
                                                            .pendapatan_dan_beban_lainnya
                                                            .jumlah_pendapatan
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-center">
                                                Beban Lainnya
                                            </th>
                                        </tr>
                                        <tr
                                            v-for="(blain, bl) in item
                                                .pendapatan_dan_beban_lainnya
                                                .beban_lainnya"
                                            :key="bl"
                                        >
                                            <td>{{ blain.name }}</td>
                                            <td>
                                                {{ formatNumber(blain.amount) }}
                                            </td>
                                        </tr>
                                        <tr v-if="!with_accountant">
                                            <th>Kas Keluar ( Pengeluaran )</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        item
                                                            .pendapatan_dan_beban_lainnya
                                                            .cash_out
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Beban</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        item
                                                            .pendapatan_dan_beban_lainnya
                                                            .jumlah_beban
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Laba Rugi Sebelum Pajak</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        item.laba_rugi_before_tax
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Laba Rugi Setelah Pajak</th>
                                            <th>
                                                {{
                                                    formatNumber(
                                                        item.laba_rugi_after_tax
                                                    )
                                                }}
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
            with_accountant: true,
            months: [
                {
                    name: "Januari",
                    value: "01",
                },
                {
                    name: "Februari",
                    value: "02",
                },
                {
                    name: "Maret",
                    value: "03",
                },
                {
                    name: "April",
                    value: "04",
                },
                {
                    name: "Mei",
                    value: "05",
                },
                {
                    name: "Juni",
                    value: "06",
                },
                {
                    name: "Juli",
                    value: "07",
                },
                {
                    name: "Agustus",
                    value: "08",
                },
                {
                    name: "September",
                    value: "09",
                },
                {
                    name: "Oktober",
                    value: "10",
                },
                {
                    name: "November",
                    value: "11",
                },
                {
                    name: "Desember",
                    value: "12",
                },
            ],
            listdata: [],
            date: {
                year: "",
                month: [],
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
                ? "app/reports/profits/priode"
                : "app/reports/profits/non-priode";
            try {
                const response = await ApiData.post(url, {
                    year: this.date.year,
                    month: this.date.month,
                });
                var data = response.data;
                this.listdata = data.list;
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
    },
    mounted: function () {},
    watch: {},
};
</script>
