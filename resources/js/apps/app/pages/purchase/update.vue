<template>
    <Form ref="ValidationForOtherInformation" class="col-12">
        <div class="row">
            <div class="col-11">
                <div class="card custom-card">
                    <div class="card-body add-product p-0">
                        <div class="p-4">
                            <div class="row gx-5">
                                <!-- Supplier -->
                                <div class="col-lg-12">
                                    <label
                                        for="transaction-ref"
                                        class="form-label"
                                        >Supplier</label
                                    >
                                    <Field
                                        :rules="{
                                            required: true,
                                        }"
                                        v-slot="{ errors }"
                                        v-model="
                                            transaction.general_information
                                                .supplier
                                        "
                                        name="Pilih Supplier"
                                    >
                                        <Multiselect
                                            v-model="
                                                transaction.general_information
                                                    .supplier
                                            "
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
                                            @select="changeSupplier"
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
                                                    <span
                                                        class="option__title"
                                                        >{{
                                                            props.option.name
                                                        }}</span
                                                    >
                                                    <br />
                                                    <span
                                                        class="option__small"
                                                        >{{
                                                            props.option.address
                                                        }}</span
                                                    >
                                                </div>
                                            </template>
                                        </Multiselect>
                                        <div class="fs-sm text-danger">
                                            {{ errors[0] }}
                                        </div>
                                    </Field>
                                </div>
                                <!-- End Supplier -->

                                <!-- Date -->
                                <div class="col-lg-6 mt-2">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                        >Tanggal
                                    </label>
                                    <Field
                                        :rules="{
                                            required: true,
                                        }"
                                        v-slot="{ errors }"
                                        v-model="
                                            transaction.general_information.date
                                        "
                                        name="Tanggal Transaksi"
                                    >
                                        <Calendar
                                            :showButtonBar="true"
                                            inputId="calendarPopup"
                                            :hideOnDateTimeSelect="true"
                                            style="width: 100%"
                                            v-model="
                                                transaction.general_information
                                                    .date
                                            "
                                            dateFormat="dd-mm-yy"
                                        />
                                        <div class="fs-sm text-danger">
                                            {{ errors[0] }}
                                        </div>
                                    </Field>
                                </div>
                                <!-- End Date -->

                                <!-- Nomor Referensi -->
                                <div class="col-lg-6 mt-2">
                                    <Field
                                        :rules="{
                                            required: true,
                                        }"
                                        v-slot="{ errors }"
                                        v-model="
                                            transaction.general_information
                                                .no_ref
                                        "
                                        name="No. Faktur"
                                    >
                                        <label
                                            for="product-name-add"
                                            class="form-label"
                                            >No.Faktur
                                        </label>

                                        <InputText
                                            v-model="
                                                transaction.general_information
                                                    .no_ref
                                            "
                                            type="text"
                                            style="width: 100%"
                                        />
                                    </Field>
                                </div>
                                <!-- End Nomor Referensi -->

                                <!-- No Supplier -->
                                <div class="col-lg-6 mt-2">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                        >No. Form
                                    </label>

                                    <InputText
                                        v-model="
                                            transaction.general_information
                                                .supplier_ref
                                        "
                                        type="text"
                                        style="width: 100%"
                                    />
                                </div>
                                <!-- End No Supplier -->

                                <!-- Warehouse -->
                                <div class="col-lg-6 mt-2">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                        >Pilih Gudang</label
                                    >
                                    <Field
                                        :rules="{
                                            required: true,
                                        }"
                                        v-slot="{ errors }"
                                        v-model="
                                            transaction.general_information
                                                .warehouse
                                        "
                                        name="Pilih Gudang"
                                    >
                                        <Multiselect
                                            v-model="
                                                transaction.general_information
                                                    .warehouse
                                            "
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
                                        <div class="fs-sm text-danger">
                                            {{ errors[0] }}
                                        </div>
                                    </Field>
                                </div>
                                <!-- End Warehouse -->

                                <!-- Kebijakan Piutang -->
                                <div class="col-lg-6 mt-2">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                        >Kebijakan Pembayaran</label
                                    >
                                    <Field
                                        :rules="{
                                            required: true,
                                        }"
                                        v-slot="{ errors }"
                                        v-model="
                                            transaction.general_information
                                                .due_limit
                                        "
                                        name="Kebijakan Pembayaran"
                                    >
                                        <Dropdown
                                            v-model="
                                                transaction.general_information
                                                    .due_limit
                                            "
                                            :options="terms"
                                            optionLabel="name"
                                            optionValue="due_date"
                                            placeholder="Pilih Opsi"
                                            style="width: 100%"
                                            class="w-full md:w-14rem"
                                        />
                                        <div class="fs-sm text-danger">
                                            {{ errors[0] }}
                                        </div>
                                    </Field>
                                </div>
                                <!-- End Term -->

                                <!-- Product -->
                                <div class="col-12">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                        >Cari dan Pilih Produk</label
                                    >
                                    <br />
                                    <small
                                        >Wajib Pilih Supplier Terlebih
                                        Dahulu</small
                                    >
                                    <span class="p-fluid">
                                        <div class="p-inputgroup">
                                            <Multiselect
                                                v-model="selected_products"
                                                :options="products"
                                                :multiple="false"
                                                :disabled="
                                                    transaction
                                                        .general_information
                                                        .supplier == []
                                                        ? true
                                                        : transaction
                                                              .general_information
                                                              .supplier?.id ==
                                                              '' ||
                                                          transaction
                                                              .general_information
                                                              .supplier?.id ==
                                                              null
                                                        ? true
                                                        : false
                                                "
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
                                                @select="selectedProducts"
                                                @search-change="getProducts"
                                            ></Multiselect>
                                            <Button
                                                icon="fe fe-box"
                                                class="p-button-info"
                                                @click="getSavingTransaction"
                                                type="button"
                                            />
                                            <Button
                                                icon="fa fa-file"
                                                class="p-button-info"
                                                @click="getPoTransaction"
                                                type="button"
                                            />
                                        </div>
                                    </span>
                                </div>
                                <!-- End Product -->

                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Qty</th>
                                                    <th>Satuan</th>
                                                    <th>Harga</th>
                                                    <th>Diskon</th>
                                                    <th
                                                        v-if="
                                                            tax_option.with_tax
                                                        "
                                                    >
                                                        Pajak
                                                    </th>
                                                    <th>Harga Total</th>
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
                                                        <InputText
                                                            v-model="item.qty"
                                                            style="width: 50px"
                                                            type="number"
                                                            class="form-control"
                                                            placeholder="Qty Pembelian"
                                                        />
                                                    </td>
                                                    <td>
                                                        <Dropdown
                                                            :reduce="
                                                                (label) =>
                                                                    label.value
                                                            "
                                                            v-model="item.unit"
                                                            :options="
                                                                item.subunits
                                                            "
                                                            style="width: 75px"
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
                                                            placeholder="Harga Modal"
                                                        />
                                                    </td>

                                                    <td class="discount-input">
                                                        <InputNumber
                                                            v-model="
                                                                item.discount_amount
                                                            "
                                                            placeholder="Diskon"
                                                        />
                                                    </td>
                                                    <td
                                                        v-if="
                                                            tax_option.with_tax
                                                        "
                                                    >
                                                        <Dropdown
                                                            :reduce="
                                                                (label) =>
                                                                    label.value
                                                            "
                                                            v-model="item.tax"
                                                            :options="taxrates"
                                                            style="width: 75px"
                                                            optionLabel="code"
                                                            optionValue="amount"
                                                            placeholder="Pilih"
                                                        />
                                                    </td>
                                                    <td>
                                                        Rp
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
                                                                RemoveItem(
                                                                    index,
                                                                    item.item_id
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
                                        </table>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="row">
                                        <!-- Tipe Diskon -->
                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Tipe Diskon Transaksi</label
                                            >
                                            <Field
                                                :rules="{
                                                    required: true,
                                                }"
                                                v-slot="{ errors }"
                                                v-model="
                                                    transaction
                                                        .payment_information
                                                        .discount_type
                                                "
                                                name="Tipe Diskon Transaksi"
                                            >
                                                <Dropdown
                                                    v-model="
                                                        transaction
                                                            .payment_information
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
                                                <div class="fs-sm text-danger">
                                                    {{ errors[0] }}
                                                </div>
                                            </Field>
                                        </div>
                                        <!-- End Tipe Diskon -->

                                        <!-- Diskon Transaksi -->
                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Diskon Transaksi</label
                                            >
                                            <InputNumber
                                                v-model="
                                                    transaction
                                                        .payment_information
                                                        .discount
                                                "
                                                style="width: 100%"
                                                :max="
                                                    transaction
                                                        .payment_information
                                                        .discount_type ==
                                                    'percent'
                                                        ? 100
                                                        : transaction
                                                              .payment_information
                                                              .subtotal
                                                "
                                                placeholder="Masukkan Diskon Transaksi"
                                                :prefix="
                                                    transaction
                                                        .payment_information
                                                        .discount_type ==
                                                    'fixed'
                                                        ? 'Rp '
                                                        : ''
                                                "
                                                :suffix="
                                                    transaction
                                                        .payment_information
                                                        .discount_type ==
                                                    'percent'
                                                        ? ' %'
                                                        : ''
                                                "
                                            />
                                            <label
                                                for="product-name-add"
                                                class="form-label mt-1 fs-12 op-5 text-muted mb-0"
                                                >Maximal Diskon
                                                {{
                                                    transaction
                                                        .payment_information
                                                        .discount_type ==
                                                    "percent"
                                                        ? 100 + " Persen"
                                                        : "Rp " +
                                                          formatNumber(
                                                              transaction
                                                                  .payment_information
                                                                  .subtotal
                                                          )
                                                }}
                                            </label>
                                        </div>
                                        <!-- End Diskon Transaksi -->

                                        <!-- Biaya Ongkos Kirim -->
                                        <div class="col-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Biaya Ongkir</label
                                            >
                                            <InputNumber
                                                v-model="
                                                    transaction
                                                        .payment_information
                                                        .shipping_cost
                                                "
                                                style="width: 100%"
                                                placeholder="Masukkan Biaya Ongkos Kirim"
                                            />
                                        </div>
                                        <!-- End Biaya Ongkos Kirim -->

                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Alokasikan Biaya Kirim Ke
                                                Barang</label
                                            >
                                            <Field
                                                :rules="{
                                                    required: true,
                                                }"
                                                v-slot="{ errors }"
                                                v-model="
                                                    transaction
                                                        .payment_information
                                                        .shipping_alocation
                                                "
                                                name="Alokasi Biaya Kirim"
                                            >
                                                <Dropdown
                                                    v-model="
                                                        transaction
                                                            .payment_information
                                                            .shipping_alocation
                                                    "
                                                    :options="[
                                                        {
                                                            label: 'Iya',
                                                            value: 'product',
                                                        },
                                                        {
                                                            label: 'Tidak',
                                                            value: 'beban',
                                                        },
                                                    ]"
                                                    optionLabel="label"
                                                    optionValue="value"
                                                    placeholder="Pilih Opsi"
                                                    style="width: 100%"
                                                    class="w-full md:w-14rem"
                                                />
                                                <div class="fs-sm text-danger">
                                                    {{ errors[0] }}
                                                </div>
                                            </Field>
                                        </div>

                                        <!-- Date -->
                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Catatan / Keterangan</label
                                            >
                                            <textarea
                                                class="form-control"
                                                v-model="
                                                    transaction
                                                        .payment_information
                                                        .note
                                                "
                                            ></textarea>
                                        </div>
                                        <!-- End Date -->

                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Alamat Pengambilan</label
                                            >
                                            <textarea
                                                class="form-control"
                                                v-model="
                                                    transaction
                                                        .general_information
                                                        .address
                                                "
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <h5>Ringkasan Transaksi Pembelian</h5>
                                    <table
                                        class="table-centered border mb-lg-0 table mt-3"
                                    >
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
                                                            this.transaction
                                                                .payment_information
                                                                .discount_product_total
                                                        )
                                                    }}
                                                </td>
                                            </tr>
                                            <tr
                                                v-if="
                                                    transaction
                                                        .payment_information
                                                        .tax_product_total > 0
                                                "
                                            >
                                                <td>PPN</td>
                                                <td class="text-right">
                                                    {{
                                                        formatNumber(
                                                            this.transaction
                                                                .payment_information
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
                                                            this.transaction
                                                                .payment_information
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
                                                            this.transaction
                                                                .payment_information
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
                                                            this.transaction
                                                                .payment_information
                                                                .shipping_cost
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
                                                                this.transaction
                                                                    .payment_information
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-1">
                <button
                    type="button"
                    @click="processTransaction(false)"
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
                    v-if="transaction.payment_information.payment.due_total > 0"
                    type="button"
                    @click="addToPayment()"
                    :disabled="loader.submit"
                    v-tooltip.top="'Simpan & Bayar Transaksi'"
                    class="btn btn-success btn-block label-btn label-end mr-2"
                >
                    <i
                        class="fa fa-money label-btn-icon ms-2"
                        style="font-size: 30px"
                    ></i>
                </button>
                <button
                    type="button"
                    :disabled="loader.submit"
                    @click="processTransaction(false, 'print')"
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

    <!-- Modal For Saving Transaction -->
    <Dialog
        v-model:visible="modal.saving_transaction"
        header="Data Transaksi Tersimpan"
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
                            v-model="filter.name"
                            @keyup="searchData()"
                            class="form-control"
                            placeholder="Cari Transaksi...."
                            aria-describedby="basic-addon1"
                        />
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <DataTable
                        :value="saving_transaction.transactions"
                        :paginator="true"
                        :rows="saving_transaction.limit"
                        :rowsPerPageOptions="[20, 50, 100]"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :lazy="true"
                        :totalRecords="saving_transaction.totalRows"
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
                                    @click="addThisTransaction(data)"
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
    <!-- End Modal For Saving Transaction -->

    <!-- Modal For Payment Account -->
    <Dialog
        v-model:visible="modal.forpayment"
        class="filter-data"
        header="Tambah dan Bayar Pembelian"
        style="width: 35rem; z-index: 1065 !important"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row p-3">
            <div class="col-lg-6 mb-2">
                <label for="user-ref" class="form-label">Tanggal </label>
                <Calendar
                    inputId="calendarPopup"
                    style="width: 100%"
                    v-model="transaction.payment_information.date"
                    dateFormat="dd-mm-yy"
                />
            </div>

            <div class="col-lg-6 mb-2">
                <label for="user-ref" class="form-label">Nominal </label>
                <InputNumber
                    style="width: 100%"
                    :max="transaction.payment_information.finalTotal"
                    v-model="transaction.payment_information.pay_total"
                />
            </div>

            <div class="col-lg-12 mb-2">
                <label for="user-date" class="form-label">Metode </label>
                <Field
                    :rules="{
                        required: true,
                    }"
                    v-slot="{ errors }"
                    v-model="transaction.payment_information.method"
                    :name="'Metode Pembayaran'"
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
                    <div class="fs-sm text-danger">
                        {{ errors[0] }}
                    </div>
                </Field>
            </div>

            <div class="col-12">
                <Divider />
            </div>
        </div>
        <template #footer>
            <button
                type="button"
                :disabled="loader.submit"
                @click="modal.forpayment = false"
                class="btn btn-outline-danger btn-wave waves-effect waves-light mr-2"
            >
                Batalkan
            </button>

            <button
                type="button"
                @click="processTransaction(true)"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                {{ loader.submit ? "Loading...." : "Simpan & Bayar" }}
            </button>
        </template>
    </Dialog>
    <!-- End Modal For Payment Account -->

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
            suppliers: [],
            products: [],
            warehouses: [],
            edit_item: [],
            selected_products: [],
            terms: [],
            supplier: {
                id: "",
            },
            warehouse: {
                id: "",
                name: "",
            },
            modal: {
                saving_transaction: null,
                forpayment: false,
            },
            saving_transaction: {
                totalRows: 0,
                page: 1,
                limit: 20,
                transactions: [],
            },
            po_transaction: {
                totalRows: 0,
                page: 1,
                limit: 20,
                modal: false,
                name: "",
                transactions: [],
            },
            show: {
                product: true,
                general: true,
                items: true,
                payment: true,
            },
            loader: {
                supplier: false,
                product: false,
                general: false,
                product: false,
                payment: false,
                data: true,
            },
            transaction: {
                status: "",
                with_pay: false,
                general_information: {
                    id: null,
                    store: {
                        id: "",
                        name: "",
                    },
                    warehouse: {
                        id: "",
                        name: "",
                    },
                    supplier: {
                        id: null,
                        name: "",
                    },
                    address: "",
                    date: null,
                    no_ref: null,
                    status: "",
                    supplier_ref: "",
                    due_limit: 0,
                },
                items: [],
                payment_information: {
                    discount_product_total: 0,
                    tax_product_total: 0,
                    subtotal: 0,
                    discount_type: "percent",
                    discount: 0,
                    discount_total: 0,
                    tax: 0,
                    tax_total: 0,
                    shipping_cost: 0,
                    note: "",
                    finalTotal: 0,
                    date: "",
                    method: {
                        id: "",
                        name: "",
                    },
                    pay_total: 0,
                    payment: {
                        due_total: 0,
                    },
                    shipping_alocation: "product",
                },
            },
            filter: {
                name: "",
            },
            taxrates: [],
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
        this.getTaxrate();
        this.getDetails();
        this.getSuppliers("");
        this.getWarehouse();
        this.getTerm();
    },
    methods: {

        async getPoTransaction(page = 1) {
            if (
                this.transaction.general_information.supplier.id == null ||
                this.transaction.general_information.supplier.id == ""
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
                    `app/transactions/purchases/po?limit=${this.po_transaction.limit}&page=${this.po_transaction.page}&ref=${this.po_transaction.name}&status=open&supplier=${this.transaction.general_information.supplier.id}`
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
                        item_id: product.id,
                        variation_id: product.variation_id,
                        product_id: product.product_id,
                        name: product.name,
                        qty: product.qty,
                        unit_price: 0,
                        without_discount: product.without_discount,
                        purchase_price_inc_tax: 0,
                        discount_amount: 0,
                        discount: 0,
                        tax:
                            product.tax_purchase == true
                                ? this.tax_option.tax_one
                                : 0,
                        total_discount: 0,
                        discount_type: "fixed",
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
        
        addToPayment() {
            this.modal.forpayment = true;
            this.getMethods("");
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

        async getTerm() {
            try {
                const response = await ApiData.get(`app/master/term?limit=30`);
                var data = response.data;
                this.terms = data.terms;
            } catch (error) {
                console.log(error);
            }
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

        async getTaxrate() {
            try {
                const response = await ApiData.get(`app/master/tax`);
                var data = response.data;
                this.taxrates = data.taxrates;
            } catch (error) {
                console.log(error);
            }
        },

        async getDetails() {
            try {
                const response = await ApiData.get(
                    `app/transactions/purchases/edit/${this.$route.params.id}`
                );
                var data = response.data;
                this.transaction.general_information = data.general_information;
                this.transaction.payment_information = data.payment_information;
                this.transaction.items = data.items;
                this.tax_option.default =
                    data.general_information.supplier.default;
                this.supplier = data.general_information.supplier;
                this.warehouse = data.general_information.warehouse;

                setTimeout(() => {
                    this.calculateSummary();
                }, 500);
            } catch (error) {
                console.log(error);
            }
        },

        // Get Informasi Supplier
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

        changeSupplier() {
            if (this.transaction.items.length > 0) {
                this.$toast.add({
                    severity: "error",
                    summary: "Peringatan",
                    detail: "Silahkan hapus seluruh item terlebih dahulu sebelum merubah supplier",
                    life: 3000,
                });

                this.transaction.general_information.supplier = {
                    id: this.supplier.id,
                    name: this.supplier.name,
                };
            } else {
                this.supplier = {
                    id: this.transaction.general_information.supplier.id,
                    name: this.transaction.general_information.supplier.name,
                };

                this.tax_option.default = e.default;
                this.tax_option.with_tax = e.tax_option;
                this.transaction.general_information.due_limit = e.due_date;
            }
        },

        changeWarehouse() {
            if (this.transaction.items.length > 0) {
                this.$toast.add({
                    severity: "error",
                    summary: "Peringatan",
                    detail: "Silahkan hapus seluruh item terlebih dahulu sebelum merubah gudang",
                    life: 3000,
                });

                this.transaction.general_information.warehouse = {
                    id: this.warehouse.id,
                    name: this.warehouse.name,
                };
            } else {
                this.warehouse = {
                    id: this.transaction.general_information.warehouse.id,
                    name: this.transaction.general_information.warehouse.name,
                };
            }
        },

        async getSavingTransaction(page = 1) {
            if (
                this.transaction.general_information.supplier.id == null ||
                this.transaction.general_information.supplier.id == ""
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
            this.saving_transaction.page = page;
            try {
                const response = await ApiData.get(
                    `app/transactions/purchases/received?limit=${this.saving_transaction.limit}&page=${this.saving_transaction.page}&ref=${this.filter.name}&status=received_not_use&supplier=${this.transaction.general_information.supplier.id}&warehouse=${this.transaction.general_information.warehouse.id}&with_warehouse=yes`
                );
                var data = response.data;
                this.saving_transaction.transactions = data.transactions;
                this.saving_transaction.totalRows = data.totalRows;
                this.loader.data = false;
                this.modal.saving_transaction = true;
            } catch (error) {
                console.log(error);
            }
        },

        searchData() {
            this.doSearch(this);
        },

        doSearch: _.debounce((rootInstance) => {
            rootInstance.getSavingTransaction();
        }, 300),

        onPageChange(e) {
            this.saving_transaction.limit = e.rows;
            this.saving_transaction.page = e.page += 1;
            this.getData(this.saving_transaction.page);
        },

        async addThisTransaction(data) {
            try {
                const response = await ApiData.get(
                    `app/transactions/purchases/received/detail/${data.id}`
                );

                var items = response.data.details.items;
                for (var i in items) {
                    var product = items[i];
                    this.transaction.items.push({
                        item_id: product.id,
                        variation_id: product.variation_id,
                        product_id: product.product_id,
                        name: product.name,
                        qty: product.qty,
                        unit_price: 0,
                        without_discount: product.without_discount,
                        purchase_price_inc_tax: 0,
                        discount_amount: 0,
                        discount: 0,
                        tax:
                            product.tax_purchase == true
                                ? this.tax_option.tax_one
                                : 0,
                        total_discount: 0,
                        discount_type: "fixed",
                        total_tax: 0,
                        unit: product.unit,
                        subtotal: product.subtotal,
                        subunits: product.subunits,
                    });
                }

                this.modal.saving_transaction = false;
                this.calculateSummary();
            } catch (error) {
                console.log(error);
            }
        },

        // Get Data Variations
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

        selectedProducts() {
            if (this.selected_products != null) {
                var product = this.selected_products;
                // var idproduct = false;
                // idproduct = this.transaction.items.filter((item) => {
                //     if (item.variation_id == product["id"]) {
                //         return true;
                //     }
                // });

                // if (idproduct == false) {

                // }

                this.transaction.items.push({
                    item_id: null,
                    variation_id: product.id,
                    product_id: product.product_id,
                    name: product.name,
                    qty: 1,
                    unit_price: product.purchase_price,
                    without_discount: product.purchase_price,
                    purchase_price_inc_tax: product.purchase_price,
                    discount_amount: 0,
                    discount: 0,
                    tax:
                        product.tax_purchase == true
                            ? this.tax_option.tax_one
                            : 0,
                    total_discount: 0,
                    discount_type: "fixed",
                    total_tax: 0,
                    unit: product.unit_purchase,
                    subtotal: product.purchase_price,
                    subunits: product.units,
                });
            }

            this.selected_products = null;
            this.calculateSummary();
        },

        RemoveItem(index, item_id) {
            if (item_id != null) {
                Swal.fire({
                    title: "Apakah Anda Yakin ?",
                    text: "Klik Ok Untuk Menghapus Item ini didalam daftar transaksi",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ok",
                }).then((result) => {
                    if (result.isConfirmed) {
                        ApiData.delete(
                            "app/transactions/purchases/delete-item/" + item_id
                        )
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                this.transaction.items.splice(index, 1);
                                setTimeout(() => {
                                    this.calculateSummary();
                                }, 500);
                            })
                            .catch((err) => {
                                this.$handleErrorResponse(err);
                            });
                    } else {
                        Swal.fire("Membatalkan Proses Hapus Data");
                    }
                });
            } else {
                this.transaction.items.splice(index, 1);
                setTimeout(() => {
                    this.calculateSummary();
                }, 500);
            }
        },

        updateItem(index) {
            var details = this.transaction.items[index];
            var taxrate = details.tax;
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

            details.discount_total = details.discount_amount;
            details.unit_price =
                details.without_discount - details.discount_amount;

            if (
                details.unit_price > 0 &&
                taxrate > 0 &&
                this.tax_option.with_tax == true
            ) {
                if (this.tax_option.default == true) {
                    var modalharga = details.unit_price / (1 + taxrate / 100);
                    var totaltax = (taxrate / 100) * modalharga;
                    details.total_tax = totaltax;
                    details.purchase_price_inc_tax = details.unit_price;
                } else {
                    var totaltax = (taxrate / 100) * details.unit_price;
                    details.total_tax = totaltax;
                    details.purchase_price_inc_tax =
                        details.unit_price + details.total_tax;
                }
            } else {
                details.purchase_price_inc_tax = details.unit_price;
            }

            details.subtotal = details.purchase_price_inc_tax * qtyItem;
            this.calculateSummary();
        },

        percentaseValidate() {
            this.transaction.payment_information.discount = 100;
            this.updateTransaction();
        },

        updateTransaction() {
            let data = this.transaction.payment_information;
            let afterDisc = this.transaction.payment_information.subtotal;

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

            let afterTax = afterDisc;
            if (data.tax > 0) {
                if (data.tax > 0) {
                    var totalTx = (data.tax / 100) * afterDisc;
                    this.transaction.payment_information.tax_total = totalTx;
                    afterTax = afterDisc + parseInt(totalTx);
                } else {
                    this.transaction.payment_information.tax_total = 0;
                    afterTax = afterDisc;
                }
            }

            let afterShipping = afterTax;
            if (data.shipping_cost != 0) {
                if (data.shipping_cost > 0) {
                    afterShipping = afterTax + data.shipping_cost;
                } else {
                    afterShipping = afterTax;
                }
            }

            this.transaction.payment_information.finalTotal = afterShipping;
        },

        calculateSummary() {
            var discountTotal = 0;
            var taxTotal = 0;
            var subtotal = 0;

            for (var i in this.transaction.items) {
                var detail = this.transaction.items[i];
                discountTotal += detail.total_discount * detail.qty;
                taxTotal += detail.total_tax * detail.qty;
                subtotal += detail.subtotal;
            }

            this.transaction.payment_information.discount_product_total =
                discountTotal;
            this.transaction.payment_information.tax_product_total = taxTotal;
            this.transaction.payment_information.subtotal = subtotal;
        },

        processTransaction(status, type = "") {
            this.transaction.with_pay = status;
            this.$refs.ValidationForOtherInformation.validate().then(
                (success) => {
                    if (!success.valid) {
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
                            "app/transactions/purchases/update/" +
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
                }
            );
        },

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
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
                this.transaction.general_information.supplier.name
            }</p>
            <p><strong>Alamat:</strong> <br /> ${
                this.transaction.general_information.supplier.address
            }</p>
            <p><strong>No. Form:</strong> ${
                this.transaction.general_information.ref_no
            }</p>
            <p><strong>Tanggal:</strong> ${
                this.transaction.general_information.date
            }</p>
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
            <p><strong>Keterangan:</strong></p>
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
        "transaction.payment_information": {
            handler: function (newVal, oldVal) {
                this.updateTransaction();
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>
