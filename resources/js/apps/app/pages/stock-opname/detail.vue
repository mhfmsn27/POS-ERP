<template>
    <div class="col-xl-12">
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
                        <p class="fw-semibold text-muted mb-1">
                            Tanggal Di Buat :
                        </p>
                        <p class="fs-15 mb-1">
                            {{ transactions.created_at }}
                            {{ transactions.time }}
                        </p>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <p class="fw-semibold text-muted mb-1">
                            Toko / Cabang :
                        </p>
                        <p class="fs-16 mb-1 fw-semibold">
                            {{ transactions.store.name }}
                        </p>
                    </div>
                    <div class="col-xl-12">
                        <div class="table-responsive">
                            <table class="table nowrap text-nowrap border mt-4">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Harga Modal Satuan</th>
                                        <th>Stok Di Sistem</th>
                                        <th>Aktual</th>
                                        <th>Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            detail_adjustment, index
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
                                                            detail_adjustment.name
                                                        }}
                                                        {{
                                                            detail_adjustment
                                                                .variation
                                                                .name ==
                                                            "no-name"
                                                                ? ""
                                                                : detail_adjustment
                                                                      .variation
                                                                      .name
                                                        }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div class="flex-1">
                                                    <h6 class="m-0">
                                                        {{
                                                            formatNumber(
                                                                detail_adjustment.price
                                                            )
                                                        }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{
                                                detail_adjustment.stock_in_system
                                            }}
                                        </td>
                                        <td>
                                            {{ detail_adjustment.actual_stock }}
                                        </td>
                                        <td>
                                            <h6>
                                                {{ detail_adjustment.qty }}
                                                {{
                                                    detail_adjustment.type_adjustment ==
                                                    "min"
                                                        ? "Dikurangi"
                                                        : "Ditambahkan"
                                                }}
                                            </h6>
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
                    `app/transactions/stock-opname/detail/${this.$route.params.id}`
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
