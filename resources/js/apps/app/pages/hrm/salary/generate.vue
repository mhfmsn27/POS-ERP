<template>
    <div class="col-lg-8 mt-4" v-if="loader.data">
        <div
            class="col-12 mb-4"
            v-for="(item, index) in salaries"
            :key="index + '-detail_salary'"
        >
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between d-block">
                    <div class="h5 mb-0">
                        <div class="ms-sm-2 ms-0 mt-sm-0 mt-2">
                            <div class="h6 fw-semibold mb-0">
                                Slip Gaji Pegawai :
                                <span class="text-primary">{{
                                    item.employee_information.name
                                }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="ms-auto mt-md-0 mt-2">
                        <button
                            class="btn btn-danger"
                            type="button"
                            @click="removeSalary(index)"
                        >
                            Hapus Slip Gaji
                            <i
                                class="bx bx-x-circle ms-1 align-middle d-inline-flex"
                            ></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-between mt-3">
                                <div>
                                    <p class="text-muted mb-2">
                                        Toko / Cabang :
                                    </p>
                                    <p class="fw-bold mb-1">
                                        {{ item.store_information.name }}
                                    </p>
                                    <p class="mb-1 text-muted">
                                        {{ item.store_information.address }}
                                    </p>
                                    <p class="mb-1 text-muted">
                                        {{ item.store_information.email }}
                                    </p>
                                    <p class="text-muted">
                                        {{ item.store_information.phone }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-muted mb-2">
                                        Informasi Pegawai :
                                    </p>
                                    <p class="fw-bold mb-1">
                                        {{ item.employee_information.name }}
                                    </p>
                                    <p class="text-muted mb-1">
                                        {{ item.employee_information.address }}
                                    </p>
                                    <p class="text-muted mb-1">
                                        {{ item.employee_information.email }}
                                    </p>
                                    <p class="text-muted">
                                        {{ item.employee_information.phone }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <p class="fw-semibold text-muted mb-1">
                                Tanggal Gaji :
                            </p>
                            <p class="fs-15 mb-1">
                                {{ $route.params.date }}
                            </p>
                        </div>
                        <div class="col-lg-3">
                            <p class="fw-semibold text-muted mb-1">
                                Absensi Bulan ini :
                            </p>
                            <p class="fs-15 mb-1">
                                {{
                                    formatNumber(
                                        item.employee_information
                                            .absensi_bulanan
                                    )
                                }}
                                <span class="text-muted fs-12">Hari</span>
                            </p>
                        </div>
                        <div class="col-lg-3">
                            <p class="fw-semibold text-muted mb-1">
                                Total Jam Kerja :
                            </p>
                            <p class="fs-15 mb-1">
                                {{ item.employee_information.total_jam_kerja }}
                            </p>
                        </div>
                        <div class="col-lg-3">
                            <p class="fw-semibold text-muted mb-1">
                                Total Keterlambatan :
                            </p>
                            <p class="fs-15 mb-1">
                                {{
                                    item.employee_information
                                        .total_keterlambatan
                                }}
                            </p>
                        </div>

                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-striped table-bordered table-sale"
                                >
                                    <thead>
                                        <tr>
                                            <th>Informasi Umum</th>
                                            <th>Nominal</th>
                                            <th>X</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th colspan="2">Gaji Pokok</th>
                                            <th colspan="2" class="text-end">
                                                <InputNumber
                                                    style="width: 100%"
                                                    v-model="item.salary"
                                                    :minFractionDigits="0"
                                                    :maxFractionDigits="2"
                                                    prefix="Rp "
                                                />
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2">
                                                Pembayaran Kasbon
                                            </th>
                                            <th colspan="2" class="text-end">
                                                <InputNumber
                                                    style="width: 100%"
                                                    v-model="item.kasbon"
                                                    :minFractionDigits="0"
                                                    :maxFractionDigits="2"
                                                    prefix="Rp "
                                                />
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="2">
                                                Komisi Penjualan
                                            </th>
                                            <th colspan="2" class="text-end">
                                                <InputNumber
                                                    style="width: 100%"
                                                    v-model="item.commission"
                                                    :minFractionDigits="0"
                                                    :maxFractionDigits="2"
                                                    prefix="Rp "
                                                />
                                            </th>
                                        </tr>
                                        <tr
                                            class="bg-light"
                                            v-if="item.tunjangan.length > 0"
                                        >
                                            <th colspan="4">
                                                <h6>Tunjangan Pegawai</h6>
                                            </th>
                                        </tr>
                                        <tr
                                            v-for="(
                                                detail_tunjangan, allowanceIndex
                                            ) in item.tunjangan"
                                            :key="allowanceIndex"
                                        >
                                            <td>
                                                {{ detail_tunjangan.name }}
                                            </td>
                                            <td>
                                                <InputNumber
                                                    style="width: 100%"
                                                    v-model="
                                                        detail_tunjangan.jumlah
                                                    "
                                                    :minFractionDigits="0"
                                                    :maxFractionDigits="2"
                                                    prefix="Rp "
                                                />
                                            </td>
                                            <td>
                                                <InputNumber
                                                    style="width: 100%"
                                                    v-model="
                                                        detail_tunjangan.hari
                                                    "
                                                    :minFractionDigits="0"
                                                    :maxFractionDigits="2"
                                                    suffix=" Hari"
                                                />
                                            </td>
                                            <td class="text-end">
                                                Rp
                                                {{
                                                    formatNumber(
                                                        detail_tunjangan.total
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                        <tr
                                            class="bg-light"
                                            v-if="item.tunjangan.length > 0"
                                        >
                                            <th colspan="2">Total Tunjangan</th>
                                            <th colspan="2" class="text-end">
                                                Rp
                                                {{
                                                    formatNumber(
                                                        item.total_tunjangan
                                                    )
                                                }}
                                            </th>
                                        </tr>

                                        <tr
                                            class="bg-light"
                                            v-if="
                                                item.potongan.length > 0 ||
                                                item.potongan_by_late == 'yes'
                                            "
                                        >
                                            <th colspan="4">
                                                <h6>Potongan Gaji Pegawai</h6>
                                            </th>
                                        </tr>
                                        <tr
                                            v-for="(
                                                detail_potongan, allowanceIndex
                                            ) in item.potongan"
                                            :key="
                                                'deductionIndex' +
                                                allowanceIndex
                                            "
                                        >
                                            <td>
                                                {{ detail_potongan.name }}
                                            </td>
                                            <td>
                                                <InputNumber
                                                    style="width: 100%"
                                                    v-model="
                                                        detail_potongan.jumlah
                                                    "
                                                    :minFractionDigits="0"
                                                    :maxFractionDigits="2"
                                                    prefix="Rp "
                                                />
                                            </td>
                                            <td>
                                                <InputNumber
                                                    style="width: 100%"
                                                    v-model="
                                                        detail_potongan.hari
                                                    "
                                                    :minFractionDigits="0"
                                                    :maxFractionDigits="2"
                                                    suffix=" Hari"
                                                />
                                            </td>
                                            <td class="text-end">
                                                Rp
                                                {{
                                                    formatNumber(
                                                        detail_potongan.total
                                                    )
                                                }}
                                            </td>
                                        </tr>

                                        <tr class="bg-light">
                                            <th colspan="2">Total Potongan</th>
                                            <th colspan="2" class="text-end">
                                                Rp
                                                {{
                                                    formatNumber(
                                                        item.total_potongan
                                                    )
                                                }}
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
                                                Subtotal
                                            </th>
                                            <th
                                                scope="row"
                                                colspan="2"
                                                class="text-end"
                                            >
                                                Rp
                                                {{
                                                    formatNumber(item.subtotal)
                                                }}
                                            </th>
                                        </tr>
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
                                                {{ item.pajak }}%
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
                                                <InputNumber
                                                    style="width: 100%"
                                                    v-model="item.bonus"
                                                    :minFractionDigits="0"
                                                    :maxFractionDigits="2"
                                                    prefix="Rp "
                                                />
                                            </th>
                                        </tr>

                                        <tr>
                                            <th
                                                class="text-end"
                                                colspan="2"
                                                scope="row"
                                            >
                                                <h6>Total Gaji Diterima</h6>
                                            </th>
                                            <th class="text-end" colspan="2">
                                                Rp
                                                {{
                                                    formatNumber(
                                                        item.after_bonus
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div>
                                <label for="invoice-note" class="form-label"
                                    >Catatan :</label
                                >
                                <textarea
                                    class="form-control form-control-light"
                                    id="invoice-note"
                                    v-model="item.catatan"
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mt-4" v-if="loader.data">
        <div class="card custom-card">
            <div class="card-header">
                <h5>Ringkasan</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Total Pegawai</th>
                        <th class="text-end">{{ salaries.length }} Pegawai</th>
                    </tr>
                    <tr>
                        <th>Total Gaji Harus Di Bayarkan</th>
                        <th class="text-end">
                            Rp {{ formatNumber(summary.total_salary) }}
                        </th>
                    </tr>
                    <tr class="text-center">
                        <th colspan="2">
                            <button
                                class="btn btn-md btn-primary"
                                type="button"
                                @click="savingSalarySlip()"
                                :disabled="loader.submit"
                            >
                                {{
                                    loader.submit ? "Loading...." : "Slip Gaji"
                                }}
                            </button>
                        </th>
                    </tr>
                </table>
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
            summary: {
                total: 0,
                total_salary: 0,
            },
            loader: {
                submit: false,
                data: false,
            },
        };
    },
    computed: {},
    created() {
        this.getData();
    },
    methods: {
        async getData() {
            try {
                const response = await ApiData.post(
                    `app/hrm/salaries/generate`,
                    {
                        date: this.$route.params.date,
                        department: this.$route.params.department,
                    }
                );
                var data = response.data;
                this.salaries = data.data;
                this.loader.data = true;
                this.totalCalculate();
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

        savingSalarySlip() {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Slip Gaji akan di simpan dan tidak dapat di edit kembali",
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
                    ApiData.post("app/hrm/salaries/create", {
                        store: {
                            id: this.$route.params.store,
                        },
                        date: this.$route.params.date,
                        detail: this.salaries,
                        total: this.summary.total_salary,
                    })
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
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
    },
    mounted: function () {},
    watch: {
        salaries: {
            handler: function (newVal, oldVal) {
                newVal.forEach((item, index) => {
                    this.updateSalaries(index);
                });
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>
