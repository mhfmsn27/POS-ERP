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
                    <div class="btn-group mt-2 mb-2">
                        <button
                            type="button"
                            class="btn btn-outline-primary dropdown-toggle"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <i class="fa fa-print"></i> Print
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" style="">
                            <li>
                                <a
                                    href="javascript:void(0);"
                                    @click="printFaktur()"
                                    ><i class="fe fe-shopping-cart mr-2"></i>
                                    Print Faktur Penjualan
                                </a>
                            </li>
                            <li>
                                <a
                                    href="javascript:void(0);"
                                    @click="printPengiriman()"
                                    ><i class="fa fa-truck mr-2"></i>
                                    Print Surat Jalan
                                </a>
                            </li>
                            <li>
                                <a
                                    href="javascript:void(0);"
                                    @click="printLabel()"
                                    ><i class="fa fa-truck mr-2"></i>
                                    Print Label Pengiriman
                                </a>
                            </li>
                        </ul>
                    </div>
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
                                <p class="text-muted mb-2">
                                    Informasi Pelanggan :
                                </p>
                                <p class="fw-bold mb-1">
                                    {{
                                        transaction.general_information.customer
                                            .name
                                    }}
                                </p>
                                <p class="text-muted mb-1">
                                    {{
                                        transaction.general_information.customer
                                            .address
                                    }}
                                </p>
                                <p class="text-muted mb-1">
                                    {{
                                        transaction.general_information.customer
                                            .email
                                    }}
                                </p>
                                <p class="text-muted">
                                    {{
                                        transaction.general_information.customer
                                            .phone
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
                    <div class="col-lg-3">
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
                                                <span v-else>
                                                    {{
                                                        formatNumber(detail.qty)
                                                    }}
                                                    {{
                                                        detail.unit_detail.name
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
import imageFragile from "@/assets/images/fragile.webp";
var _ = require("lodash");

export default {
    name: "detail_purchase",
    components: {
        Editor,
        Fieldset,
    },
    data() {
        return {
            currentDate: new Date().toLocaleString(),
            image: imageFragile,
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
    created() {
        this.getDetails();
    },
    methods: {
        async getDetails() {
            this.loader.data = true;
            try {
                const response = await ApiData.get(
                    `app/transactions/sales/detail/${this.$route.params.id}`
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

        printPengiriman() {
            // HTML content for the invoice, without the <script> tag
            const invoiceHTML = `
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Faktur Penjualan</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                        background: #f4f4f4;
                    }

                    .invoice {
                        max-width: 800px;
                        margin: 10px auto;
                        padding: 10px;
                        background: #fff;
                    }

                    header {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 2px;
                    }

                    .company-info h2 {
                        margin: 0;
                    }

                    .customer-info h3 {
                        margin: 0;
                    }

                    .items-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 20px;
                        font-size: 12px;
                        font-weight: 500;
                    }

                    .items-table th,
                    .items-table td {
                        border: 1px solid black;
                        padding: 2px;
                        text-align: left;
                    }

                    .items-table th {
                        background: #f0f0f0;
                    }

                    tfoot td {
                        font-weight: 500;
                    }

                    footer {
                        margin-top: 20px;
                    }

                    .footer-info {
                        margin-top: 10px;
                        font-size: 0.9em;
                    }

                    p {
                        margin-top: 3px;
                        margin-bottom: 3px;
                        font-size: 12px;
                    }

                    h2 {
                        font-size: 12px;
                    }
                </style>
            </head>
            <body>
                <div class="invoice">
                    <header>
                        <div class="company-info" style="max-width:400px;">
                            <h2>${
                                this.transaction.general_information.store
                                    .name || ""
                            }</h2>
                            <p>${
                                this.transaction.general_information.store
                                    .address || ""
                            }</p>
                            <p>Telp ${
                                this.transaction.general_information.store
                                    .phone || ""
                            }</p>
                            <div style="border-top:1px solid black; border-bottom: 1px solid black; margin-top:10px;">
                                <p>Kepada :</p>
                            </div>
                            <p><b>${
                                this.transaction.general_information.customer
                                    .name || ""
                            }</b><br />${
                this.transaction.general_information.customer.address || ""
            }</p>
                        </div>
                        <div class="invoice-info" style="min-width: 250px;">
                            <h2 style="text-align:center; font-size:14px !important">
                                ${
                                    this.transaction.general_information
                                        .type === "shipping_product"
                                        ? "Pengiriman Pesanan"
                                        : "Surat Jalan"
                                }
                            </h2>
                            <table style="border:1px solid black">
                                <tr>
                                    <td style="font-size:10px; min-width:100px; border-bottom: 1px solid black; border-right:1px solid black;">
                                        <p>Tanggal</p>
                                        <p><b>${
                                            this.transaction.general_information
                                                .date
                                        }</b></p>
                                    </td>
                                    <td style="font-size:10px; min-width:100px; border-bottom: 1px solid black;">
                                        <p>Nomor</p>
                                        <p><b>${
                                            this.transaction.general_information
                                                .no_ref
                                        }</b></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:10px; min-width:100px; border-right:1px solid black;">
                                        <p>Syarat Pembayaran</p>
                                        <p><b>${
                                            this.transaction.general_information
                                                .due_limit
                                        } Hari</b></p>
                                    </td>
                                    <td style="font-size:10px; min-width:100px;">
                                        <p>Ekspedisi</p>
                                        <p><b>${
                                            this.transaction.general_information
                                                .courier.name || ""
                                        }</b></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </header>

                    <table class="items-table">
                        <thead>
                            <tr>
                                <td colspan="7" style="text-align: center; width:80%;">Nama Barang</td>
                                <td style="text-align: center;">Qty</td>
                                <td style="text-align: center;">Checklist</td>
                            </tr>
                        </thead>
                        <tbody class="itemsdata">
                            ${this.transaction.product_information.items
                                .map(
                                    (sell) => `
                                <tr>
                                    <td colspan="7">${sell.name || ""}</td>
                                    <td style="text-align: center;">${Number(
                                        sell.qty
                                    ).toLocaleString()} ${
                                        sell.unit_detail.name || ""
                                    }</td>
                                    <td style="text-align: right;"></td>
                                </tr>
                                `
                                )
                                .join("")}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align:right;">Total Qty</td>
                                <td colspan="3"><b>: ${Number(
                                    this.transaction.qty_sell
                                ).toLocaleString()}</b></td>
                            </tr>
                            <tr>
                                <td colspan="9" rowspan="2" style="vertical-align: top; height:50px">Keterangan : ${
                                    this.transaction.general_information.note ||
                                    ""
                                }</td>
                            </tr>
                            <tr></tr>
                            <tr>
                                <td colspan="2" style="text-align:center; border: none; vertical-align: top; height:80px;">Admin</td>
                                <td colspan="2" style="text-align:center; border: none; vertical-align: top;">Gudang</td>
                                <td colspan="2" style="text-align:center; border: none; vertical-align: top;">Penerima</td>
                                <td colspan="2" style="text-align:center; border: none; vertical-align: bottom;">26 Jul, 2024 18:07:51</td>
                                <td colspan="2" style="text-align:center; border: none; vertical-align: bottom;">Hal 1 dari 1 Cetak 2</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </body>
            </html>`;

            // Open a new window and write the HTML content
            const printWindow = window.open("", "_blank");
            printWindow.document.open();
            printWindow.document.write(invoiceHTML);
            printWindow.document.close();

            // Wait for the content to load and then trigger print
            printWindow.onload = function () {
                printWindow.print();
                printWindow.onafterprint = function () {
                    printWindow.close();
                };
            };
        },

        printFaktur() {
            // HTML content for the invoice, without the <script> tag
            const invoiceHTML = `
    <!DOCTYPE html>
        <html lang="en">
        <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        .invoice {
            max-width: 800px;
            margin: 10px auto;
            padding: 10px;
            background: #fff;
            
        }

        header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
        }

        .company-info h2 {
            margin: 0;
        }

        .customer-info h3 {
            margin: 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 12px; 
            font-weight: 500;
        }

        
        .items-table th,
        .items-table td {
            border: 1px solid black;
            padding: 2px;
            text-align: left; 
        }


        .items-table th {
            background: #f0f0f0;
        }

        tfoot td {
            font-weight: 500;
        }

        footer {
            margin-top: 20px;
        }

        .footer-info {
            margin-top: 10px;
            font-size: 0.9em;
        }

        p {
            margin-top: 0px;
            margin-bottom: 0px;
            font-size: 10px;
        }

        h2 {
            font-size: 12px;
        }
    </style> 
</head><body> 
            <div class="invoice">
                <header>
                <div class="company-info" style="max-width:400px;">
                    <h2>${
                        this.transaction.general_information.store.name
                    }</h2>
                    <p>${
                        this.transaction.general_information.store.address
                    }</p>
                    <p>Telp ${
                        this.transaction.general_information.store.phone
                    }</p>
                    <div style="border-top:1px solid black; border-bottom: 1px solid black; margin-top:1px; margin-right:10px;">
                    <p>Kepada :</p>
                    </div>
                    <p>
                    <b>${
                        this.transaction.general_information.customer.name
                    }</b><br />
                    ${this.transaction.general_information.customer.address}
                    </p>
                </div>
                <div class="invoice-info" style="min-width: 250px;">
                    <h2 style="text-align:center; font-size:14px !important;">Faktur Penjualan</h2>
                    <table style="border:1px solid black; width:100%">
                    <tr>
                        <td style="font-size:10px; min-width:100px; border-bottom: 1px solid black; border-right:1px solid black;">
                        <p>Tanggal</p>
                        <p><b>${
                            this.transaction.general_information.date
                        }</b></p>
                        </td>
                        <td style="font-size:10px; min-width:100px; border-bottom: 1px solid black;">
                        <p>Nomor</p>
                        <p><b>${
                            this.transaction.general_information.no_ref
                        }</b></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:10px; min-width:100px; border-right:1px solid black;">
                        <p>Syarat Pembayaran</p>
                        <p><b>${
                            this.transaction.general_information.due_limit
                        } Hari</b></p>
                        </td>
                        <td style="font-size:10px; min-width:100px; vertical-align:top;">
                        <p>Ekspedisi</p>
                        <p><b>${
                            this.transaction.general_information.courier
                                .name
                        }</b></p>
                        </td>
                    </tr>
                    </table>
                </div>
                </header>

                <table class="items-table">
                <thead>
                    <tr>
                    <td colspan="5" style="text-align: center;">Nama Barang</td>
                    <td style="text-align: center; width: 45px;">Qty</td>
                    <td style="text-align: center; width: 80px;">@Harga</td>
                    <td style="text-align: center; width: 70px;">Diskon</td>
                    <td style="text-align: center; width: 90px;">Total Harga</td>
                    </tr>
                </thead>
                <tbody class="itemsdata">
                    ${this.transaction
                    .product_information.items
                        .map(
                            (item) => `
                    <tr>
                    <td colspan="5">${item.name}</td>
                    <td style="text-align: right;">${item.qty} ${
                                item.unit_detail.name
                            }</td>
                    <td style="text-align: right;">${this.formatNumber(
                        item.unit_price
                    )}</td>
                    <td style="text-align: right;">${this.formatNumber(
                        item.discount_amount
                    )}</td>
                    <td style="text-align: right;">${this.formatNumber(
                        (item.unit_price - item.discount_amount) * item.qty
                    )}</td>
                    </tr>`
                        )
                        .join("")}
                </tbody>
                <tfoot>
                    <tr>
                    <td colspan="6" rowspan="2" style="vertical-align: top;">
                        Keterangan : ${
                            this.transaction.general_information.note
                        }
                    </td>
                    <td colspan="2"><b>Subtotal DPP</b></td>
                    <td style="text-align: right;">${this.formatNumber(
                        this.transaction.payment_information.subtotal
                    )}</td>
                    </tr>
                    <tr>
                    <td colspan="2"><b>Diskon</b></td>
                    <td style="text-align: right;">${this.formatNumber(
                        this.transaction.payment_information.discount_total
                    )}</td>
                    </tr>
                    <tr>
                    <td colspan="4" style="text-align:center;">${
                        this.transaction.general_information.store
                            .footer_text
                    }</td>
                    <td colspan="2" style="text-align:right;">Total Qty : ${
                        this.transaction.qty_sell
                    }</td>
                    <td colspan="2"><b>PPN</b></td>
                    <td style="text-align: right;">${this.formatNumber(
                        this.transaction.payment_information.tax_total
                    )}</td>
                    </tr>
                    <tr>
                    <td style="text-align:center; border: none;">Marketing</td>
                    <td style="text-align:center; border: none;">Admin</td>
                    <td style="text-align:center; border: none;">Gudang</td>
                    <td style="text-align:center; border: none;">Checker</td>
                    <td colspan="2" style="text-align:center; border: none;">Diterima</td>
                    <td colspan="2"><b>Biaya Kirim</b></td>
                    <td style="text-align: right;">${this.formatNumber(
                        this.transaction.payment_information.shipping_cost
                    )}</td>
                    </tr>
                    <tr>
                    <td style="text-align:center; border: none;" rowspan="4"> <p style="font-size:11px; margin-top:20px">
                            ${
                        this.transaction.general_information?.user?.name
                    }
                            </p></td>
                    <td colspan="5" style="text-align:center; border: none;"></td>
                    <td colspan="2"><b>Total</b></td>
                    <td style="text-align: right;">${this.formatNumber(
                        this.transaction.payment_information.finalTotal
                    )}</td>
                    </tr>
                    <tr>
                    <td colspan="5" style="text-align:center; border: none;"></td>
                    <td colspan="2" style="text-align:center; border: none; font-size:10px;">${
                        this.currentDate
                    }</td>
                    <td style="text-align:center; border: none; font-size:10px;">Hal 1 dari 1 Cetak 2</td>
                    </tr>
                </tfoot>
                </table>
            </div> </body></html>`;

            // Open a new window and write the HTML content
            const printWindow = window.open("", "_blank");
            printWindow.document.open();
            printWindow.document.write(invoiceHTML);
            printWindow.document.close();

            // Wait for the content to load and then trigger print
            printWindow.onload = function () {
                printWindow.print();
                printWindow.onafterprint = function () {
                    printWindow.close();
                };
            };
        },

        printLabel() {
            // HTML content for the invoice, without the <script> tag
            const invoiceHTML = `<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>Tanda Terima Penerimaan</title>
                <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: top;
                    background-color: #f0f0f0;
                }

                .container {
                    background: #ffffff;
                    padding: 10px;
                    width: 88mm;
                    border: 1px solid #000;
                }

                header {
                    text-align: left;
                    margin-bottom: 10px;
                }

                header h1 {
                    margin: 0;
                    font-size: 14px;
                    text-transform: uppercase;
                }

                .recipient-info p,
                .remarks p,
                .signature p {
                    margin: 0;
                    font-size: 12px;
                }

                .item-table {
                    margin-bottom: 10px;
                }

                .item-table table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 12px;
                }

                .item-table th,
                .item-table td {
                    border: 1px solid #dddddd;
                    text-align: left;
                    padding: 5px;
                }

                .item-table th {
                    background-color: #f2f2f2;
                }

                .signature-box {
                    display: flex;
                    justify-content: space-between;
                    margin-top: 20px;
                }

                .signature-box p {
                    margin: 0;
                    padding: 10px;
                    border: 1px solid #000;
                    width: 40%;
                    text-align: center;
                    font-size: 12px;
                }
                </style>
                
            </head>
            <body>
                <div class="container">
                <header
                    style="
                    border-top: 1px solid black;
                    border-bottom: 1px solid black;
                    padding: 5px;
                    "
                >
                    <h1>FROM</h1>
                    <h1>${
                        this.transaction.general_information.store.name || ""
                    }</h1>
                    <h1>${
                        this.transaction.general_information.store.phone || ""
                    }</h1>
                </header>
                <section class="recipient-info">
                    <p><strong>To :</strong> <br /></p>
                    <p>
                    <strong>${
                        this.transaction.general_information.customer.name || ""
                    }</strong> <br />
                    ${this.transaction.general_information.address || ""}
                    </p>
                    <p><strong>${
                        this.transaction.general_information.customer.phone ||
                        ""
                    }</strong></p>
                </section>
                <section class="item-table" style="margin-top: 10px">
                    <table>
                    <thead>
                        <tr>
                        <th style="text-align: center">Nama Barang</th>
                        <th style="text-align: center">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${this.transaction.product_information.items
                            .map(
                                (sell) => `
                        <tr>
                        <td>${sell.name || ""}</td>
                        <td>${Number(sell.qty).toLocaleString()}</td>
                        </tr>
                        `
                            )
                            .join("")}
                    </tbody>
                    <tfoot>
                        <tr>
                        <td colspan="2">
                            Ekspedisi : ${
                                this.transaction.general_information.courier
                                    .name || ""
                            }
                        </td>
                        </tr>
                        <tr>
                        <td colspan="2">No.Faktur : ${
                            this.transaction.general_information.no_ref
                        }</td>
                        </tr>
                    </tfoot>
                    </table>
                </section>
                <section class="signature" style="margin-bottom: 20px">
                    <p><strong>Keterangan:</strong></p>
                </section>
                <div style="text-align: center">
                    <img style="max-width: 100px" src="${this.image}" />
                </div>
                </div>
            </body>
            </html>`;

            // Open a new window and write the HTML content
            const printWindow = window.open("", "_blank");
            printWindow.document.open();
            printWindow.document.write(invoiceHTML);
            printWindow.document.close();

            // Wait for the content to load and then trigger print
            printWindow.onload = function () {
                printWindow.print();
                printWindow.onafterprint = function () {
                    printWindow.close();
                };
            };
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
