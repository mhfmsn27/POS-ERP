<template>
    <!-- List Data -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between p-4">
                <div>
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control"
                            v-model="filter.name"
                            @input="searchData()"
                            placeholder="Cari Akun...."
                            aria-describedby="search-team-member"
                        />
                        <button
                            class="btn btn-light btn-primary"
                            type="button"
                            id="search-team-member"
                        >
                            <i class="fe fe-search"></i>
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-start">
                    <button
                        @click="addData()"
                        v-tooltip.top="'Tambah Data'"
                        class="btn btn-outline-primary rounded-pill btn-wave waves-effect waves-light me-2"
                    >
                        <i class="fa fa-plus mr-2"></i> Tambah Data
                    </button>
                    <button
                        type="button"
                        v-tooltip.top="'Refresh'"
                        @click="getData()"
                        class="btn btn-outline-info btn-wave waves-effect waves-light ms-2"
                    >
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <TreeTable
                        :value="accounts"
                        :paginator="true"
                        :loading="loader.data"
                        :lazy="true"
                        :rows="limit"
                        :rowsPerPageOptions="[25, 50]"
                        paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                        currentPageReportTemplate="{first} to {last} of {totalRecords}"
                        :totalRecords="totalRows"
                        v-model:expandedKeys="expandedKeys"
                        class="table"
                        @page="onPageChange($event)"
                    >
                        <Column header="Kode" expander>
                            <template #body="{ node }">
                                {{ node.coa }}
                            </template>
                        </Column>
                        <Column header="Nama">
                            <template #body="{ node }">
                                {{ node.name }}
                            </template>
                        </Column>
                        <Column header="Tipe">
                            <template #body="{ node }">
                                {{ node.type.name }}
                            </template>
                        </Column>
                        <Column header="Saldo">
                            <template #body="{ node }">
                                {{ node.balance }}
                            </template>
                        </Column>

                        <Column header="Aksi">
                            <template #body="{ node }">
                                <div class="btn-group mt-2 mb-2">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul
                                        class="dropdown-menu"
                                        role="menu"
                                        style=""
                                    >
                                        <li>
                                            <a
                                                href="javascript:void(0);"
                                                @click="editData(node)"
                                                ><i
                                                    class="fa fa-pencil mr-2"
                                                ></i>
                                                Edit Data</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                href="javascript:void(0)"
                                                @click="
                                                    $goTo({
                                                        name: 'account_history',
                                                        params: { id: node.id },
                                                    })
                                                "
                                                v-if="node.children.length == 0"
                                                ><i class="fa fa-list mr-2"></i>
                                                Histori Transaksi</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                v-if="
                                                    (node.type.type ==
                                                        'bank_cash' &&
                                                        node.children.length ==
                                                            0) ||
                                                    node.type_account == 'tax'
                                                "
                                                href="javascript:void(0);"
                                                @click="depositAccount(node)"
                                                ><i
                                                    class="fa fa-credit-card mr-2"
                                                ></i>
                                                Deposit</a
                                            >
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a
                                                v-if="
                                                    node.type.type ==
                                                        'bank_cash' &&
                                                    node.children.length == 0
                                                "
                                                href="javascript:void(0);"
                                                @click="transferAccount(node)"
                                                ><i
                                                    class="fa fa-money mr-2"
                                                ></i>
                                                Transfer</a
                                            >
                                        </li>
                                        <li>
                                            <a
                                                v-if="node.children.length == 0"
                                                href="javascript:void(0);"
                                                @click="removeData(node.id)"
                                                ><i
                                                    class="fa fa-trash mr-2"
                                                ></i>
                                                Hapus Data
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </template>
                        </Column>
                    </TreeTable>
                </div>
            </div>
        </div>
    </div>
    <!-- End List Data -->

    <!-- Modal For Create Or Update -->
    <Dialog
        v-model:visible="modal.create"
        class="filter-data"
        modal
        maximizable
        :header="editmode ? 'Edit Data' : 'Tambah Data'"
        :style="{ width: '50vw' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <ScrollPanel style="width: 100%; height: 70vh">
            <Form @submit="createAccounts()" ref="ValidationAccount">
                <div class="row p-3">
                    <div class="col-lg-6 mb-3" v-if="!editmode">
                        <label for="user-date" class="form-label"
                            >Tipe Akun</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="account.type"
                            :name="'Tipe Akun'"
                        >
                            <Multiselect
                                v-model="account.type"
                                :options="types"
                                @input="changeType"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="false"
                                :options-limit="50"
                                :loading="loader.type"
                                placeholder="Pilih Akun"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getType"
                            ></Multiselect>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <label for="user-ref" class="form-label"
                            >Sub Akun</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="account.subtype"
                            :name="'Sub Akun'"
                        >
                            <Dropdown
                                v-model="account.subtype"
                                :options="[
                                    {
                                        name: 'Bukan',
                                        value: false,
                                    },
                                    {
                                        name: 'Iya',
                                        value: true,
                                    },
                                ]"
                                optionLabel="name"
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

                    <div
                        class="col-lg-6 mb-3"
                        v-if="account.subtype && !editmode"
                    >
                        <label for="user-ref" class="form-label"
                            >Pengkodean Otomatis</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="account.autocode"
                            :name="'Kode Otomatis'"
                        >
                            <Dropdown
                                v-model="account.autocode"
                                :options="[
                                    {
                                        name: 'Iya',
                                        value: true,
                                    },
                                    {
                                        name: 'Tidak',
                                        value: false,
                                    },
                                ]"
                                optionLabel="name"
                                optionValue="value"
                                placeholder="Pilih Opsi"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                            <small>
                                Pengkodean otomatis dengan prefix kode akun
                                induk
                            </small>
                        </Field>
                    </div>

                    <div class="col-lg-6 mb-3" v-if="account.subtype">
                        <label for="user-date" class="form-label"
                            >Akun Induk</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="account.account"
                            :name="'Akun Induk'"
                        >
                            <Multiselect
                                v-model="account.account"
                                :options="account_choose"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                :searchable="true"
                                :internal-search="false"
                                :options-limit="50"
                                :loading="loader.type"
                                placeholder="Pilih Tipe Akun"
                                open-direction="bottom"
                                label="name"
                                id="id"
                                track-by="name"
                                @search-change="getAccount"
                            ></Multiselect>
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <label for="user-ref" class="form-label"
                            >Nama Akun</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="account.name"
                            :name="'Nama Akun'"
                        >
                            <input
                                type="text"
                                style="width: 100%"
                                v-model="account.name"
                                class="form-control"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div
                        class="col-lg-6 mb-3"
                        v-if="
                            !(account.subtype && account.autocode && !editmode)
                        "
                    >
                        <label for="user-ref" class="form-label"
                            >Kode Akun</label
                        >
                        <Field
                            :rules="{
                                required: true,
                            }"
                            v-slot="{ errors }"
                            v-model="account.coa"
                            :name="'Kode Akun'"
                        >
                            <input
                                type="text"
                                style="width: 100%"
                                v-model="account.coa"
                                class="form-control"
                            />
                            <div class="fs-sm text-danger">
                                {{ errors[0] }}
                            </div>
                        </Field>
                    </div>

                    <div class="col-12">
                        <Divider />
                    </div>
                    <div class="col-12">
                        <label for="regular-form-1" class="form-label"
                            >Catatan
                        </label>
                        <textarea
                            v-model="account.note"
                            class="form-control"
                        ></textarea>
                    </div>
                </div>
            </Form>
        </ScrollPanel>

        <template #footer>
            <button
                type="button"
                @click="cancelCreate()"
                :disabled="loader.submit"
                class="btn btn-outline-danger btn-wave waves-effect waves-light mr-2"
            >
                {{ editmode ? "Batal Edit" : "Batal Tambah" }}
            </button>

            <button
                type="button"
                @click="createAccounts()"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                {{ editmode ? "Edit Data" : "Tambah Data" }}
            </button>
        </template>
    </Dialog>
    <!-- End Modal Create Or Update -->

    <!-- Deposit Account -->
    <Dialog
        v-model:visible="modal.deposit"
        class="filter-data"
        :header="'Deposit Akun'"
        :style="{ width: '35rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <Form @submit="createDeposit()" ref="ValidationDepositAccount">
            <div class="row p-3">
                <div class="col-lg-6 mb-3">
                    <label for="user-ref" class="form-label"
                        >Nama Deposit</label
                    >
                    <input
                        type="text"
                        style="width: 100%"
                        v-model="deposit.name"
                        class="form-control"
                    />
                    <div class="fs-sm text-secondary">
                        Opsional, ( Di isi otomatis apabila di kosongkan)
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="user-ref" class="form-label"
                        >Tanggal Deposit</label
                    >
                    <input
                        type="date"
                        style="width: 100%"
                        v-model="deposit.date"
                        class="form-control"
                    />
                    <div class="fs-sm text-secondary">
                        Opsional, ( Di isi otomatis apabila di kosongkan)
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="user-ref" class="form-label"
                        >Nominal Deposit</label
                    >
                    <InputNumber
                        style="width: 100%"
                        v-model="deposit.amount"
                        prefix="Rp "
                    />
                    <div class="fs-sm text-gray mt-2">
                        Masukkan jumlah deposit Akun
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
                        v-model="deposit.note"
                        class="form-control"
                    ></textarea>
                </div>
            </div>
        </Form>
        <template #footer>
            <button
                type="button"
                @click="cancelDeposit()"
                :disabled="loader.submit"
                class="btn btn-outline-danger btn-wave waves-effect waves-light mr-2"
            >
                Batalkan Deposit
            </button>

            <button
                type="button"
                @click="createDeposit()"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                Deposit Akun
            </button>
        </template>
    </Dialog>
    <!-- End Deposit -->

    <!-- Transfer -->
    <Dialog
        v-model:visible="modal.transfer"
        class="filter-data"
        :header="'Transfer Akun'"
        :style="{ width: '35rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <Form @submit="createTransfer()" ref="ValidationTransferAccount">
            <div class="row p-3">
                <div class="col-lg-6 mb-3">
                    <label for="user-ref" class="form-label"
                        >Nama Transfer</label
                    >
                    <input
                        type="text"
                        style="width: 100%"
                        v-model="transfer.name"
                        class="form-control"
                    />
                    <div class="fs-sm text-secondary">
                        Opsional, ( Di isi otomatis apabila di kosongkan)
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="user-ref" class="form-label"
                        >Tanggal Transfer</label
                    >
                    <input
                        type="date"
                        style="width: 100%"
                        v-model="transfer.date"
                        class="form-control"
                    />
                    <div class="fs-sm text-secondary">
                        Opsional, ( Di isi otomatis apabila di kosongkan)
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="user-ref" class="form-label"
                        >Nominal Transfer</label
                    >
                    <InputNumber
                        style="width: 100%"
                        v-model="transfer.amount"
                        prefix="Rp "
                    />
                    <div class="fs-sm text-gray mt-2">
                        Masukkan jumlah Transfer Saldo
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="user-date" class="form-label"
                        >Tujuan Transfer</label
                    >
                    <Field
                        :rules="{
                            required: true,
                        }"
                        v-slot="{ errors }"
                        v-model="transfer.account"
                        :name="'Akun Induk'"
                    >
                        <Multiselect
                            v-model="transfer.account"
                            :options="account_transfer"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            :searchable="true"
                            :internal-search="false"
                            :options-limit="50"
                            :loading="loader.type"
                            placeholder="Pilih Akun"
                            open-direction="bottom"
                            label="name"
                            id="id"
                            track-by="name"
                            @search-change="getTransferAccount"
                        ></Multiselect>
                        <div class="fs-sm text-danger">
                            {{ errors[0] }}
                        </div>
                    </Field>
                </div>

                <div class="col-12">
                    <Divider />
                </div>
                <div class="col-12">
                    <label for="regular-form-1" class="form-label"
                        >Catatan
                    </label>
                    <textarea
                        v-model="transfer.note"
                        class="form-control"
                    ></textarea>
                </div>
            </div>
        </Form>
        <template #footer>
            <button
                type="button"
                @click="cancelTransfer()"
                :disabled="loader.submit"
                class="btn btn-outline-danger btn-wave waves-effect waves-light mr-2"
            >
                Batalkan Transfer
            </button>

            <button
                type="button"
                @click="createTransfer()"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                Transfer Saldo Akun
            </button>
        </template>
    </Dialog>
    <!-- End Transfer -->
</template>

<script>
import Swal from "sweetalert2";
import NProgress from "nprogress";
import TreeTable from "primevue/treetable";
import ScrollPanel from "primevue/scrollpanel";
import { ApiData } from "@/api/server";

var _ = require("lodash");

export default {
    name: "type_list",
    components: {
        TreeTable,
        ScrollPanel,
    },
    data() {
        return {
            editmode: false,
            expandedKeys: {},
            accounts: [],
            types: [],
            account_choose: [],
            account_transfer: [],
            modal: {
                create: false,
                deposit: false,
                transfer: false,
            },
            totalRows: 0,
            page: 1,
            limit: 25,
            loader: {
                data: false,
                account: false,
                submit: false,
                type: false,
            },
            filter: {
                name: "",
                type: {
                    id: "",
                    name: "",
                },
                store: {
                    id: "",
                    name: "",
                },
            },
            account: {
                name: "",
                coa: "",
                autocode: true,
                type: {
                    id: "",
                    name: "",
                },
                subtype: true,
                account: {
                    id: "",
                    name: "",
                },
                note: "",
            },
            deposit: {
                id: null,
                amount: 0,
                note: "",
                date: "",
                name: "",
            },
            transfer: {
                id: null,
                amount: 0,
                account: {
                    id: "",
                    name: "",
                },
                date: "",
                note: "",
                name: "",
            },
        };
    },
    methods: {
        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;

            try {
                const response = await ApiData.get(
                    `app/account?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&only_parent=no`
                );
                var data = response.data;
                this.accounts = data.accounts;
                this.totalRows = data.totalRows;
                this.loader.data = false;

                this.expandedKeys = this.getAllExpandedKeys(this.accounts);
            } catch (error) {
                console.log(error);
            }
        },

        removeData(id) {
            Swal.fire({
                title: "Apakah Anda Yakin ?",
                text: "data yang telah di hapus tidak dapat dikembalikan lagi",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.delete("app/account/delete/" + id)
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

        searchData() {
            this.doSearch(this);
        },

        doSearch: _.debounce((rootInstance) => {
            rootInstance.getData();
        }, 300),

        onPageChange(e) {
            this.limit = e.rows;
            this.page = e.page += 1;
            this.getData(this.page);
        },

        formatNumber(number) {
            if (parseFloat(number) > 0) {
                return number.toLocaleString();
            } else {
                return 0;
            }
        },

        async getType(query) {
            this.loader.type = true;
            try {
                const response = await ApiData.get(
                    `app/account/type?name=${query}`
                );
                var data = response.data;
                this.types = data.types;
                this.loader.store = false;
            } catch (error) {
                console.log(error);
            }
        },

        changeType() {
            (this.account.account = {
                id: "",
                name: "",
            }),
                this.getAccount("");
        },

        async getAccount(query) {
            this.loader.account = true;
            if (this.account.type.id != null || this.account.type.id != "") {
                try {
                    const response = await ApiData.get(
                        `app/account/components?name=${query}&type=${this.account.type.id}&cashflow=0`
                    );
                    var data = response.data;
                    this.account_choose = data.accounts;
                    this.loader.account = false;
                } catch (error) {
                    console.log(error);
                }
            }
        },

        async getTransferAccount(query) {
            this.loader.account = true;
            try {
                const response = await ApiData.get(
                    `app/account/components?name=${query}&price=bank_cash&only_parent=yes&without_data=${this.transfer.id}`
                );
                var data = response.data;
                this.account_transfer = data.accounts;
                this.loader.account = false;
            } catch (error) {
                console.log(error);
            }
        },

        createAccounts() {
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
                    if (this.editmode) {
                        ApiData.post(
                            "app/account/update/" + this.account.id,
                            this.account
                        )
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                NProgress.done();
                                this.modal.create = false;
                                this.modal.editmode = false;
                                this.getData();
                            })
                            .catch((err) => {
                                NProgress.done();
                                this.loader.submit = false;
                                this.$handleErrorResponse(err);
                            });
                    } else {
                        ApiData.post("app/account/create", this.account)
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                NProgress.done();
                                this.modal.create = false;
                                this.getData();
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

        addData() {
            this.cancelCreate();
            this.editmode = false;
            this.modal.create = true;
        },

        editData(data) {
            this.account = data;
            this.editmode = true;
            this.modal.create = true;
        },

        cancelCreate() {
            this.account = {
                name: "",
                coa: "",
                autocode: true,
                type: {
                    id: "",
                    name: "",
                },
                subtype: true,
                account: {
                    id: "",
                    name: "",
                },
                note: "",
            };
            this.modal.create = false;
        },

        depositAccount(data) {
            this.resetDeposit();
            this.deposit.id = data.id;
            this.modal.deposit = true;
        },

        transferAccount(data) {
            this.resetTransfer();
            this.transfer.id = data.id;
            this.modal.transfer = true;
        },

        cancelDeposit() {
            this.resetDeposit();
            this.modal.deposit = false;
        },

        cancelTransfer() {
            this.resetTransfer();
            this.modal.transfer = false;
        },

        resetDeposit() {
            this.deposit = {
                id: null,
                amount: 0,
                note: "",
                date: "",
                name: "",
            };
        },

        resetTransfer() {
            this.transfer = {
                id: null,
                amount: 0,
                account: {
                    id: "",
                    name: "",
                },
                date: "",
                note: "",
                name: "",
            };
        },

        createDeposit() {
            this.$refs.ValidationDepositAccount.validate().then((success) => {
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
                        "app/account/deposit/" + this.deposit.id,
                        this.deposit
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.cancelDeposit();
                            this.getData();
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.loader.submit = false;
                            this.$handleErrorResponse(err);
                        });
                }
            });
        },

        createTransfer() {
            this.$refs.ValidationTransferAccount.validate().then((success) => {
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
                        "app/account/transfer/" + this.transfer.id,
                        this.transfer
                    )
                        .then((response) => {
                            this.$handleSuccessResponse(response.data.message);
                            NProgress.done();
                            this.cancelTransfer();
                            this.getData();
                        })
                        .catch((err) => {
                            NProgress.done();
                            this.loader.submit = false;
                            this.$handleErrorResponse(err);
                        });
                }
            });
        },

        onNodeToggle(event) {
            this.expandedKeys = event.expandedKeys;
        },
        getAllExpandedKeys(nodes) {
            const expanded = {};
            nodes.forEach((node) => {
                expanded[node.id] = true; // Set node sebagai terbuka
                if (node.children && node.children.length > 0) {
                    Object.assign(
                        expanded,
                        this.getAllExpandedKeys(node.children)
                    );
                }
            });
            return expanded;
        },
    },
    mounted() {
        this.getData();
        this.getType();
    },
    watch: {},
};
</script>
