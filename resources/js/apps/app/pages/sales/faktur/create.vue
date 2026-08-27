<template>
    <Form ref="ValidationForOtherInformation" class="col-12">
        <div class="row">
            <div class="col-10">
                <div class="card custom-card">
                    <div class="card-body add-product p-0">
                        <div class="p-4">
                            <div class="row gx-5">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <!-- customer -->
                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Terima Dari
                                                {{
                                                    transaction.customer.id !=
                                                    undefined
                                                        ? "Saldo Rp (" +
                                                          formatNumber(
                                                              transaction
                                                                  .customer
                                                                  .total_saldo
                                                          ) +
                                                          ") "
                                                        : ""
                                                }}
                                            </label>
                                            <Field
                                                :rules="{
                                                    required: true,
                                                }"
                                                v-slot="{ errors }"
                                                v-model="transaction.customer"
                                                name="Informasi Pelanggan"
                                            >
                                                <div class="p-inputgroup">
                                                    <Multiselect
                                                        v-model="
                                                            transaction.customer
                                                        "
                                                        :options="customers"
                                                        :multiple="false"
                                                        :close-on-select="true"
                                                        :clear-on-select="true"
                                                        :preserve-search="true"
                                                        :searchable="true"
                                                        :loading="
                                                            loader.customer
                                                        "
                                                        :internal-search="true"
                                                        :options-limit="50"
                                                        :disabled="
                                                            transaction.fakturs
                                                                .length > 0
                                                                ? true
                                                                : false
                                                        "
                                                        placeholder="Pilih customer"
                                                        open-direction="bottom"
                                                        label="name"
                                                        id="id"
                                                        track-by="name"
                                                        tagPlaceholder=""
                                                        selectLabel=""
                                                        @search-change="
                                                            getCustomer
                                                        "
                                                    >
                                                        <template
                                                            #singleLabel="props"
                                                        >
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
                                                                >{{
                                                                    props.option
                                                                        .address
                                                                }}</span
                                                            >
                                                        </template>
                                                        <template
                                                            #option="props"
                                                        >
                                                            <div
                                                                class="option__desc"
                                                            >
                                                                <span
                                                                    class="option__title"
                                                                    >{{
                                                                        props
                                                                            .option
                                                                            .name
                                                                    }}</span
                                                                >
                                                                <br />
                                                                <span
                                                                    class="option__small"
                                                                    >{{
                                                                        props
                                                                            .option
                                                                            .address
                                                                    }}</span
                                                                >
                                                            </div>
                                                        </template>
                                                    </Multiselect>
                                                    <button
                                                        class="btn btn-sm btn-info"
                                                        type="button"
                                                        @click="
                                                            modalTransaction()
                                                        "
                                                        v-if="
                                                            transaction.customer
                                                                .id
                                                        "
                                                    >
                                                        <i
                                                            class="fa fa-search"
                                                        ></i>
                                                    </button>
                                                </div>

                                                <div class="fs-sm text-danger">
                                                    {{ errors[0] }}
                                                </div>
                                            </Field>
                                        </div>
                                        <!-- End customer -->

                                        <!-- Date  -->
                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Tanggal</label
                                            >
                                            <Field
                                                :rules="{
                                                    required: true,
                                                }"
                                                v-slot="{ errors }"
                                                v-model="transaction.date"
                                                name="Tanggal Pembayaran"
                                            >
                                                <Calendar
                                                    :showButtonBar="true"
                                                    inputId="calendarPopup"
                                                    :hideOnDateTimeSelect="true"
                                                    style="width: 100%"
                                                    v-model="transaction.date"
                                                    dateFormat="dd-mm-yy"
                                                />
                                                <div class="fs-sm text-danger">
                                                    {{ errors[0] }}
                                                </div>
                                            </Field>
                                        </div>
                                        <!-- End Date -->

                                        <!-- Metode Bayar -->
                                        <!-- <div class="col-lg-6 mt-2">
                                        <label
                                            for="product-name-add"
                                            class="form-label"
                                            >Bayar Menggunakan</label
                                        >
                                        <Field
                                            :rules="{
                                                required: true,
                                            }"
                                            v-slot="{ errors }"
                                            v-model="transaction.payment_method"
                                            name="Bayar Menggunakan"
                                        >
                                            <Dropdown
                                                v-model="
                                                    transaction.payment_method
                                                "
                                                :options="[
                                                    {
                                                        label: 'Bank / Cash',
                                                        value: 'cash',
                                                    },
                                                    {
                                                        label: 'Saldo',
                                                        value: 'saldo',
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
                                    </div> -->
                                        <!-- End Metode Bayar -->

                                        <!-- Methods -->
                                        <div
                                            class="col-lg-6 mt-2"
                                            v-if="
                                                transaction.payment_method ==
                                                'cash'
                                            "
                                        >
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Metode Pembayaran</label
                                            >
                                            <Field
                                                :rules="{
                                                    required: true,
                                                }"
                                                v-slot="{ errors }"
                                                v-model="transaction.method"
                                                name="Metode Pembayaran"
                                            >
                                                <Multiselect
                                                    v-model="transaction.method"
                                                    :options="methods"
                                                    :multiple="false"
                                                    :close-on-select="true"
                                                    :clear-on-select="true"
                                                    :preserve-search="true"
                                                    :searchable="true"
                                                    :internal-search="false"
                                                    :options-limit="50"
                                                    :loading="loader.method"
                                                    placeholder="Pilih Metode Pembayaran"
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
                                        <!-- End Methods -->

                                        <!-- Payment -->
                                        <div class="col-lg-6 mt-2">
                                            <label
                                                for="product-name-add"
                                                class="form-label"
                                                >Nilai Di Bayarkan</label
                                            >

                                            <div class="p-inputgroup">
                                                <InputNumber
                                                    v-model="
                                                        transaction.total_payment
                                                    "
                                                    style="width: 100%"
                                                    placeholder="Masukkan Nominal Di Bayarkan"
                                                    prefix="Rp "
                                                />
                                                <button
                                                    class="btn btn-sm btn-info"
                                                    type="button"
                                                    @click="selectInput"
                                                >
                                                    <i
                                                        class="fa fa-check-circle"
                                                    ></i>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- End Payment -->

                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>No.Faktur</th>
                                                            <th>Tanggal</th>
                                                            <th>
                                                                Total Faktur
                                                            </th>
                                                            <th>Ter Hutang</th>
                                                            <th>Bayar</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr
                                                            v-for="(
                                                                item, index
                                                            ) in transaction.fakturs"
                                                            :key="index"
                                                        >
                                                            <td>
                                                                <a
                                                                    href="javascript:void(0)"
                                                                    @click="
                                                                        $goTo({
                                                                            name: 'sales_update',
                                                                            params: {
                                                                                id: item.transaction_id,
                                                                            },
                                                                        })
                                                                    "
                                                                >
                                                                    {{
                                                                        item.ref_no
                                                                    }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                {{ item.date }}
                                                            </td>
                                                            <td>
                                                                Rp
                                                                {{
                                                                    formatNumber(
                                                                        item.amount
                                                                    )
                                                                }}
                                                            </td>
                                                            <td>
                                                                Rp
                                                                {{
                                                                    formatNumber(
                                                                        item.total_due
                                                                    )
                                                                }}
                                                            </td>
                                                            <td>
                                                                Rp
                                                                {{
                                                                    formatNumber(
                                                                        item.total_pay
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
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-12">
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
                                                            Jumlah Nominal Perlu
                                                            Di Bayarkan
                                                        </td>
                                                        <td class="text-right">
                                                            {{
                                                                formatNumber(
                                                                    this
                                                                        .transaction
                                                                        .total_due
                                                                )
                                                            }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Jumlah Nominal Akan
                                                            Di Bayarkan
                                                        </td>
                                                        <td class="text-right">
                                                            {{
                                                                formatNumber(
                                                                    this
                                                                        .transaction
                                                                        .total_payment
                                                                )
                                                            }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Sisa Hutang Perlu Di
                                                            Bayarkan
                                                        </td>
                                                        <td class="text-right">
                                                            {{
                                                                formatNumber(
                                                                    this
                                                                        .transaction
                                                                        .subtotal
                                                                )
                                                            }}
                                                        </td>
                                                    </tr>
                                                    <tr
                                                        v-if="
                                                            transaction.total_credit >
                                                            0
                                                        "
                                                    >
                                                        <td>
                                                            Nominal Akan Di
                                                            Kreditkan
                                                        </td>
                                                        <td class="text-right">
                                                            {{
                                                                formatNumber(
                                                                    this
                                                                        .transaction
                                                                        .total_credit
                                                                )
                                                            }}
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
                </div>
            </div>
            <div class="col-2">
                <button
                    type="button"
                    @click="processPaymentTransaction('final')"
                    :disabled="loader.submit"
                    class="btn btn-success btn-block label-btn label-end"
                >
                    {{
                        loader.submit ? "Mohon Tunggu...." : "Proses Pembayaran"
                    }}
                    <i class="ri-check-line label-btn-icon ms-2"></i>
                </button>
            </div>
        </div>
    </Form>

    <!-- Modal For Saving Transaction -->
    <Dialog
        v-model:visible="modal.transaction"
        header="Faktur Penjualan"
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
                            v-model="modal.search"
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
                        :value="fakturs"
                        :paginator="true"
                        :rows="faktur.limit"
                        :rowsPerPageOptions="[20, 50, 100]"
                        paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        :lazy="true"
                        :totalRecords="faktur.totalRows"
                        @page="onPageChange($event)"
                        class="table"
                        :loading="loader.faktur"
                        responsiveLayout="scroll"
                        sortField="dynamicSortField"
                        currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                    >
                        <Column field="date" header="Tanggal"></Column>
                        <Column field="supplier_ref" header="No.Faktur">
                        </Column>
                        <Column header="Tipe">
                            <template #body="{ data }">
                                {{
                                    data.type == "hutang"
                                        ? "Penjualan"
                                        : "Retur Penjualan"
                                }}
                            </template>
                        </Column>
                        <Column header="Nominal">
                            <template #body="{ data }">
                                {{ formatNumber(data.amount) }}
                            </template>
                        </Column>
                        <Column header="Terbayar">
                            <template #body="{ data }">
                                {{ formatNumber(data.total_pay) }}
                            </template>
                        </Column>
                        <Column header="Sisa">
                            <template #body="{ data }">
                                {{ formatNumber(data.total_due) }}
                            </template>
                        </Column>

                        <Column field="action" header="Aksi">
                            <template #body="slotProps">
                                <button
                                    class="btn btn-sm btn-success"
                                    type="button"
                                    @click="
                                        selectedFakturs(
                                            slotProps.data,
                                            slotProps.index
                                        )
                                    "
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
</template>

<script>
import NProgress from "nprogress";
import Editor from "primevue/editor";
import Fieldset from "primevue/fieldset";
import Swal from "sweetalert2";
import { ApiData } from "@/api/server";
var _ = require("lodash");

export default {
    name: "create_faktur",
    components: {
        Editor,
        Fieldset,
    },
    data() {
        return {
            customers: [],
            methods: [],
            dues: [],
            fakturs: [],
            faktur: {
                page:1,
                limit: 20,
                totalRows: 0,
            },
            selected_fakturs: [],
            modal: {
                transaction: false,
                search: "",
            },
            loader: {
                customer: false,
                faktur: false,
                method: false,
                data: true,
            },
            transaction: {
                status: "",
                id: null,
                method: {
                    id: "",
                    name: "",
                },
                payment_method: "cash",
                customer: {
                    id: null,
                    name: "",
                    total_saldo: 0,
                },
                date: null,
                no_ref: null,
                fakturs: [],
                subtotal: 0,
                total_payment: 0,
                total_due: 0,
                total_credit: 0,
                subtotal: 0,
            },
        };
    },
    computed: {},
    created() {
        this.getCustomer("");
        this.getMethods("");
        const today = new Date().toISOString().substr(0, 10);
        this.transaction.date = today;
    },
    methods: {
        modalTransaction() {
            this.getFakturs("");
            this.modal.transaction = true;
        },

        searchData() {
            this.doSearch(this);
        },

        doSearch: _.debounce((rootInstance) => {
            rootInstance.getFakturs();
        }, 300),

        selectInput() {
            this.transaction.total_payment = this.transaction.total_due;
        },

        onPageChange(e) {
            this.faktur.limit = e.rows;
            this.faktur.page = e.page += 1;
            this.getFakturs(this.faktur.page);
        },

        async getFakturs(page = 1) {
            if (
                this.transaction.customer.id == null ||
                this.transaction.customer.id == ""
            ) {
                this.$toast.add({
                    severity: "error",
                    summary: "Peringatan",
                    detail: "Silahkan Pilih customer Terlebih dahulu",
                    life: 3000,
                });
                return false;
            } 
            this.faktur.page = page;
            this.loader.faktur = true;

            try {
                const response = await ApiData.get(
                    `app/transactions/transaction-due?limit=${this.faktur.limit}&ref=${this.modal.search}&customer=${this.transaction.customer.id}&status=due&type=&order=asc&page=${this.faktur.page}`
                );
                var data = response.data;
                this.faktur.totalRows = data.totalRows;

                const transactionFaktursIds = this.transaction.fakturs.map(
                    (faktur) => faktur.id
                );

                this.fakturs = data.transactions.filter(
                    (faktur) => !transactionFaktursIds.includes(faktur.id)
                );

                this.loader.faktur = false;
            } catch (error) {
                console.log(error);
            }
        },

        // Get Informasi customer
        async getCustomer(query) {
            this.loader.customer = true;
            try {
                const response = await ApiData.get(
                    `app/crm/customers?name=${query}`
                );
                var data = response.data;
                this.customers = data.customers;
                this.loader.customer = false;
            } catch (error) {
                console.log(error);
            }
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

        selectedFakturs(data, index) {
            var idfaktur = false;
            var faktur = data;
            idfaktur = this.transaction.fakturs.filter((item) => {
                if (item.id == data.id) {
                    return true;
                }
            });

            if (idfaktur == false) {
                var amount = faktur.amount;
                var totaldue = faktur.total_due;

                if (faktur.type == "saldo") {
                    amount = faktur.amount - faktur.amount * 2;
                }

                if (faktur.amount == totaldue) {
                    totaldue = amount;
                }

                this.transaction.fakturs.push({
                    item_id: null,
                    id: faktur.id,
                    transaction_id: faktur.transaction.id,
                    ref_no: faktur.ref_no,
                    type: faktur.type,
                    date: faktur.date,
                    amount: amount,
                    total_pay: faktur.total_pay,
                    total_due: totaldue,
                });

                this.fakturs.splice(index, 1);
                this.calculateSummary();
            }
        },

        RemoveItem(index) {
            this.transaction.fakturs.splice(index, 1);
            setTimeout(() => {
                this.calculateSummary();
            }, 500);
        },

        updateTransaction() {
            var totalPay = this.transaction.total_payment;
            var totalSaldo = this.transaction.customer.total_saldo;

            if (
                this.transaction.payment_method == "saldo" &&
                this.transaction.total_payment > totalSaldo
            ) {
                this.transaction.total_payment = totalSaldo;
            }

            if (this.transaction.total_payment > this.transaction.total_due) {
                this.transaction.total_credit =
                    this.transaction.total_payment - this.transaction.total_due;
            } else {
                this.transaction.total_credit = 0;
            }

            this.transaction.subtotal =
                totalPay > this.transaction.total_due
                    ? 0
                    : this.transaction.total_due - totalPay;
        },

        calculateSummary() {
            var subtotal = 0;

            for (var i in this.transaction.fakturs) {
                var detail = this.transaction.fakturs[i];
                subtotal += detail.total_due;
            }

            this.transaction.total_due = subtotal;
        },

        processPaymentTransaction(status) {
            this.transaction.status = status;
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
                        if (this.transaction.total_payment == 0) {
                            Swal.fire({
                                title: "Peringatan!",
                                text: "Nominal yang akan di bayarkan adalah nol ? klik ok jika ingin melanjutkan proses pembayaran",
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
                                        "app/transactions/sales/faktur/create",
                                        this.transaction
                                    )
                                        .then((response) => {
                                            this.$handleSuccessResponse(
                                                response.data.message
                                            );
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
                                } else {
                                    Swal.fire("Membatalkan Proses");
                                }
                            });
                        } else {
                            this.loader.submit = true;
                            NProgress.start();
                            NProgress.set(0.1);
                            ApiData.post(
                                "app/transactions/sales/faktur/create",
                                this.transaction
                            )
                                .then((response) => {
                                    this.$handleSuccessResponse(
                                        response.data.message
                                    );
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
                    }
                }
            );
        },

        formatNumber(number) {
            if (parseFloat(number) >= 0) {
                return number.toLocaleString();
            } else {
                return "-" + (-number).toLocaleString();
            }
        },
    },
    mounted: function () {},
    watch: {
        transaction: {
            handler: function (newVal, oldVal) {
                this.updateTransaction();
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>
