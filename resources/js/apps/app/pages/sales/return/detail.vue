<template>
    <div class="col-12" v-if="!loader.data">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between d-block">
                <div class="h5 mb-0 d-sm-flex d-bllock align-items-center">
                    <div class="ms-sm-2 ms-0 mt-sm-0 mt-2">
                        <div class="h6 fw-semibold mb-0">
                            NO REFERENSI :
                            <span class="text-primary"
                                >#{{ transaction.ref_no }}</span
                            >
                        </div>
                    </div>
                </div>
                <div class="ms-auto mt-md-0 mt-2">
                    <button class="btn btn-secondary me-1">
                        Print<i
                            class="fe fe-printer ms-1 align-middle d-inline-flex"
                        ></i>
                    </button>
                    <button class="btn btn-info me-1">
                        Export ke PDF<i
                            class="ri-file-pdf-line ms-1 align-middle d-inline-flex"
                        ></i>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-lg-12">
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
                                <p class="text-muted mb-2">customer :</p>
                                <p class="fw-bold mb-1">
                                    {{ transaction.customer.name }}
                                </p>
                                <p class="text-muted mb-1">
                                    {{ transaction.customer.country }}
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
                    <div class="col-lg-3 col-sm-6">
                        <p class="fw-semibold text-muted mb-1">
                            No Ref Pembelian :
                        </p>
                        <a 
                            class="fs-15 mb-1"
                            href="javascript:void(0)"
                            @click="
                                $goTo({
                                    name: 'sales_detail',
                                    params: { id: transaction.transaction.id },
                                })
                            "
                        >
                            #{{ transaction.transaction.ref_no }}
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <p class="fw-semibold text-muted mb-1">
                            Tanggal Di Buat :
                        </p>
                        <p class="fs-15 mb-1">
                            {{ transaction.created_date.date }} -
                            <span class="text-muted fs-12">{{
                                transaction.created_date.time
                            }}</span>
                        </p>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <p class="fw-semibold text-muted mb-1">
                            Tanggal Transaksi :
                        </p>
                        <p class="fs-15 mb-1">{{ transaction.date }}</p>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <p class="fw-semibold text-muted mb-1">Dibuat Oleh :</p>
                        <p class="fs-16 mb-1 fw-semibold">
                            {{ transaction.created.name }}
                        </p>
                    </div>

                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Quantity</th>
                                        <th>Harga Pengembalian</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            detail, index
                                        ) in transaction.items"
                                        :key="index"
                                    >
                                        <td>
                                            <p
                                                style="
                                                    font-size: 14px;
                                                    font-weight: 500;
                                                "
                                                class="mb-0"
                                            >
                                                {{ detail.product.name }}
                                                {{
                                                    detail.variation.name ==
                                                    "no-name"
                                                        ? ""
                                                        : detail.variation.name
                                                }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="fs-15 mb-1">
                                                {{
                                                    formatNumber(
                                                        detail.unit.qty
                                                    )
                                                }}
                                                {{ detail.unit.name }}

                                                <span
                                                    class="text-muted fs-12"
                                                    v-if="
                                                        detail.unit.id !=
                                                        detail.first_unit.id
                                                    "
                                                >
                                                    {{ detail.qty }}
                                                    {{ detail.first_unit.name }}
                                                </span>
                                            </p>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div class="flex-1">
                                                    Rp
                                                    {{
                                                        formatNumber(
                                                            detail.price
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            Rp
                                            {{ formatNumber(detail.subtotal) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th
                                            scope="row"
                                            colspan="3"
                                            class="text-right"
                                        >
                                            Subtotal Pengembalian
                                        </th>
                                        <th scope="row">
                                            Rp
                                            {{
                                                formatNumber(
                                                    transaction.subtotal
                                                )
                                            }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th
                                            colspan="3"
                                            class="text-right"
                                            scope="row"
                                        >
                                            Diskon
                                        </th>
                                        <th>
                                            -
                                            {{
                                                transaction.discount.type !=
                                                "percent"
                                                    ? "Rp"
                                                    : ""
                                            }}
                                            {{
                                                formatNumber(
                                                    transaction.discount.amount
                                                )
                                            }}
                                            {{
                                                transaction.discount.type !=
                                                "percent"
                                                    ? ""
                                                    : "%"
                                            }}
                                        </th>
                                    </tr>

                                    <tr>
                                        <th
                                            colspan="3"
                                            class="text-right"
                                            scope="row"
                                        >
                                            Pajak Pengembalian
                                        </th>
                                        <th>
                                            Rp
                                            {{
                                                formatNumber(
                                                    transaction.tax_amount
                                                )
                                            }}
                                            ( {{ transaction.tax_percent }} % )
                                        </th>
                                    </tr>

                                    <tr class="bg-light">
                                        <th colspan="3" class="text-right">
                                            Total Tagihan
                                        </th>
                                        <th>
                                            Rp
                                            {{
                                                formatNumber(
                                                    transaction.final_total
                                                )
                                            }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
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
import Swal from "sweetalert2";
import NProgress from "nprogress";
import Editor from "primevue/editor";
import Fieldset from "primevue/fieldset";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "detail",
    components: {
        Editor,
        Fieldset,
    },
    data() {
        return {
            methods: [],
            accounts: [],
            editmode: false,
            modal: {
                payment: false,
            },

            payment: {
                payment_methode: "",
                account_integration: "",
                payment_date: "",
                amount: 0,
                note: "",
            },
            loader: {
                submit: false,
                data: false,
            },
            transaction: {
                id: "",
                ref_no: "",
                date: "",
                status: "",
                subtotal: 0,
                tax_percent: 0,
                tax_amount: 0,
                final_total: 0,
                payment_total: 0,
                payment_status: "",
                due_total: 0,
                discount: {
                    type: "",
                    amount: 0,
                    total: 0,
                },
                transaction: {
                    id: "",
                    ref_no: "",
                },
                store: {
                    id: "",
                    name: "",
                    address: "",
                    phone: "",
                    email: "",
                },
                customer: {
                    id: "",
                    name: "",
                    country: "",
                    phone: "",
                    email: "",
                    address: "",
                },
                created: {
                    id: "",
                    name: "",
                },
                created_date: {
                    date: "",
                    time: "",
                },
                transaction: {
                    id: "",
                    ref_no: "",
                },
                items: [],
                payments: [],
            },
        };
    },
    computed: {},
    created() {
        this.getDetails();
    },
    methods: {
        async getDetails() {
            this.loader.data = true;
            try {
                const response = await ApiData.get(
                    `app/transactions/sales/returns/detail/${this.$route.params.id}`
                );
                this.transaction = response.data.detail;
                this.loader.data = false;
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
