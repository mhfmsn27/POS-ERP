<template>
    <Form
        @submit="createofferProducts()"
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
                                                <th>Nama Barang</th>
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
                                                    class="btn btn-danger btn-sm mr-3"
                                                    type="button"
                                                    v-tooltip.top="'Hapus Item'"
                                                    @click="RemoveItem(index)"
                                                >
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <button
                                                    class="btn btn-info btn-sm"
                                                    type="button"
                                                    v-tooltip.top="
                                                        'Histori Harga'
                                                    "
                                                    @click="
                                                        getHistories(
                                                            item.variation_id
                                                        )
                                                    "
                                                >
                                                    <i
                                                        class="fa fa-history"
                                                    ></i>
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
                                    >Catatan / Keterangan
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
                        class="fe fe-save label-btn-icon ms-2"
                        style="font-size: 30px"
                    ></i>
                </button>
                <div
                    class="btn-group-vertical mt-4 btn-block"
                    role="group"
                    aria-label="Button group with nested dropdown"
                >
                    <div class="btn-group" role="group">
                        <button
                            v-tooltip.top="'Cetak Transaksi'"
                            id="btnGroupDrop2"
                            type="button"
                            class="btn btn-primary dropdown-toggle"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <i
                                class="fa fa-print label-btn-icon ms-2"
                                style="font-size: 30px"
                            ></i>
                        </button>
                        <div
                            class="dropdown-menu"
                            aria-labelledby="btnGroupDrop2"
                        >
                            <a
                                class="dropdown-item"
                                @click="saveAndPrint()"
                                href="javascript:void(0);"
                                >Penawaran Pesanan</a
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Form>

    <Dialog
        v-model:visible="history.modal"
        header="Histori Harga Penjualan"
        :style="{ width: '70rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row p-2">
            <div class="col-12">
                <div class="table-responsive">
                    <DataTable
                        :value="history.data"
                        :paginator="false"
                        :rows="20"
                        :rowsPerPageOptions="[20, 50, 100]"
                        :lazy="true"
                        :totalRecords="history.totalRows"
                        @page="onPageChange($event)"
                        class="table"
                    >
                        <Column field="date" header="Tanggal"></Column>
                        <Column field="ref_no" header="Nomor Ref"> </Column>
                        <Column field="name" header="Produk"></Column>
                        <Column field="customer" header="Pelanggan"></Column>
                        <Column header="Harga">
                            <template #body="{ data }">
                                {{ formatNumber(data.price) }}
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
import Swal from "sweetalert2";
import { ApiData } from "@/api/server";
import draggable from "vuedraggable";
import { v4 as uuidv4 } from "uuid";
export default {
    name: "package_transaction",
    components: {
        draggable,
    },
    data() {
        return {
            couriers: [],
            customers: [],
            warehouses: [],
            transaction: {
                customer: {
                    id: "",
                    name: "",
                },
                warehouse: {
                    id: "",
                    name: "",
                },
                courier: {
                    id: "",
                    name: "",
                },
                address: "",
                date: "",
                ref_no: "",
                note: "",
                items: [],
                summary: {
                    subtotal: 0,
                    discount: 0,
                    tax: 0,
                    total: 0,
                },
            },
            history: {
                modal: false,
                totalRows: 0,
                data: [],
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
    created() {},
    methods: {
        onDragEnd() {
            this.transaction.items.forEach((item, index) => {
                item.item_position = index + 1;
            }); 
        },
        
        async getHistories(variationid) {
            try {
                const response = await ApiData.get(
                    `app/transactions/sales/price-history?variation=${variationid}`
                );
                var data = response.data;
                this.history = {
                    modal: true,
                    totalRows: data.totalRows,
                    data: data.sells,
                };
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
                this.transaction.warehouse = data.warehouses[0];
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

        selectCustomer(e) {
            this.transaction.address = e?.address;
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

        createofferProducts(type = "") {
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
                                    "app/transactions/sales/offer/create",
                                    this.transaction
                                )
                                    .then((response) => {
                                        this.$handleSuccessResponse(
                                            response.data.message
                                        );
                                        NProgress.done();

                                        if (type == "print") {
                                            window.open(
                                                "/pos-admin/prints/faktur-Penawaran/" +
                                                    response.data.transaction,
                                                "_blank"
                                            );
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
                                Swal.fire("Membatalkan Proses Input Data");
                            }
                        });
                    } else {
                        this.loader.submit = true;
                        NProgress.start();
                        NProgress.set(0.1);
                        ApiData.post(
                            "app/transactions/sales/offer/create",
                            this.transaction
                        )
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                NProgress.done();

                                if (type == "print") {
                                    window.open(
                                        "/pos-admin/prints/faktur-Penawaran/" +
                                            response.data.transaction,
                                        "_blank"
                                    );
                                } else {
                                    window.parent.postMessage({
                                        action: "closeActiveMenu",
                                        data: "",
                                    });
                                }
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
            this.createofferProducts("print");
        },
    },
    mounted: function () {
        this.getCustomers("");
        this.getWarehouse();
        this.getCouriers();
        const today = new Date().toISOString().substr(0, 10);
        this.transaction.date = today;
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
