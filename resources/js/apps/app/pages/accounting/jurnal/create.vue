<template>
    <div class="col-12">
        <Form @submit="createJurnal()" ref="ValidationTransactions">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row p-3">
                        <div class="col-lg-6">
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

                        <div class="col-lg-6">
                            <label for="transaction-ref" class="form-label"
                                >Nama / Catatan</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="transaction.name"
                                name="Nama Jurnal"
                            >
                                <InputText
                                    v-model="transaction.name"
                                    style="width: 100%"
                                    placeholder="Masukan Nama"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12 m-2">
                            <Divider />
                        </div>
                        <div class="col-12">
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

                        <div class="col-12 mt-3">
                            <div class="table-responsive">
                                <table class="table text-nowrap table-bordered">
                                    <thead>
                                        <tr>
                                            <th>COA</th>
                                            <th>Nama Akun</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
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
                                                <InputNumber
                                                    v-model="item.amount_credit"
                                                    style="width: 100%"
                                                    placeholder="Masukan Nilai"
                                                    prefix="Rp "
                                                />
                                            </td>
                                            <td>
                                                <button
                                                    class="btn btn-danger btn-sm"
                                                    type="button"
                                                    v-tooltip.top="'Hapus Akun'"
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
                                : "Tambahkan Daata"
                        }}
                        <i class="ti ti-circle-check label-btn-icon ms-2"></i>
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
            transaction: {
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
    methods: {
        addItem() {
            this.transaction.items.push({
                account_id: null,
                coa: "",
                name: "",
                amount_credit: 0,
                type: "debit",
                amount: 0,
            });
        },

        async getAccounts(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}`
                );
                var data = response.data;
                this.accounts = data.accounts;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },

        updateItem(item) {
            var details = this.transaction.items[item];

            if (details.amount > 0) {
                details.type = "debit";
                details.amount_credit = 0;
            }

            if (details.amount_credit > 0) {
                details.type = "credit";
                details.amount = 0;
            }
        },

        selectedAccount() {
            if (this.accountselect != null) {
                var account = this.accountselect;
                this.transaction.items.push({
                    account_id: account.id,
                    coa: account.coa,
                    name: account.name,
                    amount_credit: 0,
                    type: "debit",
                    amount: 0,
                });

                this.accountselect = null;
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

        createJurnal() {
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
                    ApiData.post("app/jurnal/create", this.transaction)
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
    mounted: function () {
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
