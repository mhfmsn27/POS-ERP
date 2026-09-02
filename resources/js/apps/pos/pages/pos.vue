<template>
    <div class="row pos-content">
        <div class="col-xl-7 col-md-7 col-sm-12">
            <!-- POS Quick Hotkey Guide Bar -->
            <div class="d-flex align-items-center justify-content-between bg-white border rounded px-3 py-2 mb-2 shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <span><span class="pos-kbd">F2</span> <span class="small fw-semibold text-muted ms-1">Cari Produk</span></span>
                    <span><span class="pos-kbd">F4</span> <span class="small fw-semibold text-muted ms-1">Bayar Cepat</span></span>
                    <span><span class="pos-kbd">ESC</span> <span class="small fw-semibold text-muted ms-1">Batal/Tutup</span></span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill small fw-bold">
                        <i class="fa fa-circle text-success me-1" style="font-size: 8px;"></i> POS Siap
                    </span>
                </div>
            </div>

            <div
                class="row"
                style="height: 70vh; overflow: auto"
                id="productData"
            >
                <!-- Items -->
                <div
                    class="col-lx-3 col-lg-3 col-md-4 col-sm-6 productList mb-3"
                    v-for="(item, index) in products"
                    :key="index"
                >
                    <div class="card h-100 pos-product-card position-relative shadow-sm" @click="modalCart(item)">
                        <!-- Dynamic Stock Level Badge -->
                        <div class="position-absolute top-0 end-0 m-2" style="z-index: 5;">
                            <span v-if="item.stock > 10" class="badge bg-success shadow-sm rounded-pill px-2 py-1" style="font-size: 10px;">
                                Stok: {{ item.stock }}
                            </span>
                            <span v-else-if="item.stock > 0" class="badge bg-warning text-dark shadow-sm rounded-pill px-2 py-1" style="font-size: 10px;">
                                Sisa: {{ item.stock }}
                            </span>
                            <span v-else class="badge bg-danger shadow-sm rounded-pill px-2 py-1" style="font-size: 10px;">
                                Habis
                            </span>
                        </div>

                        <div class="card-content text-center pt-2">
                            <img
                                id="productImage"
                                width="8vh"
                                style="height: 11vh; object-fit: contain;"
                                :src="item.image"
                                loading="lazy"
                                class="card-img-top img-fluid rounded"
                                :alt="item.name"
                            />
                        </div>
                        <ul class="list-group list-group-flush text-center border-0">
                            <li
                                class="list-group-item productName border-0 px-2 py-1 fw-semibold text-dark"
                                style="
                                    font-size: 13px;
                                    height: 7vh;
                                    overflow: hidden;
                                "
                            >
                                {{ item.name }}
                            </li>
                            <li class="list-group-item border-0 py-1 fw-bold text-primary" id="productPrice">
                                Rp {{ formatNumber(item.selling_price) }}
                            </li>
                            <div class="px-2 pb-2">
                                <button
                                    id="28"
                                    class="btn btn-sm btn-primary w-100 rounded-pill shadow-sm"
                                    type="button"
                                    @click.stop="modalCart(item)"
                                >
                                    <i class="fa fa-cart-plus me-1"></i> Tambah
                                </button>
                            </div>
                        </ul>
                    </div>
                </div>
                <!-- End Item -->
            </div>
        </div>
        <div class="col-xl-5 col-md-5 col-sm-12 billing-pos">
            <div class="table-responsive pos-billing" style="min-height: 250px;">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th><i class="fa fa-trash"></i></th>
                        </tr>
                    </thead>
                    <tbody id="cartProduct">
                        <!-- Empty Cart Illustrated State -->
                        <tr v-if="!transaction.items || transaction.items.length === 0">
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa fa-shopping-basket fa-3x mb-2 text-secondary opacity-50"></i>
                                    <h6 class="fw-bold mb-1">Keranjang Masih Kosong</h6>
                                    <p class="small text-muted mb-0">Tekan <span class="pos-kbd">F2</span> untuk mencari atau klik produk di sebelah kiri</p>
                                </div>
                            </td>
                        </tr>

                        <tr v-for="(ct, c) in transaction.items" :key="c">
                            <td>
                                <span class="fw-semibold">{{ ct.name }}</span> <br />
                                <small class="text-muted">Rp {{ formatNumber(ct.without_discount) }}</small>
                            </td>
                            <td id="listPrice">
                                <InputText
                                    type="number"
                                    v-model="ct.qty"
                                    style="width: 60px"
                                    placeholder="Qty"
                                    class="mt-1 form-control-sm text-center"
                                />
                            </td>
                            <td class="fw-bold">Rp {{ formatNumber(ct.subtotal) }}</td>
                            <td>
                                <a
                                    id="31"
                                    class="btn btn-sm btn-outline-danger me-1 p-1 rounded-circle"
                                    @click="deleteCart(c)"
                                    title="Hapus"
                                >
                                    <i class="fa fa-times"></i>
                                </a>
                                <a
                                    id="31"
                                    class="btn btn-sm btn-outline-warning p-1 rounded-circle"
                                    @click="modalCart(ct, c)"
                                    title="Edit"
                                >
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width: 50%"></th>
                        <th style="width: 50%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="">
                        <td>
                            <label for="discount">Tipe Diskon</label>
                            <Dropdown
                                v-model="
                                    transaction.payment_information
                                        .discount_type
                                "
                                :options="[
                                    {
                                        label: 'Persentase',
                                        value: 'percent',
                                    },
                                    {
                                        label: 'Nominal',
                                        value: 'fixed',
                                    },
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Pilih Tipe"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </td>
                        <td>
                            <label for="tax">Diskon Transaksi</label>
                            <InputNumber
                                v-model="
                                    transaction.payment_information.discount
                                "
                                style="width: 100%"
                                :max="
                                    transaction.payment_information
                                        .discount_type == 'percent'
                                        ? 100
                                        : transaction.payment_information
                                              .subtotal
                                "
                                placeholder="Masukkan Diskon Transaksi"
                                :prefix="
                                    transaction.payment_information
                                        .discount_type == 'fixed'
                                        ? 'Rp '
                                        : ''
                                "
                                :suffix="
                                    transaction.payment_information
                                        .discount_type == 'percent'
                                        ? ' %'
                                        : ''
                                "
                            />
                        </td>
                    </tr>
                    <tr class="">
                        <td colspan="2">
                            <label for="shipping">Ongkos Kirim</label>
                            <InputNumber
                                v-model="
                                    transaction.payment_information
                                        .shipping_cost
                                "
                                style="width: 100%"
                                placeholder="Masukkan Biaya Ongkos Kirim"
                                prefix="Rp "
                            />
                        </td>
                    </tr>
                </tbody>
            </table>

            <a
                href="javascript:void(0)"
                id="pay_shop"
                @click="modal.payment = true"
                class="card text-white my-2"
            >
                <div>
                    <div class="float-end">
                        <div class="text-white">
                            <input
                                type="hidden"
                                name="fixtotal"
                                id="jumlahtotal"
                                value="0"
                            />
                            <p
                                class="mb-0 font-weight-bold text-white"
                                id="fixTotal"
                            >
                                Rp
                                {{
                                    formatNumber(
                                        transaction.payment_information
                                            .finalTotal
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                    <p class="text-white-50 mb-0 mt-1">
                        <i class="fas fa-shopping-basket h5 text-white"></i>
                    </p>
                </div>
            </a>
            <a
                href="javascript:void(0)"
                class="d-none"
                id="pay_modal_click"
                data-bs-toggle="modal"
                data-bs-target="#paymodal"
            ></a>
        </div>
    </div>

    <!-- Modal Cart -->
    <Dialog
        v-model:visible="modal.cart"
        class="filter-data"
        :header="cart.index == null ? 'Tambah Keranjang' : 'Edit Keranjang'"
        :style="{ width: '35rem' }"
        modal
    >
        <div class="row p-2">
            <div class="col-lg-6 col-sm-12 mt-3">
                <label for="product-name-add" class="form-label"
                    >Nama Produk
                </label>
                <InputText
                    style="width: 100%"
                    v-model="cart.name"
                    readonly
                    type="text"
                    placeholder="Nama Produk"
                />
            </div>
            <div class="col-lg-6 col-sm-12 mt-3">
                <label for="product-name-add" class="form-label"
                    >Harga Produk
                </label>
                <InputNumber
                    v-model="cart.without_discount"
                    style="width: 100%"
                    placeholder="Masukkan Harga"
                />
            </div>
            <div class="col-lg-6 col-sm-12 mt-3">
                <label for="product-name-add" class="form-label"
                    >Satuan Produk
                </label>
                <Dropdown
                    v-model="cart.unit"
                    :options="cart.subunits"
                    style="width: 100%"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Pilih"
                />
            </div>
            <div class="col-lg-6 col-sm-12 mt-3">
                <label for="product-name-add" class="form-label"
                    >Diskon Produk
                </label>
                <InputNumber
                    v-model="cart.discount_amount"
                    style="width: 100%"
                    :maxFractionDigits="2"
                    placeholder="Masukkan Diskon"
                />
            </div>
            <div class="col-lg-6 col-sm-12 mt-3" v-if="tax_option.with_tax">
                <label for="product-name-add" class="form-label"
                    >Pajak Produk
                </label>
                <Dropdown
                    v-model="cart.tax"
                    :options="taxrates"
                    style="width: 100%"
                    optionLabel="code"
                    optionValue="amount"
                    placeholder="Pilih Pajak"
                />
            </div>
        </div>
        <div class="row p-2">
            <div class="col-12 d-flex justify-content-end">
                <button
                    type="button"
                    @click="modal.cart = false"
                    class="btn btn-warning me-2"
                >
                    Batal
                </button>
                <button
                    type="button"
                    @click="addToCart(cart)"
                    class="btn btn-info me-2"
                >
                    {{
                        cart.index == null
                            ? "Tambah Keranjang"
                            : "Simpan Perubahan"
                    }}
                </button>
            </div>
        </div>
    </Dialog>
    <!-- End Modal Cart -->

    <!-- Payment Modal -->
    <Dialog
        v-model:visible="modal.payment"
        class="filter-data"
        :header="'Bayar Transaksi'"
        style="width: 130vh; height: 80vh"
        :position="'top'"
        :modal="true"
        :draggable="false"
    >
        <div class="row p-2" style="height: 70vh">
            <div class="col-lg-8 col-sm-12">
                <div class="row">
                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label for="product-name-add" class="form-label"
                            >Syarat Pembayaran</label
                        >
                        <Dropdown
                            v-model="transaction.general_information.due_limit"
                            :options="terms"
                            optionLabel="name"
                            optionValue="due_date"
                            placeholder="Pilih Opsi"
                            style="width: 100%"
                            class="w-full md:w-14rem"
                        />
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label for="product-name-add" class="form-label"
                            >Gudang</label
                        >
                        <Multiselect
                            v-model="transaction.general_information.warehouse"
                            :options="warehouses"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            :searchable="true"
                            :internal-search="true"
                            :options-limit="50"
                            placeholder="Pilih Gudang"
                            open-direction="bottom"
                            label="name"
                            id="id"
                            track-by="name"
                            :allowEmpty="false"
                            tagPlaceholder=""
                            selectLabel=""
                        ></Multiselect>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label for="product-name-add" class="form-label"
                            >Ekspedisi</label
                        >
                        <Multiselect
                            v-model="transaction.general_information.courier"
                            :options="couriers"
                            :allowEmpty="false"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            :searchable="true"
                            :internal-search="true"
                            :options-limit="50"
                            placeholder="Pilih Ekspedisi"
                            open-direction="bottom"
                            label="name"
                            id="id"
                            track-by="name"
                        ></Multiselect>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label for="product-name-add" class="form-label"
                            >Metode Pembayaran</label
                        >
                        <Multiselect
                            v-model="transaction.payment_information.method"
                            :options="methods"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            :searchable="true"
                            :internal-search="false"
                            :options-limit="50"
                            placeholder="Pilih Metode "
                            open-direction="bottom"
                            label="name"
                            id="id"
                            track-by="name"
                            @search-change="getMethods"
                        ></Multiselect>
                    </div>

                    <div class="col-lg-6 col-sm-12 mt-3">
                        <label for="product-name-add" class="form-label fw-bold"
                            >Nominal Di Bayarkan</label
                        >
                        <div class="p-inputgroup">
                            <InputNumber
                                style="width: 100%"
                                v-model="
                                    transaction.payment_information.pay_total
                                "
                                prefix="Rp "
                                placeholder="0"
                            />
                            <button
                                class="btn btn-sm btn-info"
                                type="button"
                                @click="selectInput"
                                title="Set Uang Pas"
                            >
                                <i class="fa fa-check-circle"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Quick Cash Tender Suggestions -->
                    <div class="col-12 mt-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase mb-1">
                            <i class="fa fa-coins me-1 text-warning"></i> Pilihan Cepat Nominal Uang (Tender)
                        </label>
                        <div class="d-flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="btn btn-sm btn-quick-tender"
                                :class="transaction.payment_information.pay_total == transaction.payment_information.finalTotal ? 'active-exact' : ''"
                                @click="setQuickTender(transaction.payment_information.finalTotal)"
                            >
                                <i class="fa fa-check-circle me-1"></i> Uang Pas (Rp {{ formatNumber(transaction.payment_information.finalTotal) }})
                            </button>
                            <button
                                v-for="nominal in quickNominals"
                                :key="nominal"
                                type="button"
                                class="btn btn-sm btn-quick-tender"
                                :class="transaction.payment_information.pay_total == nominal ? 'active-exact' : ''"
                                @click="setQuickTender(nominal)"
                            >
                                Rp {{ formatNumber(nominal) }}
                            </button>
                        </div>
                    </div>

                    <!-- High-Contrast Kembalian Card -->
                    <div class="col-12 mt-3">
                        <div class="card border-0 shadow-sm" :class="changeAmount >= 0 ? 'bg-success-subtle border-success' : 'bg-danger-subtle border-danger'" style="border-radius: 12px;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-uppercase small fw-bold" :class="changeAmount >= 0 ? 'text-success' : 'text-danger'">
                                        <i :class="changeAmount >= 0 ? 'fa fa-hand-holding-usd me-1' : 'fa fa-exclamation-triangle me-1'"></i>
                                        {{ changeAmount >= 0 ? 'Uang Kembalian' : 'Kurang Bayar' }}
                                    </span>
                                    <h3 class="mb-0 fw-bold" :class="changeAmount >= 0 ? 'text-success' : 'text-danger'">
                                        Rp {{ formatNumber(Math.abs(changeAmount)) }}
                                    </h3>
                                </div>
                                <span class="badge rounded-pill" :class="changeAmount >= 0 ? 'bg-success text-white' : 'bg-danger text-white'" style="font-size: 13px; padding: 6px 12px;">
                                    {{ changeAmount >= 0 ? 'Lunas / Pas' : 'Belum Lunas' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12">
                <h5>Ringkasan Transaksi Penjualan</h5>
                <table class="table-centered border mb-lg-0 table mt-3">
                    <thead class="bg-light">
                        <tr>
                            <td colspan="2">Keterangan</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Diskon Produk</td>
                            <td class="text-right">
                                {{
                                    formatNumber(
                                        this.transaction.payment_information
                                            .discount_product_total
                                    )
                                }}
                            </td>
                        </tr>
                        <tr
                            v-if="
                                transaction.payment_information
                                    .tax_product_total > 0
                            "
                        >
                            <td>PPN</td>
                            <td class="text-right">
                                {{
                                    formatNumber(
                                        this.transaction.payment_information
                                            .tax_product_total
                                    )
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td>Subtotal Produk</td>
                            <td class="text-right">
                                {{
                                    formatNumber(
                                        this.transaction.payment_information
                                            .subtotal
                                    )
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td>Diskon Total</td>
                            <td class="text-right">
                                {{
                                    formatNumber(
                                        this.transaction.payment_information
                                            .discount_total
                                    )
                                }}
                            </td>
                        </tr>

                        <tr>
                            <td>Biaya Kirim</td>
                            <td class="text-right">
                                {{
                                    formatNumber(
                                        this.transaction.payment_information
                                            .shipping_cost
                                    )
                                }}
                            </td>
                        </tr>
                        <tr
                            v-if="
                                transaction.payment_information.service_tax > 0
                            "
                        >
                            <td>Pph 23</td>
                            <td class="text-right">
                                -
                                {{
                                    formatNumber(
                                        transaction.payment_information
                                            .service_tax
                                    )
                                }}
                            </td>
                        </tr>
                        <tr
                            v-if="
                                transaction.payment_information.goverment_tax >
                                0
                            "
                        >
                            <td>Pph 22</td>
                            <td class="text-right">
                                -
                                {{
                                    formatNumber(
                                        transaction.payment_information
                                            .goverment_tax
                                    )
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td>Harga Total :</td>
                            <td class="text-right">
                                <h6>
                                    Rp
                                    {{
                                        formatNumber(
                                            this.transaction.payment_information
                                                .finalTotal
                                        )
                                    }}
                                </h6>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <template #footer>
            <div class="row p-2">
                <div class="col-12 d-flex justify-content-end">
                    <button
                        type="button"
                        @click="modal.payment = false"
                        class="btn btn-warning me-2"
                    >
                        Tutup Tab
                    </button>
                    <button
                        type="button"
                        @click="sendPayment(true)"
                        :disabled="loader.submit"
                        class="btn btn-info me-2"
                    >
                        {{ loader.submit ? "Loading...." : "Simpan" }}
                    </button>
                    <button
                        type="button"
                        @click="sendPayment(true, 'print')"
                        :disabled="loader.submit"
                        class="btn btn-info"
                    >
                        {{ loader.submit ? "Loading...." : "Simpan dan Cetak" }}
                    </button>
                </div>
            </div>
        </template>
    </Dialog>
    <!-- End Payment Modal -->
</template>

<script>
var _ = require("lodash");
import Swal from "sweetalert2";
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
export default {
    name: "pos",
    components: {},
    props: ["search_product", "choose_customer", "choose_user"],
    data() {
        return {
            loader: {
                product: false,
                submit: false,
            },
            modal: {
                cart: false,
                payment: false,
            },
            new_transaction: {},
            cart: {
                index: null,
                item_id: null,
                variation_id: null,
                product_type: "",
                product_id: null,
                name: "",
                qty: 1,
                unit_price: 0,
                without_discount: 0,
                unit_price_inc_tax: 0,
                purchase_price: 0,
                discount_amount: 0,
                discount: 0,
                tax: 0,
                total_discount: 0,
                goverment_tax: 0,
                service_tax: 0,
                discount_type: "fixed",
                discount_subtotal: 0,
                total_tax: 0,
                stock: 0,
                unit: null,
                subtotal: 0,
                subunits: [],
            },
            methods: [],
            terms: [],
            couriers: [],
            users: [],
            customers: [],
            taxrates: [],
            warehouses: [],
            customer: {
                id: "",
                npwp: null,
            },
            tax_option: {
                with_tax: false,
                default: false,
                tax_one: 0,
                tax_two: 0,
                tax_tree: 0,
                customer_type: "",
            },
            products: [],
            transaction: {
                status: "",
                with_pay: false,
                general_information: {
                    id: null,
                    user: {
                        id: "",
                        name: "",
                    },
                    warehouse: {
                        id: "",
                        name: "",
                    },
                    store: {
                        id: "",
                        name: "",
                    },
                    customer: {
                        id: null,
                        name: "",
                        npwp: null,
                    },
                    courier: {
                        id: "",
                        name: "",
                    },
                    address: "",
                    date: null,
                    no_ref: null,
                    status: "",
                    customer_ref: "",
                    due_limit: 0,
                },
                items: [],
                payment_information: {
                    date: "",
                    method: {
                        id: "",
                        name: "",
                    },
                    pay_total: 0,
                    discount_product_total: 0,
                    tax_product_total: 0,
                    goverment_tax: 0,
                    service_tax: 0,
                    subtotal: 0,
                    discount_type: "fixed",
                    discount: 0,
                    discount_total: 0,
                    tax: 0,
                    tax_total: 0,
                    shipping_cost: 0,
                    note: "",
                    finalTotal: 0,
                },
            },
        };
    },
    computed: {
        changeAmount() {
            const pay = parseFloat(this.transaction?.payment_information?.pay_total) || 0;
            const total = parseFloat(this.transaction?.payment_information?.finalTotal) || 0;
            return pay - total;
        },
        quickNominals() {
            const total = parseFloat(this.transaction?.payment_information?.finalTotal) || 0;
            if (total <= 0) return [10000, 20000, 50000, 100000];

            const presets = new Set();
            if (total < 100000) {
                [10000, 20000, 50000, 100000].forEach(n => { if (n > total) presets.add(n); });
            } else if (total < 500000) {
                const ceil50k = Math.ceil(total / 50000) * 50000;
                const ceil100k = Math.ceil(total / 100000) * 100000;
                if (ceil50k > total) presets.add(ceil50k);
                if (ceil100k > total) presets.add(ceil100k);
                presets.add(ceil100k + 50000);
                presets.add(500000);
            } else {
                const ceil50k = Math.ceil(total / 50000) * 50000;
                const ceil100k = Math.ceil(total / 100000) * 100000;
                if (ceil50k > total) presets.add(ceil50k);
                if (ceil100k > total) presets.add(ceil100k);
                presets.add(ceil100k + 100000);
                presets.add(ceil100k + 200000);
            }
            return Array.from(presets).slice(0, 4);
        }
    },
    created() {
        this.settup();
        const today = new Date().toISOString().substr(0, 10);
        this.transaction.general_information.date = today;
        this.transaction.payment_information.date = today;
    },
    methods: {
        setQuickTender(amount) {
            this.transaction.payment_information.pay_total = amount;
            this.playBeep('scan');
        },

        playBeep(type = 'success') {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                if (type === 'success') {
                    osc.frequency.setValueAtTime(880, ctx.currentTime);
                    gain.gain.setValueAtTime(0.12, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.18);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.18);
                } else if (type === 'scan') {
                    osc.frequency.setValueAtTime(1100, ctx.currentTime);
                    gain.gain.setValueAtTime(0.09, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.09);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.09);
                }
            } catch (e) {}
        },

        handleGlobalHotkeys(e) {
            if (e.key === "F2") {
                e.preventDefault();
                const el = document.getElementById("searchProduct");
                if (el) {
                    el.focus();
                    el.select();
                }
            } else if (e.key === "F4") {
                e.preventDefault();
                if (this.transaction.items && this.transaction.items.length > 0) {
                    this.modal.payment = true;
                    this.playBeep('scan');
                }
            } else if (e.key === "Escape") {
                this.modal.cart = false;
                this.modal.payment = false;
            }
        },

        selectInput() {
            this.transaction.payment_information.pay_total =
                this.transaction.payment_information.finalTotal;
            this.playBeep('scan');
        },

        formatNumber(number) {
            const num = parseFloat(number);
            if (isNaN(num)) return "0";
            if (num >= 0) {
                return num.toLocaleString();
            } else {
                return "-" + (-num).toLocaleString();
            }
        },

        broadcastCustomerDisplay() {
            try {
                const payload = {
                    items: this.transaction.items || [],
                    subtotal: this.transaction.payment_information.subtotal || 0,
                    discount_total: this.transaction.payment_information.discount_total || 0,
                    tax_total: this.transaction.payment_information.tax_total || 0,
                    grand_total: this.transaction.payment_information.finalTotal || 0,
                    pay_amount: this.transaction.payment_information.pay_total || 0,
                    change_amount: this.changeAmount || 0,
                };
                if (window.BroadcastChannel) {
                    const bc = new BroadcastChannel('poshub_customer_display');
                    bc.postMessage(payload);
                }
            } catch (e) {}
        },

        deleteCart(index) {
            this.transaction.items.splice(index, 1);
            setTimeout(() => {
                this.calculateSummary();
            }, 500);
        },

        modalCart(product, index = null) {
            if (
                this.transaction.general_information.customer.id == null ||
                this.transaction.general_information.customer.id == ""
            ) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Silahkan pilih pelanggan terlebih dahulu",
                    icon: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ok, Saya Mengerti",
                }).then((result) => {});
                return false;
            }

            if (index == null) {
                this.cart = {
                    index: index,
                    item_id: null,
                    variation_id: product.id,
                    product_type: product.type,
                    product_id: product.product_id,
                    name: product.name,
                    qty: 1,
                    unit_price: product.selling_price,
                    without_discount: product.selling_price,
                    unit_price_inc_tax: product.selling_price,
                    purchase_price: product.purchase_price,
                    discount_amount: 0,
                    discount: 0,
                    tax:
                        this.tax_option.customer_type == "general"
                            ? this.tax_option.tax_one
                            : 0,
                    total_discount: 0,
                    goverment_tax: 0,
                    service_tax: 0,
                    discount_type: "fixed",
                    discount_subtotal: 0,
                    total_tax: 0,
                    stock: product.stock,
                    unit: product.unit_sale,
                    subtotal: product.selling_price,
                    subunits: product.units,
                };
            } else {
                this.cart = {
                    index: index,
                    item_id: null,
                    variation_id: product.variation_id,
                    product_type: product.product_type,
                    product_id: product.product_id,
                    name: product.name,
                    qty: product.qty,
                    unit_price: product.unit_price,
                    without_discount: product.without_discount,
                    unit_price_inc_tax: product.unit_price_inc_tax,
                    purchase_price: product.purchase_price,
                    discount_amount: product.discount_amount,
                    discount: product.discount,
                    tax: product.tax,
                    total_discount: product.total_discount,
                    goverment_tax: product.goverment_tax,
                    service_tax: product.service_tax,
                    discount_type: "fixed",
                    discount_subtotal: product.discount_subtotal,
                    total_tax: product.total_tax,
                    stock: product.stock,
                    unit: product.unit,
                    subtotal: product.subtotal,
                    subunits: product.subunits,
                };
            }

            this.modal.cart = true;
        },

        addToCart() {
            var product = this.cart;
            if (product.index != null) {
                this.transaction.items[product.index] = {
                    item_id: null,
                    variation_id: product.variation_id,
                    product_type: product.product_type,
                    product_id: product.product_id,
                    name: product.name,
                    qty: product.qty,
                    unit_price: product.unit_price,
                    without_discount: product.without_discount,
                    unit_price_inc_tax: product.unit_price_inc_tax,
                    purchase_price: product.purchase_price,
                    discount_amount: product.discount_amount,
                    discount: product.discount,
                    tax: product.tax,
                    total_discount: product.total_discount,
                    goverment_tax: product.goverment_tax,
                    service_tax: product.service_tax,
                    discount_type: "fixed",
                    discount_subtotal: product.discount_subtotal,
                    total_tax: product.total_tax,
                    stock: product.stock,
                    unit: product.unit,
                    subtotal: product.subtotal,
                    subunits: product.subunits,
                };
            } else {
                this.transaction.items.push({
                    item_id: null,
                    variation_id: product.variation_id,
                    product_type: product.product_type,
                    product_id: product.product_id,
                    name: product.name,
                    qty: 1,
                    unit_price: product.unit_price,
                    without_discount: product.without_discount,
                    unit_price_inc_tax: product.unit_price_inc_tax,
                    purchase_price: product.purchase_price,
                    discount_amount: product.discount_amount,
                    discount: product.discount,
                    tax: product.tax,
                    total_discount: product.total_discount,
                    goverment_tax: product.goverment_tax,
                    service_tax: product.service_tax,
                    discount_type: "fixed",
                    discount_subtotal: product.discount_subtotal,
                    total_tax: product.total_tax,
                    stock: product.stock,
                    unit: product.unit,
                    subtotal: product.subtotal,
                    subunits: product.subunits,
                });
            }

            this.modal.cart = false;
            this.calculateSummary();
        },

        calculateSummary() {
            var discountTotal = 0;
            var taxTotal = 0;
            var subtotal = 0;
            var govermentTax = 0;
            var serviceTax = 0;
            var discountSubtotal =
                this.transaction.payment_information.discount_total;
            var discountSales = 0;

            if (discountSubtotal > 0) {
                discountSales =
                    discountSubtotal / this.transaction.items.length;
            }

            for (var i in this.transaction.items) {
                var detail = this.transaction.items[i];
                detail.discount_subtotal = discountSales;
                var taxdiscount = 0;
                var goverment = 0;
                var service = 0;

                if (detail.tax > 0 && discountSales > 0) {
                    taxdiscount =
                        (detail.tax / 100) *
                        (this.tax_option.default == true
                            ? discountSales / (detail.tax / 100 + 1)
                            : discountSales);
                }

                if (detail.goverment_tax > 0) {
                    goverment =
                        (this.tax_option.tax_tree / 100) * discountSales;
                }

                if (detail.service_tax > 0) {
                    service = (this.tax_option.tax_two / 100) * discountSales;
                }

                discountTotal += detail.total_discount * detail.qty;
                taxTotal += detail.total_tax * detail.qty - taxdiscount;
                subtotal += detail.subtotal;
                govermentTax += detail.goverment_tax * detail.qty - goverment;
                serviceTax += detail.service_tax * detail.qty - service;
            }

            this.transaction.payment_information.discount_product_total =
                discountTotal;
            this.transaction.payment_information.tax_product_total = taxTotal;
            this.transaction.payment_information.subtotal = subtotal;
            this.transaction.payment_information.goverment_tax = govermentTax;
            this.transaction.payment_information.service_tax = serviceTax;
        },

        async getProducts(query) {
            this.loader.product = true;

            try {
                const response = await ApiData.get(
                    `app/inventory/components/variations?name=${query}`
                );
                var data = response.data;
                this.products = data.variations;
                this.loader.product = false;
            } catch (error) {
                this.loader.product = false;
                console.log(error);
            }
        },

        updateItem(index) {
            var details = this.transaction.items[index];
            var qtyItem = details.qty;
            var taxrate = details.tax;
            var totaltax = 0;
            var detailUnitPo = null;
            detailUnitPo = details.subunits.filter((item) => {
                if (details.unit == item.id) {
                    return item;
                }
            });

            if (detailUnitPo != null) {
                if (detailUnitPo.length > 0) {
                    if (detailUnitPo[0].value != null) {
                        qtyItem = parseInt(details.qty) * detailUnitPo[0].value;
                    }
                }
            }

            details.discount_total = details.discount_amount;
            details.unit_price =
                details.without_discount - details.discount_total;

            if (
                details.unit_price > 0 &&
                taxrate > 0 &&
                this.tax_option.with_tax == true
            ) {
                if (this.tax_option.default == true) {
                    var modalharga = details.unit_price / (1 + taxrate / 100);
                    totaltax = (taxrate / 100) * modalharga;
                    details.total_tax = totaltax;
                    details.unit_price_inc_tax = details.unit_price;
                } else {
                    totaltax = (taxrate / 100) * details.unit_price;
                    details.total_tax = totaltax;
                    details.unit_price_inc_tax =
                        details.unit_price + details.total_tax;
                }
            } else {
                details.unit_price_inc_tax = details.unit_price;
            }

            details.service_tax =
                details.product_type == false && details.unit_price > 0
                    ? (this.tax_option.tax_two / 100) * details.unit_price
                    : 0;

            if (details.service_tax > 0) {
                if (
                    this.tax_option.customer_type == "general" &&
                    this.tax_option.with_tax == false
                ) {
                    details.service_tax = 0;
                }
            }

            details.goverment_tax =
                this.tax_option.customer_type != "general" &&
                details.unit_price > 0
                    ? (this.tax_option.tax_tree / 100) * details.unit_price
                    : 0;
            details.subtotal = details.unit_price_inc_tax * qtyItem;

            this.calculateSummary();
        },

        percentaseValidate() {
            this.transaction.payment_information.discount = 100;
            this.updateTransaction();
        },

        updateTransaction() {
            let data = this.transaction.payment_information;
            let afterDisc = this.transaction.payment_information.subtotal;
            let govermentTax =
                this.transaction.payment_information.goverment_tax;
            let serviceTax = this.transaction.payment_information.service_tax;

            if (data.discount > 0) {
                if (data.discount > 0) {
                    if (data.discount_type == "percent") {
                        if (data.discount > 100) {
                            Swal.fire({
                                title: "Peringatan!",
                                text: "Persentase Diskon Tidak Boleh Melebihi 100%",
                                icon: "warning",
                                showCancelButton: false,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Ok, Saya Mengerti",
                            }).then((result) => {
                                this.percentaseValidate();
                            });
                        }

                        var totalDisc =
                            (data.discount / 100) *
                            this.transaction.payment_information.subtotal;
                        this.transaction.payment_information.discount_total =
                            totalDisc;
                        afterDisc =
                            this.transaction.payment_information.subtotal -
                            this.transaction.payment_information.discount_total;
                    } else {
                        this.transaction.payment_information.discount_total =
                            data.discount;
                        afterDisc =
                            this.transaction.payment_information.subtotal -
                            this.transaction.payment_information.discount_total;
                    }
                } else {
                    this.transaction.payment_information.discount_total = 0;
                    afterDisc = this.transaction.payment_information.subtotal;
                }
            } else {
                this.transaction.payment_information.discount_total = 0;
            }

            let afterShipping = afterDisc;
            if (data.shipping_cost != 0) {
                if (data.shipping_cost > 0) {
                    afterShipping = afterDisc + data.shipping_cost;
                } else {
                    afterShipping = afterDisc;
                }
            }

            this.transaction.payment_information.finalTotal =
                afterShipping - (govermentTax + serviceTax);
            //  this.transaction.payment_information.pay_total = afterShipping - (govermentTax + serviceTax);

            this.calculateSummary();
        },

        changeCustomer(e) {
            if (this.transaction.general_information.customer.id != e.id) {
                this.transaction.items = [];
                this.calculateSummary();
            }

            this.transaction.general_information.customer = {
                id: e.id,
                name: e.name,
                type: e.type,
                address: e.address,
                npwp: e.npwp,
            };

            this.transaction.general_information.address = e.address;

            this.tax_option.default = e.tax.default;
            this.tax_option.with_tax = e.tax.tax_option;
            this.tax_option.customer_type = e.type;
            this.transaction.general_information.due_limit = e.tax.due_date;
            this.transaction.general_information.address = e.address;
        },

        async settup() {
            try {
                const response = await ApiData.get(`app/master/tax/sett`);
                var data = response.data;
                this.tax_option = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getTerm() {
            try {
                const response = await ApiData.get(`app/master/term?limit=30`);
                var data = response.data;
                this.terms = data.terms;
            } catch (error) {
                console.log(error);
            }
        },

        async getCouriers() {
            try {
                const response = await ApiData.get(
                    `app/transactions/components/couriers`
                );
                var data = response.data;
                this.couriers = data.couriers;
            } catch (error) {
                console.log(error);
            }
        },

        async getTaxrate() {
            try {
                const response = await ApiData.get(`app/master/tax`);
                var data = response.data;
                this.taxrates = data.taxrates;
            } catch (error) {
                console.log(error);
            }
        },

        sendPayment(status, type = "") {
            if (this.transaction.payment_information.pay_total == 0) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Uang pembayaran belum di isi, apakah ingin tetap melanjutkan ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ok",
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.processTransaction(status, type);
                    } else {
                        Swal.fire("Membatalkan Proses Input Data");
                    }
                });
            } else {
                this.processTransaction(status, type);
            }
        },

        processTransaction(status, type = "") {
            this.transaction.status = "received";

            if (this.transaction.payment_information.pay_total > 0) {
                this.transaction.with_pay = true;
            } else {
                this.transaction.with_pay = false;
            }

            const filteredMinusProduct = this.transaction.items.filter(
                (item) => item.qty > item.stock
            );

            const filteredLowPrice = this.transaction.items.filter(
                (item) => item.without_discount < item.purchase_price
            );

            if (
                filteredMinusProduct.length == 0 &&
                filteredLowPrice.length == 0
            ) {
                this.createData(type);
            } else {
                if (filteredLowPrice.length > 0) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Salah satu produk di bawah hpp, apakah anda yakin untuk melanjutkannya ?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ok",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.createData(type);
                        } else {
                            Swal.fire("Membatalkan Proses Input Data");
                        }
                    });
                }

                if (filteredMinusProduct.length > 0) {
                    Swal.fire({
                        title: "Peringatan!",
                        text: "Salah satu Stok barang, tidak mencukupi",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ok",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.createData(type);
                        } else {
                            Swal.fire("Membatalkan Proses Input Data");
                        }
                    });
                }
            }
        },

        createData(type = "") {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            ApiData.post("app/transactions/sales/create", this.transaction)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    this.playBeep("success");
                    NProgress.done();

                    this.modal.payment = false;
                    this.loader.submit = false;
                    this.resetData();
                    if (type == "print") {
                        var data = response.data.detail;
                        this.new_transaction.qty_sell = data.qty_sell;
                        this.new_transaction.general_information =
                            data.general_information;
                        this.new_transaction.payment_information =
                            data.payment_information;
                        this.new_transaction.items = data.items;
                        this.printFaktur();
                    }
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
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
                        this.new_transaction.general_information.store.name
                    }</h2>
                    <p>${
                        this.new_transaction.general_information.store.address
                    }</p>
                    <p>Telp ${
                        this.new_transaction.general_information.store.phone
                    }</p>
                    <div style="border-top:1px solid black; border-bottom: 1px solid black; margin-top:1px; margin-right:10px;">
                    <p>Kepada :</p>
                    </div>
                    <p>
                    <b>${
                        this.new_transaction.general_information.customer.name
                    }</b><br />
                    ${this.new_transaction.general_information.customer.address}
                    </p>
                </div>
                <div class="invoice-info" style="min-width: 250px;">
                    <h2 style="text-align:center; font-size:14px !important;">Faktur Penjualan</h2>
                    <table style="border:1px solid black; width:100%">
                    <tr>
                        <td style="font-size:10px; min-width:100px; border-bottom: 1px solid black; border-right:1px solid black;">
                        <p>Tanggal</p>
                        <p><b>${
                            this.new_transaction.general_information.date
                        }</b></p>
                        </td>
                        <td style="font-size:10px; min-width:100px; border-bottom: 1px solid black;">
                        <p>Nomor</p>
                        <p><b>${
                            this.new_transaction.general_information.no_ref
                        }</b></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:10px; min-width:100px; border-right:1px solid black;">
                        <p>Syarat Pembayaran</p>
                        <p><b>${
                            this.new_transaction.general_information.due_limit
                        } Hari</b></p>
                        </td>
                        <td style="font-size:10px; min-width:100px; vertical-align:top;">
                        <p>Ekspedisi</p>
                        <p><b>${
                            this.new_transaction.general_information.courier
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
                    ${this.new_transaction.items
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
                            this.new_transaction.general_information.note
                        }
                    </td>
                    <td colspan="2"><b>Subtotal DPP</b></td>
                    <td style="text-align: right;">${this.formatNumber(
                        this.new_transaction.payment_information.subtotal
                    )}</td>
                    </tr>
                    <tr>
                    <td colspan="2"><b>Diskon</b></td>
                    <td style="text-align: right;">${this.formatNumber(
                        this.new_transaction.payment_information.discount_total
                    )}</td>
                    </tr>
                    <tr>
                    <td colspan="4" style="text-align:center;">${
                        this.new_transaction.general_information.store
                            .footer_text
                    }</td>
                    <td colspan="2" style="text-align:right;">Total Qty : ${
                        this.new_transaction.qty_sell
                    }</td>
                    <td colspan="2"><b>PPN</b></td>
                    <td style="text-align: right;">${this.formatNumber(
                        this.new_transaction.payment_information.tax_total
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
                        this.new_transaction.payment_information.shipping_cost
                    )}</td>
                    </tr>
                    <tr>
                    <td style="text-align:center; border: none;" rowspan="4">
                        <p style="font-size:11px; margin-top:20px">
                            ${
                                this.new_transaction.general_information?.user
                                    ?.name
                            }
                            </p>
                        </td>
                    <td colspan="5" style="text-align:center; border: none;"></td>
                    <td colspan="2"><b>Total</b></td>
                    <td style="text-align: right;">${this.formatNumber(
                        this.new_transaction.payment_information.finalTotal
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

        resetData() {
            this.transaction.items = [];
            this.transaction.payment_information = {
                date: "",
                method: {
                    id: "",
                    name: "",
                },
                pay_total: 0,
                discount_product_total: 0,
                tax_product_total: 0,
                goverment_tax: 0,
                service_tax: 0,
                subtotal: 0,
                discount_type: "fixed",
                discount: 0,
                discount_total: 0,
                tax: 0,
                tax_total: 0,
                shipping_cost: 0,
                note: "",
                finalTotal: 0,
            };

            this.calculateSummary();
        },

        async getWarehouse() {
            try {
                const response = await ApiData.get(
                    `app/settings/warehouses/search`
                );
                var data = response.data;
                this.warehouses = data.warehouses;
                this.warehouse = data.warehouses[0];
                this.transaction.general_information.warehouse = this.warehouse;
            } catch (error) {
                console.log(error);
            }
        },

        async getMethods(query) {
            this.loader.method = true;
            try {
                const response = await ApiData.get(
                    `app/master/payment-method?name=${query}&`
                );
                var data = response.data;
                this.methods = data.methods;
                this.loader.method = false;
            } catch (error) {
                console.log(error);
            }
        },
    },
    mounted: function () {
        this.getProducts("");
        this.getTaxrate();
        this.getTerm();
        this.getCouriers();
        this.getWarehouse();
        this.getMethods("");
        window.addEventListener("keydown", this.handleGlobalHotkeys);
    },
    beforeUnmount() {
        window.removeEventListener("keydown", this.handleGlobalHotkeys);
    },
    watch: {
        search_product(newProduct) {
            this.getProducts(newProduct);
        },

        choose_customer(newCustomer) {
            this.changeCustomer(newCustomer);
        },

        choose_user(pengguna) {
            this.transaction.general_information.user = pengguna;
        },

        "transaction.items": {
            handler: function (newVal, oldVal) {
                newVal.forEach((item, index) => {
                    this.updateItem(index);
                });
                this.broadcastCustomerDisplay();
            },
            deep: true,
            immediate: true,
        },
        "transaction.payment_information": {
            handler: function (newVal, oldVal) {
                this.updateTransaction();
                this.broadcastCustomerDisplay();
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>
<style>
.p-dialog-mask {
    z-index: 1060 !important;
}
</style>
