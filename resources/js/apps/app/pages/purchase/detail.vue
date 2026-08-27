<template>
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
                                        transaction.general_information.store
                                            .name
                                    }}
                                </p>
                                <p class="mb-1 text-muted">
                                    {{
                                        transaction.general_information.store
                                            .address
                                    }}
                                </p>
                                <p class="mb-1 text-muted">
                                    {{
                                        transaction.general_information.store
                                            .email
                                    }}
                                </p>
                                <p class="text-muted">
                                    {{
                                        transaction.general_information.store
                                            .phone
                                    }}
                                </p>
                            </div>
                            <div>
                                <p class="text-muted mb-2">Tujuan Supplier :</p>
                                <p class="fw-bold mb-1">
                                    {{
                                        transaction.general_information.supplier
                                            .name
                                    }}
                                </p>
                                <p class="text-muted mb-1">
                                    {{
                                        transaction.general_information.supplier
                                            .country
                                    }}
                                    {{
                                        transaction.general_information.supplier
                                            .address
                                    }}
                                </p>
                                <p class="text-muted mb-1">
                                    {{
                                        transaction.general_information.supplier
                                            .email
                                    }}
                                </p>
                                <p class="text-muted">
                                    {{
                                        transaction.general_information.supplier
                                            .phone
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <p class="fw-semibold text-muted mb-1">
                            No.Faktur :
                        </p>
                        <p class="fs-15 mb-1">
                            #{{ transaction.general_information.no_ref }}
                        </p>
                    </div>
                    <div class="col-lg-2">
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
                    <div class="col-lg-2">
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
                                    : transaction.general_information.status ==
                                      "ordered"
                                    ? "Proses Pemesanan"
                                    : ""
                            }}
                            {{
                                transaction.general_information.status == "void"
                                    ? "Di Batalkan"
                                    : ""
                            }}
                        </p>
                    </div>
                    <div class="col-lg-2">
                        <p class="fw-semibold text-muted mb-1">Dibuat Oleh :</p>
                        <p class="fs-16 mb-1 fw-semibold">
                            {{ transaction.general_information.created.name }}
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
                                        v-for="(detail, index) in transaction
                                            .product_information.items"
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
                                                {{ formatNumber(detail.qty) }}
                                                {{ detail.unit_detail.name }}

                                                <span
                                                    class="text-muted fs-12"
                                                    v-if="
                                                        detail.unit !=
                                                        detail.first_unit.id
                                                    "
                                                >
                                                    {{ detail.qty_no_unit }}
                                                    {{ detail.first_unit.name }}
                                                </span>
                                            </p>
                                        </td>
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <div class="flex-1">
                                                    <p>
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
                                                            detail.unit_detail
                                                                .name
                                                        }}

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
                                                            detail.first_unit
                                                                .id &&
                                                        detail.discount_amount >
                                                            0
                                                    "
                                                >
                                                    <br />
                                                    Harga Setelah Diskon <br />
                                                    {{
                                                        formatNumber(
                                                            detail.unit_price
                                                        )
                                                    }}
                                                    /
                                                    {{ detail.first_unit.name }}
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
                                                            detail.first_unit
                                                                .id &&
                                                        detail.tax > 0
                                                    "
                                                >
                                                    <br />
                                                    Harga Setelah Pajak <br />
                                                    {{
                                                        formatNumber(
                                                            detail.purchase_price_inc_tax
                                                        )
                                                    }}
                                                    /
                                                    {{ detail.first_unit.name }}
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
                                                transaction.payment_information
                                                    .discount_type != "percent"
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
                                                transaction.payment_information
                                                    .discount_type != "percent"
                                                    ? ""
                                                    : "%"
                                            }}

                                            {{
                                                transaction.payment_information
                                                    .discount_type == "percent"
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
                                                transaction.payment_information
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
                                        v-if="
                                            transaction.general_information
                                                .status == 'void'
                                        "
                                    >
                                        <th colspan="2">Di Batalkan Oleh</th>
                                        <th colspan="4">
                                            {{
                                                transaction.payment_information
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
                                        <th colspan="2">Alasan Di Batalkan</th>
                                        <th colspan="4">
                                            {{
                                                transaction.payment_information
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
                        <h5>Informasi Retur Pembelian</h5>
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
                                                formatNumber(retur.final_total)
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
                                        <th>Ditambahkan Oleh</th>
                                        <th>Jumlah Di Bayarkan</th>
                                        <th>Catatan</th>
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
                                                class="d-flex align-items-center"
                                            >
                                                <div
                                                    class="avatar avatar-md me-2 lh-1"
                                                    v-if="
                                                        payment.method.icon !=
                                                        null
                                                    "
                                                >
                                                    <img
                                                        :src="
                                                            payment.method.icon
                                                        "
                                                        :alt="
                                                            payment.method.name
                                                        "
                                                    />
                                                </div>
                                                <div class="lh-1">
                                                    {{ payment.method.name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ payment.createdby.name }}
                                        </td>
                                        <td>
                                            Rp
                                            {{ formatNumber(payment.amount) }}
                                        </td>
                                        <td>
                                            {{ payment.note }}
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
                                            Sisa Hutang / Yang Harus Di Bayarkan
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
                                                transaction.general_information
                                                    .payment_status == "paid"
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
   

    <!-- Void Request -->
    <Dialog
        v-model:visible="modal.void"
        modal
        header=""
        :style="{ width: '60vh' }"
    >
        <div class="card-body ps-5 pe-5 pt-2 pb-5 rectangle3">
            <div class="d-flex justify-content-center">
                <img
                    src="@/assets/images/void_transaction.png"
                    style="width: 55%"
                />
            </div>

            <p class="h4 fw-semibold mb-2 text-center">Void Transaksi</p>
            <p class="mb-4 text-muted op-7 fw-normal text-center">
                Pembatalan Transaksi Pembelian!
            </p>
            <Form @submit="sendAskVoid()" ref="ValidationAskCode">
                <div class="row" v-if="void_request.step == 1">
                    <div class="col-12 mt-2">
                        <label for="product-name-add" class="form-label"
                            >Masukkan Alasan Pembatalan Transaksi</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="void_request.reason"
                            name="Alasan Void"
                        >
                            <textarea
                                class="form-control"
                                v-model="void_request.reason"
                            ></textarea>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>
                    <div class="col-xl-12 d-grid mt-4">
                        <button
                            type="submit"
                            :disabled="loader.submit"
                            class="btn btn-warning label-btn label-end"
                        >
                            {{
                                loader.submit
                                    ? "Mohon Tunggu...."
                                    : "Minta Kode Void"
                            }}
                            <i
                                class="ri-send-plane-line label-btn-icon ms-2"
                            ></i>
                        </button>
                    </div>
                </div>
            </Form>
            <Form @submit="verificationCode()">
                <div class="row gy-3" v-if="void_request.step == 2">
                    <!-- Code Verifikasi Form -->
                    <div class="col-xl-12">
                        <!-- Code Verification -->
                        <div class="col-xl-12">
                            <label
                                for="signin-code"
                                class="form-label text-default"
                                >Kode Verifikasi</label
                            >
                            <div class="row">
                                <!-- 1 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_1"
                                        type="text"
                                        v-model="code.one"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_1')"
                                    />
                                </div>

                                <!-- 2 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_2"
                                        type="text"
                                        v-model="code.two"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_2')"
                                    />
                                </div>

                                <!-- 3 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_3"
                                        type="text"
                                        v-model="code.tree"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_3')"
                                    />
                                </div>

                                <!-- 4 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_4"
                                        type="text"
                                        v-model="code.for"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_4')"
                                    />
                                </div>

                                <!-- 5 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_5"
                                        type="text"
                                        v-model="code.five"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_5')"
                                    />
                                </div>

                                <!-- 6 -->
                                <div class="col-2 verifycode">
                                    <input
                                        inputId="code_6"
                                        type="text"
                                        v-model="code.six"
                                        class="form-control form-control-lg"
                                        data-inputmask="'mask': '9', 'placeholder': ''"
                                        pattern="[0-9]*"
                                        maxlength="1"
                                        @keyup="nextField($event, 'code_6')"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- End Code Verification -->

                        <div class="d-flex justify-content-end mt-4">
                            <a
                                href="javascript:void(0);"
                                class="text-blue mb-0 pointer"
                                @click="sendAskVoid"
                                v-if="!resend"
                            >
                                Kirim Permintaan Kode Verifikasi!
                            </a>
                            <vue-countdown
                                v-if="resend"
                                :time="time_left"
                                :interval="100"
                                v-slot="{ seconds }"
                            >
                                <p class="mb-0" v-if="seconds > 1">
                                    Kirim Ulang Setelah ({{ seconds }})
                                </p>

                                <p class="text-dark-blue mb-0" v-else>
                                    Tidak menerima kode ?
                                    <a
                                        href="javascript:void(0);"
                                        class="text-blue text-decoration-none pointer"
                                        @click="sendAskVoid"
                                        >Kirim Ulang</a
                                    >
                                </p>
                            </vue-countdown>
                        </div>
                    </div>
                    <!-- End Code Form -->

                    <div class="col-xl-12 d-grid mt-4">
                        <button
                            type="submit"
                            :disabled="loader.submit"
                            class="btn btn-warning label-btn label-end"
                        >
                            {{
                                loader.submit
                                    ? "Mohon Tunggu...."
                                    : "Batalkan Transaksi Sekarang!"
                            }}
                            <i
                                class="ri-send-plane-line label-btn-icon ms-2"
                            ></i>
                        </button>
                    </div>
                </div>
            </Form>
        </div>
    </Dialog>
    <!-- End Void Request -->
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
            editmode: false,
            modal: {
                void: false,
            },
            void_request: {
                step: 1,
                reason: "",
                code: "",
            },
            code: {
                one: "",
                two: "",
                tree: "",
                for: "",
                five: "",
                six: "",
            },
            resend: false,
            time_left: 0,

            loader: {
                submit: false,
                data: false,
            },
            transaction: {
                general_information: {
                    id: null,
                    store: {
                        id: "",
                        name: "",
                        address: "",
                        email: "",
                        phone: "",
                    },
                    supplier: {
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
    created() {
        this.getDetails();
    },
    methods: {
        async getDetails() {
            this.loader.data = true;
            try {
                const response = await ApiData.get(
                    `app/transactions/purchases/detail/${this.$route.params.id}`
                );
                this.transaction = response.data;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        RemovePayment(id) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Data yang telah dihapus tidak dapat dikembalikan lagi",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    this.$store
                        .dispatch("purchases/deletePayment", id)
                        .then((response) => {
                            NProgress.done();
                            this.$handleSuccessResponse(response.message);
                            this.transaction.payment_information =
                                response.payment_information;
                            this.transaction.general_information =
                                response.general_information;
                            this.transaction.payments = response.payments;
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

        // Ask Void Code
        sendAskVoid() {
            this.$refs.ValidationAskCode.validate().then((success) => {
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
                    this.$store
                        .dispatch("purchases/voidRequest", {
                            request: {
                                reason: this.void_request.reason,
                            },
                            id: this.$route.params.id,
                        })
                        .then((response) => {
                            this.$handleSuccessResponse(response.message);
                            NProgress.done();
                            this.void_request.step = 2;
                            this.loader.submit = false;
                            this.resend = true;
                            this.time_left = this.time_left + 60000;
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.loader.submit = false;
                            this.$handleErrorResponse(err);
                        });
                }
            });
        },

        // Verification Void
        verificationCode() {
            if (
                this.code.one == "" ||
                this.code.two == "" ||
                this.code.tree == "" ||
                this.code.for == "" ||
                this.code.five == "" ||
                this.code.six == ""
            ) {
                this.$toast.add({
                    severity: "error",
                    summary: "Terjadi kesalahan",
                    detail: "Silahkan Check kembali form inputan anda",
                    life: 3000,
                });
            } else {
                this.loader.submit = true;
                var code =
                    this.code.one.toString() +
                    this.code.two.toString() +
                    this.code.tree.toString() +
                    this.code.for.toString() +
                    this.code.five.toString() +
                    this.code.six.toString();

                this.$store
                    .dispatch("purchases/voidTransaction", {
                        request: {
                            code: code,
                        },
                        id: this.$route.params.id,
                    })
                    .then((res) => {
                        NProgress.done();
                        this.$handleSuccessResponse(res.message);
                        setTimeout(() => {
                            return this.$router.push({ name: "void_purchase" });
                        }, 1000);
                    })
                    .catch((err) => {
                        NProgress.done();
                        this.loader.submit = false;
                        this.$handleErrorResponse(err);
                    });
            }
        },

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },

        nextField(e, nomor) {
            var inputname = nomor;
            var parts = inputname.split("_");
            var input = document.querySelector("[inputId=" + nomor + "]");

            var currentLength = input.value.length;

            if (parseInt(parts[1]) < 6) {
                var newField = parts[0] + "_" + (parseInt(parts[1]) + 1);
                input = document.querySelector("[inputId=" + newField + "]");
                input.focus();
            }

            if (e.key === "Backspace" && currentLength === 0) {
                if (parseInt(parts[1]) > 1) {
                    var newField = parts[0] + "_" + (parseInt(parts[1]) - 1);
                    input = document.querySelector(
                        "[inputId=" + newField + "]"
                    );
                    input.focus();
                }
            }

            if (e.key === "ArrowLeft") {
                if (parseInt(parts[1]) > 1) {
                    var newField = parts[0] + "_" + (parseInt(parts[1]) - 1);
                    input = document.querySelector(
                        "[inputId=" + newField + "]"
                    );
                    input.focus();
                }
            }

            if (e.key === "ArrowRight") {
                if (parseInt(parts[1]) < 6) {
                    var newField = parts[0] + "_" + (parseInt(parts[1]) + 1);
                    input = document.querySelector(
                        "[inputId=" + newField + "]"
                    );
                    input.focus();
                }
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
