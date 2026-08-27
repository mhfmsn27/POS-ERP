<template>
    <Form
        @submit="createReceivedPurchase()"
        ref="ValidationTransactions"
        class="col-12"
    >
        <div class="row">
            <div class="col-11">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row p-3">
                            <div class="col-lg-12">
                                <label for="transaction-ref" class="form-label"
                                    >Penerimaan Dari</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="transaction.supplier"
                                    name="Pilih Supplier"
                                >
                                    <Multiselect
                                        v-model="transaction.supplier"
                                        :options="suppliers"
                                        :multiple="false"
                                        :close-on-select="true"
                                        :clear-on-select="true"
                                        :preserve-search="true"
                                        :searchable="true"
                                        :loading="loader.supplier"
                                        :internal-search="true"
                                        :options-limit="50"
                                        placeholder="Pilih Kategori"
                                        open-direction="bottom"
                                        label="name"
                                        id="id"
                                        track-by="name"
                                        @select="selectSupplier"
                                        @search-change="getSuppliers"
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
                                    >Tanggal</label
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

                            <div class="col-lg-6">
                                <label for="transaction-ref" class="form-label"
                                    >No.Form</label
                                >
                                <InputText
                                    v-model="transaction.ref_no"
                                    style="width: 100%"
                                    placeholder="Masukkan Nomor Referensi"
                                />

                                <label
                                    for="barcode-product-add"
                                    class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                    >Kosongkan untuk di isi secara otomatis
                                </label>
                            </div>

                            <div class="col-lg-6">
                                <label for="transaction-ref" class="form-label"
                                    >No. Penerimaan</label
                                >
                                <InputText
                                    v-model="transaction.supplier_ref"
                                    style="width: 100%"
                                    placeholder="Masukkan Nomor Penerimaan"
                                />
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
                                            :close-on-select="true"
                                            :clear-on-select="true"
                                            :preserve-search="true"
                                            :searchable="true"
                                            :internal-search="false"
                                            :show-no-results="false"
                                            :hide-selected="true"
                                            :options-limit="100"
                                            :loading="loader.product"
                                            placeholder="Ketik Untuk Mencari Produk"
                                            :showNoOptions="false"
                                            open-direction="bottom"
                                            label="name"
                                            id="id"
                                            track-by="name"
                                            :preselect-first="true"
                                            @select="selectedProduct"
                                            @search-change="getProducts"
                                        ></Multiselect>
                                        <Button
                                            icon="fa fa-floppy-o"
                                            class="p-button-info"
                                            @click="getPoTransaction"
                                            type="button"
                                        />
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
                                                <th>Harga</th>
                                                <th>Qty</th>
                                                <th>Satuan</th>
                                                <th>Subtotal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="(
                                                    item, index
                                                ) in transaction.items"
                                                :key="index"
                                            >
                                                <td>
                                                    {{ item.name }}
                                                </td>
                                                <td>
                                                    <InputNumber
                                                        v-model="
                                                            item.without_discount
                                                        "
                                                        style="width: 100%"
                                                        placeholder="Harga Modal"
                                                        prefix="Rp "
                                                    />
                                                </td>
                                                <td>
                                                    <InputText
                                                        v-model="item.qty"
                                                        style="width: 100px"
                                                        type="number"
                                                        class="form-control"
                                                        placeholder="Qty "
                                                    />
                                                </td>
                                                <td>
                                                    <Dropdown
                                                        :reduce="
                                                            (label) =>
                                                                label.value
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
                                                    {{
                                                        formatNumber(
                                                            item.subtotal
                                                        )
                                                    }}
                                                </td>

                                                <td>
                                                    <button
                                                        class="btn btn-danger btn-sm"
                                                        type="button"
                                                        v-tooltip.top="
                                                            'Hapus Item'
                                                        "
                                                        @click="
                                                            RemoveItem(index)
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
                                    >Catatan / Keterangan
                                </label>
                                <textarea
                                    v-model="transaction.note"
                                    class="form-control"
                                ></textarea>
                            </div>
                            <div class="col-lg-6">
                                <label for="regular-form-1" class="form-label"
                                    >Alamat Penerimaan
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
                        class="fe fe-save label-btn-icon ms-2"
                        style="font-size: 30px"
                    ></i>
                </button>
                <button
                    type="button"
                    :disabled="loader.submit"
                    @click="saveandPrintPenerimaan('print')"
                    v-tooltip.top="'Print Penerimaan'"
                    class="btn btn-success btn-block label-btn label-end mr-2"
                >
                    <i
                        class="fa fa-print label-btn-icon ms-2"
                        style="font-size: 30px"
                    ></i>
                </button>
            </div>
        </div>
    </Form>

    <Dialog
        v-model:visible="po_transaction.modal"
        header="Data Transaksi PO ( Pemesanan ) "
        :style="{ width: '70rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row p-2">
            <div class="col-12">
                <div>
                    <label class="form-label">Cari Transaksi</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"
                            ><i class="fa fa-search"></i>
                        </span>
                        <input
                            type="text"
                            v-model="po_transaction.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari PO...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <DataTable
                        :value="po_transaction.transactions"
                        :paginator="true"
                        :rows="po_transaction.limit"
                        :rowsPerPageOptions="[20, 50, 100]"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :lazy="true"
                        :totalRecords="po_transaction.totalRows"
                        @page="onPageChange($event)"
                        class="table"
                        :loading="loader.data"
                        responsiveLayout="scroll"
                        sortField="dynamicSortField"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                    >
                        <Column field="date" header="Tanggal"></Column>
                        <Column field="ref_no" header="Nomor Ref"> </Column>
                        <Column field="store.name" header="Toko"></Column>
                        <Column
                            field="supplier.name"
                            header="Supplier"
                        ></Column>

                        <Column header="Subtotal">
                            <template #body="{ data }">
                                {{ formatNumber(data.final_total) }}
                            </template>
                        </Column>

                        <Column field="action" header="Aksi">
                            <template #body="{ data }">
                                <button
                                    class="btn btn-sm btn-success"
                                    type="button"
                                    @click="addThisPo(data)"
                                >
                                    <i class="fa fa-check-circle"></i>
                                </button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </Dialog>
</template>

<script>
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
export default {
    name: "package_transaction",
    components: {},
    data() {
        return {
            suppliers: [],
            warehouses: [],
            transaction: {
                supplier: {
                    id: "",
                    name: "",
                },
                warehouse: {
                    id: "",
                    name: "",
                },
                date: "",
                ref_no: "",
                supplier_ref: "",
                address: "",
                note: "",
                items: [],
                summary: {
                    subtotal: 0,
                    discount: 0,
                    tax: 0,
                    total: 0,
                },
            },
            po_transaction: {
                totalRows: 0,
                page: 1,
                limit: 20,
                modal: false,
                name: "",
                transactions: [],
            },
            product_select: null,
            loader: {
                product: false,
                submit: false,
                supplier: false,
            },
            products: [],
        };
    },
    computed: {},
    created() {
        this.getSuppliers("");
        this.getWarehouse();
        const today = new Date().toISOString().substr(0, 10);
        this.transaction.date = today;
    },
    methods: {
        async getPoTransaction(page = 1) {
            if (
                this.transaction.supplier.id == null ||
                this.transaction.supplier.id == ""
            ) {
                this.$toast.add({
                    severity: "error",
                    summary: "Peringatan",
                    detail: "Silahkan Pilih Supplier Terlebih dahulu",
                    life: 3000,
                });
                return false;
            }

            this.loader.data = true;
            this.po_transaction.page = page;
            try {
                const response = await ApiData.get(
                    `app/transactions/purchases/po?limit=${this.po_transaction.limit}&page=${this.po_transaction.page}&ref=${this.po_transaction.name}&status=open&supplier=${this.transaction.supplier.id}`
                );
                var data = response.data;
                this.po_transaction.transactions = data.transactions;
                this.po_transaction.totalRows = data.totalRows;
                this.loader.data = false;
                this.po_transaction.modal = true;
            } catch (error) {
                console.log(error);
            }
        },

        async addThisPo(data) {
            try {
                const response = await ApiData.get(
                    `app/transactions/purchases/po/detail/${data.id}`
                );

                var items = response.data.details.items;
                for (var i in items) {
                    var product = items[i];
                    this.transaction.items.push({
                        id:product.id,
                        variation_id: product.variation_id,
                        product_id: product.product_id,
                        name: product.name,
                        qty: product.qty,
                        without_discount: product.unit_price,
                        without_discount: product.unit_price,
                        purchase_price_inc_tax: product.unit_price,
                        discount_amount: 0,
                        discount: 0,
                        tax: 0,
                        total_discount: 0,
                        discount_type: "percent",
                        total_tax: 0,
                        unit: product.unit,
                        subtotal: product.subtotal,
                        subunits: product.subunits,
                    });
                }

                this.po_transaction.modal = false;
                this.calculateSummary();
            } catch (error) {
                console.log(error);
            }
        },
        selectSupplier(e) {
            this.transaction.address = e.address;
        },

        async getWarehouse() {
            try {
                const response = await ApiData.get(
                    `app/settings/warehouses/search`
                );
                var data = response.data;
                this.warehouses = data.warehouses;
                this.transaction.warehouse = data.warehouses[0];
            } catch (error) {
                console.log(error);
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

        async getSuppliers(query) {
            this.loader.supplier = true;
            try {
                const response = await ApiData.get(
                    `app/crm/components/suppliers?name=${query}`
                );
                var data = response.data;
                this.suppliers = data.suppliers;
                this.loader.supplier = false;
            } catch (error) {
                console.log(error);
            }
        },

        selectedProduct() {
            if (this.product_select != null) {
                var product = this.product_select;

                this.transaction.items.push({
                    id:null,
                    variation_id: product.id,
                    product_id: product.product_id,
                    name: product.name,
                    qty: 1,
                    without_discount: product.purchase_price,
                    without_discount: product.purchase_price,
                    purchase_price_inc_tax: product.purchase_price,
                    discount_amount: 0,
                    discount: 0,
                    tax: 0,
                    total_discount: 0,
                    discount_type: "percent",
                    total_tax: 0,
                    unit: product.unit_purchase,
                    subtotal: product.purchase_price,
                    subunits: product.units,
                });

                this.product_select = null;
                this.calculateSummary();
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

        RemoveItem(index) {
            this.transaction.items.splice(index, 1);
            this.calculateSummary();
        },

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },

        createReceivedPurchase(type = "") {
            this.$refs.ValidationTransactions.validate().then((success) => {
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
                        "app/transactions/purchases/received/create",
                        this.transaction
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();

                            if (type == "print") {
                                this.printLabel();
                            }

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

        saveandPrintPenerimaan() {
            this.createReceivedPurchase("print");
        },

        printLabel() {
            const receiptHTML = `<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            text-align: center;
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
            margin: 5px 0;
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

<body><div class="container">
            <header style="border-top: 1px solid black; border-bottom: 1px solid black; padding: 5px;">
            <h1>PENERIMAAN PEMBELIAN</h1>
            </header>
            
            <section class="recipient-info">
            <p><strong>Kepada:</strong> <br /> ${
                this.transaction.supplier.name
            }</p>
            <p><strong>Alamat:</strong> <br /> ${
                this.transaction.supplier.address
            }</p>
            <p><strong>No. Form:</strong> ${this.transaction.ref_no}</p>
            <p><strong>Tanggal:</strong> ${this.transaction.date}</p>
            </section>
            
            <section class="item-table">
            <table>
                <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                </tr>
                </thead>
                <tbody>
                     ${this.transaction.items
                         .map(
                             (sell) => `
                        <tr>
                        <td>${sell.name || ""}</td>
                        <td>${this.formatNumber(sell.qty)}</td>
                        </tr>
                        `
                         )
                         .join("")}
                </tbody>
            </table>
            </section>
            
            <section class="signature" style="margin-bottom: 100px;">
            <p><strong>Keterangan:</strong> ${this.transaction.note} </p>
            </section>
        </div></body></html>`;

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
    },
    mounted: function () {},
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
