<template>
    <Form
        @submit="updateofferProducts()"
        ref="ValidationTransactions"
        class="col-12"
    >
        <div class="row">
            <div class="col-11">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row p-3">
                            <div class="col-12">
                                <label for="transaction-ref" class="form-label"
                                    >Penawaran Ke</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="transaction.customer"
                                    name="Pilih Pelanggan"
                                >
                                    <Multiselect
                                        v-model="transaction.customer"
                                        :options="customers"
                                        :multiple="false"
                                        :close-on-select="true"
                                        :clear-on-select="true"
                                        :preserve-search="true"
                                        :searchable="true"
                                        :loading="loader.customer"
                                        :show-labels="false"
                                        :internal-search="true"
                                        :options-limit="50"
                                        placeholder="Pilih Pelanggan"
                                        open-direction="bottom"
                                        label="name"
                                        id="id"
                                        track-by="name"
                                        @select="selectCustomer"
                                        @search-change="getCustomers"
                                    >
                                        <template #singleLabel="props">
                                            <span class="option__title">{{
                                                props.option.name
                                            }}</span>
                                            <br />
                                            <span class="option__small">{{
                                                props.option.address
                                            }}</span>
                                        </template>
                                        <template #option="props">
                                            <div class="option__desc">
                                                <span class="option__title">{{
                                                    props.option.name
                                                }}</span>
                                                <br />
                                                <span class="option__small">{{
                                                    props.option.address
                                                }}</span>
                                            </div>
                                        </template>
                                    </Multiselect>
                                    <div class="fs-sm text-danger">
                                        {{ errors[0] }}
                                    </div>
                                </Field>
                            </div>
                            <div class="col-lg-6 mt-3">
                                <label for="transaction-date" class="form-label"
                                    >Tanggal Transaksi</label
                                >
                                <Calendar
                                    v-model="transaction.date"
                                    style="width: 100%"
                                />
                                <label
                                    for="barcode-product-add"
                                    class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                    >Kosongkan untuk di isi secara otomatis
                                </label>
                            </div>
                            <div class="col-lg-6 mt-3">
                                <label for="transaction-ref" class="form-label"
                                    >Gudang</label
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
                                        v-model="transaction.warehouse"
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
                            <div class="col-lg-6 mt-3">
                                <label for="transaction-ref" class="form-label"
                                    >No Penawaran</label
                                >
                                <InputText
                                    v-model="transaction.ref_no"
                                    style="width: 100%"
                                    placeholder="Masukkan Nomor Penawaran"
                                />
                                <label
                                    for="barcode-product-add"
                                    class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                    >Kosongkan untuk di isi secara otomatis
                                </label>
                            </div>
                            <div class="col-lg-6 mt-3">
                                <label for="transaction-ref" class="form-label"
                                    >Ekspedisi</label
                                >
                                <Multiselect
                                    v-model="transaction.courier"
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

                            <div class="col-12 m-2">
                                <Divider />
                            </div>
                            <div class="col-12">
                                <label for="regular-form-1" class="form-label"
                                    >Cari & Pilih Produk
                                </label>
                                <span class="p-fluid">
                                    <div class="p-inputgroup">
                                        <Multiselect
                                            v-tooltip.top="
                                                'Cari dan Pilih Produk yang akan di Stok Opname, Lalu Klik Tombol hijau di samping untuk memasukkan kedalam table stok opname'
                                            "
                                            v-model="product_select"
                                            :options="products"
                                            :multiple="false"
                                            :showNoOptions="false"
                                            :close-on-select="true"
                                            :clear-on-select="true"
                                            :preserve-search="true"
                                            :searchable="true"
                                            :internal-search="false"
                                            :hide-selected="true"
                                            :options-limit="100"
                                            :loading="loader.product"
                                            placeholder="Ketik Untuk Mencari Produk"
                                            open-direction="bottom"
                                            label="name"
                                            id="id"
                                            track-by="name"
                                            :preselect-first="true"
                                            @select="selectedProduct"
                                            @search-change="getProducts"
                                        >
                                            <template #singleLabel="props">
                                                <span class="option__title">{{
                                                    props.option.name
                                                }}</span>
                                                <br />
                                                <span class="option__small"
                                                    >Stok :
                                                    {{ props.option.stock }} ({{
                                                        props.option.unit_name
                                                    }})
                                                </span>
                                            </template>
                                            <template #option="props">
                                                <div class="option__desc">
                                                    <span
                                                        class="option__title"
                                                        >{{
                                                            props.option.name
                                                        }}</span
                                                    >
                                                    <br />
                                                    <span class="option__small"
                                                        >Stok :
                                                        {{ props.option.stock }}
                                                        ({{
                                                            props.option
                                                                .unit_name
                                                        }})
                                                    </span>
                                                </div>
                                            </template>
                                        </Multiselect>
                                    </div>
                                </span>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="table-responsive">
                                    <table
                                        class="table text-nowrap table-bordered"
                                    >
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Qty</th>
                                                <th>Satuan</th>
                                                <th>Harga</th>
                                                <th>Total Harga</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tr
                                            v-for="(
                                                item, index
                                            ) in transaction.items"
                                            :key="index"
                                        >
                                            <td>
                                                <InputText
                                                    v-model="item.name"
                                                    class="form-control"
                                                />
                                            </td>
                                            <td>
                                                <InputText
                                                    v-model="item.qty"
                                                    style="width: 50px"
                                                    type="number"
                                                    class="form-control"
                                                    placeholder="Qty"
                                                />
                                            </td>
                                            <td>
                                                <Dropdown
                                                    :reduce="
                                                        (label) => label.value
                                                    "
                                                    v-model="item.unit"
                                                    :options="item.subunits"
                                                    style="width: 100%"
                                                    optionLabel="name"
                                                    optionValue="id"
                                                    placeholder="Pilih"
                                                />
                                            </td>
                                            <td>
                                                <InputNumber
                                                    v-model="
                                                        item.without_discount
                                                    "
                                                    style="width: 100%"
                                                    placeholder="Harga Jual"
                                                    prefix="Rp "
                                                />
                                            </td>
                                            <td>
                                                {{
                                                    formatNumber(item.subtotal)
                                                }}
                                            </td>
                                            <td>
                                                <button
                                                    class="btn btn-danger btn-sm"
                                                    type="button"
                                                    v-tooltip.top="'Hapus Item'"
                                                    @click="
                                                        RemoveItem(
                                                            item.id,
                                                            index
                                                        )
                                                    "
                                                >
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tfoot>
                                            <tr>
                                                <th
                                                    colspan="4"
                                                    class="text-right"
                                                >
                                                    Subtotal
                                                </th>
                                                <th
                                                    colspan="2"
                                                    class="text-left"
                                                >
                                                    {{
                                                        formatNumber(
                                                            transaction.summary
                                                                .total
                                                        )
                                                    }}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <Divider />
                            </div>
                            <div class="col-lg-6">
                                <label for="regular-form-1" class="form-label"
                                    >Catatan
                                </label>
                                <textarea
                                    v-model="transaction.note"
                                    class="form-control"
                                ></textarea>
                            </div>
                            <div class="col-lg-6">
                                <label for="regular-form-1" class="form-label"
                                    >Alamat Penawaran
                                </label>
                                <textarea
                                    v-model="transaction.address"
                                    class="form-control"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-1">
                <button
                    type="submit"
                    :disabled="loader.submit"
                    v-tooltip.top="'Simpan Transaksi'"
                    class="btn btn-success btn-block label-btn label-end mr-2"
                >
                    <i
                        class="fe fe-save label-btn-icon"
                        style="font-size: 30px"
                    ></i>
                </button>

                <button
                    type="button"
                    :disabled="loader.submit"
                    @click="saveAndPrint()"
                    v-tooltip.top="'Print dan Simpan'"
                    class="btn btn-success btn-block label-btn label-end mr-2 mt-4"
                >
                    <i
                        class="fe fe-printer label-btn-icon"
                        style="font-size: 30px"
                    ></i>
                </button>
            </div>
        </div>
    </Form>
</template>

<script>
import Swal from "sweetalert2";
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
import imageFragile from "@/assets/images/fragile.webp";
import draggable from "vuedraggable";
import { v4 as uuidv4 } from "uuid";
export default {
    name: "package_transaction",
    components: {
        draggable,
    },
    data() {
        return {
            image: imageFragile,
            customers: [],
            warehouses: [],
            couriers: [],
            transaction: {
                customer: {
                    id: "",
                    name: "",
                },
                courier: {
                    id: "",
                    name: "",
                },
                date: "",
                ref_no: "",
                customer_ref: "",
                note: "",
                address: "",
                items: [],
                summary: {
                    subtotal: 0,
                    discount: 0,
                    tax: 0,
                    total: 0,
                },
            },
            product_select: null,
            loader: {
                product: false,
                submit: false,
                customer: false,
            },
            products: [],
        };
    },
    computed: {},
    mounted() {
        this.getDetail();
        this.getCustomers("");
        this.getWarehouse();
        this.getCouriers();
    },
    methods: {
        onDragEnd() {
            this.transaction.items.forEach((item, index) => {
                item.item_position = index + 1;
            });

            // Optional: Simpan perubahan ke server jika diperlukan
            //   this.saveUpdatedPositions();
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

        async getDetail() {
            try {
                const response = await ApiData.get(
                    `app/transactions/sales/offer/detail/${this.$route.params.id}`
                );
                var data = response.data;
                this.transaction = data.details;
            } catch (error) {
                this.loader.product = false;
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

        async getCustomers(query) {
            this.loader.customer = true;
            try {
                const response = await ApiData.get(
                    `app/crm/components/customers?name=${query}`
                );
                var data = response.data;
                this.customers = data.customers;
                this.loader.customer = false;
            } catch (error) {
                console.log(error);
            }
        },

        updateItem(index) {
            var details = this.transaction.items[index];
            var qtyItem = details.qty;
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

            details.subtotal = details.without_discount * qtyItem;
            this.calculateSummary();
        },

        calculateSummary() {
            var discountTotal = 0;
            var taxTotal = 0;
            var subtotal = 0;
            for (var i in this.transaction.items) {
                var detail = this.transaction.items[i];
                discountTotal += detail.total_discount * detail.qty;
                taxTotal += detail.total_tax;
                subtotal += detail.subtotal;
            }
            this.transaction.summary.discount = discountTotal;
            this.transaction.summary.tax = taxTotal;
            this.transaction.summary.subtotal = subtotal;

            this.transaction.summary.total =
                subtotal + taxTotal - discountTotal;
        },

        RemoveItem(id, index) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Data yang telah di hapus tidak dapat di kembalikan lagi",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.delete(
                        "app/transactions/sales/offer/delete-item/" + id
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            this.transaction.items.splice(index, 1);
                            this.calculateSummary();
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

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },

        selectCustomer(e) {
            this.transaction.address = e?.address;
        },

        updateofferProducts(type = "") {
            this.$refs.ValidationTransactions.validate().then((success) => {
                if (!success) {
                    this.$toast.add({
                        severity: "error",
                        summary: "Terjadi kesalahan",
                        detail: "Silahkan Check kembali form inputan anda",
                        life: 3000,
                    });
                } else {
                    const filteredLowPrice = this.transaction.items.filter(
                        (item) => item.without_discount < item.purchase_price
                    );

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
                                this.loader.submit = true;
                                NProgress.start();
                                NProgress.set(0.1);
                                ApiData.post(
                                    "app/transactions/sales/offer/update/" +
                                        this.$route.params.id,
                                    this.transaction
                                )
                                    .then((response) => {
                                        this.$handleSuccessResponse(
                                            response.data.message
                                        );
                                        NProgress.done();
                                        if (type == "print") {
                                            this.printLabel();
                                        }

                                        window.parent.postMessage({
                                            action: "closeActiveMenu",
                                            data: "",
                                        });
                                    })
                                    .catch((err) => {
                                        NProgress.done();
                                        this.loader.submit = false;
                                        this.$handleErrorResponse(err);
                                    });
                            } else {
                                Swal.fire("Membatalkan Proses Update Data");
                            }
                        });
                    } else {
                        this.loader.submit = true;
                        NProgress.start();
                        NProgress.set(0.1);
                        ApiData.post(
                            "app/transactions/sales/offer/update/" +
                                this.$route.params.id,
                            this.transaction
                        )
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                NProgress.done();

                                if (type == "print") {
                                    this.printLabel();
                                }

                                window.parent.postMessage({
                                    action: "closeActiveMenu",
                                    data: "",
                                });
                            })
                            .catch((err) => {
                                NProgress.done();
                                this.loader.submit = false;
                                this.$handleErrorResponse(err);
                            });
                    }
                }
            });
        },

        saveAndPrint() {
            this.updateofferProducts("print");
        },

        printLabel() {
            const receiptHTML = `<!DOCTYPE html>
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
                    <h1>${this.transaction.store.name || ""}</h1>
                    <h1>${this.transaction.store.phone || ""}</h1>
                </header>
                <section class="recipient-info">
                    <p><strong>To :</strong> <br /></p>
                    <p>
                    <strong>${
                        this.transaction.customer.name || ""
                    }</strong> <br />
                    ${this.transaction.address || ""}
                    </p>
                    <p><strong>${
                        this.transaction.customer.phone || ""
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
                        ${this.transaction.items
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
                            Ekspedisi : ${this.transaction.courier.name || ""}
                        </td>
                        </tr>
                        <tr>
                        <td colspan="2">No.Faktur : ${
                            this.transaction.ref_no
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
            printWindow.document.write(receiptHTML);
            printWindow.document.close();

            // Wait for the content to load and then trigger print
            printWindow.onload = function () {
                printWindow.print();
                printWindow.onafterprint = function () {
                    printWindow.close();
                };
            };
        },

        selectedProduct() {
            if (this.product_select != null) {
                var product = this.product_select;
                this.transaction.items.push({
                    id: uuidv4(),
                    variation_id: product.id,
                    product_id: product.product_id,
                    name: product.name,
                    qty: 1,
                    unit_price: product.selling_price,
                    without_discount: product.selling_price,
                    purchase_price: product.purchase_price,
                    discount_amount: 0,
                    discount: 0,
                    tax: 0,
                    total_discount: 0,
                    discount_type: "fixed",
                    total_tax: 0,
                    unit: product.selling_price,
                    subtotal: product.selling_price,
                    subunits: product.units,
                    item_position: this.transaction.items.length + 1,
                });

                this.product_select = null;
                this.calculateSummary();
            }
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
    },
    watch: {
        "transaction.items": {
            handler: function (newVal, oldVal) {
                newVal.forEach((item, index) => {
                    this.updateItem(index);
                });
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>
<style>
.draggable-item {
    cursor: move;
}

.draggable-item.dragging {
    background-color: #f0f0f0;
    opacity: 0.8;
}
</style>
