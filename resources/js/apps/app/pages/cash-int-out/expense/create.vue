<template> 
        <div class="col-12">
            <Form
                @submit="createReceivedPurchase()"
                ref="ValidationTransactions"
            >
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row p-3">
                            <div class="col-lg-4">
                                <label for="transaction-date" class="form-label"
                                    >Tanggal Transaksi</label
                                >
                                <Calendar
                                    :showButtonBar="true"
                                    inputId="calendarPopup"
                                    :hideOnDateTimeSelect="true"
                                    style="width: 100%"
                                    v-model="transaction.date"
                                    dateFormat="dd-mm-yy"
                                />
                            </div>

                            <div class="col-lg-4">
                                <label for="transaction-ref" class="form-label"
                                    >Metode Pembayaran</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="transaction.method"
                                    name="Pilih Metode"
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

                            <div class="col-lg-4">
                                <label for="transaction-ref" class="form-label"
                                    >Kategori Pembayaran</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="transaction.category"
                                    name="Pilih Kategori"
                                >
                                    <Multiselect
                                        v-model="transaction.category"
                                        :options="categories"
                                        :multiple="false"
                                        :close-on-select="true"
                                        :clear-on-select="true"
                                        :preserve-search="true"
                                        :searchable="true"
                                        :internal-search="false"
                                        :options-limit="50"
                                        :loading="loader.category"
                                        placeholder="Pilih Kategori"
                                        open-direction="bottom"
                                        label="name"
                                        id="id"
                                        track-by="name"
                                        @search-change="getCategories"
                                    ></Multiselect>
                                    <div class="fs-sm text-danger">
                                        {{ errors[0] }}
                                    </div>
                                </Field>
                            </div>

                            <div class="col-12 m-2">
                                <Divider />
                            </div>
                            <div class="col-12" v-if="with_accountant">
                                <label for="regular-form-1" class="form-label"
                                    >Cari Akun
                                </label>
                                <span class="p-fluid">
                                    <div class="p-inputgroup">
                                        <Multiselect
                                            v-model="accountselect"
                                            :options="accounts"
                                            :multiple="false"
                                            :close-on-select="true"
                                            :clear-on-select="true"
                                            :preserve-search="true"
                                            :searchable="true"
                                            :internal-search="false"
                                            :show-no-results="false"
                                            :hide-selected="true"
                                            :options-limit="100"
                                            :loading="loader.account"
                                            placeholder="Cari dan Pilih Akun"
                                            open-direction="bottom"
                                            label="name"
                                            id="id"
                                            track-by="name"
                                            :preselect-first="true"
                                            @select="selectedAccount"
                                            @search-change="getAccounts"
                                        ></Multiselect>
                                    </div>
                                </span>
                            </div>
                            <div
                                class="col-12 d-flex justify-content-between"
                                v-else
                            >
                                <button
                                    type="button"
                                    @click="addItem()"
                                    class="btn btn-icon btn-outline-info rounded-pill btn-wave waves-effect waves-light"
                                >
                                    <i class="fa fa-plus-circle"></i> Tambah
                                    Daftar Pengeluaran
                                </button>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="table-responsive">
                                    <table
                                        class="table text-nowrap table-bordered"
                                    >
                                        <thead>
                                            <tr>
                                                <th v-if="with_accountant">
                                                    Akun
                                                </th>
                                                <th>Nama Akun</th>
                                                <th>Nilai</th>
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
                                                <td v-if="with_accountant">
                                                    {{ item.coa }}
                                                </td>
                                                <td>
                                                    <InputText
                                                        v-model="item.name"
                                                        style="width: 100%"
                                                        placeholder="Masukan Nama"
                                                    />
                                                </td>
                                                <td>
                                                    <InputNumber
                                                        v-model="item.amount"
                                                        style="width: 100%"
                                                        placeholder="Masukan Nilai"
                                                        prefix="Rp "
                                                    />
                                                </td>
                                                <td>
                                                    <button
                                                        class="btn btn-danger btn-sm"
                                                        type="button"
                                                        v-tooltip.top="
                                                            'Hapus Akun'
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
                                                    :colspan="
                                                        with_accountant ? 3 : 2
                                                    "
                                                    class="text-right"
                                                >
                                                    Subtotal
                                                </th>
                                                <th class="text-left">
                                                    {{
                                                        formatNumber(
                                                            transaction.summary
                                                                .subtotal
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
                    <div class="card-footer d-flex justify-content-end p-4">
                        <button
                            type="submit"
                            :disabled="loader.submit"
                            class="btn label-btn label-end btn-primary"
                        >
                            {{
                                loader.submit
                                    ? "Tunggu Sebentar...."
                                    : "Buat Pembayaran"
                            }}
                            <i
                                class="ti ti-circle-check label-btn-icon ms-2"
                            ></i>
                        </button>
                    </div>
                </div>
            </Form>
        </div> 
</template>

<script>
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
export default {
    name: "package_transaction",
    components: {},
    data() {
        return {
            accounts: [],
            methods: [],
            categories: [],
            with_accountant: true,
            transaction: {
                type: "expense",
                method: {
                    id: "",
                    name: "",
                },
                category: {
                    id: "",
                    name: "",
                },
                date: "",
                note: "",
                items: [],
                summary: {
                    subtotal: 0,
                },
            },
            accountselect: null,
            loader: {
                method: false,
                submit: false,
                account: false,
                category: false,
            },
            accounts: [],
        };
    },
    computed: {},
    created() {
        this.settup();
        this.getMethods("");
        this.getCategories("");
        const today = new Date().toISOString().substr(0, 10);
        this.transaction.date = today;
    },
    methods: {
        async settup() {
            try {
                const response = await ApiData.get(`app/master/tax/sett`);
                var data = response.data;

                if (data.accountant_use == "no") {
                    this.with_accountant = false;
                } else {
                    this.getAccounts("");
                }
            } catch (error) {
                console.log(error);
            }
        },

        addItem() {
            this.transaction.items.push({
                account_id: null,
                coa: "",
                name: "",
                amount: 0,
            });
        },

        async getCategories(query) {
            this.loader.category = true;
            try {
                const response = await ApiData.get(
                    `app/expenses/categories?name=${query}&limit=20`
                );
                var data = response.data;
                this.categories = data.categories;
                this.loader.category = false;
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

        async getAccounts(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&only_parent=yes&price=without_bank`
                );
                var data = response.data;
                this.accounts = data.accounts;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },

        selectedAccount() {
            if (this.accountselect != null) {
                var account = this.accountselect;
                this.transaction.items.push({
                    account_id: account.id,
                    coa: account.coa,
                    name: account.name,
                    amount: 0,
                });

                this.accountselect = null;
            }
        },

        updateItem(index) {
            this.calculateSummary();
        },

        calculateSummary() {
            var subtotal = 0;
            for (var i in this.transaction.items) {
                var detail = this.transaction.items[i];
                subtotal += detail.amount;
            }
            this.transaction.summary.subtotal = subtotal;
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

        createReceivedPurchase() {
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
                    ApiData
                        .post("app/expenses/create", this.transaction)
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
