<template>
    <div class="col-lg-9 col-sm-12 row">
        <div class="col-12" v-if="!loader.data">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between">
                    <div class="h5 mb-0 d-sm-flex d-bllock align-items-center">
                        <div class="ms-sm-2 ms-0 mt-sm-0 mt-2">
                            <div class="h6 fw-semibold mb-0">
                                NO REFERENSI :
                                <span class="text-primary"
                                    >#{{
                                        transaction.general_information.no_ref
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>
                    <div class="ms-auto mt-md-0 mt-2">
                        <button
                            class="btn btn-info"
                            type="button"
                            @click="shipping.modal = true"
                            v-if="
                                transaction.general_information.status ==
                                'ordered'
                            "
                        >
                            Kirim Pesanan<i class="fe fe-truck ms-2"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-xl-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted mb-2">Dari Toko :</p>
                                    <p class="fw-bold mb-1">
                                        {{
                                            transaction.general_information
                                                .store.name
                                        }}
                                    </p>
                                    <p class="mb-1 text-muted">
                                        {{
                                            transaction.general_information
                                                .store.address
                                        }}
                                    </p>
                                    <p class="mb-1 text-muted">
                                        {{
                                            transaction.general_information
                                                .store.email
                                        }}
                                    </p>
                                    <p class="text-muted">
                                        {{
                                            transaction.general_information
                                                .store.phone
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-muted mb-2">
                                        Informasi Pelanggan :
                                    </p>
                                    <p class="fw-bold mb-1">
                                        {{
                                            transaction.general_information
                                                .customer.name
                                        }}
                                    </p>
                                    <p class="text-muted mb-1">
                                        {{
                                            transaction.general_information
                                                .customer.address
                                        }}
                                    </p>
                                    <p class="text-muted mb-1">
                                        {{
                                            transaction.general_information
                                                .customer.email
                                        }}
                                    </p>
                                    <p class="text-muted">
                                        {{
                                            transaction.general_information
                                                .customer.phone
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <p class="fw-semibold text-muted mb-1">
                                Tanggal Di Buat :
                            </p>
                            <p class="fs-15 mb-1">
                                {{
                                    transaction.general_information.created_date
                                        .date
                                }}
                                -
                                <span class="text-muted fs-12">{{
                                    transaction.general_information.created_date
                                        .time
                                }}</span>
                            </p>
                        </div>
                        <div class="col-lg-3">
                            <p class="fw-semibold text-muted mb-1">
                                Tanggal Transaksi :
                            </p>
                            <p class="fs-15 mb-1">
                                {{ transaction.general_information.date }}
                            </p>
                        </div>
                        <div class="col-lg-3">
                            <p class="fw-semibold text-muted mb-1">
                                Status Transaksi :
                            </p>
                            <p class="fs-16 mb-1 fw-semibold">
                                {{
                                    transaction.general_information.status ==
                                    "received"
                                        ? "Diterima"
                                        : transaction.general_information
                                              .status == "ordered"
                                        ? "Proses Pemesanan"
                                        : ""
                                }}
                                {{
                                    transaction.general_information.status ==
                                    "void"
                                        ? "Di Batalkan"
                                        : ""
                                }}
                            </p>
                        </div>
                        <div class="col-lg-3">
                            <p class="fw-semibold text-muted mb-1">
                                Dibuat Oleh :
                            </p>
                            <p class="fs-16 mb-1 fw-semibold">
                                {{
                                    transaction.general_information.created.name
                                }}
                            </p>
                        </div>

                        <div class="col-xl-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-striped table-bordered table-sale"
                                >
                                    <thead>
                                        <tr>
                                            <th>Nama Produk</th>
                                            <th>Quantity</th>
                                            <th>Harga Pembelian</th>
                                            <th>Diskon</th>
                                            <th>Pajak</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                detail, index
                                            ) in transaction.product_information
                                                .items"
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
                                                    {{ detail.name }}
                                                </p>
                                            </td>
                                            <td>
                                                <p class="fs-15 mb-1">
                                                    <span
                                                        class="text-muted fs-12"
                                                        v-if="
                                                            detail.unit !=
                                                            detail.first_unit.id
                                                        "
                                                    >
                                                        {{ detail.qty_no_unit }}
                                                        {{
                                                            detail.first_unit
                                                                .name
                                                        }}
                                                    </span>
                                                    <span v-else>
                                                        {{
                                                            formatNumber(
                                                                detail.qty
                                                            )
                                                        }}
                                                        {{
                                                            detail.unit_detail
                                                                .name
                                                        }}
                                                    </span>
                                                </p>
                                            </td>
                                            <td>
                                                <div
                                                    class="d-flex align-items-center"
                                                >
                                                    <div class="flex-1">
                                                        <p>
                                                            <span
                                                                class="text-muted fs-12"
                                                                v-if="
                                                                    detail.unit_id !=
                                                                    detail
                                                                        .first_unit
                                                                        .id
                                                                "
                                                            >
                                                                <br />
                                                                {{
                                                                    formatNumber(
                                                                        detail.without_discount
                                                                    )
                                                                }}
                                                                /
                                                                {{
                                                                    detail
                                                                        .first_unit
                                                                        .name
                                                                }}
                                                            </span>
                                                            <span v-else>
                                                                Rp
                                                                {{
                                                                    formatNumber(
                                                                        detail
                                                                            .unit_detail
                                                                            .price
                                                                    )
                                                                }}
                                                                /
                                                                {{
                                                                    detail
                                                                        .unit_detail
                                                                        .name
                                                                }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p>
                                                    {{
                                                        detail.discount_type !=
                                                        "percent"
                                                            ? "Rp "
                                                            : ""
                                                    }}
                                                    {{ detail.discount_amount }}
                                                    {{
                                                        detail.discount_type ==
                                                        "percent"
                                                            ? " %"
                                                            : ""
                                                    }}

                                                    <span
                                                        class="text-muted fs-12"
                                                        v-if="
                                                            detail.unit_id !=
                                                                detail
                                                                    .first_unit
                                                                    .id &&
                                                            detail.discount_amount >
                                                                0
                                                        "
                                                    >
                                                        <br />
                                                        Harga Setelah Diskon
                                                        <br />
                                                        {{
                                                            formatNumber(
                                                                detail.unit_price
                                                            )
                                                        }}
                                                        /
                                                        {{
                                                            detail.first_unit
                                                                .name
                                                        }}
                                                    </span>
                                                </p>
                                            </td>
                                            <td>
                                                <p>
                                                    {{ detail.tax }}%

                                                    <span
                                                        class="text-muted fs-12"
                                                        v-if="
                                                            detail.unit_id !=
                                                                detail
                                                                    .first_unit
                                                                    .id &&
                                                            detail.tax > 0
                                                        "
                                                    >
                                                        <br />
                                                        Harga Setelah Pajak
                                                        <br />
                                                        {{
                                                            formatNumber(
                                                                detail.purchase_price_inc_tax
                                                            )
                                                        }}
                                                        /
                                                        {{
                                                            detail.first_unit
                                                                .name
                                                        }}
                                                    </span>
                                                </p>
                                            </td>
                                            <td>
                                                <p>
                                                    Rp
                                                    {{
                                                        formatNumber(
                                                            detail.subtotal
                                                        )
                                                    }}
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th
                                                scope="row"
                                                colspan="5"
                                                class="text-end"
                                            >
                                                Subtotal Pembelian
                                            </th>
                                            <th scope="row">
                                                Rp
                                                {{
                                                    formatNumber(
                                                        transaction
                                                            .payment_information
                                                            .subtotal
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th
                                                colspan="5"
                                                class="text-end"
                                                scope="row"
                                            >
                                                Diskon
                                            </th>
                                            <th>
                                                -
                                                {{
                                                    transaction
                                                        .payment_information
                                                        .discount_type !=
                                                    "percent"
                                                        ? "Rp"
                                                        : ""
                                                }}
                                                {{
                                                    formatNumber(
                                                        transaction
                                                            .payment_information
                                                            .discount
                                                    )
                                                }}

                                                {{
                                                    transaction
                                                        .payment_information
                                                        .discount_type !=
                                                    "percent"
                                                        ? ""
                                                        : "%"
                                                }}

                                                {{
                                                    transaction
                                                        .payment_information
                                                        .discount_type ==
                                                    "percent"
                                                        ? "(Rp " +
                                                          formatNumber(
                                                              transaction
                                                                  .payment_information
                                                                  .discount_total
                                                          ) +
                                                          ")"
                                                        : ""
                                                }}
                                            </th>
                                        </tr>

                                        <tr>
                                            <th
                                                colspan="5"
                                                class="text-end"
                                                scope="row"
                                            >
                                                Pajak Pembelian
                                            </th>
                                            <th>
                                                Rp
                                                {{
                                                    formatNumber(
                                                        transaction
                                                            .payment_information
                                                            .tax_total
                                                    )
                                                }}
                                                (
                                                {{
                                                    transaction
                                                        .payment_information
                                                        .tax
                                                }}% )
                                            </th>
                                        </tr>

                                        <tr>
                                            <th
                                                colspan="5"
                                                class="text-end"
                                                scope="row"
                                            >
                                                Biaya Pengiriman
                                            </th>
                                            <th>
                                                Rp
                                                {{
                                                    formatNumber(
                                                        transaction
                                                            .payment_information
                                                            .shipping_cost
                                                    )
                                                }}
                                            </th>
                                        </tr>

                                        <tr class="bg-light">
                                            <th colspan="5" class="text-end">
                                                Total Tagihan
                                            </th>
                                            <th>
                                                Rp
                                                {{
                                                    formatNumber(
                                                        transaction
                                                            .payment_information
                                                            .finalTotal
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr
                                            class="bg-light"
                                            v-if="transaction.resi_no != ''"
                                        >
                                            <th colspan="5" class="text-end">
                                                Nomor Resi
                                            </th>
                                            <th>
                                                {{ transaction.resi_no }}
                                            </th>
                                        </tr>
                                        <tr
                                            class="bg-light"
                                            v-if="
                                                transaction.general_information
                                                    .status == 'void'
                                            "
                                        >
                                            <th colspan="2">
                                                Di Batalkan Oleh
                                            </th>
                                            <th colspan="4">
                                                {{
                                                    transaction
                                                        .payment_information
                                                        .void.created
                                                }}
                                            </th>
                                        </tr>
                                        <tr
                                            class="bg-light"
                                            v-if="
                                                transaction.general_information
                                                    .status == 'void'
                                            "
                                        >
                                            <th colspan="2">
                                                Alasan Di Batalkan
                                            </th>
                                            <th colspan="4">
                                                {{
                                                    transaction
                                                        .payment_information
                                                        .void.reason
                                                }}
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
                                    >{{
                                        transaction.payment_information.note
                                    }}</textarea
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

        <div class="col-12 mt-3" v-if="transaction.returns.length > 0">
            <div class="card custom-card mb-4 p-4">
                <div class="card-body p-0">
                    <div class="row gy-4">
                        <div class="col-lg-12 d-flex justify-content-between">
                            <h5>Informasi Retur Penjualan</h5>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-bordered table-striped table-responsive-sm"
                                >
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>No Referensi</th>
                                            <th>Total</th>
                                            <th>Ditambahkan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                retur, r
                                            ) in transaction.returns"
                                            :key="r"
                                        >
                                            <td>
                                                {{ retur.date }}
                                            </td>
                                            <td>
                                                {{ retur.ref_no }}
                                            </td>
                                            <td>
                                                Rp
                                                {{
                                                    formatNumber(
                                                        retur.final_total
                                                    )
                                                }}
                                            </td>
                                            <td>
                                                {{ retur.created.name }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="col-12 mt-3"
            v-if="
                transaction.general_information.status != 'void' &&
                transaction.general_information.status != 'draft' &&
                !loader.data
            "
        >
            <div class="card custom-card mb-4 p-4">
                <div class="card-body p-0">
                    <div class="row gy-4">
                        <div class="col-lg-12 d-flex justify-content-between">
                            <h5>Informasi Pembayaran</h5>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-bordered table-striped table-responsive-sm"
                                >
                                    <thead>
                                        <tr>
                                            <th>Metode Pembayaran</th>
                                            <th>Jumlah Di Bayarkan</th>
                                            <th>Bukti Transfer</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                payment, pay
                                            ) in transaction.payments"
                                            :key="pay"
                                        >
                                            <td>
                                                <div
                                                    v-if="
                                                        payment.method.name !=
                                                        'bank_transfer'
                                                    "
                                                    class="d-flex align-items-center"
                                                >
                                                    <div
                                                        class="avatar avatar-md me-2 lh-1"
                                                        v-if="
                                                            payment.method
                                                                .icon != null
                                                        "
                                                    >
                                                        <img
                                                            :src="
                                                                payment.method
                                                                    .icon
                                                            "
                                                            :alt="
                                                                payment.method
                                                                    .name
                                                            "
                                                        />
                                                    </div>
                                                    <div class="lh-1">
                                                        {{
                                                            payment.method.name
                                                        }}
                                                    </div>
                                                </div>
                                                <div v-else>
                                                    <p>
                                                        Dari Bank
                                                        {{
                                                            payment.method
                                                                .from_bank
                                                        }}
                                                        <br />
                                                        Ke Bank
                                                        {{
                                                            payment.method
                                                                .to_bank
                                                        }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                Rp
                                                {{
                                                    formatNumber(payment.amount)
                                                }}
                                            </td>
                                            <td>
                                                <a
                                                    :href="payment.method.file"
                                                    download
                                                    v-tooltip="
                                                        'Download Bukti Pembayaran'
                                                    "
                                                    class="btn btn-icon btn-outline-info rounded-pill btn-wave waves-effect waves-light"
                                                >
                                                    <i
                                                        class="fe fe-download"
                                                    ></i>
                                                </a>
                                            </td>
                                            <td>
                                                <button
                                                    v-if="
                                                        payment.status ==
                                                        'success'
                                                    "
                                                    type="button"
                                                    v-tooltip="
                                                        'Sudah di Verifikasi'
                                                    "
                                                    class="btn btn-icon btn-outline-info rounded-pill btn-wave waves-effect waves-light"
                                                >
                                                    <i class="fe fe-check"></i>
                                                </button>
                                                <div
                                                    v-else
                                                    class="d-flex justify-content-center"
                                                >
                                                    <button
                                                        type="button"
                                                        @click="
                                                            rejectedPayment(
                                                                payment.id
                                                            )
                                                        "
                                                        v-tooltip="
                                                            'Tolak Pembayaran'
                                                        "
                                                        class="btn btn-icon btn-outline-danger rounded-pill btn-wave waves-effect waves-light me-2"
                                                    >
                                                        <i
                                                            class="fe fe-x-circle"
                                                        ></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @click="
                                                            approvePayment(
                                                                payment.id
                                                            )
                                                        "
                                                        v-tooltip="
                                                            'Terima Pembayaran'
                                                        "
                                                        class="btn btn-icon btn-outline-success rounded-pill btn-wave waves-effect waves-light"
                                                    >
                                                        <i
                                                            class="fe fe-check-circle"
                                                        ></i>
                                                    </button>
                                                </div>
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
                                                Total Di Bayarkan
                                            </th>
                                            <th colspan="2">
                                                Rp
                                                {{
                                                    formatNumber(
                                                        transaction
                                                            .payment_information
                                                            .payment.pay_total
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th
                                                scope="row"
                                                colspan="2"
                                                class="text-end"
                                            >
                                                Sisa Hutang / Yang Harus Di
                                                Bayarkan
                                            </th>
                                            <th colspan="2">
                                                Rp
                                                {{
                                                    formatNumber(
                                                        transaction
                                                            .payment_information
                                                            .payment.due_total
                                                    )
                                                }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th
                                                scope="row"
                                                colspan="2"
                                                class="text-end"
                                            >
                                                Status
                                            </th>
                                            <th colspan="2">
                                                {{
                                                    transaction
                                                        .general_information
                                                        .payment_status ==
                                                    "paid"
                                                        ? "Lunas"
                                                        : "Hutang"
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

        <!-- Modal For Shipping -->
        <Dialog
            v-model:visible="shipping.modal"
            class="filter-data"
            modal
            maximizable
            :header="'Kirim Pesanan'"
            :style="{ width: '50vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <Form @submit="sendShipping()" ref="ValidationShipping">
                <div class="row p-3">
                    <div class="col-12 mb-3">
                        <label for="user-ref" class="form-label"
                            >Masukkan Nomor Resi</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="shipping.resi_no"
                            :name="'Nomor Resi'"
                        >
                            <InputText
                                type="text"
                                style="width: 100%"
                                v-model="shipping.resi_no"
                                class="form-control"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                </div>
            </Form>
            <template #footer>
                <button
                    type="button"
                    :disabled="loader.submit"
                    @click="sendShipping()"
                    class="btn btn-outline-info btn-wave waves-effect waves-light"
                >
                    {{ loader.submit ? "Mohon Tunggu...." : "Kirim Pesanan" }}
                </button>
            </template>
        </Dialog>
        <!-- End Modal -->
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
    name: "detail_purchase",
    components: {
        Editor,
        Fieldset,
    },
    data() {
        return {
            shipping: {
                modal: false,
                resi_no: "",
            },
            loader: {
                submit: false,
                data: false,
            },
            transaction: {
                resi_no: "",
                general_information: {
                    id: null,
                    store: {
                        id: "",
                        name: "",
                        address: "",
                        email: "",
                        phone: "",
                    },
                    customer: {
                        id: null,
                        name: "",
                        country: "",
                        address: "",
                        email: "",
                        phone: "",
                    },
                    created_date: {
                        date: "",
                        time: "",
                    },
                    created: {
                        id: "",
                        name: "",
                    },
                    date: null,
                    no_ref: null,
                    status: "",
                    payment_status: "",
                    supplier_ref: "",
                },
                product_information: {
                    discount_product_total: 0,
                    tax_product_total: 0,
                    subtotal: 0,
                    items: [],
                },
                payment_information: {
                    subtotal: 0,
                    discount_type: "percent",
                    discount: 0,
                    discount_total: 0,
                    tax: 0,
                    tax_total: 0,
                    shipping_cost: 0,
                    note: "",
                    finalTotal: 0,
                    payment: {
                        pay_total: 0,
                        due_total: 0,
                    },
                    void: {
                        reason: "",
                        created: "",
                    },
                },
                payments: [],
                returns: [],
            },
        };
    },
    computed: {},
    mounted() {
        this.getDetails();
    },
    methods: {
        sendShipping() {
            this.$refs.ValidationShipping.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Terjadi kesalahan",
                        detail: "Silahkan Check kembali form inputan anda",
                        life: 3000,
                    });
                } else {
                    this.loader.submit = true;
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.post(
                        `app/ecommerce/orders/send-order/${this.$route.params.id}`,
                        this.shipping
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.shipping.modal = false;
                            this.loader.submit = false;
                            this.getDetails();
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.loader.submit = false;
                            this.$handleErrorResponse(err);
                        });
                }
            });
        },

        approvePayment(id) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Pembayaran yang sudah di terima tidak dapat di batalkan lagi",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.post(
                        `app/ecommerce/orders/confirmation-payment/${id}`
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.getDetails();
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.$handleErrorResponse(err);
                        });
                } else {
                    Swal.fire("Membatalkan Proses Hapus Data");
                }
            });
        },

        rejectedPayment(id) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Pembayaran di tolak akan otomatis menghapus pembayaran ini dan tidak dapat di pulihkan lagi",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.post(`app/ecommerce/orders/rejected-payment/${id}`)
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.getDetails();
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.$handleErrorResponse(err);
                        });
                } else {
                    Swal.fire("Membatalkan Proses Hapus Data");
                }
            });
        },

        async getDetails() {
            this.loader.data = true;
            try {
                const response = await ApiData.get(
                    `app/ecommerce/orders/detail/${this.$route.params.id}`
                );
                this.transaction = response.data;
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
