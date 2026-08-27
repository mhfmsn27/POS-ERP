<template>
    <div class="col-12">
        <div class="row gx-5">
            <!-- General Information -->
            <div class="col-xl-6 col-lg-6 col-md-6">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card custom-card shadow-none mb-0">
                            <div class="card-body p-0">
                                <Fieldset
                                    legend="Informasi Umum"
                                    :toggleable="true"
                                >
                                    <Form
                                        @submit="updateGeneralInformation()"
                                        ref="ValidationUpdateProduct"
                                    >
                                        <div class="row gy-3">
                                            <!-- Image Media -->
                                            <div class="col-lg-12">
                                                <label
                                                    for="product-name-add"
                                                    class="form-label"
                                                    >Gambar Produk ( Maximal 5
                                                    gambar)
                                                </label>
                                                <FileUpload
                                                    ref="galleryUploader"
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
                                                <label
                                                    for="product-name-add"
                                                    class="form-label"
                                                    >Nama Produk</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors, field }"
                                                    v-model="product.name"
                                                    name="Nama Produk"
                                                >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        v-bind="field"
                                                        v-model="product.name"
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
                                                <label
                                                    for="barcode-type"
                                                    class="form-label"
                                                    >Tipe Barcode</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="
                                                        product.barcode_type
                                                    "
                                                    name="Tipe Barcode"
                                                >
                                                    <Dropdown
                                                        v-model="
                                                            product.barcode_type
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
                                                <label
                                                    for="category-product"
                                                    class="form-label"
                                                    >Kategori Produk</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="product.category"
                                                    name="Kategori Produk"
                                                >
                                                    <Multiselect
                                                        v-model="
                                                            product.category
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
                                                <label
                                                    for="brand-product"
                                                    class="form-label"
                                                    >Brand Produk
                                                </label>
                                                <Multiselect
                                                    v-model="product.brand"
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
                                                                    class="form-label"
                                                                    >Aktivasi
                                                                    Produk</label
                                                                >
                                                                <br />
                                                                <InputSwitch
                                                                    v-model="
                                                                        product.is_active
                                                                    "
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <div
                                                                class="col-12 mb-3"
                                                            >
                                                                <label
                                                                    for="brand-product"
                                                                    class="form-label"
                                                                    >Produk
                                                                    Variant</label
                                                                >
                                                                <br />
                                                                <InputSwitch
                                                                    @click="
                                                                        validationVariant()
                                                                    "
                                                                    v-model="
                                                                        product.is_variant
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
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="row">
                                                            <div
                                                                class="col-12 mb-3"
                                                                v-if="
                                                                    product.is_stock
                                                                "
                                                            >
                                                                <label
                                                                    for="brand-product"
                                                                    class="form-label"
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
                                                                        product.alert_qty
                                                                    "
                                                                    name="Peringatan Stok Qty"
                                                                >
                                                                    <InputNumber
                                                                        style="
                                                                            width: 100%;
                                                                        "
                                                                        v-model="
                                                                            product.alert_qty
                                                                        "
                                                                        prefix="Qty: "
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
                                                            <div
                                                                class="col-12 mb-3"
                                                            >
                                                                <label
                                                                    for="brand-product"
                                                                    class="form-label"
                                                                    >Berat
                                                                    Produk (
                                                                    Gram )
                                                                </label>
                                                                <InputNumber
                                                                    style="
                                                                        width: 100%;
                                                                    "
                                                                    v-model="
                                                                        product.weight
                                                                    "
                                                                    suffix=" Gram"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <label
                                                    for="product-description"
                                                    class="form-label"
                                                    >Deskripsi Produk</label
                                                >
                                                <Editor
                                                    v-model="
                                                        product.description
                                                    "
                                                    editorStyle="height: 320px"
                                                />
                                            </div>
                                            <div
                                                class="col-12 d-flex justify-content-end"
                                            >
                                                <button
                                                    class="btn btn-outline-primary rounded-pill btn-wave waves-effect waves-light btn-sm"
                                                    type="submit"
                                                    :disabled="loader.submit"
                                                >
                                                    <i
                                                        class="bx bx-check-circle mr-2"
                                                    ></i>
                                                    Simpan Perubahan
                                                </button>
                                            </div>
                                        </div>
                                    </Form>
                                </Fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="card custom-card shadow-none mb-0">
                            <div class="card-body p-0">
                                <Fieldset
                                    legend="Gallery Produk"
                                    :toggleable="true"
                                >
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Ditambahkan</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                    <tbody>
                                                        <tr
                                                            v-for="(
                                                                image, i
                                                            ) in gallery"
                                                            :key="'gallery' + i"
                                                        >
                                                            <td>
                                                                <span
                                                                    class="avatar avatar-xxl me-2 mb-2 "
                                                                >
                                                                    <img
                                                                        :src="
                                                                            image.url
                                                                        "
                                                                        class="w-75"
                                                                        alt="product-gallery"
                                                                    />
                                                                </span>
                                                            </td>
                                                            <td>
                                                                {{
                                                                    image.created
                                                                }}
                                                            </td>
                                                            <td>
                                                                <button
                                                                    @click="
                                                                        removeImage(
                                                                            image,
                                                                            i
                                                                        )
                                                                    "
                                                                    class="btn btn-outline-danger rounded-pill btn-wave waves-effect waves-light btn-sm"
                                                                    type="button"
                                                                >
                                                                    <i
                                                                        class="fa fa-trash"
                                                                    ></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </Fieldset>
                            </div>
                        </div>
                    </div>
                    <Form
                        @submit="updateAccountInformation()"
                        ref="ValidationAccount"
                    >
                        <div
                            class="col-12 mb-4"
                            v-if="product.is_account && with_accountant"
                        >
                            <div class="card custom-card shadow-none mb-0">
                                <div class="card-body p-0">
                                    <Fieldset
                                        legend="Akun Akutansi"
                                        :toggleable="true"
                                        :collapsed="true"
                                    >
                                        <div class="row gy-3">
                                            <!-- Supply Account -->
                                            <div class="col-lg-6 mt-3">
                                                <label for="category-product"
                                                    >Persediaan</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="account.supply"
                                                    name="Akun Persediaan"
                                                >
                                                    <Multiselect
                                                        v-model="account.supply"
                                                        :options="
                                                            supply_account
                                                        "
                                                        :multiple="false"
                                                        :close-on-select="true"
                                                        :clear-on-select="true"
                                                        :preserve-search="true"
                                                        :searchable="true"
                                                        :loading="loader.supply"
                                                        :internal-search="true"
                                                        :options-limit="50"
                                                        placeholder="Pilih Akun"
                                                        open-direction="bottom"
                                                        label="name"
                                                        id="id"
                                                        track-by="name"
                                                        @search-change="
                                                            getSupplyAccount
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
                                                <label for="category-product"
                                                    >Penjualan</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="account.sale"
                                                    name="Penjualan Akun"
                                                >
                                                    <Multiselect
                                                        v-model="account.sale"
                                                        :options="sale_account"
                                                        :multiple="false"
                                                        :close-on-select="true"
                                                        :clear-on-select="true"
                                                        :preserve-search="true"
                                                        :searchable="true"
                                                        :loading="loader.sale"
                                                        :internal-search="true"
                                                        :options-limit="50"
                                                        placeholder="Pilih Akun"
                                                        open-direction="bottom"
                                                        label="name"
                                                        id="id"
                                                        track-by="name"
                                                        @search-change="
                                                            getSaleAccount
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
                                                <label for="category-product"
                                                    >Retur Penjualan</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="
                                                        account.return_sale
                                                    "
                                                    name="Retur Penjualan Akun"
                                                >
                                                    <Multiselect
                                                        v-model="
                                                            account.return_sale
                                                        "
                                                        :options="sale_account"
                                                        :multiple="false"
                                                        :close-on-select="true"
                                                        :clear-on-select="true"
                                                        :preserve-search="true"
                                                        :searchable="true"
                                                        :loading="loader.sale"
                                                        :internal-search="true"
                                                        :options-limit="50"
                                                        placeholder="Pilih Akun"
                                                        open-direction="bottom"
                                                        label="name"
                                                        id="id"
                                                        track-by="name"
                                                        @search-change="
                                                            getSaleAccount
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
                                                <label for="category-product"
                                                    >Diskon Penjualan</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="account.discount"
                                                    name="Diskon Penjualan Akun"
                                                >
                                                    <Multiselect
                                                        v-model="
                                                            account.discount
                                                        "
                                                        :options="sale_account"
                                                        :multiple="false"
                                                        :close-on-select="true"
                                                        :clear-on-select="true"
                                                        :preserve-search="true"
                                                        :searchable="true"
                                                        :loading="loader.sale"
                                                        :internal-search="true"
                                                        :options-limit="50"
                                                        placeholder="Pilih Akun"
                                                        open-direction="bottom"
                                                        label="name"
                                                        id="id"
                                                        track-by="name"
                                                        @search-change="
                                                            getSaleAccount
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
                                                <label for="category-product"
                                                    >Beban Pokok
                                                    Penjualan</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="account.cost"
                                                    name="Beban Pokok Akun"
                                                >
                                                    <Multiselect
                                                        v-model="account.cost"
                                                        :options="cost_account"
                                                        :multiple="false"
                                                        :close-on-select="true"
                                                        :clear-on-select="true"
                                                        :preserve-search="true"
                                                        :searchable="true"
                                                        :loading="loader.cost"
                                                        :internal-search="true"
                                                        :options-limit="50"
                                                        placeholder="Pilih Akun"
                                                        open-direction="bottom"
                                                        label="name"
                                                        id="id"
                                                        track-by="name"
                                                        @search-change="
                                                            getCostAccount
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
                                                <label for="category-product"
                                                    >Return Pembelian</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="
                                                        account.retur_purchase
                                                    "
                                                    name="Return Pembelian Akun"
                                                >
                                                    <Multiselect
                                                        v-model="
                                                            account.retur_purchase
                                                        "
                                                        :options="
                                                            supply_account
                                                        "
                                                        :multiple="false"
                                                        :close-on-select="true"
                                                        :clear-on-select="true"
                                                        :preserve-search="true"
                                                        :searchable="true"
                                                        :loading="loader.supply"
                                                        :internal-search="true"
                                                        :options-limit="50"
                                                        placeholder="Pilih Akun"
                                                        open-direction="bottom"
                                                        label="name"
                                                        id="id"
                                                        track-by="name"
                                                        @search-change="
                                                            getSupplyAccount
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

                                            <div
                                                class="col-12 d-flex justify-content-end"
                                            >
                                                <button
                                                    class="btn btn-outline-primary rounded-pill btn-wave waves-effect waves-light btn-sm"
                                                    type="submit"
                                                    :disabled="loader.submit"
                                                >
                                                    <i
                                                        class="bx bx-check-circle mr-2"
                                                    ></i>
                                                    Simpan Perubahan
                                                </button>
                                            </div>
                                        </div>
                                    </Fieldset>
                                </div>
                            </div>
                        </div>
                    </Form>
                </div>
            </div>
            <!-- End General Information -->

            <!-- Variant Information -->
            <div class="col-xl-6 col-lg-6 col-md-6">
                <div class="card custom-card shadow-none mb-0">
                    <div class="card-body p-0">
                        <div class="row gy-4">
                            <div
                                class="col-lg-12 d-flex justify-content-between mb-3 p-4"
                            >
                                <h4>Informasi Harga & Variant</h4>
                                <button
                                    @click="addVariant()"
                                    v-if="product.is_variant"
                                    class="btn btn-outline-primary rounded-pill btn-wave waves-effect waves-light btn-sm"
                                    type="button"
                                >
                                    <i class="bx bx-plus mr-2"></i> Tambah
                                    Variant
                                </button>
                            </div>
                            <div
                                class="col-lg-12 mb-3 p-4"
                                v-if="!loader.system"
                                v-for="(variant, index) in variations"
                                :key="index"
                            >
                                <Fieldset
                                    :legend="
                                        product.is_variant
                                            ? variant.name
                                            : 'Detail'
                                    "
                                    :toggleable="variant.show"
                                >
                                    <Form @submit="updateVariation(variant)">
                                        <div class="row">
                                            <!-- Nama Variant -->
                                            <div
                                                class="col-12 mb-3"
                                                v-if="product.is_variant"
                                            >
                                                <label class="form-label"
                                                    >Nama Variant</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="variant.name"
                                                    :name="
                                                        'Nama Variant' +
                                                        (index + 1)
                                                    "
                                                >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        v-model="variant.name"
                                                        placeholder="Masukkan Nama Variant"
                                                    />
                                                    <div
                                                        class="fs-sm text-danger"
                                                    >
                                                        {{ errors[0] }}
                                                    </div>
                                                </Field>
                                            </div>
                                            <!-- End Nama Variant -->

                                            <!-- Sku -->
                                            <div class="col-lg-6 mb-3">
                                                <label
                                                    for="product-name-add"
                                                    class="form-label"
                                                    >SKU</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="variant.sku"
                                                    :name="'SKU ' + (index + 1)"
                                                >
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        v-model="variant.sku"
                                                        placeholder="Masukkan SKU Produk"
                                                    />
                                                    <div
                                                        class="fs-sm text-danger"
                                                    >
                                                        {{ errors[0] }}
                                                    </div>
                                                </Field>
                                            </div>
                                            <!-- End Sku -->

                                            <!-- Barcode -->
                                            <div class="col-lg-6 mb-3">
                                                <label
                                                    for="barcode-product-add"
                                                    class="form-label"
                                                    >Barcode Produk</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    v-model="variant.barcode"
                                                />
                                                <label
                                                    for="barcode-product-add"
                                                    class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                                    >*Kamu bisa mengkosongkan
                                                    bidang ini untuk mengenerate
                                                    kode barcode secara
                                                    otomatis</label
                                                >
                                            </div>
                                            <!-- End Barcode -->

                                            <!-- Harga Jual -->
                                            <div class="col-lg-6 mb-3">
                                                <label
                                                    for="product-name-add"
                                                    class="form-label"
                                                    >Harga Jual</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="
                                                        variant.selling_price
                                                    "
                                                    :name="
                                                        'Harga Jual ' +
                                                        (index + 1)
                                                    "
                                                >
                                                    <InputNumber
                                                        style="width: 100%"
                                                        v-model="
                                                            variant.selling_price
                                                        "
                                                        prefix="Rp "
                                                    />
                                                    <div
                                                        class="fs-sm text-danger"
                                                    >
                                                        {{ errors[0] }}
                                                    </div>
                                                </Field>
                                            </div>
                                            <!-- End Harga Jual -->

                                            <!-- Harga Grosir -->
                                            <div class="col-lg-6 mb-3">
                                                <label
                                                    for="product-name-add"
                                                    class="form-label"
                                                    >Harga Grosir</label
                                                >
                                                <InputNumber
                                                    style="width: 100%"
                                                    v-model="variant.grocery"
                                                    prefix="Rp "
                                                />
                                            </div>
                                            <!-- End Harga Grosir -->

                                            <!-- Satuan Dasar -->
                                            <div class="col-lg-6 mb-3">
                                                <label
                                                    for="barcode-type"
                                                    class="form-label"
                                                    >Satuan Dasar</label
                                                >
                                                <Field
                                                    :rules="{
                                                        required: true,
                                                    }"
                                                    v-slot="{ errors }"
                                                    v-model="variant.unit"
                                                    :name="
                                                        'Satuan Dasar ' +
                                                        (index + 1)
                                                    "
                                                >
                                                    <Dropdown
                                                        v-model="variant.unit"
                                                        :options="units"
                                                        optionLabel="name"
                                                        optionValue="id"
                                                        placeholder="Pilih"
                                                        style="width: 100%"
                                                        @change="getUnitChild()"
                                                        class="w-full md:w-14rem"
                                                    />
                                                    <div
                                                        class="fs-sm text-danger"
                                                    >
                                                        {{ errors[0] }}
                                                    </div>
                                                </Field>
                                            </div>
                                            <!-- End Satuan Dasar -->

                                            <!-- Satuan Penjualan -->
                                            <div class="col-lg-6 mb-3">
                                                <label
                                                    for="barcode-type"
                                                    class="form-label"
                                                    >Satuan Penjualan</label
                                                >
                                                <Dropdown
                                                    v-model="variant.unit_sale"
                                                    :options="variant.subunits"
                                                    optionLabel="name"
                                                    optionValue="id"
                                                    placeholder="Pilih"
                                                    style="width: 100%"
                                                    class="w-full md:w-14rem"
                                                />
                                            </div>
                                            <!-- End Satuan Penjualan -->

                                            <!-- Satuan Pembelian -->
                                            <div class="col-lg-6 mb-3">
                                                <label
                                                    for="barcode-type"
                                                    class="form-label"
                                                    >Satuan Pembelian</label
                                                >
                                                <Dropdown
                                                    v-model="
                                                        variant.unit_purchase
                                                    "
                                                    :options="variant.subunits"
                                                    optionLabel="name"
                                                    optionValue="id"
                                                    placeholder="Pilih"
                                                    style="width: 100%"
                                                    class="w-full md:w-14rem"
                                                />
                                            </div>
                                            <!-- End Satuan Pembelian -->

                                            <!-- Rak -->
                                            <div class="col-lg-6">
                                                <label
                                                    for="category-product"
                                                    class="form-label"
                                                    >Rak Penyimpanan</label
                                                >
                                                <Multiselect
                                                    v-model="variant.rak"
                                                    :options="raks"
                                                    :multiple="false"
                                                    :close-on-select="true"
                                                    :clear-on-select="true"
                                                    :preserve-search="true"
                                                    :searchable="true"
                                                    :loading="loader.rak"
                                                    :internal-search="true"
                                                    :options-limit="50"
                                                    placeholder="Pilih Rak"
                                                    open-direction="bottom"
                                                    label="name"
                                                    id="id"
                                                    track-by="name"
                                                    @search-change="getRaks"
                                                ></Multiselect>
                                            </div>
                                            <!-- End Rak -->

                                            <div class="col-12">
                                                <div class="row">
                                                    <div
                                                        class="col-lg-6"
                                                        v-if="with_tax"
                                                    >
                                                        <div class="row">
                                                            <div
                                                                class="col-12 mb-3"
                                                            >
                                                                <label
                                                                    for="brand-product"
                                                                    class="form-label"
                                                                    >Menggunakan
                                                                    Pajak
                                                                    Penjualan</label
                                                                >
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
                                                        v-if="with_tax"
                                                    >
                                                        <div class="row">
                                                            <div
                                                                class="col-12 mb-3"
                                                            >
                                                                <label
                                                                    for="brand-product"
                                                                    class="form-label"
                                                                    >Menggunakan
                                                                    Pajak
                                                                    Pembelian</label
                                                                >
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
                                                    <div class="col-12">
                                                        <Fieldset
                                                            :legend="'Stok Awal'"
                                                            :toggleable="true"
                                                            :collapsed="true"
                                                        >
                                                            <table
                                                                class="table"
                                                            >
                                                                <tr>
                                                                    <th>
                                                                        Harga
                                                                        Modal
                                                                    </th>
                                                                    <th
                                                                        v-if="
                                                                            product.is_stock
                                                                        "
                                                                    >
                                                                        Stok
                                                                        Awal
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <th>
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
                                                                                (index +
                                                                                    1)
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
                                                                    </th>
                                                                    <th
                                                                        v-if="
                                                                            product.is_stock
                                                                        "
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
                                                                    </th>
                                                                </tr>
                                                            </table>
                                                        </Fieldset>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="col-12 d-flex justify-content-end"
                                            >
                                                <button
                                                    @click="
                                                        removeVariant(
                                                            variant,
                                                            index
                                                        )
                                                    "
                                                    v-if="
                                                        product.is_variant &&
                                                        index > 0
                                                    "
                                                    class="btn btn-outline-danger rounded-pill btn-wave waves-effect waves-light btn-sm me-2"
                                                    type="button"
                                                >
                                                    <i
                                                        class="bx bx-x-circle mr-2"
                                                    ></i>
                                                    Hapus Variant
                                                </button>

                                                <button
                                                    class="btn btn-outline-primary rounded-pill btn-wave waves-effect waves-light btn-sm"
                                                    type="submit"
                                                    :disabled="loader.submit"
                                                >
                                                    <i
                                                        class="bx bx-check-circle mr-2"
                                                    ></i>
                                                    Simpan Perubahan
                                                </button>
                                            </div>
                                        </div>
                                    </Form>
                                </Fieldset>
                            </div>
                            <div
                                class="col-12 d-flex justify-content-center"
                                v-else
                            >
                                <ProgressSpinner />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Variant Information -->
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
            categories: [],
            brands: [],
            taxrates: [],
            with_tax: false,
            with_accountant: true,
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
            loader: {
                category: false,
                rak: false,
                brand: false,
                submit: false,
                rak: false,
                system: false,
                sale: false,
                cost: false,
                debt: false,
                supply: false,
            },
            product: {
                id: "",
                name: "",
                is_variant: true,
                is_stock: true,
                price_type: "",
                barcode_type: "",
                alert_qty: 0,
                weight: 0,
                description: null,
                is_active: true,
                category: {
                    id: "",
                    name: "",
                },
                brand_id: {
                    id: "",
                    name: "",
                },
                media: [],
            },
            account: {
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
            variations: [],
            gallery: [],
            supply_account: [],
            sale_account: [],
            cost_account: [],
            debt_account: [],
        };
    },
    computed: {},
    created() {
        this.settup();
        this.getDetails();
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
                }
            } catch (error) {
                console.log(error);
            }
        },

        async getDetails() {
            this.loader.system = true;
            try {
                const response = await ApiData.get(
                    `app/inventory/detail/${this.$route.params.id}`
                );
                var data = response.data;
                this.product = data.details;
                this.variations = data.variations;
                this.gallery = data.gallery;
                this.account = data.account;
                console.log(data.account);
                this.loader.system = false;
                setTimeout(() => {
                    for (var v in this.variations) {
                        this.getUnitChild(this.variations[v]);
                    }
                }, 1000);
            } catch (error) {
                console.log(error);
            }
        },

        async getSaleAccount(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&only_parent=yes&default=pendapatan`
                );
                var data = response.data;
                this.sale_account = data.accounts;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getCostAccount(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&only_parent=yes&default=beban_penjualan`
                );
                var data = response.data;
                this.cost_account = data.accounts;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getSupplyAccount(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&only_parent=yes&default=persediaan`
                );
                var data = response.data;
                this.supply_account = data.accounts;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getLiabilityAccount(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&only_parent=yes&default=liabilitas_pendek`
                );
                var data = response.data;
                this.debt_account = data.accounts;
                this.loader.account = false;
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
            var totalVariant = this.variations.length;
            if (totalVariant > 1) {
                Swal.fire({
                    title: "Peringatan!",
                    text: "Produk ini memiliki lebih dari satu variant, silahkan hapus beberapa variant hingga menyisakan 1 variant untuk menjadikan produk ini non variant",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ok",
                }).then((result) => {
                    this.product.is_variant = true;
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
            if (this.product.is_variant == true) {
                var newVariant = {
                    id: null,
                    show: true,
                    name: "",
                    barcode: "",
                    purchase_price: 0,
                    selling_price: 0,
                    grocery: 0,
                    sku: "",
                    unit: "",
                    unit_sale: "",
                    unit_purchase: "",
                    subunits: [],
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
                this.variations.push(newVariant);
            }
        },

        removeVariant(variant, index) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "Data yang telah di hapus tidak dapat di kembalikan lagi!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yakin",
            }).then((result) => {
                if (result.isConfirmed) {
                    if (variant.id != null) {
                        NProgress.start();
                        NProgress.set(0.1);
                        ApiData.delete(
                            "app/inventory/delete-variations/" + variant.id
                        )
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                NProgress.done();
                                this.variations.splice(index, 1);
                            })
                            .catch((err) => {
                                NProgress.done();
                                this.$handleErrorResponse(err);
                            });
                    } else {
                        this.variations.splice(index, 1);
                    }
                } else {
                    Swal.fire("Membatalkan Proses Hapus Variant");
                }
            });
        },

        removeImage(image, index) {
            NProgress.start();
            NProgress.set(0.1);
            ApiData.delete("app/inventory/delete-media/" + image.id)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.gallery.splice(index, 1);
                })
                .catch((err) => {
                    NProgress.done();
                    this.$handleErrorResponse(err);
                });
        },

        updateGeneralInformation() {
            this.$refs.ValidationUpdateProduct.validate().then((success) => {
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
                        "app/inventory/update/" + this.$route.params.id,
                        this.product
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.product.media = [];
                            this.$refs.galleryUploader.clear();
                            this.files = null;
                            this.gallery = response.data.gallery;
                            this.loader.submit = false; 
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.loader.submit = false;
                            this.$handleErrorResponse(err);
                        });
                }
            });
        },

        updateVariation(variant) {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            ApiData.post(
                "app/inventory/update-variations/" + this.$route.params.id,
                variant
            )
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    variant.id = response.data.variation_id;
                    this.loader.submit = false;
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        updateAccountInformation() {
            this.$refs.ValidationAccount.validate().then((success) => {
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
                        "app/inventory/update-account/" + this.$route.params.id,
                        this.account
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.loader.submit = false;
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
