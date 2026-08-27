<template>
    <div class="col-lg-12 mt-4">
        <div class="row">
            <div class="col-10" v-if="!loader.data">
                <div class="card custom-card">
                    <div
                        class="card-header d-flex justify-content-between d-block"
                    >
                        <div
                            class="h5 mb-0 d-sm-flex d-bllock align-items-center"
                        >
                            <div class="ms-sm-2 ms-0 mt-sm-0 mt-2">
                                <div class="h6 fw-semibold mb-0">
                                    Transaksi Penjualan :
                                    <span class="text-primary"
                                        >#{{
                                            detail.general_information.no_ref
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body add-product p-0">
                        <div class="p-4">
                            <div class="row gx-5">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <!-- Gudang -->
                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="transaction-ref"
                                                class="form-label"
                                                >Pilih Gudang</label
                                            >
                                            <Field
                                                :rules="{
                                                    required: true,
                                                }"
                                                v-slot="{ errors }"
                                                v-model="transaction.warehouse"
                                                name="Pilih Gudang"
                                            >
                                                <Multiselect
                                                    v-model="
                                                        transaction.warehouse
                                                    "
                                                    :options="warehouses"
                                                    :allowEmpty="false"
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
                                                ></Multiselect>
                                                <div class="fs-sm text-danger">
                                                    {{ errors[0] }}
                                                </div>
                                            </Field>
                                        </div>
                                        <!-- End Gudang -->

                                        <!-- Product -->
                                        <div class="col-lg-6">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Cari dan Pilih Produk</label
                                            >
                                            <span class="p-fluid">
                                                <div class="p-inputgroup">
                                                    <Multiselect
                                                        v-model="
                                                            selected_product
                                                        "
                                                        :options="
                                                            detail
                                                                .product_information
                                                                .items
                                                        "
                                                        :multiple="false"
                                                        :close-on-select="true"
                                                        :clear-on-select="true"
                                                        :preserve-search="true"
                                                        :searchable="true"
                                                        :internal-search="false"
                                                        :show-no-results="false"
                                                        :hide-selected="true"
                                                        :options-limit="100"
                                                        :loading="
                                                            loader.product
                                                        "
                                                        placeholder="Ketik Untuk Mencari Produk"
                                                        :showNoOptions="false"
                                                        open-direction="bottom"
                                                        label="name"
                                                        id="id"
                                                        track-by="name"
                                                        :preselect-first="true"
                                                        @select="
                                                            selectedProducts
                                                        "
                                                    ></Multiselect>
                                                </div>
                                            </span>
                                        </div>
                                        <!-- End Product -->

                                        <!-- Product List -->
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Barang</th>
                                                            <th>Qty</th>
                                                            <th>Satuan</th>
                                                            <th>Harga</th>

                                                            <th
                                                                v-if="
                                                                    tax_option.with_tax
                                                                "
                                                            >
                                                                Pajak
                                                            </th>
                                                            <th>Total Harga</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr
                                                            v-for="(
                                                                returnitem,
                                                                index
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
                                                                    {{
                                                                        returnitem.name
                                                                    }}
                                                                </p>
                                                                <ul>
                                                                    <li>
                                                                        Qty :
                                                                        {{
                                                                            returnitem
                                                                                .qty
                                                                                .qty
                                                                        }}
                                                                    </li>
                                                                    <li>
                                                                        Return
                                                                        Qty :
                                                                        {{
                                                                            returnitem
                                                                                .qty
                                                                                .qty_return
                                                                        }}
                                                                    </li>
                                                                    <li>
                                                                        Dapat Di
                                                                        Return :
                                                                        {{
                                                                            returnitem
                                                                                .qty
                                                                                .can_return
                                                                        }}
                                                                    </li>
                                                                </ul>
                                                            </td>

                                                            <td>
                                                                <InputText
                                                                    v-model="
                                                                        returnitem.return_qty
                                                                    "
                                                                    type="number"
                                                                    style="
                                                                        width: 100px;
                                                                    "
                                                                    placeholder="Qty Return"
                                                                />
                                                            </td>
                                                            <td>
                                                                <Dropdown
                                                                    :reduce="
                                                                        (
                                                                            label
                                                                        ) =>
                                                                            label.value
                                                                    "
                                                                    v-model="
                                                                        returnitem.unit
                                                                    "
                                                                    :options="
                                                                        returnitem.subunits
                                                                    "
                                                                    style="
                                                                        width: 100%;
                                                                    "
                                                                    optionLabel="name"
                                                                    optionValue="id"
                                                                    placeholder="Pilih"
                                                                />
                                                            </td>

                                                            <td>
                                                                <InputNumber
                                                                    v-model="
                                                                        returnitem.price
                                                                    "
                                                                    style="
                                                                        width: 100%;
                                                                    "
                                                                    prefix="Rp "
                                                                />
                                                            </td>

                                                            <td
                                                                v-if="
                                                                    tax_option.with_tax
                                                                "
                                                            >
                                                                {{
                                                                    returnitem.tax
                                                                }}
                                                            </td>
                                                            <td>
                                                                Rp
                                                                {{
                                                                    formatNumber(
                                                                        returnitem.subtotal
                                                                    )
                                                                }}
                                                            </td>
                                                            <td>
                                                                <button
                                                                    class="btn btn-sm btn-danger"
                                                                    type="button"
                                                                    v-tooltip.left="
                                                                        'Hapus Item'
                                                                    "
                                                                    @click="
                                                                        RemoveItem(
                                                                            index
                                                                        )
                                                                    "
                                                                >
                                                                    <i
                                                                        class="fa fa-trash"
                                                                    ></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th
                                                                colspan="6"
                                                                class="text-center"
                                                            >
                                                                <h5>
                                                                    Ringkasan
                                                                </h5>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th
                                                                scope="row"
                                                                colspan="4"
                                                                class="text-right"
                                                            >
                                                                Jumlah Qty Akan
                                                                Dikembalikan
                                                            </th>
                                                            <th colspan="4">
                                                                {{
                                                                    formatNumber(
                                                                        transaction
                                                                            .summary
                                                                            .total_qty
                                                                    )
                                                                }}
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th
                                                                class="text-right"
                                                                colspan="4"
                                                            >
                                                                Subtotal
                                                            </th>
                                                            <th
                                                                class="text-right"
                                                                colspan="2"
                                                            >
                                                                {{
                                                                    formatNumber(
                                                                        transaction
                                                                            .summary
                                                                            .subtotal_return
                                                                    )
                                                                }}
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th
                                                                scope="row"
                                                                colspan="4"
                                                                class="text-right"
                                                            >
                                                                Pengembalian
                                                                Diskon
                                                            </th>
                                                            <th
                                                                class="text-right"
                                                                colspan="2"
                                                            >
                                                                <div
                                                                    class="flex-1"
                                                                >
                                                                    {{
                                                                        detail.discount_percent
                                                                    }}%
                                                                    <p
                                                                        class="mb-0"
                                                                    >
                                                                        - Rp
                                                                        {{
                                                                            formatNumber(
                                                                                transaction.discount_subtotal
                                                                            )
                                                                        }}
                                                                    </p>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                        <tr
                                                            v-if="
                                                                transaction.tax_subtotal >
                                                                0
                                                            "
                                                        >
                                                            <th
                                                                scope="row"
                                                                colspan="4"
                                                                class="text-right"
                                                            >
                                                                Pengembalian PPN
                                                            </th>
                                                            <th
                                                                class="text-right"
                                                                colspan="2"
                                                            >
                                                                <div
                                                                    class="flex-1"
                                                                >
                                                                    <p
                                                                        class="mb-0"
                                                                    >
                                                                        Rp
                                                                        {{
                                                                            formatNumber(
                                                                                transaction.tax_subtotal
                                                                            )
                                                                        }}
                                                                    </p>
                                                                </div>
                                                            </th>
                                                        </tr>

                                                        <tr
                                                            v-if="
                                                                transaction.goverment_tax >
                                                                0
                                                            "
                                                        >
                                                            <th
                                                                scope="row"
                                                                colspan="4"
                                                                class="text-right"
                                                            >
                                                                Pengembalian Pph
                                                                22
                                                            </th>
                                                            <th
                                                                class="text-right"
                                                                colspan="2"
                                                            >
                                                                <div
                                                                    class="flex-1"
                                                                >
                                                                    <p
                                                                        class="mb-0"
                                                                    >
                                                                        Rp
                                                                        {{
                                                                            formatNumber(
                                                                                transaction.goverment_tax
                                                                            )
                                                                        }}
                                                                    </p>
                                                                </div>
                                                            </th>
                                                        </tr>

                                                        <tr
                                                            v-if="
                                                                transaction.service_tax >
                                                                0
                                                            "
                                                        >
                                                            <th
                                                                scope="row"
                                                                colspan="4"
                                                                class="text-right"
                                                            >
                                                                Pengembalian Pph
                                                                23
                                                            </th>
                                                            <th
                                                                class="text-right"
                                                                colspan="2"
                                                            >
                                                                <div
                                                                    class="flex-1"
                                                                >
                                                                    <p
                                                                        class="mb-0"
                                                                    >
                                                                        Rp
                                                                        {{
                                                                            formatNumber(
                                                                                transaction.service_tax
                                                                            )
                                                                        }}
                                                                    </p>
                                                                </div>
                                                            </th>
                                                        </tr>

                                                        <tr class="bg-light">
                                                            <th colspan="4">
                                                                Final Total
                                                            </th>
                                                            <th
                                                                class="text-right"
                                                                colspan="2"
                                                            >
                                                                <h5>
                                                                    Rp
                                                                    {{
                                                                        formatNumber(
                                                                            transaction.final_total
                                                                        )
                                                                    }}
                                                                </h5>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- End Product List -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-2" v-if="!loader.data">
                <button
                    type="button"
                    @click="processTransaction"
                    :disabled="loader.submit"
                    class="btn btn-success btn-block label-btn label-end"
                >
                    {{
                        loader.submit
                            ? "Mohon Tunggu...."
                            : "Proses Return Penjualan"
                    }}
                    <i class="ri-check-line label-btn-icon ms-2"></i>
                </button>
            </div>
            <div class="col-12 d-flex justify-content-center p-4" v-else>
                <ProgressSpinner />
            </div>
        </div>
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
    name: "create_product",
    components: {
        Editor,
        Fieldset,
    },
    data() {
        return {
            methods: [],
            account: [],
            warehouses: [],
            show: {
                payment: true,
            },
            selected_product: null,
            loader: {
                data: false,
                product: false,
                submit: false,
            },
            detail: {
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
                    discount_percent: 0,
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
            },
            transaction: {
                ref_no: "",
                date: "",
                discount_percent: 0,
                discount_subtotal: 0,
                tax_percent: 0,
                tax_subtotal: 0,
                subtotal: 0,
                final_total: 0,
                warehouse: {
                    id: "",
                    name: "",
                },
                goverment_tax: 0,
                service_tax: 0,
                summary: {
                    total_qty: 0,
                    subtotal_return: 0,
                },
                payment_information: {
                    payment_methode: "",
                    account_integration: "",
                    payment_date: "",
                    amount: 0,
                    note: "",
                },
                items: [],
            },
            tax_option: {
                with_tax: false,
                default: false,
                tax_one: 0,
                tax_two: 0,
            },
        };
    },
    computed: {},
    created() {
        this.settup();
        this.getDetails();
        this.getWarehouse();
    },
    methods: {
        async settup() {
            try {
                const response = await ApiData.get(`app/master/tax/sett`);
                var data = response.data;
                this.tax_option = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getWarehouse() {
            try {
                const response = await ApiData.get(
                    `app/settings/warehouses/search`
                );
                var data = response.data;
                this.warehouses = data.warehouses;
            } catch (error) {
                console.log(error);
            }
        },

        async getDetails() {
            this.loader.data = true;
            try {
                const response = await ApiData.get(
                    `app/transactions/sales/detail/${this.$route.params.id}`
                );
                this.detail = response.data;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        RemoveItem(index) {
            this.transaction.items.splice(index, 1);
            this.calculateSummary();
        },

        selectedProducts() {
            var detail = this.selected_product;
            var idproduct = false;
            idproduct = this.transaction.items.filter((item) => {
                if (item.id == detail["item_id"]) {
                    return true;
                }
            });

            if (idproduct == false) {
                this.transaction.items.push({
                    id: detail.item_id,
                    variation_id: detail.variation_id,
                    tax: detail.tax,
                    tax_total: detail.total_tax,
                    service_tax: detail.service_tax,
                    goverment_tax: detail.goverment_tax,
                    name: detail.name,
                    price: detail.unit_price,
                    qty: detail.qty_detail,
                    subtotal: 0,
                    unit: detail.first_unit.id,
                    subunits: detail.subunits,
                    return_qty: 0,
                });
            }

            this.selected_product = null;
        },

        updateQtyReturn(index) {
            this.doUpdateQty(index, this);
        },

        doUpdateQty: _.debounce((index, rootInstance) => {
            let details = rootInstance.transaction.items[index];
            let returnqty = rootInstance.transaction.items[index].return_qty;
            let maxqty = rootInstance.transaction.items[index].qty.can_return;
            let price = rootInstance.transaction.items[index].price;
            let tax = details.tax_total;
            let qty = parseInt(returnqty);
            var unit_sales = details.unit;
            let qtywithSubunits = parseInt(returnqty);
            let subtotal = qty * price;
            var detailUnitPo = null;

            detailUnitPo = details.subunits.filter((item) => {
                if (unit_sales == item.id) {
                    return item;
                }
            });

            if (detailUnitPo != null) {
                if (detailUnitPo.length > 0) {
                    if (detailUnitPo[0].value != null) {
                        subtotal =
                            parseInt(qty) * detailUnitPo[0].value * price;
                        qtywithSubunits = parseInt(qty) * detailUnitPo[0].value;
                    }
                }
            }

            if (qtywithSubunits > maxqty) {
                Swal.fire({
                    title: "Peringatan!",
                    text:
                        "Maaf, Jumlah Qty yang dapat dikembalikan hanya " +
                        maxqty +
                        " Dalam Satuan " +
                        details.unit_name,
                    icon: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ok, Saya Mengerti",
                }).then((result) => {
                    let subtotal = maxqty * price;
                    rootInstance.transaction.items[index].subtotal = subtotal;
                    rootInstance.transaction.items[index].return_qty = maxqty;
                    if (detailUnitPo != null) {
                        if (detailUnitPo.length > 0) {
                            if (maxqty * detailUnitPo[0].value > maxqty) {
                                rootInstance.transaction.items[
                                    index
                                ].subtotal = 0;
                                rootInstance.transaction.items[
                                    index
                                ].return_qty = 0;
                            }
                        }
                    }
                    rootInstance.calculateSummary();
                });
                return;
            }

            rootInstance.transaction.items[index].subtotal = subtotal;
            rootInstance.calculateSummary();
        }, 200),

        calculateSummary() {
            var qty_total_return = 0;
            var subtotal = 0;
            var discountTotal = 0;
            var taxTotal = 0;
            var discountTambahan = 0;
            var serviceTax = 0;
            var govermentTax = 0;
            for (var i in this.transaction.items) {
                qty_total_return += parseInt(
                    this.transaction.items[i].return_qty
                );
                taxTotal +=
                    parseFloat(this.transaction.items[i].tax_total) *
                    parseInt(this.transaction.items[i].return_qty);
                subtotal += this.transaction.items[i].subtotal;
                govermentTax +=
                    parseFloat(this.transaction.items[i].goverment_tax) *
                    parseInt(this.transaction.items[i].return_qty);
                serviceTax +=
                    parseFloat(this.transaction.items[i].service_tax) *
                    parseInt(this.transaction.items[i].return_qty);
            }
            this.transaction.summary.total_qty = qty_total_return;
            this.transaction.summary.subtotal_return = subtotal;

            if (subtotal > 0) {
                discountTotal =
                    (this.detail.payment_information.discount_percent / 100) *
                    parseInt(subtotal);
            }

            this.transaction.discount_percent =
                this.detail.payment_information.discount_percent;
            this.transaction.discount_subtotal = discountTotal;
            this.transaction.tax_percent = this.detail.payment_information.tax;
            this.transaction.tax_subtotal = taxTotal;
            this.transaction.subtotal =
                subtotal - (discountTotal + discountTambahan);
            this.transaction.goverment_tax = govermentTax;
            this.transaction.service_tax = serviceTax;
            this.transaction.final_total =
                subtotal +
                govermentTax +
                serviceTax -
                (discountTotal + discountTambahan);
        },

        processTransaction() {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Transaksi Return Penjualan yang telah di lakukan tidak dapat di batalkan kembali",
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
                    ApiData.post(
                        "app/transactions/sales/returns/create/" +
                            this.$route.params.id,
                        this.transaction
                    )
                        .then((response) => {
                            NProgress.done();
                            this.$handleSuccessResponse(response.data.message);
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
        "transaction.items": {
            handler: function (newVal, oldVal) {
                newVal.forEach((item, index) => {
                    this.updateQtyReturn(index);
                });
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>
