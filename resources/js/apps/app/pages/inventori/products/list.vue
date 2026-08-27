<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card">
            <div class="card-header p-4 d-flex justify-content-between">
                <div class="d-flex justify-content-start">
                    <SelectButton
                        v-model="filter.status"
                        optionLabel="name"
                        optionValue="value"
                        :options="[
                            {
                                name: 'Aktif',
                                value: 'yes',
                            },
                            {
                                name: 'Tidak Aktif',
                                value: 'no',
                            },
                        ]"
                        aria-labelledby="basic"
                        @change="getData(page)"
                    />
                    <button
                        type="button"
                        v-tooltip.top="'Refresh'"
                        @click="getData()"
                        class="btn btn-outline-info btn-wave waves-effect waves-light ms-3"
                    >
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
                <div>
                    <div class="btn-group mt-2 mb-2">
                        <button
                            type="button"
                            @click="
                                $goTo({
                                    name: 'create_product',
                                    params: {},
                                })
                            "
                            class="btn btn-primary"
                        >
                            Tambah Produk
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary dropdown-toggle border-end-white-2"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu" style="">
                            <li class="dropdown-plus-title">
                                Aksi
                                <b class="fa fa-angle-up"></b>
                            </li>
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    @click="exportData()"
                                    ><i class="fa fa-download mr-2"></i> Export
                                    Produk</a
                                >
                            </li>
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    @click="exportDataSpt()"
                                    ><i class="fa fa-download mr-2"></i> Export
                                    SPT Produk</a
                                >
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="javascript:void(0);"
                                    @click="modal.import = true"
                                    ><i class="fa fa-upload mr-2"></i> Import
                                    Produk</a
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <DataTable
                        :value="products"
                        :paginator="true"
                        :rows="limit"
                        :rowsPerPageOptions="[10, 20, 50]"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :lazy="true"
                        @sort="onSort($event)"
                        :totalRecords="totalRows"
                        @page="onPageChange($event)"
                        class="table text-nowrap"
                        :loading="loader.data"
                        responsiveLayout="scroll"
                        sortField="dynamicSortField"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                        dataKey="id"
                        editMode="cell"
                        v-model:selection="selectedProducts"
                        @cell-edit-complete="onCellEditComplete"
                    >
                        <template #header>
                            <div class="row">
                                <div class="col">
                                    <label class="form-label"
                                        >Cari Produk</label
                                    >
                                    <span class="p-fluid">
                                        <div class="p-inputgroup">
                                            <span class="input-group-text"
                                                ><i class="fa fa-search"></i>
                                            </span>
                                            <InputText
                                                v-model="filter.name"
                                                placeholder="Cari Produk"
                                            />
                                        </div>
                                    </span>
                                </div>
                                <div class="col">
                                    <label class="form-label"
                                        >Filter Kategori</label
                                    >
                                    <SelectOption
                                        v-model="filter.category"
                                        :options="categories"
                                        filter
                                        :loading="loader.category"
                                        optionLabel="name"
                                        optionValue="id"
                                        placeholder="Pilih Kategori"
                                        filterPlaceholder="Cari Kategori"
                                        style="width: 100%; max-width: 100%"
                                        :maxSelectedLabels="2"
                                        @filter="onFilterCategories"
                                    />
                                </div>
                                <div class="col">
                                    <label class="form-label"
                                        >Filter Brand</label
                                    >
                                    <SelectOption
                                        v-model="filter.brand"
                                        :options="brands"
                                        filter
                                        :loading="loader.brand"
                                        optionLabel="name"
                                        optionValue="id"
                                        placeholder="Pilih Brand"
                                        filterPlaceholder="Cari Brand"
                                        style="width: 100%; max-width: 100%"
                                        :maxSelectedLabels="2"
                                        @filter="onFilterBrands"
                                    />
                                </div>
                                <div class="col">
                                    <label class="form-label"
                                        >Tampilkan Data</label
                                    >
                                    <SelectOption
                                        v-model="column_show.shows"
                                        :options="column_show.data"
                                        optionLabel="name"
                                        optionValue="value"
                                        placeholder="Pilih Tampilan Data"
                                        style="width: 100%; max-width: 100%"
                                        :maxSelectedLabels="2"
                                        @hide="viewChangeData"
                                    />
                                </div>
                            </div>
                        </template>
                        <Column
                            selectionMode="multiple"
                            :styless="{ width: '3rem' }"
                            :exportable="false"
                        ></Column>
                        <Column
                            header="Produk"
                            field="name"
                            sortable
                            v-if="isColumnVisible('name')"
                        >
                            <template #body="{ data }">
                                <a
                                    class="d-flex"
                                    href="javascript:void(0);"
                                    @click="
                                        $goTo({
                                            name: 'product_details',
                                            params: { id: data.id },
                                        })
                                    "
                                >
                                    <img
                                        :src="data.image"
                                        alt=""
                                        class="avatar avatar-sm bg-default bradius"
                                    />
                                    <div class="ms-3 mt-0 mt-sm-2 d-block">
                                        <h6 class="mb-0 fs-12 fw-semibold">
                                            {{ data.name.substring(0, 250) }}
                                        </h6>
                                    </div>
                                </a>
                            </template>
                        </Column>
                        <Column
                            header="Kategori"
                            field="category"
                            sortable
                            v-if="isColumnVisible('category')"
                        >
                            <template #body="{ data }">
                                <p class="p-2">{{ data.category }}</p>
                            </template>
                        </Column>
                        <Column
                            header="Brand"
                            field="brand"
                            sortable
                            v-if="isColumnVisible('brand')"
                        >
                            <template #body="{ data }">
                                <p class="p-2">{{ data.brand }}</p>
                            </template>
                        </Column>
                        <Column
                            header="Varian"
                            v-if="isColumnVisible('variant')"
                        >
                            <template #body="{ data }">
                                <p
                                    class="p-variant inner-wrapper"
                                    style="
                                        white-space: nowrap;
                                        text-overflow: ellipsis;
                                        overflow: hidden;
                                        max-width: 230px;
                                    "
                                    v-for="(variant, vn) in data.variants"
                                    :key="'vn' + vn"
                                >
                                    <span>{{ variant.name }}</span>
                                </p>
                            </template>
                        </Column>
                        <Column
                            header="H-Modal"
                            field="purchase_price"
                            v-if="isColumnVisible('modal')"
                        >
                            <template #body="{ data }">
                                <div>
                                    <p
                                        class="p-variant"
                                        v-for="(variant, vn) in data.variants"
                                        :key="'vm' + vn"
                                    >
                                        {{
                                            formatNumber(variant.purchase_price)
                                        }}
                                    </p>
                                </div>
                            </template>
                        </Column>
                        <Column
                            header="H-Grosir"
                            field="grosir_price"
                            v-if="isColumnVisible('grosir')"
                        >
                            <template #body="{ data }">
                                <p
                                    class="p-variant"
                                    v-for="(variant, vn) in data.variants"
                                    :key="'vg' + vn"
                                >
                                    <a
                                        href="javascript:void(0)"
                                        style="text-decoration: none"
                                        >{{ formatNumber(variant.grocery) }}</a
                                    >
                                </p>
                            </template>
                            <template #editor="{ data }">
                                <div
                                    v-for="(variant, vn) in data.variants"
                                    :key="'evg' + vn"
                                >
                                    <InputNumber
                                        v-model="variant.grocery"
                                        class="p-input-price"
                                        :minFractionDigits="0"
                                        :maxFractionDigits="2"
                                        @blur="
                                            changePrice(
                                                variant,
                                                'grocery_price'
                                            )
                                        "
                                        prefix="Rp "
                                    />
                                </div>
                            </template>
                        </Column>
                        <Column
                            header="H-Jual"
                            field="selling_price"
                            v-if="isColumnVisible('jual')"
                        >
                            <template #body="{ data }">
                                <p
                                    class="p-variant"
                                    v-for="(variant, vn) in data.variants"
                                    :key="'vj' + vn"
                                >
                                    <a
                                        href="javascript:void(0)"
                                        style="text-decoration: none"
                                        >{{
                                            formatNumber(variant.sell_price)
                                        }}</a
                                    >
                                </p>
                            </template>
                            <template #editor="{ data }">
                                <div
                                    v-for="(variant, vn) in data.variants"
                                    :key="'evj' + vn"
                                >
                                    <InputNumber
                                        v-model="variant.sell_price"
                                        class="p-input-price"
                                        :minFractionDigits="0"
                                        :maxFractionDigits="2"
                                        @blur="
                                            changePrice(variant, 'sell_price')
                                        "
                                        prefix="Rp "
                                    />
                                </div>
                            </template>
                        </Column>
                        <Column
                            header="Stok Toko"
                            field="product_all_stock"
                            sortable
                            v-if="isColumnVisible('stok')"
                        >
                            <template #body="{ data }">
                                <p
                                    class="p-variant"
                                    v-for="(variant, vn) in data.variants"
                                    :key="'vs' + vn"
                                >
                                    <a
                                        href="javascript:void(0)"
                                        v-if="data.is_stock"
                                        style="text-decoration: none"
                                        >{{ formatNumber(variant.stock) }}</a
                                    >
                                    <a
                                        href="javascript:void(0)"
                                        v-else
                                        style="text-decoration: none"
                                        >--</a
                                    >
                                </p>
                            </template>
                        </Column>
                        <Column
                            v-for="(warehouse, wr) in warehouses"
                            :key="wr"
                            :header="warehouse.name"
                            v-if="isColumnVisible('gudang')"
                        >
                            <template #body="{ data }">
                                <p
                                    class="p-variant"
                                    v-for="(wstock, ws) in data.warehouses[wr]
                                        .variations"
                                    :key="'vj' + ws"
                                >
                                    {{ formatNumber(wstock.stock) }}
                                </p>
                            </template>
                        </Column>
                        <Column header="Aksi">
                            <template #body="{ data }">
                                <div class="btn-group mt-2 mb-2">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        Aksi <span class="caret"></span>
                                    </button>
                                    <ul
                                        class="dropdown-menu"
                                        role="menu"
                                        style=""
                                    >
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                @click="
                                                    $goTo({
                                                        name: 'product_details',
                                                        params: { id: data.id },
                                                    })
                                                "
                                                ><i class="fa fa-eye mr-2"></i>
                                                Detail</a
                                            >
                                        </li>

                                        <li class="divider"></li>
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                @click="
                                                    $goTo({
                                                        name: 'update_product',
                                                        params: { id: data.id },
                                                    })
                                                "
                                                ><i
                                                    class="fa fa-pencil mr-2"
                                                ></i>
                                                Edit</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="javascript:void(0);"
                                                @click="
                                                    removePermanently(data.id)
                                                "
                                                ><i
                                                    class="fa fa-trash mr-2"
                                                ></i>
                                                Hapus Data</a
                                            >
                                        </li>
                                    </ul>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </div>
    <!-- End List Data -->

    <!-- Modal Import Data -->
    <Dialog
        v-model:visible="modal.import"
        modal
        header=""
        :style="{ width: '60vh' }"
    >
        <div class="card-body ps-5 pe-5 pt-2 pb-5 rectangle3">
            <div class="d-flex justify-content-center">
                <img src="@/assets/images/import_data.png" style="width: 75%" />
            </div>

            <p class="h4 fw-semibold mb-2 text-center">Import Data Produk</p>
            <p class="mb-4 text-muted op-7 fw-normal text-center">
                Upload file Excel (.xlsx) yang berisikan data Produk di bawah
                ini, lalu klik tekan Import data untuk memulai proses import
            </p>
            <div class="row gy-3">
                <div class="col-xl-12 d-flex justify-content-center mt-3 mb-3">
                    <FileUpload
                        mode="basic"
                        v-model="import_data.model"
                        v-tooltip="'Upload File Disini'"
                        @select="onFileSelected"
                        accept=".xlsx"
                        :maxFileSize="1000000"
                    />
                </div>
                <!-- End Code Form -->

                <div
                    class="col-xl-12 d-grid mt-4 d-flex justify-content-center"
                >
                    <button
                        type="button"
                        @click="downloadExample"
                        :disabled="loader.submit"
                        class="btn btn-info label-btn mr-3"
                    >
                        <i class="ti ti-download label-btn-icon mr-2"></i>
                        Unduh Sample
                    </button>
                    <button
                        type="button"
                        @click="importData"
                        v-tooltip="
                            'Sebelum Import Data, Pastikan File Telah di unggah'
                        "
                        :disabled="loader.submit"
                        class="btn btn-primary label-btn label-end"
                    >
                        {{ loader.submit ? "Mohon Tunggu...." : "Import Data" }}
                        <i class="ti ti-upload label-btn-icon ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </Dialog>
    <!-- End Import Data -->
</template>

<script>
import Swal from "sweetalert2";
import NProgress from "nprogress";
import SelectOption from "primevue/multiselect";
import SelectButton from "primevue/selectbutton";
import { ApiData } from "@/api/server";
import { mapActions, mapGetters } from "vuex";

var _ = require("lodash");

export default {
    name: "product_list",
    components: {
        SelectOption,
        SelectButton,
    },
    data() {
        return {
            sort: {
                field: "name",
                order: "desc",
            },
            column_show: {
                data: [
                    {
                        name: "Nama Produk",
                        value: "name",
                    },
                    {
                        name: "Kategori Produk",
                        value: "category",
                    },
                    {
                        name: "Brand Produk",
                        value: "brand",
                    },
                    {
                        name: "Variant Produk",
                        value: "variant",
                    },
                    {
                        name: "Harga Modal",
                        value: "modal",
                    },
                    {
                        name: "Harga Grosir",
                        value: "grosir",
                    },
                    {
                        name: "Harga Jual",
                        value: "jual",
                    },
                    {
                        name: "Stok",
                        value: "stok",
                    },
                    {
                        name: "Gudang",
                        value: "gudang",
                    },
                ],
                shows: [
                    "name",
                    "category",
                    "brand",
                    "variant",
                    "modal",
                    "grosir",
                    "jual",
                    "stok",
                    "gudang",
                ],
            },
            data: new FormData(),
            products: [],
            warehouses: [],
            categories: [],
            brands: [],
            stores: [],
            selectedProducts: [],
            store_selected: [],
            modal: {
                import: false,
                sample: "",
                filter: false,
            },
            totalRows: 0,
            page: 1,
            limit: 10,
            loader: {
                category: false,
                data: false,
                brand: false,
                submit: false,
                store: false,
            },
            import_data: {
                file: null,
                model: null,
            },
            filter: {
                category: [],
                brand: [],
                name: "",
                status: "yes",
            },
            links: [],
        };
    },

    created() {
        this.getOptions();
    },
    methods: {
        onCellEditComplete(event) {
            let { data, newValue, field } = event;

            switch (field) {
                default:
                    data[field] = newValue;
                    break;
            }
        },

        async getOptions() {
            try {
                const response = await ApiData.get(
                    `app/settings/table-view?table=products`
                );
                var data = response.data;
                this.column_show.shows =
                    data.options == null
                        ? this.column_show.shows
                        : data.options;
            } catch (error) {
                console.log(error);
            }
        },

        viewChangeData() {
            ApiData.post("app/settings/table-view/store", {
                table: "products",
                options: this.column_show.shows,
            }).then((response) => {});
        },

        onSort(event) {
            this.sort = {
                field: event.sortField,
                order: event.sortOrder > 0 ? "asc" : "desc",
            };
            this.getData(this.page);
        },

        isColumnVisible(columnName) {
            return this.column_show.shows.includes(columnName);
        },

        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;

            try {
                const response = await ApiData.get(
                    `app/inventory?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&category=${this.filter.category}&brand=${this.filter.brand}&sort=${this.sort.field}&sortby=${this.sort.order}&status=${this.filter.status}`
                );
                var data = response.data;
                this.products = data.products;
                this.totalRows = data.totalRows;
                this.warehouses = data.warehouses;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        async downloadExample() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);

            try {
                const response = await ApiData.get(
                    `app/inventory/download-sample?name=${this.filter.name}&category=${this.filter.category.id}&brand=${this.filter.brand.id}`,
                    {
                        responseType: "blob",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                    }
                );

                const url = window.URL.createObjectURL(
                    new Blob([response.data])
                );
                const link = document.createElement("a");
                link.href = url;

                link.setAttribute("download", "sample_import_products.xlsx");
                document.body.appendChild(link);
                link.click();

                this.loader.submit = false;
                NProgress.done();
            } catch (error) {
                this.loader.submit = false;
                NProgress.done();
                console.log(error);
            }
        },

        async exportData() {
            if (!this.loader.submit) {
                this.loader.submit = true;
                NProgress.start();
                NProgress.set(0.1);

                try {
                    const response = await ApiData.get(
                        `app/inventory/download?name=${this.filter.name}&category=${this.filter.category.id}&brand=${this.filter.brand.id}`,
                        {
                            responseType: "blob",
                            headers: {
                                "Content-Type": "application/json",
                                Accept: "application/json",
                            },
                        }
                    );

                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;

                    link.setAttribute("download", "master_data_products.xlsx");
                    document.body.appendChild(link);
                    link.click();

                    this.loader.submit = false;
                    NProgress.done();
                } catch (error) {
                    this.loader.submit = false;
                    NProgress.done();
                    console.log(error);
                }
            }
        },

        async exportDataSpt() {
            if (!this.loader.submit) {
                this.loader.submit = true;
                NProgress.start();
                NProgress.set(0.1);

                try {
                    const response = await ApiData.get(
                        `app/inventory/download-spt?name=${this.filter.name}&category=${this.filter.category.id}&brand=${this.filter.brand.id}`,
                        {
                            responseType: "blob",
                            headers: {
                                "Content-Type": "application/json",
                                Accept: "application/json",
                            },
                        }
                    );

                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;

                    link.setAttribute("download", "product_spt_format.xlsx");
                    document.body.appendChild(link);
                    link.click();

                    this.loader.submit = false;
                    NProgress.done();
                } catch (error) {
                    this.loader.submit = false;
                    NProgress.done();
                    console.log(error);
                }
            }
        },

        async onFileSelected(e) {
            if (e.files[0] != undefined) {
                this.import_data.file = e.files[0];
            } else {
                this.import_data.file = null;
            }
        },

        importData() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            this.data.append("file", this.import_data.file);
            ApiData.post("app/inventory/import", this.data)
                .then((response) => {
                    this.loader.submit = false;
                    this.$handleSuccessResponse(response.data.message);
                    this.modal.import = false;
                    NProgress.done();
                    this.getData();
                })
                .catch((error) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(error);
                });
        },

        searchData() {
            this.doSearch(this);
            this.modal.filter = false;
        },

        doSearch: _.debounce((rootInstance) => {
            rootInstance.getData();
        }, 300),

        onPageChange(e) {
            this.limit = e.rows;
            this.page = e.page += 1;
            this.getData(this.page);
        },

        onFilterCategories(event) {
            const query = event.value;
            this.getCategories(query);
        },

        onFilterBrands(event) {
            const query = event.value;
            this.getBrands(query);
        },

        async getCategories(query) {
            this.loader.category = true;
            try {
                const response = await ApiData.get(
                    `app/inventory/components/categories?name=${query}`
                );
                var data = response.data;
                this.categories = data.categories;
                this.loader.category = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getBrands(query) {
            this.loader.brand = true;
            try {
                const response = await ApiData.get(
                    `app/inventory/components/brands?name=${query}`
                );
                var data = response.data;
                this.brands = data.brands;
                this.loader.brand = false;
            } catch (error) {
                console.log(error);
            }
        },

        resetFilter() {
            this.filter.category = [];
            this.filter.brand = [];
            this.searchData();
        },

        removeBulkPermanently() {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Produk beserta stok di dalam nya akan di hapus secara permanent di seluruh toko / cabang yang anda miliki",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.post("app/inventory/delete-many-product", {
                        product_selected: this.selectedProducts,
                    })
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.getData();
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

        removePermanently(productId) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Produk beserta stok di dalam nya akan di hapus secara permanent di seluruh toko / cabang yang anda miliki",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    ApiData.delete("app/inventory/delete/" + productId)
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.getData();
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

        changePrice(variant, type) {
            setTimeout(() => {
                ApiData.post(`app/inventory/change-price/${variant.id}`, {
                    type: type,
                    price:
                        type == "grocery_price"
                            ? parseInt(variant.grocery)
                            : parseInt(variant.sell_price),
                })
                    .then((response) => {
                        this.$handleSuccessResponse(response.data.message);
                    })
                    .catch((err) => {
                        this.$handleErrorResponse(err);
                    });
            }, 2000);
        },

        formatNumber(number) {
            if (parseFloat(number) >= 0) {
                return number.toLocaleString();
            } else {
                return "-" + (-number).toLocaleString();
            }
        },
    },
    mounted: function () {
        this.getData();
        this.getBrands("");
        this.getCategories("");
    },
    watch: {
        filter: {
            handler: function () {
                this.searchData();
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>
