<template>
    <div class="col-12">
        <Form @submit="createStockOpname()" ref="ValidationStockOpname">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row p-3">
                        <div class="col-lg-4">
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
                        <div class="col-lg-4">
                            <label for="transaction-ref" class="form-label"
                                >Nomor Referensi</label
                            >
                            <InputText
                                v-model="transaction.ref_no"
                                style="width: 100%"
                                type="text"
                                class="form-control"
                                placeholder="Masukkan Nomor Referensi"
                            />
                            <label
                                for="barcode-product-add"
                                class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                >Kosongkan untuk di isi secara otomatis
                            </label>
                        </div>
                        <div class="col-lg-4">
                            <label for="product-name-add" class="form-label"
                                >Pilih Gudang</label
                            >
                            <Multiselect
                                v-model="transaction.warehouse"
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
                                @select="changeWarehouse"
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
                                        v-model="selected_products"
                                        :options="products"
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
                                </div>
                            </span>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="table-responsive">
                                <table class="table text-nowrap table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Produk</th>
                                            <th>Biaya Modal</th>
                                            <th>Stok Tercatat</th>
                                            <th>Aktual Stok</th>
                                            <th>Satuan Unit</th>
                                            <th>Hasil</th>
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
                                                <Field
                                                    v-if="item.type != 'min'"
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="item.qty"
                                                    :name="
                                                        'Harga Modal ' +
                                                        (1 + index)
                                                    "
                                                >
                                                    <InputNumber
                                                        v-model="
                                                            item.purchase_price
                                                        "
                                                        style="width: 100%"
                                                        placeholder="Harga Modal"
                                                        prefix="Rp "
                                                    />
                                                    <div
                                                        class="fs-sm text-danger"
                                                    >
                                                        {{ errors[0] }}
                                                    </div>
                                                </Field>
                                            </td>
                                            <td>
                                                <p class="mb-0">
                                                    {{ item.quantity }}
                                                    {{
                                                        item.unit_name != ""
                                                            ? item.unit_name
                                                            : ""
                                                    }}
                                                </p>
                                            </td>
                                            <td>
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="item.qty"
                                                    :name="'Qty ' + (1 + index)"
                                                >
                                                    <input
                                                        type="number"
                                                        style="width: 100%"
                                                        v-tooltip.top="
                                                            'Stok Aktual atau stok fisik real yang saat ini ada di toko'
                                                        "
                                                        v-model="item.qty"
                                                        @change="
                                                            updateAdjustment(
                                                                index
                                                            )
                                                        "
                                                        class="form-control"
                                                    />
                                                    <div
                                                        class="fs-sm text-danger"
                                                    >
                                                        {{ errors[0] }}
                                                    </div>
                                                </Field>
                                            </td>
                                            <td>
                                                <Dropdown
                                                    :reduce="
                                                        (label) => label.value
                                                    "
                                                    v-model="item.unit"
                                                    @change="
                                                        updateAdjustment(index)
                                                    "
                                                    :options="item.subunits"
                                                    style="width: 100%"
                                                    optionLabel="name"
                                                    optionValue="id"
                                                    placeholder="Pilih"
                                                />
                                            </td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-wave waves-effect waves-light"
                                                    :class="
                                                        item.type == 'min'
                                                            ? 'btn-outline-danger'
                                                            : 'btn-outline-primary'
                                                    "
                                                >
                                                    {{ item.hasil_qty }}
                                                    {{
                                                        item.type == "min"
                                                            ? "Di kurangi"
                                                            : "Di Tambah"
                                                    }}
                                                </button>
                                            </td>

                                            <td>
                                                <button
                                                    class="btn btn-danger btn-sm"
                                                    type="button"
                                                    v-tooltip.left="
                                                        'Hapus Item'
                                                    "
                                                    @click="RemoveItem(index)"
                                                >
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12">
                            <Divider />
                        </div>
                        <div class="col-12">
                            <label for="regular-form-1" class="form-label"
                                >Catatan
                            </label>
                            <textarea
                                v-model="transaction.note"
                                class="form-control"
                            ></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button
                        type="submit"
                        :disabled="loader.submit"
                        class="btn label-btn label-end btn-primary"
                    >
                        {{
                            loader.submit
                                ? "Tunggu Sebentar...."
                                : "Buat Transaksi"
                        }}
                        <i class="ti ti-circle-check label-btn-icon ms-2"></i>
                    </button>
                </div>
            </div>
        </Form>
    </div>
</template>

<script>
import { ApiData } from "@/api/server";
import NProgress from "nprogress";

export default {
    name: "adjustment_detail",
    components: {},
    data() {
        return {
            warehouses: [],
            warehouse: {
                id: "",
                name: "",
            },
            transaction: {
                date: "",
                ref_no: "",
                note: "",
                warehouse: {
                    id: "",
                    name: "",
                },
                items: [],
            },
            selected_products: null,
            loader: {
                product: false,
                submit: false,
            },
            products: [],
        };
    },
    computed: {},
    created() {
        this.getWarehouse();
        const today = new Date().toISOString().substr(0, 10);
        this.transaction.date = today;
    },
    methods: {
        async getProducts(query) {
            this.loader.product = true;

            try {
                const response = await ApiData.get(
                    `app/inventory/components/variations?name=${query}&warehouse=${this.transaction.warehouse.id}`
                );
                var data = response.data;
                this.products = data.variations;
                this.loader.product = false;
            } catch (error) {
                this.loader.product = false;
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
                this.warehouse = data.warehouses[0];
                this.transaction.warehouse = this.warehouse;
            } catch (error) {
                console.log(error);
            }
        },

        changeWarehouse() {
            if (this.transaction.items.length > 0) {
                this.$toast.add({
                    severity: "error",
                    summary: "Peringatan",
                    detail: "Terdapat item yang di ambil dari gudang, silahkan hapus terlebih dahulu seluruh item",
                    life: 3000,
                });

                this.transaction.warehouse = {
                    id: this.warehouse.id,
                    name: this.warehouse.name,
                };
            } else {
                this.warehouse = {
                    id: this.transaction.warehouse.id,
                    name: this.transaction.warehouse.name,
                };
                this.products = [];
            }
        },

        selectedProduct() {
            if (this.selected_products != null) {
                var product = this.selected_products;
                var idproduct = false;
                idproduct = this.transaction.items.filter((item) => {
                    if (item.variation_id == product["id"]) {
                        return true;
                    }
                });

                if (idproduct == false) {
                    this.transaction.items.push({
                        variation_id: product.id,
                        product_id: product.product_id,
                        name: product.name,
                        qty: 0,
                        purchase_price: product.purchase_price,
                        unit_price: product.purchase_price,
                        unit_name: product.unit_name,
                        unit: product.unit_purchase,
                        unit_awal_id: product.unit,
                        quantity: product.stock_adjustment,
                        subunits: product.units,
                        subtotal: 0,
                        hasil_qty: product.stock,
                        type: "min",
                    });
                }

                this.selected_products = null;
            }
        },

        updateAdjustment(index) {
            var details = this.transaction.items[index];
            var qtyAdjustment = details.qty;
            var hasil = 0;
            var detailUnitPo = null;
            detailUnitPo = details.subunits.filter((item) => {
                if (details.unit == item.id) {
                    return item;
                }
            });

            if (detailUnitPo != null) {
                if (detailUnitPo.length > 0) {
                    if (detailUnitPo[0].value != null) {
                        qtyAdjustment =
                            parseInt(details.qty) * detailUnitPo[0].value;
                    }
                }
            }

            if (qtyAdjustment > details.quantity) {
                this.transaction.items[index].type = "add";
                hasil = qtyAdjustment - details.quantity;
                this.transaction.items[index].hasil_qty = hasil;
                details.subtotal = 0;
            } else {
                this.transaction.items[index].type = "min";
                hasil = details.quantity - qtyAdjustment;
                this.transaction.items[index].hasil_qty = hasil;
                details.subtotal = 0;
            }
        },

        RemoveItem(index) {
            this.transaction.items.splice(index, 1);
        },

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },

        createStockOpname() {
            this.$refs.ValidationStockOpname.validate().then((success) => {
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
                        "app/transactions/stock-opname/store",
                        this.transaction
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.message);
                            NProgress.done();
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
            });
        },
    },
    mounted: function () {},
};
</script>
