<template>
    <Form @submit="updateRma()" ref="ValidationTransactions" class="col-12">
        <div class="row">
            <div class="col-lg-11 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row p-3">
                            <div class="col-lg-4 col-sm-12 mt-3">
                                <label for="transaction-ref" class="form-label"
                                    >Pelanggan</label
                                >
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="transaction.customer"
                                    name="Pilih Pelanggan"
                                >
                                    <Multiselect
                                        v-model="transaction.customer"
                                        :options="customers"
                                        :multiple="false"
                                        :close-on-select="true"
                                        :clear-on-select="true"
                                        :preserve-search="true"
                                        :searchable="true"
                                        :loading="loader.customer"
                                        :show-labels="false"
                                        :internal-search="true"
                                        :options-limit="50"
                                        placeholder="Pilih Pelanggan"
                                        open-direction="bottom"
                                        label="name"
                                        id="id"
                                        track-by="name"
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
                                                <span class="option__title">{{
                                                    props.option.name
                                                }}</span>
                                                <br />
                                                <span class="option__small">{{
                                                    props.option.address
                                                }}</span>
                                            </div>
                                        </template>
                                    </Multiselect>
                                    <div class="fs-sm text-danger">
                                        {{ errors[0] }}
                                    </div>
                                </Field>
                            </div>

                            <div class="col-lg-4 col-sm-12 mt-3">
                                <label for="transaction-ref" class="form-label"
                                    >Nama Pelanggan ( Pengganti )
                                </label>
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="transaction.customer_name"
                                    name="Nama Pelanggan"
                                >
                                    <InputText
                                        v-model="transaction.customer_name"
                                        style="width: 100%"
                                        placeholder="Masukan Nama"
                                    />
                                    <div class="fs-sm text-danger">
                                        {{ errors[0] }}
                                    </div>
                                </Field>
                            </div>

                            <div class="col-lg-4 col-sm-12 mt-3">
                                <label for="transaction-ref" class="form-label"
                                    >No.Wa Pelanggan ( Pengganti )
                                </label>
                                <Field
                                    :rules="{
                                        required: true,
                                    }"
                                    v-slot="{ errors }"
                                    v-model="transaction.phone"
                                    name="No.Wa Pelanggan"
                                >
                                    <InputText
                                        type="number"
                                        v-model="transaction.phone"
                                        style="width: 100%"
                                        placeholder="Masukan No.Wa"
                                    />
                                    <div class="fs-sm text-danger">
                                        {{ errors[0] }}
                                    </div>
                                </Field>
                            </div>

                            <div class="col-lg-4 mt-3">
                                <label for="transaction-date" class="form-label"
                                    >Estimasi Selesai</label
                                >
                                <Calendar
                                    v-model="transaction.estimate_date"
                                    style="width: 100%"
                                />
                            </div>
                            <div class="col-lg-4 mt-3">
                                <label for="transaction-date" class="form-label"
                                    >Estimasi Harga</label
                                >
                                <InputNumber
                                    v-model="transaction.estimate_price"
                                    style="width: 100%"
                                    placeholder="Harga Jual"
                                    prefix="Rp "
                                />
                            </div>
                            <div class="col-12 d-flex justify-content-end mt-4">
                                <button
                                    type="button"
                                    @click="addItem()"
                                    class="btn btn-icon btn-outline-info rounded-pill btn-wave waves-effect waves-light"
                                >
                                    <i class="fa fa-plus-circle"></i> Tambah
                                    Item
                                </button>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="table-responsive">
                                    <table
                                        class="table text-nowrap table-bordered"
                                    >
                                        <thead>
                                            <tr>
                                                <th>Nama Item</th>
                                                <th>Keluhan</th>
                                                <th>Kelengkapan</th>
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
                                                    <InputText
                                                        v-model="item.name"
                                                        style="width: 100%"
                                                        placeholder="Masukan Nama"
                                                    />
                                                </td>
                                                <td>
                                                    <InputText
                                                        v-model="item.complaint"
                                                        style="width: 100%"
                                                        placeholder="Masukan Keluhan"
                                                    />
                                                </td>
                                                <td>
                                                    <InputText
                                                        v-model="
                                                            item.completeness
                                                        "
                                                        style="width: 100%"
                                                        placeholder="Masukan Kelengkapan"
                                                    />
                                                </td>
                                                <td>
                                                    <button
                                                        class="btn btn-danger btn-sm"
                                                        type="button"
                                                        @click="
                                                            RemoveItem(
                                                                index,
                                                                item.id
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

                            <div class="col-12 mt-3">
                                <label for="regular-form-1" class="form-label"
                                    >Catatan
                                </label>
                                <textarea
                                    v-model="transaction.note"
                                    class="form-control"
                                ></textarea>
                            </div>

                            <div class="col-12 m-2">
                                <Divider />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-1 col-sm-12">
                <button
                    type="submit"
                    :disabled="loader.submit"
                    v-tooltip.top="'Simpan Transaksi'"
                    class="btn btn-success btn-block label-btn label-end mr-2"
                >
                    <i
                        class="fe fe-save label-btn-icon ms-2"
                        style="font-size: 30px"
                    ></i>
                </button>
            </div>
        </div>
    </Form>
</template>

<script>
import NProgress from "nprogress";
import Swal from "sweetalert2";
import { ApiData } from "@/api/server";
export default {
    name: "package_transaction",
    components: {},
    data() {
        return {
            couriers: [],
            customers: [],
            warehouses: [],
            transaction: {
                customer: {
                    id: "",
                    name: "",
                },
                estimate_date: "",
                estimate_price: 0,
                note: "",
                customer_name: "",
                phone: "",
                items: [],
            },

            loader: {
                product: false,
                submit: false,
                customer: false,
            },
        };
    },
    computed: {},
    created() {
        this.getDetail();
        this.getCustomers("");
    },
    methods: {
        addItem() {
            this.transaction.items.push({
                id: null,
                complaint: "",
                name: "",
                completeness: "",
            });
        },

        RemoveItem(index, id = null) {
            if (id != null) {
                Swal.fire({
                    title: "Apakah Anda Yakin ?",
                    text: "Data Transaksi yang telah di hapus tidak dapat dikembalikan lagi",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ok",
                }).then((result) => {
                    if (result.isConfirmed) {
                        ApiData.delete("app/transactions/rma/delete-item/" + id)
                            .then((response) => {
                                this.$handleSuccessResponse(
                                    response.data.message
                                );
                                this.transaction.items.splice(index, 1);
                            })
                            .catch((err) => {
                                NProgress.done();
                                this.$handleErrorResponse(err);
                            });
                    } else {
                        Swal.fire("Membatalkan Proses Hapus Data");
                    }
                });
            } else {
                this.transaction.items.splice(index, 1);
            }
        },

        async getDetail() {
            try {
                const response = await ApiData.get(
                    `app/transactions/rma/detail/${this.$route.params.id}`
                );
                var data = response.data;
                this.transaction = data.transactions;
            } catch (error) {
                this.loader.product = false;
                console.log(error);
            }
        },

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

        updateRma(type = "") {
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
                    ApiData.post(
                        "app/transactions/rma/update/" + this.$route.params.id,
                        this.transaction
                    )
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
