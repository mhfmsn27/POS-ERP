<template>
    <div class="row justify-content-center mt-4">
        <div class="col-md-12 col-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-header">
                    <h4>Daftar Histori Transaksi Berlangganan</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <DataTable
                            :value="transactions"
                            :paginator="true"
                            :rows="limit"
                            :rowsPerPageOptions="[20, 50, 100]"
                            paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                            :lazy="true"
                            :totalRecords="totalRows"
                            @page="onPageChange($event)"
                            class="table"
                            :loading="loader.data"
                            responsiveLayout="scroll"
                            sortField="dynamicSortField"
                            currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
                        >
                            <Column field="date" header="Tanggal"></Column>
                            <Column field="store" header="Toko / Cabang">
                            </Column>
                            <Column field="package" header="Paket"></Column>
                            <Column header="Grand Total">
                                <template #body="{ data }">
                                    {{ $formatAmount(data.grand_total) }}
                                </template>
                            </Column>
                            <Column header="Status" field="status">
                                <template #body="{ data }">
                                    <span
                                        class="badge bg-warning"
                                        v-if="data.status == 'pending'"
                                        >Pending</span
                                    >
                                    <span
                                        class="badge bg-info"
                                        v-if="data.status == 'process'"
                                        >Proses</span
                                    >
                                    <span
                                        class="badge bg-success"
                                        v-if="data.status == 'success'"
                                        >Selesai</span
                                    >
                                </template>
                            </Column>
                            <Column field="action" header="Aksi">
                                <template #body="{ data }">
                                    <a
                                        v-if="data.status == 'pending'"
                                        href="javascript:void(0);"
                                        @click="deleteData(data.id)"
                                        class="btn btn-danger deletebutton me-2"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <a
                                        v-if="data.status == 'pending'"
                                        @click="payTransaction(data.id)"
                                        href="javascript:void(0);"
                                        class="btn btn-info"
                                    >
                                        <i class="fa fa-money"></i>
                                    </a>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import NProgress from "nprogress";
import { ApiData } from "@/api/server";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Swal from "sweetalert2";
export default {
    name: "transaction",
    components: {
        DataTable,
        Column,
    },
    data() {
        return {
            transactions: [],
            totalRows: 0,
            page: 1,
            limit: 20,
            loader: {
                data: true,
            },
        };
    },
    mounted() {
        this.getCredential();
        this.getData();
    },
    methods: {
        async getCredential() {
            try {
                const response = await ApiData.get(
                    `starter/transactions/midtrans-key`
                );
                var data = response.data;
                var snapSrcUrl = "https://app.midtrans.com/snap/snap.js";
                const myMidtransClientKey = data.key;
                const script = document.createElement("script");
                script.src = snapSrcUrl;
                script.setAttribute("data-client-key", myMidtransClientKey);
                script.async = true;
                document.body.appendChild(script);
            } catch (error) {
                console.log(error);
            }
        },

        deleteData(id) {
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
                    NProgress.start();
                    NProgress.set(0.1);
                    ApiData.delete("starter/transactions/delete/" + id)
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

        payTransaction(transactionId) {
            NProgress.start();
            NProgress.set(0.1);
            ApiData.post(`starter/transactions/add-payment/${transactionId}`)
                .then((response) => {
                    window.snap.pay(response.data.snap, {
                        onSuccess: function (result) {
                            Swal.fire({
                                title: "Pembayaran Berhasil Di Lakukan",
                                text: "Silahkan tunggu hingga 10 Menit, lalu refresh kembali halaman untuk melihat status transaksi pembayaran",
                                icon: "success",
                                showCancelButton: false,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Ok",
                            }).then((result) => {
                                location.reload();
                            });
                        },
                        onPending: function (result) {
                            Swal.fire({
                                title: "Pending Pembayaran",
                                text: "Kami lihat jika status pembayaran masih pending, jika anda merasa telah melakukan pembayaran, silahkan tunggu 10 menit dan refresh halaman",
                                icon: "warning",
                                showCancelButton: false,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Ok",
                            }).then((result) => {
                                location.reload();
                            });
                        },
                        onError: function (result) {
                            Swal.fire({
                                title: "Terjadi Kesalahan!",
                                text: "Silahkan Refresh halaman dan ulangi kembali, jika anda merasa telah melakukan pembayaram, silahkan tunggu 10 menit dan refresh halaman",
                                icon: "warning",
                                showCancelButton: false,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Ok",
                            }).then((result) => {
                                location.reload();
                            });
                        },
                        onClose: function () {
                            location.reload();
                        },
                    });
                })
                .catch((err) => {
                    NProgress.done();
                    this.loading = false;
                    this.$handleErrorResponse(err);
                });
        },

        onPageChange(e) {
            this.limit = e.rows;
            this.page = e.page += 1;
            this.getData(this.page);
        },

        async getData(page = 1) {
            this.loader.data = true;
            this.page = page;

            try {
                const response = await ApiData.get(
                    `starter/transactions?limit=${this.limit}&page=${this.page}`
                );
                var data = response.data;
                this.transactions = data.transactions;
                this.totalRows = data.totalRows;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },
    },
};
</script>
