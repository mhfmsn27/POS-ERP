<template>
    <Form ref="ValidationForOtherInformation" class="col-12">
        <div class="row">
            <div class="col-11">
                <div class="card custom-card">
                    <div class="card-body add-product p-0">
                        <div class="p-4">
                            <div class="row gx-5">
                                <!-- customer -->
                                <div class="col-12 mt-2">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                        >Pelanggan</label
                                    >
                                    <Field
                                        :rules="{
                                            required: true,
                                        }"
                                        v-slot="{ errors }"
                                        v-model="
                                            transaction.general_information
                                                .customer
                                        "
                                        name="Informasi Pelanggan"
                                    >
                                        <Multiselect
                                            v-model="
                                                transaction.general_information
                                                    .customer
                                            "
                                            :options="customers"
                                            :multiple="false"
                                            :close-on-select="true"
                                            :clear-on-select="true"
                                            :preserve-search="true"
                                            :searchable="true"
                                            :loading="loader.customer"
                                            :internal-search="true"
                                            :options-limit="50"
                                            placeholder="Pilih customer"
                                            open-direction="bottom"
                                            label="name"
                                            id="id"
                                            track-by="name"
                                            :allowEmpty="false"
                                            tagPlaceholder=""
                                            selectLabel=""
                                            @select="changeCustomer"
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
                                <!-- End customer -->

                                <!-- customer Referensi -->
                                <div class="col-lg-6 mt-2">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                        >Tanggal Transaksi</label
                                    >
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
                                <!-- End customer Referensi -->

                                <!-- Nomor Referensi -->
                                <div class="col-lg-6 mt-2">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                        >Nomor Faktur
                                    </label>

                                    <InputText
                                        v-model="
                                            transaction.general_information
                                                .no_ref
                                        "
                                        type="text"
                                        style="width: 100%"
                                        placeholder="Masukkan No Ref Penjualan"
                                    />
                                </div>
                                <!-- End Nomor Referensi -->

                                <!-- Kebijakan Piutang -->
                                <div class="col-lg-6 mt-2">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                        >Syarat Pembayaran</label
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

                                <!-- Warehouse -->
                                <div class="col-lg-6 mt-2">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                    >
                                        Gudang</label
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

                                <!-- Ekspedisi -->
                                <div class="col-lg-6 mt-2">
                                    <label
                                        for="transaction-ref"
                                        class="form-label"
                                        >Ekspedisi</label
                                    >
                                    <Multiselect
                                        v-model="
                                            transaction.general_information
                                                .courier
                                        "
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
                                <!-- End Ekspedisi -->

                                <!-- Komisi Penjualann -->
                                <div class="col-lg-6 mt-2">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                        >Pilih Penjual</label
                                    >
                                    <Multiselect
                                        v-model="
                                            transaction.general_information.user
                                        "
                                        :options="users"
                                        :multiple="false"
                                        :close-on-select="true"
                                        :clear-on-select="true"
                                        :preserve-search="true"
                                        :searchable="true"
                                        :loading="loader.user"
                                        :internal-search="true"
                                        :options-limit="50"
                                        placeholder="Pilih Pengguna"
                                        open-direction="bottom"
                                        label="name"
                                        id="id"
                                        track-by="name"
                                        tagPlaceholder=""
                                        selectLabel=""
                                        @search-change="getUsers"
                                    ></Multiselect>
                                </div>
                                <!-- End Komisi Penjualan -->

                                <!-- Product -->
                                <div class="col-12">
                                    <label
                                        for="product-name-add"
                                        class="form-label"
                                        >Cari dan Pilih Produk</label
                                    >
                                    <span class="p-fluid">
                                        <div class="p-inputgroup">
                                            <Multiselect
                                                v-model="selected_products"
                                                :disabled="
                                                    transaction
                                                        .general_information
                                                        .customer == []
                                                        ? true
                                                        : transaction
                                                              .general_information
                                                              .customer?.id ==
                                                              '' ||
                                                          transaction
                                                              .general_information
                                                              .customer?.id ==
                                                              null
                                                        ? true
                                                        : false
                                                "
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
                                                @select="selectedProducts"
                                                @search-change="getProducts"
                                            >
                                                <template #singleLabel="props">
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
                                                </template>
                                                <template #option="props">
                                                    <div class="option__desc">
                                                        <span
                                                            class="option__title"
                                                            >{{
                                                                props.option
                                                                    .name
                                                            }}</span
                                                        >
                                                        <br />
                                                        <span
                                                            class="option__small"
                                                            >Stok :
                                                            {{
                                                                props.option
                                                                    .stock
                                                            }}
                                                            ({{
                                                                props.option
                                                                    .unit_name
                                                            }})
                                                        </span>
                                                    </div>
                                                </template>
                                            </Multiselect>
                                            <Button
                                                icon="fa fa-truck"
                                                class="p-button-info"
                                                @click="getSavingTransaction"
                                                type="button"
                                            />
                                            <Button
                                                icon="fa fa-file"
                                                class="p-button-info"
                                                @click="getOfferTransaction"
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
                                                    <th>
                                                        <i
                                                            class="fa fa-list"
                                                        ></i>
                                                    </th>
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
                                                    <th
                                                        style="min-width: 120px"
                                                    >
                                                        Total Harga
                                                    </th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <draggable
                                                tag="tbody"
                                                v-model="transaction.items"
                                                :handle="'.drag-handle'"
                                                item-key="id"
                                                @end="onDragEnd"
                                            >
                                                <template
                                                    #item="{
                                                        element: item,
                                                        index,
                                                    }"
                                                >
                                                    <tr :key="index">
                                                        <td
                                                            class="drag-handle"
                                                            style="cursor: move"
                                                        >
                                                            <i
                                                                class="fa fa-list"
                                                            ></i>
                                                        </td>
                                                        <td>
                                                            <InputText
                                                                v-model="
                                                                    item.name
                                                                "
                                                                class="form-control"
                                                            />
                                                        </td>
                                                        <td>
                                                            <InputText
                                                                v-model="
                                                                    item.qty
                                                                "
                                                                style="
                                                                    width: 50px;
                                                                "
                                                                type="number"
                                                                class="form-control"
                                                                placeholder="Qty Penjualan"
                                                            />
                                                        </td>
                                                        <td>
                                                            <Dropdown
                                                                :reduce="
                                                                    (label) =>
                                                                        label.value
                                                                "
                                                                v-model="
                                                                    item.unit
                                                                "
                                                                :options="
                                                                    item.subunits
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
                                                                    item.without_discount
                                                                "
                                                                style="
                                                                    width: 100%;
                                                                "
                                                                placeholder="Harga Jual"
                                                            />
                                                        </td>

                                                        <td
                                                            class="discount-input"
                                                        >
                                                            <InputNumber
                                                                v-model="
                                                                    item.discount_amount
                                                                "
                                                                style="
                                                                    width: 70%;
                                                                "
                                                                placeholder="Masukkan Diskon"
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
                                                                v-model="
                                                                    item.tax
                                                                "
                                                                :options="
                                                                    taxrates
                                                                "
                                                                style="
                                                                    width: 100%;
                                                                "
                                                                optionLabel="code"
                                                                optionValue="amount"
                                                                placeholder="Pilih Pajak"
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
                                                                class="btn btn-danger btn-sm mr-2"
                                                                type="button"
                                                                v-tooltip.top="
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
                                                </template>
                                            </draggable>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <!-- Tipe Diskon -->
                                                <div class="col-lg-6 mt-2">
                                                    <label
                                                        for="product-name-add"
                                                        class="form-label"
                                                        >Tipe Diskon
                                                        Transaksi</label
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
                                                        <div
                                                            class="fs-sm text-danger"
                                                        >
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
                                                        :maxFractionDigits="2"
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
                                                                ? 100 +
                                                                  " Persen"
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
                                                <div class="col-12 mt-2">
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
                                                        prefix="Rp "
                                                    />
                                                </div>
                                                <!-- End Biaya Ongkos Kirim -->

                                                <!-- Date -->
                                                <div
                                                    class="col-lg-6 col-sm-12 mt-2"
                                                >
                                                    <label
                                                        for="product-name-add"
                                                        class="form-label"
                                                        >Catatan /
                                                        Keterangan</label
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

                                                <div
                                                    class="col-lg-6 col-sm-12 mt-2"
                                                >
                                                    <label
                                                        for="product-name-add"
                                                        class="form-label"
                                                        >Alamat
                                                        Pengiriman</label
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
                                            <h5>
                                                Ringkasan Transaksi Penjualan
                                            </h5>
                                            <table
                                                class="table-centered border mb-lg-0 table mt-3"
                                            >
                                                <thead class="bg-light">
                                                    <tr>
                                                        <td colspan="2">
                                                            Keterangan
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            Total Diskon Produk
                                                        </td>
                                                        <td class="text-right">
                                                            {{
                                                                formatNumber(
                                                                    this
                                                                        .transaction
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
                                                                .tax_product_total >
                                                            0
                                                        "
                                                    >
                                                        <td>PPN</td>
                                                        <td class="text-right">
                                                            {{
                                                                formatNumber(
                                                                    this
                                                                        .transaction
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
                                                                    this
                                                                        .transaction
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
                                                                    this
                                                                        .transaction
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
                                                                    this
                                                                        .transaction
                                                                        .payment_information
                                                                        .shipping_cost
                                                                )
                                                            }}
                                                        </td>
                                                    </tr>
                                                    <tr
                                                        v-if="
                                                            transaction
                                                                .payment_information
                                                                .service_tax > 0
                                                        "
                                                    >
                                                        <td>Pph 23</td>
                                                        <td class="text-right">
                                                            -
                                                            {{
                                                                formatNumber(
                                                                    transaction
                                                                        .payment_information
                                                                        .service_tax
                                                                )
                                                            }}
                                                        </td>
                                                    </tr>
                                                    <tr
                                                        v-if="
                                                            transaction
                                                                .payment_information
                                                                .goverment_tax >
                                                            0
                                                        "
                                                    >
                                                        <td>Pph 22</td>
                                                        <td class="text-right">
                                                            -
                                                            {{
                                                                formatNumber(
                                                                    transaction
                                                                        .payment_information
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
                                                                        this
                                                                            .transaction
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
                    <div class="card-footer d-flex justify-content-end"></div>
                </div>
            </div>
            <div class="col-1">
                <button
                    type="button"
                    @click="processTransaction(false)"
                    :disabled="loader.submit"
                    v-tooltip.top="'Simpan Transaksi'"
                    class="btn btn-block btn-success label-btn label-end mr-3"
                >
                    <i
                        class="fe fe-save label-btn-icon ms-2"
                        style="font-size: 30px"
                    ></i>
                </button>
                <button
                    type="button"
                    @click="addToPayment()"
                    :disabled="loader.submit"
                    v-tooltip.top="'Simpan & Bayar Transaksi'"
                    class="btn btn-info btn-block label-btn label-end mt-4"
                >
                    <i
                        class="fa fa-money label-btn-icon ms-2"
                        style="font-size: 30px"
                    ></i>
                </button>

                <div class="btn-group mt-2 mb-2">
                    <button
                        type="button"
                        class="btn btn-outline-primary dropdown-toggle"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <i
                            class="fa fa-print label-btn-icon"
                            style="font-size: 30px"
                        ></i>
                    </button>
                    <ul class="dropdown-menu" role="menu" style="">
                        <li>
                            <a
                                class="dropdown-item"
                                href="javascript:void(0);"
                                @click="processTransaction(false, 'faktur')"
                                >Faktur Penjualan</a
                            >
                        </li>
                        <li>
                            <a
                                class="dropdown-item"
                                href="javascript:void(0);"
                                @click="processTransaction(false, 'pengiriman')"
                                >Surat Jalan</a
                            >
                        </li>
                        <li>
                            <a
                                class="dropdown-item"
                                href="javascript:void(0);"
                                @click="processTransaction(false, 'label')"
                                >Label Pengiriman</a
                            >
                        </li>
                    </ul>
                </div>
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
                            field="customer.name"
                            header="customer"
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

    <Dialog
        v-model:visible="modal.forpayment"
        class="filter-data"
        header="Tambah dan Bayar Penjualan"
        :style="{ width: '35rem' }"
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
                    prefix="Rp "
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
                {{ loader.submit ? "Loading...." : "Tambahkan & Bayar " }}
            </button>
        </template>
    </Dialog>

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

    <Dialog
        v-model:visible="offer_transaction.modal"
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
                            v-model="offer_transaction.name"
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
                        :value="offer_transaction.transactions"
                        :paginator="true"
                        :rows="offer_transaction.limit"
                        :rowsPerPageOptions="[20, 50, 100]"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :lazy="true"
                        :totalRecords="offer_transaction.totalRows"
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
                            field="customer.name"
                            header="customer"
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
                                    @click="addThisOffer(data)"
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
import imageFragile from "@/assets/images/fragile.webp";
import draggable from "vuedraggable";
import { v4 as uuidv4 } from "uuid";
var _ = require("lodash");

export default {
    name: "create_product",
    components: {
        Editor,
        Fieldset,
        draggable,
    },
    data() {
        return {
            currentDate: new Date().toLocaleString(),
            image: imageFragile,
            customers: [],
            products: [],
            methods: [],
            edit_item: [],
            users: [],
            taxrates: [],
            selected_products: [],
            warehouses: [],
            terms: [],
            couriers: [],
            customer: {
                id: "",
                npwp: null,
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
            show: {
                product: true,
                general: true,
                items: true,
                payment: true,
            },
            loader: {
                customer: false,
                product: false,
                general: false,
                user: false,
                product: false,
                payment: false,
                method: false,
                data: true,
            },
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
                        phone: "",
                        address: "",
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
            new_transaction: {},
            filter: {
                name: "",
            },
            tax_option: {
                with_tax: false,
                default: false,
                tax_one: 0,
                tax_two: 0,
                tax_tree: 0,
                customer_type: "",
            },
            offer_transaction: {
                totalRows: 0,
                modal: false,
                page: 1,
                limit: 20,
                name: "",
                transactions: [],
            },
            history: {
                modal: false,
                totalRows: 0,
                data: [],
            },
        };
    },
    computed: {},
    created() {
        this.settup();

        const today = new Date().toISOString().substr(0, 10);
        this.transaction.general_information.date = today;
        this.transaction.payment_information.date = today;
    },
    methods: {
        async addThisOffer(data) {
            try {
                const response = await ApiData.get(
                    `app/transactions/sales/offer/detail/${data.id}`
                );

                var items = response.data.details.items;
                for (var i in items) {
                    var product = items[i];
                    var idproduct = false;
                    idproduct = this.transaction.items.filter((item) => {
                        if (
                            item.variation_id == product.variation_id ||
                            item.item_id == product.id ||
                            product.transaction_id != null
                        ) {
                            return true;
                        }
                    });

                    if (idproduct == false) {
                        this.transaction.items.push({
                            item_position: this.transaction.items.length + 1,
                            item_id: product.id,
                            product_type: product.product_type,
                            variation_id: product.variation_id,
                            product_id: product.product_id,
                            name: product.name,
                            tax_type: this.tax_option.type,
                            qty: product.qty,
                            unit_price: product.unit_price,
                            purchase_price: product.purchase_price,
                            without_discount: product.unit_price,
                            unit_price: product.unit_price,
                            unit_price_inc_tax: product.unit_price,
                            discount_amount: 0,
                            discount: 0,
                            discount_subtotal: 0,
                            tax:
                                this.tax_option.customer_type == "general"
                                    ? this.tax_option.tax_one
                                    : 0,
                            goverment_tax: 0,
                            service_tax: 0,
                            total_discount: 0,
                            discount_type: "fixed",
                            total_tax: 0,
                            stock: product.stock,
                            unit: product.unit,
                            subtotal: product.subtotal,
                            subunits: product.subunits,
                        });
                    }
                }

                this.offer_transaction.modal = false;
                this.calculateSummary();
            } catch (error) {
                console.log(error);
            }
        },

        async getOfferTransaction(page = 1) {
            if (
                this.transaction.general_information.customer.id == null ||
                this.transaction.general_information.customer.id == ""
            ) {
                this.$toast.add({
                    severity: "error",
                    summary: "Peringatan",
                    detail: "Silahkan Pilih customer Terlebih dahulu",
                    life: 3000,
                });
                return false;
            }

            this.loader.data = true;
            this.offer_transaction.page = page;
            try {
                const response = await ApiData.get(
                    `app/transactions/sales/offer?limit=${this.offer_transaction.limit}&page=${this.offer_transaction.page}&ref=${this.offer_transaction.name}&status=open&customer=${this.transaction.general_information.customer.id}`
                );
                var data = response.data;
                this.offer_transaction.transactions = data.transactions;
                this.offer_transaction.totalRows = data.totalRows;
                this.loader.data = false;
                this.offer_transaction.modal = true;
            } catch (error) {
                console.log(error);
            }
        },

        onDragEnd() {
            this.transaction.items.forEach((item, index) => {
                item.item_position = index + 1;
            });
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

        async getTaxrate() {
            try {
                const response = await ApiData.get(`app/master/tax`);
                var data = response.data;
                this.taxrates = data.taxrates;
            } catch (error) {
                console.log(error);
            }
        },

        async getUsers(query) {
            this.loader.user = true;
            try {
                const response = await ApiData.get(
                    `app/master/components/users?name=${query}&type=yes`
                );
                var data = response.data;
                this.users = data.users;
                this.loader.user = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getSign() {
            try {
                const response = await ApiData.get(
                    `app/master/components/sign`
                );
                var data = response.data;
                this.transaction.general_information.user = {
                    id: data.id,
                    name: data.name,
                };

                this.transaction.general_information.store = data.store;
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

        // Get Informasi customer
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

        changeCustomer(e) {
            if (this.transaction.items.length > 0) {
                this.$toast.add({
                    severity: "error",
                    summary: "Peringatan",
                    detail: "Terdapat item yang di ambil dari penerimaan barang di customer lain",
                    life: 3000,
                });

                this.transaction.general_information.customer = {
                    id: this.customer.id,
                    name: this.customer.name,
                    type: this.customer.type,
                    address: this.customer.address,
                    npwp: this.customer.npwp,
                };

                this.transaction.general_information.address =
                    this.customer.address;
            } else {
                this.customer = {
                    id: this.transaction.general_information.customer.id,
                    name: this.transaction.general_information.customer.name,
                    type: this.transaction.general_information.customer.type,
                    npwp: this.transaction.general_information.customer.npwp,
                    address:
                        this.transaction.general_information.customer.address,
                };

                this.tax_option.default = e.default;
                this.tax_option.with_tax = e.tax_option;
                this.tax_option.customer_type = e.type;
                this.transaction.general_information.due_limit = e.due_date;
                this.transaction.general_information.address = e.address;
            }
        },

        changeWarehouse() {
            if (this.transaction.items.length > 0) {
                this.$toast.add({
                    severity: "error",
                    summary: "Peringatan",
                    detail: "Terdapat item yang di ambil dari penerimaan barang di gudang lain",
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
                this.transaction.general_information.customer.id == null ||
                this.transaction.general_information.customer.id == ""
            ) {
                this.$toast.add({
                    severity: "error",
                    summary: "Peringatan",
                    detail: "Silahkan Pilih customer Terlebih dahulu",
                    life: 3000,
                });
                return false;
            }

            this.loader.data = true;
            this.saving_transaction.page = page;
            try {
                const response = await ApiData.get(
                    `app/transactions/sales/shipping?limit=${this.saving_transaction.limit}&page=${this.saving_transaction.page}&ref=${this.filter.name}&status=shipping_not_use&customer=${this.transaction.general_information.customer.id}&warehouse=${this.transaction.general_information.warehouse.id}&with_warehouse=yes`
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
                    `app/transactions/sales/shipping/detail/${data.id}`
                );

                var items = response.data.details.items;
                for (var i in items) {
                    var product = items[i];
                    var idproduct = false;
                    idproduct = this.transaction.items.filter((item) => {
                        if (
                            item.variation_id == product["variation_id"] ||
                            item.item_id == product["id"] ||
                            product["transaction_id"] != null
                        ) {
                            return true;
                        }
                    });

                    if (idproduct == false) {
                        this.transaction.items.push({
                            item_position: this.transaction.items.length + 1,
                            item_id: product.id,
                            product_type: product.product_type,
                            variation_id: product.variation_id,
                            product_id: product.product_id,
                            name: product.name,
                            tax_type: this.tax_option.type,
                            qty: product.qty,
                            unit_price: product.unit_price,
                            purchase_price: product.purchase_price,
                            without_discount: product.unit_price,
                            unit_price: product.unit_price,
                            unit_price_inc_tax: product.unit_price,
                            discount_amount: 0,
                            discount: 0,
                            discount_subtotal: 0,
                            tax:
                                this.tax_option.customer_type == "general"
                                    ? this.tax_option.tax_one
                                    : 0,
                            goverment_tax: 0,
                            service_tax: 0,
                            total_discount: 0,
                            discount_type: "fixed",
                            total_tax: 0,
                            stock: product.stock,
                            unit: product.unit,
                            subtotal: product.subtotal,
                            subunits: product.subunits,
                        });
                    }
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

                this.transaction.items.push({
                    id: uuidv4(),
                    item_position: this.transaction.items.length + 1,
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
                });
            }

            this.selected_products = null;
            this.calculateSummary();
        },

        RemoveItem(index) {
            this.transaction.items.splice(index, 1);
            setTimeout(() => {
                this.calculateSummary();
            }, 500);
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
            this.transaction.payment_information.pay_total =
                afterShipping - (govermentTax + serviceTax);

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

                discountTotal += detail.discount_amount * detail.qty;
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

        processTransaction(status, type = "") {
            this.transaction.status = "received";
            this.transaction.with_pay = status;
            this.$refs.ValidationForOtherInformation.validate().then(
                (success) => {
                    if (!success) {
                        this.$toast.add({
                            severity: "error",
                            summary: "Terjadi kesalahan",
                            detail: "Silahkan Check kembali form inputan anda",
                            life: 3000,
                        });
                    } else {
                        const filteredMinusProduct =
                            this.transaction.items.filter(
                                (item) => item.qty > item.stock
                            );

                        const filteredLowPrice = this.transaction.items.filter(
                            (item) =>
                                item.without_discount < item.purchase_price
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
                                        Swal.fire(
                                            "Membatalkan Proses Input Data"
                                        );
                                    }
                                });
                            }

                            if (filteredMinusProduct.length > 0) {
                                Swal.fire({
                                    title: "Peringatan!",
                                    text: "Produk yang anda jual bernilai minus",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#3085d6",
                                    cancelButtonColor: "#d33",
                                    confirmButtonText: "Ok",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        this.createData(type);
                                    } else {
                                        Swal.fire(
                                            "Membatalkan Proses Input Data"
                                        );
                                    }
                                });
                            }
                        }
                    }
                }
            );
        },

        createData(type = "") {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            ApiData.post("app/transactions/sales/create", this.transaction)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();

                    var data = response.data.detail;
                    this.new_transaction.qty_sell = data.qty_sell;
                    this.new_transaction.general_information =
                        data.general_information;
                    this.new_transaction.payment_information =
                        data.payment_information;
                    this.new_transaction.items = data.items;

                    if (type == "faktur") {
                        this.printFaktur();
                    } else if (type == "pengiriman") {
                        this.printPengiriman();
                    } else if (type == "label") {
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
                    console.log(err);
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },

        formatNumber(number) {
            if (parseFloat(number) >= 0) {
                return number.toLocaleString();
            } else {
                return "-" + (-number).toLocaleString();
            }
        },

        printPengiriman() {
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
                margin-bottom: 2px;
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
                margin-top: 20px;
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
                margin-top: 3px;
                margin-bottom: 3px;
                font-size: 12px;
            }

            h2 {
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        <div class="invoice">
            <header>
                <div class="company-info" style="max-width:400px;">
                    <h2>${
                        this.new_transaction.general_information.store.name ||
                        ""
                    }</h2>
                    <p>${
                        this.new_transaction.general_information.store
                            .address || ""
                    }</p>
                    <p>Telp ${
                        this.new_transaction.general_information.store.phone ||
                        ""
                    }</p>
                    <div style="border-top:1px solid black; border-bottom: 1px solid black; margin-top:10px;">
                        <p>Kepada :</p>
                    </div>
                    <p><b>${
                        this.new_transaction.general_information.customer
                            .name || ""
                    }</b><br />${
                this.new_transaction.general_information.customer.address || ""
            }</p>
                </div>
                <div class="invoice-info" style="min-width: 250px;">
                    <h2 style="text-align:center; font-size:14px !important">
                        ${
                            this.new_transaction.general_information.type ===
                            "shipping_product"
                                ? "Pengiriman Pesanan"
                                : "Surat Jalan"
                        }
                    </h2>
                    <table style="border:1px solid black">
                        <tr>
                            <td style="font-size:10px; min-width:100px; border-bottom: 1px solid black; border-right:1px solid black;">
                                <p>Tanggal</p>
                                <p><b>${
                                    this.new_transaction.general_information
                                        .date
                                }</b></p>
                            </td>
                            <td style="font-size:10px; min-width:100px; border-bottom: 1px solid black;">
                                <p>Nomor</p>
                                <p><b>${
                                    this.new_transaction.general_information
                                        .no_ref
                                }</b></p>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:10px; min-width:100px; border-right:1px solid black;">
                                <p>Syarat Pembayaran</p>
                                <p><b>${
                                    this.new_transaction.general_information
                                        .due_limit
                                } Hari</b></p>
                            </td>
                            <td style="font-size:10px; min-width:100px;">
                                <p>Ekspedisi</p>
                                <p><b>${
                                    this.new_transaction.general_information
                                        .courier.name || ""
                                }</b></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </header>

            <table class="items-table">
                <thead>
                    <tr>
                        <td colspan="7" style="text-align: center; width:80%;">Nama Barang</td>
                        <td style="text-align: center;">Qty</td>
                        <td style="text-align: center;">Checklist</td>
                    </tr>
                </thead>
                <tbody class="itemsdata">
                    ${this.new_transaction.items
                        .map(
                            (sell) => `
                        <tr>
                            <td colspan="7">${sell.name || ""}</td>
                            <td style="text-align: center;">${Number(
                                sell.qty
                            ).toLocaleString()} ${
                                sell.unit_detail.name || ""
                            }</td>
                            <td style="text-align: right;"></td>
                        </tr>
                        `
                        )
                        .join("")}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:right;">Total Qty</td>
                        <td colspan="3"><b>: ${Number(
                            this.new_transaction.qty_sell
                        ).toLocaleString()}</b></td>
                    </tr>
                    <tr>
                        <td colspan="9" rowspan="2" style="vertical-align: top; height:50px">Keterangan : ${
                            this.new_transaction.payment_information.note || ""
                        }</td>
                    </tr>
                    <tr></tr>
                    <tr>
                        <td colspan="2" style="text-align:center; border: none; vertical-align: top; height:80px;">Admin</td>
                        <td colspan="2" style="text-align:center; border: none; vertical-align: top;">Gudang</td>
                        <td colspan="2" style="text-align:center; border: none; vertical-align: top;">Penerima</td>
                        <td colspan="2" style="text-align:center; border: none; vertical-align: bottom;">26 Jul, 2024 18:07:51</td>
                        <td colspan="2" style="text-align:center; border: none; vertical-align: bottom;">Hal 1 dari 1 Cetak 2</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </body>
    </html>`;

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

        printLabel() {
            // HTML content for the invoice, without the <script> tag
            const invoiceHTML = `<!DOCTYPE html>
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
            <h1>${
                this.new_transaction.general_information.store.name || ""
            }</h1>
            <h1>${
                this.new_transaction.general_information.store.phone || ""
            }</h1>
        </header>
        <section class="recipient-info">
            <p><strong>To :</strong> <br /></p>
            <p>
            <strong>${
                this.new_transaction.general_information.customer.name || ""
            }</strong> <br />
            ${this.new_transaction.general_information.address || ""}
            </p>
            <p><strong>${
                this.new_transaction.general_information.customer.phone || ""
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
                ${this.new_transaction.items
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
                    Ekspedisi : ${
                        this.new_transaction.general_information.courier.name ||
                        ""
                    }
                </td>
                </tr>
                <tr>
                <td colspan="2">No.Faktur : ${
                    this.new_transaction.general_information.no_ref
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
    },
    mounted: function () {
        this.getTaxrate();
        this.getCustomers("");
        this.getUsers("");
        this.getSign();
        this.getWarehouse();
        this.getTerm();
        this.getCouriers();
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
<style>
.p-dialog-mask {
    z-index: 1060 !important;
}
.draggable-item {
    cursor: move;
}

.draggable-item.dragging {
    background-color: #f0f0f0;
    opacity: 0.8;
}
</style>
