<template>
    <div class="col-lg-12 mt-4" v-if="loader.data">
        <div class="card custom-card">
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-lg-3">
                        <p class="fw-semibold text-muted mb-1">Priode :</p>
                        <p class="fs-15 mb-1">
                            {{ spt.start_date }}
                            - {{ spt.end_date }}
                        </p>
                    </div>
                    <div class="col-lg-3">
                        <p class="fw-semibold text-muted mb-1">
                            Tanggal Pembayaran :
                        </p>
                        <p class="fs-16 mb-1 fw-semibold">
                            {{ spt.payment_date }}
                        </p>
                    </div>
                    <div class="col-lg-3">
                        <p class="fw-semibold text-muted mb-1">NTPT :</p>
                        <p class="fs-15 mb-1">
                            {{ spt.ntpt }}
                        </p>
                    </div>

                    <div class="col-lg-3">
                        <p class="fw-semibold text-muted mb-1">Tipe :</p>
                        <p class="fs-16 mb-1 fw-semibold">
                            {{
                                spt.type == "lebih"
                                    ? "Lebih Bayar"
                                    : "Kurang Bayar"
                            }}
                        </p>
                    </div>

                    <div class="col-xl-12 mt-3">
                        <div class="table-responsive">
                            <table
                                class="table table-striped table-bordered table-sale"
                            >
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Di Kreditkan</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(detail, index) in spt.items"
                                        :key="index"
                                    >
                                        <td>
                                            {{ detail.name }}
                                        </td>
                                        <td>
                                            Rp {{ formatNumber(detail.credit) }}
                                        </td>
                                        <td>
                                            Rp {{ formatNumber(detail.amount) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th
                                            scope="row"
                                            colspan="2"
                                            class="text-end"
                                        >
                                            Total
                                        </th>
                                        <th scope="row">
                                            Rp
                                            {{ formatNumber(spt.amount) }}
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
                                >{{ spt.note }}</textarea
                            >
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
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    components: {},
    data() {
        return {
            salaries: [],
            spt: {
                start_date: "",
                end_date: "",
                ntpt: "",
                payment_date: "",
                amount: 0,
                type: "",
                note: "",
                items: [],
            },
            loader: {
                submit: false,
                data: true,
            },
        };
    },
    computed: {},
    created() {
        this.getData();
    },
    methods: {
        async getData() {
            this.loader.data = false;
            try {
                const response = await ApiData.get(
                    `app/taxs/spt/detail/${this.$route.params.id}`
                );
                var data = response.data;
                this.spt = data.details;
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
};
</script>
