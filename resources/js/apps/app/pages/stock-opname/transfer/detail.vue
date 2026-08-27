<template>
    <div class="col-xl-12 mt-4">
        <div class="card custom-card">
            <div class="card-header d-md-flex d-block">
                <div class="h5 mb-0 d-sm-flex d-bllock align-items-center">
                    <div class="ms-sm-2 ms-0 mt-sm-0 mt-2">
                        <div class="h6 fw-semibold mb-0">Detail Stok Opnme</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-lg-3 col-sm-6">
                        <p class="fw-semibold text-muted mb-1">No Ref :</p>
                        <p class="fs-15 mb-1">#{{ transactions.ref_no }}</p>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <p class="fw-semibold text-muted mb-1">
                            Tanggal Transaksi :
                        </p>
                        <p class="fs-15 mb-1">
                            {{ transactions.date }}
                        </p>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <p class="fw-semibold text-muted mb-1">Dari Gudang :</p>
                        <p class="fs-16 mb-1 fw-semibold">
                            {{ transactions.from.name }}
                        </p>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <p class="fw-semibold text-muted mb-1">
                            Tujuan Gudang :
                        </p>
                        <p class="fs-16 mb-1 fw-semibold">
                            {{ transactions.to.name }}
                        </p>
                    </div>
                    <div class="col-xl-12">
                        <div class="table-responsive">
                            <table class="table nowrap text-nowrap border mt-4">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Stok Awal</th>
                                        <th>Stok Di Transfer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            transfer_detail, index
                                        ) in transactions.items"
                                        :key="index"
                                    >
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div class="flex-1">
                                                    <h6 class="m-0">
                                                        {{
                                                            transfer_detail.name
                                                        }}
                                                        {{
                                                            transfer_detail
                                                                .variation
                                                                .name ==
                                                            "no-name"
                                                                ? ""
                                                                : transfer_detail
                                                                      .variation
                                                                      .name
                                                        }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{
                                                transfer_detail.stock_in_system
                                            }}
                                        </td>
                                        <td>
                                            {{ transfer_detail.actual_stock }}
                                        </td>
                                    </tr>
                                </tbody>
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
                                id="invoice-note"
                                disabled
                                rows="3"
                                >{{ transactions.note }}</textarea
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
var _ = require("lodash");
import { ApiData } from "@/api/server";
export default {
    name: "stock_opname_list",
    components: {},
    data() {
        return {
            transactions: {
                ref_no: "",
                from: {
                    name: "",
                },
                to: {
                    name: "",
                },
                store: {
                    id: "",
                    name: "",
                },
                created: {
                    id: "",
                    name: "",
                },
                created_at: "",
                time: "",
                date: "",
                note: "",
                items: [],
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
                const response = await ApiData.get(
                    `app/transactions/transfer-warehouse/detail/${this.$route.params.id}`
                );
                var data = response.data;
                this.transactions = data.details;
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
    },
    mounted: function () {},
};
</script>
