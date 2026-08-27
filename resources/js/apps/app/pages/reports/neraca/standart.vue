<template>
    <div class="col-lg-4 mt-4" v-if="loader.data">
        <div class="card custom-card">
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th colspan="2">
                            <label class="form-label">Filter Tanggal</label>
                            <div class="input-group">
                                <Calendar
                                    v-model="date.start"
                                    style="width: 100%"
                                />
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
                                    loader.submit ? "Loading...." : "Ambil Data"
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
                            <div class="neracahead">
                                <header>
                                    <h1>SAC</h1>
                                    <h2>Neraca (Standar)</h2>
                                    <p>{{date.start.toString().substring(0,15)}}</p>
                                    <p>Mata Uang: Indonesian Rupiah</p>
                                </header>
                                <section class="content">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Deskripsi</th>
                                                <th>Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="2">
                                                    <strong>ASET</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <strong>ASET LANCAR</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Kas dan Setara Kas</td>
                                                <td class="right-align">
                                                  
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&emsp;Kas & Bank</td>
                                                <td> {{ reports.asset_lancar.bank.total }} </td>
                                            </tr>
                                            <tr
                                                v-for="(bank, b) in reports.asset_lancar.bank.items"
                                                :key="b + 'bankcash'"
                                            >
                                                <td>&emsp;&emsp;{{bank.name}}</td>
                                                <td class="right-align">
                                                   {{bank.amount}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>&emsp;Setara Kas</td>
                                                <td> {{ reports.asset_lancar.setara_kas.total }} </td>
                                            </tr>
                                            <tr
                                                v-for="(setara, s) in reports.asset_lancar.setara_kas.items"
                                                :key="s + 'setaracash'"
                                            >
                                                <td>&emsp;&emsp;{{setara.name}}</td>
                                                <td class="right-align">
                                                   {{setara.amount}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>
                                                    Jumlah Kas dan Setara Kas
                                                </td>
                                                <td class="right-align">
                                                    {{reports.asset_lancar.jumlah_kas}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Piutang Usaha</td>
                                                <td class="right-align">
                                                  
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&emsp;Piutang Usaha</td>
                                                <td> {{ reports.asset_lancar.piutang.total }} </td>
                                            </tr>
                                            <tr
                                                v-for="(piutang, p) in reports.asset_lancar.piutang.items"
                                                :key="p + 'piutang'"
                                            >
                                                <td>&emsp;&emsp;{{piutang.name}}</td>
                                                <td class="right-align">
                                                   {{piutang.amount}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>&emsp;Jumlah Piutang Usaha</td>
                                                <td> {{ reports.asset_lancar.piutang.total }} </td>
                                            </tr>

                                            <!-- Persediaan -->
                                            <tr>
                                                <td>Persediaan</td>
                                                <td class="right-align">
                                                  
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&emsp;Persediaan</td>
                                                <td> {{ reports.asset_lancar.persediaan.total }} </td>
                                            </tr>
                                            <tr
                                                v-for="(persediaan, p) in reports.asset_lancar.persediaan.items"
                                                :key="p + 'piutang'"
                                            >
                                                <td>&emsp;&emsp;{{persediaan.name}}</td>
                                                <td class="right-align">
                                                   {{persediaan.amount}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>&emsp;Jumlah Persediaan</td>
                                                <td> {{ reports.asset_lancar.persediaan.total }} </td>
                                            </tr>
                                            <!-- End Persediaan -->

                                            <!-- Asset Lancar Lainnya -->
                                            <tr>
                                                <td>Asset Lancar Lainnya</td>
                                                <td class="right-align">
                                                  
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&emsp;Asset Lancar Lainnya</td>
                                                <td> {{ reports.asset_lancar.asset_lainnya.total }} </td>
                                            </tr>
                                            <tr
                                                v-for="(asset, a) in reports.asset_lancar.asset_lainnya.items"
                                                :key="a + 'asset'"
                                            >
                                                <td>&emsp;&emsp;{{asset.name}}</td>
                                                <td class="right-align">
                                                   {{asset.amount}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>&emsp;Jumlah Asset Lainnya</td>
                                                <td> {{ reports.asset_lancar.asset_lainnya.total }} </td>
                                            </tr>
                                            <!-- End Asset Lancar Lainnya -->

                                            <tr>
                                                <td>Jumlah Asset Lancar</td>
                                                <td class="right-align">
                                                  {{ reports.asset_lancar.jumlah }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2">
                                                    <strong>ASET TIDAK LANCAR</strong>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Nilai Histori</td>
                                                <td class="right-align">
                                                  
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&emsp;Asset Tetap</td>
                                                <td> {{ reports.tidak_lancar.tetap.total }} </td>
                                            </tr>
                                            <tr
                                                v-for="(tetap, t) in reports.tidak_lancar.tetap.items"
                                                :key="t + 'tetap'"
                                            >
                                                <td>&emsp;&emsp;{{tetap.name}}</td>
                                                <td class="right-align">
                                                   {{tetap.amount}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>&emsp;Jumlah Asset Tetap</td>
                                                <td> {{ reports.tidak_lancar.tetap.total }} </td>
                                            </tr>

                                            <!-- Penyusutan -->
                                            <tr>
                                                <td>Akumulasi Penyusutan</td>
                                                <td class="right-align">
                                                  
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&emsp;Akumulasi Depresiasi Aset Tetap</td>
                                                <td> {{ reports.tidak_lancar.penyusutan.total }} </td>
                                            </tr>
                                            <tr
                                                v-for="(penyusutan, p) in reports.tidak_lancar.penyusutan.items"
                                                :key="p + 'penyusutan'"
                                            >
                                                <td>&emsp;&emsp;{{penyusutan.name}}</td>
                                                <td class="right-align">
                                                   {{penyusutan.amount}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>&emsp;Jumlah Akumulasi Penyusutan</td>
                                                <td> {{ reports.tidak_lancar.penyusutan.total }} </td>
                                            </tr>

                                            <tr>
                                                <td>Jumlah Asset Tidak Lancar</td>
                                                <td class="right-align">
                                                  {{ reports.tidak_lancar.jumlah }}
                                                </td>
                                            </tr>
                                            <!-- End Penyusutan -->

                                            <tr>
                                                <td>Jumlah Asset</td>
                                                <td class="right-align">
                                                  {{ reports.jumlah }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2">
                                                    <strong>LIABILITAS dan EKUITAS</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <strong>Liabilitas</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Liabilitas Jangka Pendek</td>
                                                <td class="right-align">
                                                  
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&emsp;Hutang Usaha</td>
                                                <td> {{ reports.liabilitas.hutang.total }} </td>
                                            </tr>
                                            <tr
                                                v-for="(hutang, h) in reports.liabilitas.hutang.items"
                                                :key="h + 'hutang'"
                                            >
                                                <td>&emsp;&emsp;{{hutang.name}}</td>
                                                <td class="right-align">
                                                   {{hutang.amount}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>&emsp;Jumlah Utang</td>
                                                <td> {{ reports.liabilitas.hutang.total }} </td>
                                            </tr>

                                            
                                            <tr>
                                                <td>Kewajiban Jangka Pendek Lainnya</td>
                                                <td class="right-align">
                                                  
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&emsp;Kewajiban jangka pendek Lainnya</td>
                                                <td> {{ reports.liabilitas.lainnya.total }} </td>
                                            </tr>
                                            <tr
                                                v-for="(lainnya, l) in reports.liabilitas.lainnya.items"
                                                :key="l + 'hutang'"
                                            >
                                                <td>&emsp;&emsp;{{lainnya.name}}</td>
                                                <td class="right-align">
                                                   {{lainnya.amount}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>&emsp;Jumlah Kewajiban Jangka Pendek Lainnya</td>
                                                <td> {{ reports.liabilitas.lainnya.total }} </td>
                                            </tr>
                                            <tr>
                                                <td>Jumlah Kewajiban Jangka Pendek</td>
                                                <td> {{ reports.liabilitas.jumlah }} </td>
                                            </tr>

                                            <!-- Liabilitas jangka panjang -->
                                            <tr>
                                                <td>Kewajiban Jangka Panjang</td>
                                                <td class="right-align">
                                                  
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&emsp;Kewajiban jangka Panjang</td>
                                                <td> {{ reports.liabilitas_panjang.total }} </td>
                                            </tr>
                                            <tr
                                                v-for="(liabilitas, l) in reports.liabilitas_panjang.items"
                                                :key="l + 'hutang'"
                                            >
                                                <td>&emsp;&emsp;{{liabilitas.name}}</td>
                                                <td class="right-align">
                                                   {{liabilitas.amount}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>&emsp;Jumlah Kewajiban Jangka Panjang</td>
                                                <td> {{ reports.liabilitas_panjang.total }} </td>
                                            </tr>
                                            <tr>
                                                <td>Jumlah Kewajiban</td>
                                                <td> {{ reports.jumlah_liabilitas }} </td>
                                            </tr>
                                            <!-- End Liabilitas jangka panjang -->

                                            <!-- Equitas dan modal -->
                                            <tr>
                                                <td>Ekuitas</td>
                                                <td class="right-align">
                                                  
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>&emsp;Modal</td>
                                                <td> {{ reports.ekuitas.modal.total }} </td>
                                            </tr>
                                            <tr
                                                v-for="(modal, m) in reports.ekuitas.modal.items"
                                                :key="m + 'modal'"
                                            >
                                                <td>&emsp;&emsp;{{modal.name}}</td>
                                                <td class="right-align">
                                                   {{modal.amount}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td>&emsp;Laba Tahun ini</td>
                                                <td> {{ reports.ekuitas.laba_tahunan }} </td>
                                            </tr>
                                            <tr>
                                                <td>&emsp;Jumlah Ekuitas</td>
                                                <td> {{ reports.ekuitas.jumlah }} </td>
                                            </tr>
                                            <tr>
                                                <td>JUMLAH LIABILITAS DAN EKUITAS</td>
                                                <td> {{ reports.liabilitas_dan_ekuitas }} </td>
                                            </tr>
                                            <!-- End Equitas dan modal -->
                                            <!-- Add the rest of the items similarly -->
                                        </tbody>
                                    </table>
                                </section>
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
            reports: {
                asset_lancar: {
                    bank: {
                        total: 0,
                        items: [],
                    },
                    setara_kas: {
                        total: 0,
                        items: [],
                    },
                    jumlah_kas: 0,
                    piutang: {
                        total: 0,
                        items: [],
                    },
                    persediaan: {
                        total: 0,
                        items: [],
                    },
                    asset_lainnya: {
                        total: [],
                        items: 0,
                    },
                    jumlah: 0,
                },
                tidak_lancar: {
                    tetap: {
                        items: [],
                        total: 0,
                    },
                    penyusutan: {
                        items: [],
                        total: 0,
                    },
                    jumlah: 0,
                },
                jumlah: 0,
                liabilitas: {
                    hutang: {
                        items: [],
                        total: 0,
                    },
                    lainnya: {
                        items: [],
                        total: 0,
                    },
                    jumlah: 0,
                },
                liabilitas_panjang: {
                    total: 0,
                    items: [],
                },
                jumlah_liabilitas: 0,
                ekuitas: {
                    modal: {
                        items: [],
                        total: 0,
                    },
                    laba_tahunan: 0,
                    jumlah: 0,
                },
                liabilitas_dan_ekuitas: 0,
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
                const response = await ApiData.post(
                    `app/reports/neraca/standart`,
                    {
                        start_date: this.date.start,
                    }
                );
                var data = response.data;
                this.reports = data.asset; 
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
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}
 
header {
    text-align: center;
    margin-bottom: 20px;
}

header h1 {
    margin: 0;
    font-size: 24px;
}

header h2 {
    margin: 0;
    font-size: 20px;
    color: #b71c1c;
}

header p {
    margin: 0;
    font-size: 16px;
}

.content {
    padding: 15px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table,
th,
td {
    border: 1px solid #ddd;
}

th,
td {
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f4f4f4;
    font-weight: bold;
}

.right-align {
    text-align: right;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}
</style>
