<template>
    <div class="col-lg-12">
        <div class="card custom-card">
            <Form
                @submit="createProductValidation()"
                ref="ValidationCreateProduct"
            >
                <div class="card-body add-product p-0">
                    <div class="p-4">
                        <div class="row gx-5">
                            <!-- General Information -->
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <div class="card custom-card shadow-none mb-0">
                                    <div class="card-body p-0">
                                        <div class="row gy-3">
                                            <div class="col-lg-12">
                                                <h4>Informasi Umum</h4>
                                            </div>

                                            <!-- Image Media -->
                                            <div class="col-lg-12">
                                                <label for="product-name-add"
                                                    >Gambar Produk ( Maximal 5
                                                    gambar)
                                                </label>
                                                <FileUpload
                                                    v-model="files"
                                                    @select="
                                                        customBase64Uploader
                                                    "
                                                    :multiple="true"
                                                    :fileLimit="5"
                                                    :maxFileSize="5000000"
                                                    customUpload
                                                    :showUploadButton="false"
                                                    cancelLabel="Batalkan"
                                                    chooseLabel="Pilih Gambar"
                                                >
                                                    <template #empty>
                                                        <p>
                                                            Upload Gambar produk
                                                            ke sini.
                                                        </p>
                                                    </template>
                                                </FileUpload>
                                            </div>
                                            <!-- End Image Media -->

                                            <!-- Nama Produk -->
                                            <div class="col-lg-6 mt-3">
                                                <label for="product-name-add"
                                                    >Nama Produk</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors, field }"
                                                    name="Nama Produk"
                                                >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        v-bind="field"
                                                        v-model="
                                                            product.info_general
                                                                .name
                                                        "
                                                        placeholder="Masukkan Nama Produk"
                                                    />
                                                    <div
                                                        class="fs-sm text-danger"
                                                    >
                                                        {{ errors[0] }}
                                                    </div>
                                                </Field>
                                            </div>
                                            <!-- End Nama Produk -->

                                            <!-- Barcode Type -->
                                            <div class="col-lg-6 mt-3">
                                                <label for="barcode-type"
                                                    >Tipe Barcode</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="
                                                        product.info_general
                                                            .barcode_type
                                                    "
                                                    name="Tipe Barcode"
                                                >
                                                    <Dropdown
                                                        v-model="
                                                            product.info_general
                                                                .barcode_type
                                                        "
                                                        :options="barcode_types"
                                                        optionLabel="label"
                                                        optionValue="value"
                                                        placeholder="Pilih Tipe"
                                                        style="width: 100%"
                                                        class="w-full md:w-14rem"
                                                    />
                                                    <div
                                                        class="fs-sm text-danger"
                                                    >
                                                        {{ errors[0] }}
                                                    </div>
                                                </Field>
                                            </div>
                                            <!-- End Barcode Type -->

                                            <!-- Kategori Produk -->
                                            <div class="col-lg-6 mt-3">
                                                <label for="category-product"
                                                    >Kategori Produk</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="
                                                        product.info_general
                                                            .category
                                                    "
                                                    name="Kategori Produk"
                                                >
                                                    <Multiselect
                                                        v-model="
                                                            product.info_general
                                                                .category
                                                        "
                                                        :options="categories"
                                                        :multiple="false"
                                                        :close-on-select="true"
                                                        :clear-on-select="true"
                                                        :preserve-search="true"
                                                        :searchable="true"
                                                        :loading="
                                                            loader.category
                                                        "
                                                        :internal-search="true"
                                                        :options-limit="50"
                                                        placeholder="Pilih Kategori"
                                                        open-direction="bottom"
                                                        label="name"
                                                        id="id"
                                                        track-by="name"
                                                        @search-change="
                                                            getCategories
                                                        "
                                                    ></Multiselect>
                                                    <div
                                                        class="fs-sm text-danger"
                                                    >
                                                        {{ errors[0] }}
                                                    </div>
                                                </Field>
                                            </div>
                                            <!-- End Kategori Produk -->

                                            <!-- Brand -->
                                            <div class="col-lg-6 mt-3">
                                                <label for="brand-product"
                                                    >Brand Produk</label
                                                >
                                                <Multiselect
                                                    v-model="
                                                        product.other_detail
                                                            .brand
                                                    "
                                                    :options="brands"
                                                    :multiple="false"
                                                    :close-on-select="true"
                                                    :clear-on-select="true"
                                                    :preserve-search="true"
                                                    :searchable="true"
                                                    :loading="loader.brand"
                                                    :internal-search="true"
                                                    :options-limit="50"
                                                    placeholder="Pilih Brand"
                                                    open-direction="bottom"
                                                    label="name"
                                                    id="id"
                                                    track-by="name"
                                                    @search-change="getBrands"
                                                ></Multiselect>
                                            </div>
                                            <!-- End Brand -->

                                            <div class="col-12 mt-3">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <div
                                                                class="col-12 mb-3"
                                                            >
                                                                <label
                                                                    for="brand-product"
                                                                    >Barang
                                                                    Persediaan</label
                                                                >
                                                                <br />
                                                                <InputSwitch
                                                                    v-model="
                                                                        product
                                                                            .info_general
                                                                            .is_stock
                                                                    "
                                                                />
                                                                <label
                                                                    for="product-name-add"
                                                                    class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                                                    >*Checklist
                                                                    opsi ini
                                                                    jika Barang
                                                                    anda
                                                                    merupakan
                                                                    barang
                                                                    persediaan</label
                                                                >
                                                            </div>
                                                            <div
                                                                class="col-12 mb-3"
                                                            >
                                                                <label
                                                                    for="brand-product"
                                                                    >Produk
                                                                    Variant</label
                                                                >
                                                                <br />
                                                                <InputSwitch
                                                                    @click="
                                                                        validationVariant()
                                                                    "
                                                                    v-model="
                                                                        product
                                                                            .info_general
                                                                            .is_variant
                                                                    "
                                                                />
                                                                <label
                                                                    for="product-name-add"
                                                                    class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                                                    >*Checklist
                                                                    opsi ini
                                                                    jika produk
                                                                    anda
                                                                    memiliki
                                                                    variant</label
                                                                >
                                                            </div>
                                                            <div
                                                                class="col-12 mb-3"
                                                                v-if="
                                                                    with_accountant
                                                                "
                                                            >
                                                                <label
                                                                    for="brand-product"
                                                                    >Sinkronkan
                                                                    Dengan
                                                                    Akuntansi
                                                                </label>
                                                                <br />
                                                                <InputSwitch
                                                                    v-model="
                                                                        product
                                                                            .info_general
                                                                            .is_account
                                                                    "
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <div
                                                                class="col-12 mb-3"
                                                                v-if="
                                                                    product
                                                                        .info_general
                                                                        .is_stock
                                                                "
                                                            >
                                                                <label
                                                                    for="brand-product"
                                                                    >Peringatan
                                                                    Stok</label
                                                                >
                                                                <Field
                                                                    :rules="{
                                                                        required: true,
                                                                    }"
                                                                    v-slot="{
                                                                        errors,
                                                                    }"
                                                                    v-model="
                                                                        product
                                                                            .info_general
                                                                            .alert_qty
                                                                    "
                                                                    name="Peringatan Stok Qty"
                                                                >
                                                                    <InputNumber
                                                                        style="
                                                                            width: 100%;
                                                                        "
                                                                        v-model="
                                                                            product
                                                                                .info_general
                                                                                .alert_qty
                                                                        "
                                                                        prefix="Qty: "
                                                                    />
                                                                    <label
                                                                        for="product-name-add"
                                                                        class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                                                    >
                                                                    </label>

                                                                    <div
                                                                        class="fs-sm text-danger"
                                                                    >
                                                                        {{
                                                                            errors[0]
                                                                        }}
                                                                    </div>
                                                                </Field>
                                                            </div>
                                                            <div
                                                                class="col-12 mb-3"
                                                            >
                                                                <label
                                                                    for="brand-product"
                                                                    >Berat
                                                                    Produk (
                                                                    Gram )
                                                                </label>
                                                                <InputNumber
                                                                    style="
                                                                        width: 100%;
                                                                    "
                                                                    v-model="
                                                                        product
                                                                            .other_detail
                                                                            .weight
                                                                    "
                                                                    suffix=" Gram"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mt-3">
                                                <label for="product-description"
                                                    >Deskripsi Produk</label
                                                >
                                                <Editor
                                                    v-model="
                                                        product.other_detail
                                                            .description
                                                    "
                                                    editorStyle="height: 320px"
                                                />
                                            </div>

                                            <div
                                                class="col-12 mt-3"
                                                v-if="
                                                    product.info_general
                                                        .is_account &&
                                                    with_accountant
                                                "
                                            ></div>
                                            <Fieldset
                                                :legend="'Akun Akutansi'"
                                                :toggleable="true"
                                                :collapsed="true"
                                            >
                                                <div class="row">
                                                    <!-- Supply Account -->
                                                    <div class="col-lg-6 mt-3">
                                                        <label
                                                            for="category-product"
                                                            >Persediaan</label
                                                        >
                                                        <Field
                                                            :rules="{
                                                                required: true,
                                                            }"
                                                            v-slot="{ errors }"
                                                            v-model="
                                                                product
                                                                    .info_general
                                                                    .supply
                                                            "
                                                            name="Akun Persediaan"
                                                        >
                                                            <Multiselect
                                                                v-model="
                                                                    product
                                                                        .info_general
                                                                        .supply
                                                                "
                                                                :options="
                                                                    accounts
                                                                "
                                                                :multiple="
                                                                    false
                                                                "
                                                                :close-on-select="
                                                                    true
                                                                "
                                                                :clear-on-select="
                                                                    true
                                                                "
                                                                :preserve-search="
                                                                    true
                                                                "
                                                                :searchable="
                                                                    true
                                                                "
                                                                :internal-search="
                                                                    true
                                                                "
                                                                :options-limit="
                                                                    50
                                                                "
                                                                placeholder="Pilih Akun"
                                                                open-direction="bottom"
                                                                label="name"
                                                                id="id"
                                                                track-by="name"
                                                                @search-change="
                                                                    getAccounts
                                                                "
                                                            ></Multiselect>
                                                            <div
                                                                class="fs-sm text-danger"
                                                            >
                                                                {{ errors[0] }}
                                                            </div>
                                                        </Field>
                                                    </div>
                                                    <!-- End Supply Account -->

                                                    <!-- Sale Account -->
                                                    <div class="col-lg-6 mt-3">
                                                        <label
                                                            for="category-product"
                                                            >Penjualan</label
                                                        >
                                                        <Field
                                                            :rules="{
                                                                required: true,
                                                            }"
                                                            v-slot="{ errors }"
                                                            v-model="
                                                                product
                                                                    .info_general
                                                                    .sale
                                                            "
                                                            name="Penjualan Akun"
                                                        >
                                                            <Multiselect
                                                                v-model="
                                                                    product
                                                                        .info_general
                                                                        .sale
                                                                "
                                                                :options="
                                                                    accounts
                                                                "
                                                                :multiple="
                                                                    false
                                                                "
                                                                :close-on-select="
                                                                    true
                                                                "
                                                                :clear-on-select="
                                                                    true
                                                                "
                                                                :preserve-search="
                                                                    true
                                                                "
                                                                :searchable="
                                                                    true
                                                                "
                                                                :loading="
                                                                    loader.sale
                                                                "
                                                                :internal-search="
                                                                    true
                                                                "
                                                                :options-limit="
                                                                    50
                                                                "
                                                                placeholder="Pilih Akun"
                                                                open-direction="bottom"
                                                                label="name"
                                                                id="id"
                                                                track-by="name"
                                                                @search-change="
                                                                    getAccounts
                                                                "
                                                            ></Multiselect>
                                                            <div
                                                                class="fs-sm text-danger"
                                                            >
                                                                {{ errors[0] }}
                                                            </div>
                                                        </Field>
                                                    </div>
                                                    <!-- End Sale Account -->

                                                    <!-- Return Sale -->
                                                    <div class="col-lg-6 mt-3">
                                                        <label
                                                            for="category-product"
                                                            >Retur
                                                            Penjualan</label
                                                        >
                                                        <Field
                                                            :rules="{
                                                                required: true,
                                                            }"
                                                            v-slot="{ errors }"
                                                            v-model="
                                                                product
                                                                    .info_general
                                                                    .return_sale
                                                            "
                                                            name="Retur Penjualan Akun"
                                                        >
                                                            <Multiselect
                                                                v-model="
                                                                    product
                                                                        .info_general
                                                                        .return_sale
                                                                "
                                                                :options="
                                                                    accounts
                                                                "
                                                                :multiple="
                                                                    false
                                                                "
                                                                :close-on-select="
                                                                    true
                                                                "
                                                                :clear-on-select="
                                                                    true
                                                                "
                                                                :preserve-search="
                                                                    true
                                                                "
                                                                :searchable="
                                                                    true
                                                                "
                                                                :loading="
                                                                    loader.sale
                                                                "
                                                                :internal-search="
                                                                    true
                                                                "
                                                                :options-limit="
                                                                    50
                                                                "
                                                                placeholder="Pilih Akun"
                                                                open-direction="bottom"
                                                                label="name"
                                                                id="id"
                                                                track-by="name"
                                                                @search-change="
                                                                    getAccounts
                                                                "
                                                            ></Multiselect>
                                                            <div
                                                                class="fs-sm text-danger"
                                                            >
                                                                {{ errors[0] }}
                                                            </div>
                                                        </Field>
                                                    </div>
                                                    <!-- End Return Sale -->

                                                    <!-- Discount Account -->
                                                    <div class="col-lg-6 mt-3">
                                                        <label
                                                            for="category-product"
                                                            >Diskon
                                                            Penjualan</label
                                                        >
                                                        <Field
                                                            :rules="{
                                                                required: true,
                                                            }"
                                                            v-slot="{ errors }"
                                                            v-model="
                                                                product
                                                                    .info_general
                                                                    .discount
                                                            "
                                                            name="Diskon Penjualan Akun"
                                                        >
                                                            <Multiselect
                                                                v-model="
                                                                    product
                                                                        .info_general
                                                                        .discount
                                                                "
                                                                :options="
                                                                    accounts
                                                                "
                                                                :multiple="
                                                                    false
                                                                "
                                                                :close-on-select="
                                                                    true
                                                                "
                                                                :clear-on-select="
                                                                    true
                                                                "
                                                                :preserve-search="
                                                                    true
                                                                "
                                                                :searchable="
                                                                    true
                                                                "
                                                                :internal-search="
                                                                    true
                                                                "
                                                                :options-limit="
                                                                    50
                                                                "
                                                                placeholder="Pilih Akun"
                                                                open-direction="bottom"
                                                                label="name"
                                                                id="id"
                                                                track-by="name"
                                                                @search-change="
                                                                    getAccounts
                                                                "
                                                            ></Multiselect>
                                                            <div
                                                                class="fs-sm text-danger"
                                                            >
                                                                {{ errors[0] }}
                                                            </div>
                                                        </Field>
                                                    </div>
                                                    <!-- End Discount Account -->

                                                    <!-- Cost Account -->
                                                    <div class="col-lg-6 mt-3">
                                                        <label
                                                            for="category-product"
                                                            >Beban Pokok
                                                            Penjualan</label
                                                        >
                                                        <Field
                                                            :rules="{
                                                                required: true,
                                                            }"
                                                            v-slot="{ errors }"
                                                            v-model="
                                                                product
                                                                    .info_general
                                                                    .cost
                                                            "
                                                            name="Beban Pokok Akun"
                                                        >
                                                            <Multiselect
                                                                v-model="
                                                                    product
                                                                        .info_general
                                                                        .cost
                                                                "
                                                                :options="
                                                                    accounts
                                                                "
                                                                :multiple="
                                                                    false
                                                                "
                                                                :close-on-select="
                                                                    true
                                                                "
                                                                :clear-on-select="
                                                                    true
                                                                "
                                                                :preserve-search="
                                                                    true
                                                                "
                                                                :searchable="
                                                                    true
                                                                "
                                                                :loading="
                                                                    loader.cost
                                                                "
                                                                :internal-search="
                                                                    true
                                                                "
                                                                :options-limit="
                                                                    50
                                                                "
                                                                placeholder="Pilih Akun"
                                                                open-direction="bottom"
                                                                label="name"
                                                                id="id"
                                                                track-by="name"
                                                                @search-change="
                                                                    getAccounts
                                                                "
                                                            ></Multiselect>
                                                            <div
                                                                class="fs-sm text-danger"
                                                            >
                                                                {{ errors[0] }}
                                                            </div>
                                                        </Field>
                                                    </div>
                                                    <!-- End Cost Account -->

                                                    <!-- Return Purchase Account -->
                                                    <div class="col-lg-6 mt-3">
                                                        <label
                                                            for="category-product"
                                                            >Return
                                                            Pembelian</label
                                                        >
                                                        <Field
                                                            :rules="{
                                                                required: true,
                                                            }"
                                                            v-slot="{ errors }"
                                                            v-model="
                                                                product
                                                                    .info_general
                                                                    .retur_purchase
                                                            "
                                                            name="Return Pembelian Akun"
                                                        >
                                                            <Multiselect
                                                                v-model="
                                                                    product
                                                                        .info_general
                                                                        .retur_purchase
                                                                "
                                                                :options="
                                                                    accounts
                                                                "
                                                                :multiple="
                                                                    false
                                                                "
                                                                :close-on-select="
                                                                    true
                                                                "
                                                                :clear-on-select="
                                                                    true
                                                                "
                                                                :preserve-search="
                                                                    true
                                                                "
                                                                :searchable="
                                                                    true
                                                                "
                                                                :loading="
                                                                    loader.supply
                                                                "
                                                                :internal-search="
                                                                    true
                                                                "
                                                                :options-limit="
                                                                    50
                                                                "
                                                                placeholder="Pilih Akun"
                                                                open-direction="bottom"
                                                                label="name"
                                                                id="id"
                                                                track-by="name"
                                                                @search-change="
                                                                    getAccounts
                                                                "
                                                            ></Multiselect>
                                                            <div
                                                                class="fs-sm text-danger"
                                                            >
                                                                {{ errors[0] }}
                                                            </div>
                                                        </Field>
                                                    </div>
                                                    <!-- End Return Purchase Account -->
                                                </div>
                                            </Fieldset>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End General Information -->

                            <!-- Variant Information -->
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <div class="card custom-card shadow-none mb-0">
                                    <div class="card-body p-0">
                                        <div class="row gy-4">
                                            <div
                                                class="col-lg-12 d-flex justify-content-between"
                                            >
                                                <h4>
                                                    Informasi Harga & Variant
                                                </h4>
                                                <button
                                                    @click="addVariant()"
                                                    v-if="
                                                        product.info_general
                                                            .is_variant
                                                    "
                                                    class="btn btn-outline-primary rounded-pill btn-wave waves-effect waves-light btn-sm"
                                                    type="button"
                                                >
                                                    <i
                                                        class="bx bx-plus mr-2"
                                                    ></i>
                                                    Tambah Variant
                                                </button>
                                            </div>
                                            <div
                                                class="col-lg-12 mb-3"
                                                v-for="(
                                                    variant, index
                                                ) in product.variations"
                                                :key="index"
                                            >
                                                <Fieldset
                                                    :legend="
                                                        product.info_general
                                                            .is_variant
                                                            ? variant.name
                                                            : 'Detail'
                                                    "
                                                    :toggleable="variant.show"
                                                >
                                                    <div class="row">
                                                        <!-- Nama Variant -->
                                                        <div
                                                            class="col-12 mb-3"
                                                            v-if="
                                                                product
                                                                    .info_general
                                                                    .is_variant
                                                            "
                                                        >
                                                            <label
                                                                >Nama
                                                                Variant</label
                                                            >
                                                            <Field
                                                                :rules="{
                                                                    required: true,
                                                                }"
                                                                v-slot="{
                                                                    errors,
                                                                }"
                                                                v-model="
                                                                    product
                                                                        .variations[
                                                                        index
                                                                    ].name
                                                                "
                                                                :name="
                                                                    'Nama Variant ' +
                                                                    (1 + index)
                                                                "
                                                            >
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    v-model="
                                                                        product
                                                                            .variations[
                                                                            index
                                                                        ].name
                                                                    "
                                                                    placeholder="Masukkan Nama Variant"
                                                                />
                                                                <div
                                                                    class="fs-sm text-danger"
                                                                >
                                                                    {{
                                                                        errors[0]
                                                                    }}
                                                                </div>
                                                            </Field>
                                                        </div>
                                                        <!-- End Nama Variant -->

                                                        <!-- Sku -->
                                                        <div
                                                            class="col-lg-6 mb-3"
                                                        >
                                                            <label
                                                                for="product-name-add"
                                                                >SKU</label
                                                            >
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="
                                                                    variant.sku
                                                                "
                                                                placeholder="Masukkan SKU Produk"
                                                            />
                                                        </div>
                                                        <!-- End Sku -->

                                                        <!-- Barcode -->
                                                        <div
                                                            class="col-lg-6 mb-3"
                                                        >
                                                            <label
                                                                for="barcode-product-add"
                                                                >Barcode
                                                                Produk</label
                                                            >
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                v-model="
                                                                    variant.barcode
                                                                "
                                                            />
                                                            <label
                                                                for="barcode-product-add"
                                                                class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                                                >*Kamu bisa
                                                                mengkosongkan
                                                                bidang ini untuk
                                                                mengenerate kode
                                                                barcode secara
                                                                otomatis</label
                                                            >
                                                        </div>
                                                        <!-- End Barcode -->

                                                        <!-- Harga Jual -->
                                                        <div
                                                            class="col-lg-6 mb-3"
                                                        >
                                                            <label
                                                                for="product-name-add"
                                                                >Harga
                                                                Jual</label
                                                            >
                                                            <Field
                                                                :rules="{
                                                                    required: true,
                                                                }"
                                                                v-slot="{
                                                                    errors,
                                                                }"
                                                                v-model="
                                                                    variant.selling_price
                                                                "
                                                                :name="
                                                                    'Harga Jual ' +
                                                                    (1 + index)
                                                                "
                                                            >
                                                                <InputNumber
                                                                    style="
                                                                        width: 100%;
                                                                    "
                                                                    v-model="
                                                                        variant.selling_price
                                                                    "
                                                                    prefix="Rp "
                                                                />
                                                                <div
                                                                    class="fs-sm text-danger"
                                                                >
                                                                    {{
                                                                        errors[0]
                                                                    }}
                                                                </div>
                                                            </Field>
                                                        </div>
                                                        <!-- End Harga Jual -->

                                                        <!-- Harga Grosir -->
                                                        <div
                                                            class="col-lg-6 mb-3"
                                                        >
                                                            <label
                                                                for="product-name-add"
                                                                >Harga
                                                                Grosir</label
                                                            >
                                                            <InputNumber
                                                                style="
                                                                    width: 100%;
                                                                "
                                                                v-model="
                                                                    variant.grocery
                                                                "
                                                                prefix="Rp "
                                                            />
                                                        </div>
                                                        <!-- End Harga Grosir -->

                                                        <!-- Satuan Dasar -->
                                                        <div
                                                            class="col-lg-6 mb-3"
                                                        >
                                                            <label
                                                                for="barcode-type"
                                                                >Satuan
                                                                Dasar</label
                                                            >
                                                            <Field
                                                                :rules="{
                                                                    required: true,
                                                                }"
                                                                v-slot="{
                                                                    errors,
                                                                }"
                                                                v-model="
                                                                    variant.unit
                                                                "
                                                                :name="
                                                                    'Satuan Dasar ' +
                                                                    (1 + index)
                                                                "
                                                            >
                                                                <Dropdown
                                                                    v-model="
                                                                        variant.unit
                                                                    "
                                                                    :options="
                                                                        units
                                                                    "
                                                                    optionLabel="name"
                                                                    optionValue="id"
                                                                    @change="
                                                                        getUnitChild(
                                                                            variant
                                                                        )
                                                                    "
                                                                    placeholder="Pilih"
                                                                    style="
                                                                        width: 100%;
                                                                    "
                                                                    class="w-full md:w-14rem"
                                                                />
                                                                <div
                                                                    class="fs-sm text-danger"
                                                                >
                                                                    {{
                                                                        errors[0]
                                                                    }}
                                                                </div>
                                                            </Field>
                                                        </div>
                                                        <!-- End Satuan Dasar -->

                                                        <!-- Satuan Penjualan -->
                                                        <div
                                                            class="col-lg-6 mb-3"
                                                        >
                                                            <label
                                                                for="barcode-type"
                                                                >Satuan
                                                                Penjualan</label
                                                            >
                                                            <Dropdown
                                                                v-model="
                                                                    variant.unit_sale
                                                                "
                                                                :options="
                                                                    variant.subunits
                                                                "
                                                                optionLabel="name"
                                                                optionValue="id"
                                                                placeholder="Pilih"
                                                                style="
                                                                    width: 100%;
                                                                "
                                                                class="w-full md:w-14rem"
                                                            />
                                                        </div>
                                                        <!-- End Satuan Penjualan -->

                                                        <!-- Satuan Pembelian -->
                                                        <div
                                                            class="col-lg-6 mb-3"
                                                        >
                                                            <label
                                                                for="barcode-type"
                                                                >Satuan
                                                                Pembelian</label
                                                            >
                                                            <Dropdown
                                                                v-model="
                                                                    variant.unit_purchase
                                                                "
                                                                :options="
                                                                    variant.subunits
                                                                "
                                                                optionLabel="name"
                                                                optionValue="id"
                                                                placeholder="Pilih"
                                                                style="
                                                                    width: 100%;
                                                                "
                                                                class="w-full md:w-14rem"
                                                            />
                                                        </div>
                                                        <!-- End Satuan Pembelian -->

                                                        <!-- Rak -->
                                                        <div class="col-lg-6">
                                                            <label
                                                                for="category-product"
                                                                >Rak
                                                                Penyimpanan</label
                                                            >
                                                            <Multiselect
                                                                v-model="
                                                                    variant.rak
                                                                "
                                                                :options="raks"
                                                                :multiple="
                                                                    false
                                                                "
                                                                :close-on-select="
                                                                    true
                                                                "
                                                                :clear-on-select="
                                                                    true
                                                                "
                                                                :preserve-search="
                                                                    true
                                                                "
                                                                :searchable="
                                                                    true
                                                                "
                                                                :loading="
                                                                    loader.rak
                                                                "
                                                                :internal-search="
                                                                    true
                                                                "
                                                                :options-limit="
                                                                    50
                                                                "
                                                                placeholder="Pilih Rak"
                                                                open-direction="bottom"
                                                                label="name"
                                                                id="id"
                                                                track-by="name"
                                                                tagPlaceholder=""
                                                                selectLabel=""
                                                                @search-change="
                                                                    getRaks
                                                                "
                                                            ></Multiselect>
                                                        </div>
                                                        <!-- End Rak -->

                                                        <div class="col-12">
                                                            <div class="row">
                                                                <div
                                                                    class="col-lg-6"
                                                                    v-if="
                                                                        with_tax
                                                                    "
                                                                >
                                                                    <div
                                                                        class="row"
                                                                    >
                                                                        <div
                                                                            class="col-12 mb-3"
                                                                        >
                                                                            <label
                                                                                for="brand-product"
                                                                                >Menggunakan
                                                                                Pajak
                                                                                Penjualan
                                                                            </label>
                                                                            <br />
                                                                            <InputSwitch
                                                                                v-model="
                                                                                    variant.tax_sell
                                                                                "
                                                                            />
                                                                            <label
                                                                                for="product-name-add"
                                                                                class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                                                            >
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="col-lg-6"
                                                                    v-if="
                                                                        with_tax
                                                                    "
                                                                >
                                                                    <div
                                                                        class="row"
                                                                    >
                                                                        <div
                                                                            class="col-12 mb-3"
                                                                        >
                                                                            <label
                                                                                for="brand-product"
                                                                                >Menggunakan
                                                                                Pajak
                                                                                Pembelian
                                                                            </label>
                                                                            <br />
                                                                            <InputSwitch
                                                                                v-model="
                                                                                    variant.tax_purchase
                                                                                "
                                                                            />
                                                                            <label
                                                                                for="product-name-add"
                                                                                class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                                                            >
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <table
                                                                class="table"
                                                            >
                                                                <tr>
                                                                    <th>
                                                                        <div>
                                                                            <label
                                                                                for="product-name-add"
                                                                                >Harga
                                                                                Modal</label
                                                                            >
                                                                            <Field
                                                                                :rules="{
                                                                                    required: true,
                                                                                }"
                                                                                v-slot="{
                                                                                    errors,
                                                                                }"
                                                                                v-model="
                                                                                    variant.purchase_price
                                                                                "
                                                                                :name="
                                                                                    'Harga Modal ' +
                                                                                    (1 +
                                                                                        index)
                                                                                "
                                                                            >
                                                                                <InputNumber
                                                                                    style="
                                                                                        width: 100%;
                                                                                    "
                                                                                    v-model="
                                                                                        variant.purchase_price
                                                                                    "
                                                                                    prefix="Rp "
                                                                                />
                                                                                <div
                                                                                    class="fs-sm text-danger"
                                                                                >
                                                                                    {{
                                                                                        errors[0]
                                                                                    }}
                                                                                </div>
                                                                            </Field>
                                                                        </div>
                                                                    </th>
                                                                    <th>
                                                                        <div
                                                                            v-if="
                                                                                product
                                                                                    .info_general
                                                                                    .is_stock
                                                                            "
                                                                        >
                                                                            <label
                                                                                for="product-name-add"
                                                                                >Stok
                                                                                Awal</label
                                                                            >
                                                                            <InputNumber
                                                                                style="
                                                                                    width: 100%;
                                                                                "
                                                                                v-model="
                                                                                    variant.stock
                                                                                "
                                                                                prefix="Stok: "
                                                                            />
                                                                        </div>
                                                                    </th>
                                                                </tr>
                                                            </table>
                                                        </div>

                                                        <div
                                                            class="col-12 d-flex justify-content-end"
                                                        >
                                                            <button
                                                                @click="
                                                                    removeVariant(
                                                                        index
                                                                    )
                                                                "
                                                                v-if="
                                                                    product
                                                                        .info_general
                                                                        .is_variant &&
                                                                    index > 0
                                                                "
                                                                class="btn btn-outline-danger rounded-pill btn-wave waves-effect waves-light btn-sm"
                                                                type="button"
                                                            >
                                                                <i
                                                                    class="bx bx-x-circle mr-2"
                                                                ></i>
                                                                Hapus Variant
                                                            </button>
                                                        </div>
                                                    </div>
                                                </Fieldset>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Variant Information -->
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end p-4">
                    <button
                        type="submit"
                        :disabled="loader.submit"
                        class="btn btn-primary label-btn label-end"
                    >
                        {{
                            loader.submit
                                ? "Mohon Tunggu...."
                                : "Tambahkan Data"
                        }}
                        <i class="ti ti-plus label-btn-icon ms-2"></i>
                    </button>
                </div>
            </Form>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";
import { ApiData } from "@/api/server";
import NProgress from "nprogress";
import Editor from "primevue/editor";
import Fieldset from "primevue/fieldset";

var _ = require("lodash");

export default {
    name: "create_product",
    components: {
        Editor,
        Fieldset,
    },
    data() {
        return {
            with_tax: false,
            with_accountant: true,
            categories: [],
            brands: [],
            supply_account: [],
            accounts: [],
            cost_account: [],
            debt_account: [],
            taxrates: [],
            barcode_types: [
                {
                    label: "Code 128 (C128)",
                    value: "c128",
                },
                {
                    label: "Code 39 (C39)",
                    value: "c39",
                },
                {
                    label: "EAN-13",
                    value: "ean13",
                },
                {
                    label: "EAN-8",
                    value: "ean8",
                },
                {
                    label: "UPC-A",
                    value: "upcA",
                },
                {
                    label: "UPC-E",
                    value: "upcE",
                },
            ],
            files: null,
            units: [],
            raks: [],
            unit_child: [],
            account_default: {
                product_supply: {
                    id: "",
                    name: "",
                },
                product_sale: {
                    id: "",
                    name: "",
                },
                product_return_sale: {
                    id: "",
                    name: "",
                },
                product_retur_sale: {
                    id: "",
                    name: "",
                },
                product_sent: {
                    id: "",
                    name: "",
                },
                product_cost: {
                    id: "",
                    name: "",
                },
                product_retur_purchase: {
                    id: "",
                    name: "",
                },
                product_supplier_debt: {
                    id: "",
                    name: "",
                },
            },
            loader: {
                category: false,
                rak: false,
                brand: false,
                submit: false,
                rak: false,
                sale: false,
                cost: false,
                debt: false,
                supply: false,
            },
            product: {
                info_general: {
                    name: "",
                    category: {
                        id: "",
                        name: "",
                    },
                    barcode_type: "ean13",
                    is_variant: false,
                    is_stock: true,
                    alert_qty: 0,
                    is_account: true,
                    supply: {
                        id: "",
                        name: "",
                    },
                    sale: {
                        id: "",
                        name: "",
                    },
                    return_sale: {
                        id: "",
                        name: "",
                    },
                    discount: {
                        id: "",
                        name: "",
                    },
                    sent: {
                        id: "",
                        name: "",
                    },
                    cost: {
                        id: "",
                        name: "",
                    },
                    retur_purchase: {
                        id: "",
                        name: "",
                    },
                    supplier_debt: {
                        id: "",
                        name: "",
                    },
                },
                other_detail: {
                    weight: 0,
                    brand: {
                        id: "",
                        name: "",
                    },
                    description: "",
                },
                variations: [
                    {
                        show: true,
                        subunits: [],
                        name: "",
                        barcode: "",
                        purchase_price: 0,
                        selling_price: 0,
                        grocery: 0,
                        sku: "",
                        unit: "",
                        unit_sale: "",
                        unit_purchase: "",
                        tax_sell: false,
                        tax_purchase: false,
                        purchase_tax: 0,
                        tax: 0,
                        stock: 0,
                        rak: {
                            id: null,
                            name: "",
                        },
                    },
                ],
                media: [],
            },
        };
    },
    computed: {},
    created() {
        this.settup();
        this.getSetting("");
        this.getUnits("");

        this.getCategories("");
        this.getBrands("");
        this.getRaks("");
    },
    methods: {
        async settup() {
            try {
                const response = await ApiData.get(`app/master/tax/sett`);
                var data = response.data;
                this.with_tax = data.with_tax;

                if (data.accountant_use == "no") {
                    this.product.info_general.is_account = false;
                    this.with_accountant = false;
                } else {
                    this.getAccounts("");
                }
            } catch (error) {
                console.log(error);
            }
        },

        async getSetting() {
            try {
                const response = await ApiData.get(
                    `app/account/components/setting`
                );
                var data = response.data;
                this.account_default = data;

                this.product.info_general = {
                    name: "",
                    category: {
                        id: "",
                        name: "",
                    },
                    barcode_type: "ean13",
                    is_variant: false,
                    is_stock: true,
                    alert_qty: 0,
                    is_account: true,
                    supply: this.account_default.product_supply,
                    sale: this.account_default.product_sale,
                    return_sale: this.account_default.product_retur_sale,
                    discount: this.account_default.product_discount_sale,
                    sent: this.account_default.product_sent,
                    cost: this.account_default.product_cost,
                    retur_purchase: this.account_default.product_retur_purchase,
                    supplier_debt: this.account_default.product_supplier_debt,
                };
            } catch (error) {
                console.log(error);
            }
        },

        async getUnits() {
            try {
                const response = await ApiData.get(
                    `app/inventory/components/units?only_parent=0`
                );
                var data = response.data;
                this.units = data.units;
                if (this.units.length > 0) {
                    this.product.variations[0].unit = this.units[0].id;
                }
            } catch (error) {
                console.log(error);
            }
        },

        async getAccounts(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&only_parent=yes`
                );
                var data = response.data;
                this.accounts = data.accounts;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getUnitChild(variant) {
            try {
                const response = await ApiData.get(
                    `app/inventory/components/units?only_parent=1&unit_parent=${variant.unit}`
                );
                var data = response.data;
                variant.subunits = data.units;
            } catch (error) {
                console.log(error);
            }
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

        async getRaks(query) {
            this.loader.rak = true;
            try {
                const response = await ApiData.get(
                    `app/inventory/components/raks?name=${query}`
                );
                var data = response.data;
                this.raks = data.raks;
                this.loader.rak = false;
            } catch (error) {
                console.log(error);
            }
        },

        validationVariant() {
            var totalVariant = this.product.variations.length;
            if (totalVariant > 1) {
                Swal.fire({
                    title: "Apakah Anda Yakin ?",
                    text: "Terdapat beberapa variant yang telah anda buat dan akan di hapus hingga menyisakan satu yang teratas",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yakin",
                }).then((result) => {
                    if (result.isConfirmed) {
                        var wajibada = totalVariant - 1;
                        var deleteVariant = totalVariant - wajibada;
                        this.product.variations.splice(deleteVariant);
                    } else {
                        this.product.info_general.is_variant = true;
                        Swal.fire("Membatalkan Opsi");
                    }
                });
            }
        },

        async customBase64Uploader(event) {
            var totalFile = parseInt(event.files.length) - 1;
            const file = event.files[totalFile];

            const reader = new FileReader();
            let blob = await fetch(file.objectURL).then((r) => r.blob());

            reader.onloadend = () => {
                const base64data = reader.result;
                var newImage = {
                    url: base64data,
                    name: file.name,
                };
                this.product.media.push(newImage);
            };

            reader.readAsDataURL(blob);
        },

        addVariant() {
            var unit = "";
            if (this.units.length > 0) {
                var unit = this.units[0].id;
            }

            if (this.product.info_general.is_variant == true) {
                var newVariant = {
                    show: true,
                    subunits: [],
                    name: "",
                    barcode: "",
                    purchase_price: 0,
                    selling_price: 0,
                    grocery: 0,
                    sku: "",
                    unit: unit,
                    unit_sale: "",
                    unit_purchase: "",
                    tax_sell: false,
                    tax_purchase: false,
                    purchase_tax: 0,
                    tax: 0,
                    stock: 0,
                    rak: {
                        id: null,
                        name: "",
                    },
                };
                this.product.variations.push(newVariant);
            }
        },

        removeVariant(index) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yakin",
            }).then((result) => {
                if (result.isConfirmed) {
                    this.product.variations.splice(index, 1);
                } else {
                    Swal.fire("Membatalkan Proses Hapus Variant");
                }
            });
        },

        createProductValidation() {
            this.$refs.ValidationCreateProduct.validate().then((success) => {
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
                    ApiData.post("app/inventory/create", this.product)
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
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
    },
    mounted: function () {},
    watch: {},
};
</script>
