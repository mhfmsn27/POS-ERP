<template>
    <div class="row d-flex align-items-center justify-content-center mt-4">
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-body p-0 product-checkout">
                    <div class="tab-content" id="myTabContent">
                        <div
                            class="tab-pane fade show active border-0 p-0"
                            id="order-tab-pane"
                            role="tabpanel"
                            aria-labelledby="order-tab-pane"
                            tabindex="0"
                        >
                            <div class="p-4">
                                <p
                                    class="mb-1 fw-semibold text-muted op-5 fs-20"
                                >
                                    {{ transaction.package.name }}
                                </p>

                                <div class="row p-3 mb-4 mt-2">
                                    <p class="fs-15 fw-semibold mb-1">
                                        Pilih Toko / Cabang
                                    </p>
                                    <div
                                        class="col-xl-6"
                                        v-for="(store, index) in stores"
                                        :key="'store' + index"
                                    >
                                        <div
                                            class="form-check shipping-method-container"
                                        >
                                            <input
                                                :id="'choose-store' + index"
                                                name="choose_store"
                                                type="radio"
                                                class="form-check-input mb-4"
                                                @click="chooseStore(store)"
                                            />
                                            <div class="form-check-label">
                                                <div
                                                    class="d-sm-flex align-items-center justify-content-between"
                                                >
                                                    <div
                                                        class="shipping-partner-details me-sm-5 me-0"
                                                    >
                                                        <p
                                                            class="mb-0 fw-semibold"
                                                        >
                                                            {{ store.name }}
                                                        </p>
                                                    </div>
                                                    <div
                                                        class="fw-semibold me-sm-5 me-0"
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end"
                            >
                                <button
                                    type="button"
                                    @click="submitTransaction()"
                                    :disabled="loader.submit"
                                    class="btn btn-primary d-inline-flex"
                                >
                                    {{
                                        loader.submit
                                            ? "Mohon Tunggu...."
                                            : "Proses Transaksi"
                                    }}<i
                                        class="bx bx-check-circle ms-2 mt-1"
                                    ></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Transaction -->
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title me-1">Ringkasan Transaksi</div>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom border-block-end-dashed">
                        <div
                            class="d-flex align-items-center justify-content-between mb-3"
                        >
                            <div class="fw-semibold fs-14">Detail</div>
                            <div class="fw-semibold fs-14"></div>
                        </div>
                        <div
                            class="d-flex align-items-center justify-content-between mb-3"
                        >
                            <div class="text-muted fs-15">Nama Paket</div>
                            <div class="fw-semibold fs-15n">
                                {{ transaction.package.name }}
                            </div>
                        </div>
                        <div
                            class="d-flex align-items-center justify-content-between mb-3"
                        >
                            <div class="text-muted fs-14">
                                Penambahan Masa Akhir
                            </div>
                            <div class="fw-semibold fs-14">
                                {{ transaction.package.limit_day }} Hari
                            </div>
                        </div>
                        <div
                            class="d-flex align-items-center justify-content-between"
                        >
                            <div class="text-muted fs-14">Toko / Cabang</div>
                            <div class="fw-semibold fs-14">
                                {{ transaction.store.name }}
                            </div>
                        </div>
                    </div>

                    <div class="p-3 border-bottom border-block-end-dashed">
                        <div
                            class="d-flex align-items-center justify-content-between mb-3"
                        >
                            <div class="text-muted op-7">Harga Paket</div>
                            <div class="fw-semibold fs-14">
                                {{ $formatAmount(transaction.package.price) }}
                            </div>
                        </div>
                    </div>
                    <div class="p-3 border-bottom border-block-end-dashed">
                        <div
                            class="d-flex align-items-center justify-content-between mb-3"
                        >
                            <div class="text-muted op-7">Pajak</div>
                            <div class="fw-semibold fs-14">
                                {{ $formatAmount(transaction.price.tax.final) }}
                                ( {{ transaction.price.tax.amount }}% )
                            </div>
                        </div>
                    </div>

                    <div class="p-2 m-2 bg-success-transparent">
                        <div
                            class="d-flex justify-content-between align-items-center"
                        >
                            <p class="fs-12 mb-0 text-success">
                                Biaya yang harus di bayarkan
                            </p>
                            <div class="fw-semibold fs-16 text-Success">
                                {{ $formatAmount(transaction.price.final) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Summary -->
    </div>
</template>
<script>
import NProgress from "nprogress";
import ProgressSpinner from "primevue/progressspinner";
import { ApiData } from "@/api/server";
export default {
    name: "business_register",
    components: {
        ProgressSpinner,
    },
    data() {
        return {
            stores: [],
            transaction: {
                store: {
                    id: "",
                    name: "",
                },
                package: {
                    id: "",
                    price: 0,
                    name: "",
                    limit_day: 0,
                },
                price: {
                    subtotal: 0,
                    final: 0,
                    tax: {
                        final: 0,
                        amount: 0,
                    },
                },
            },
            loader: {
                submit: false,
                data: true,
            },
        };
    },
    mounted() {
        this.packageDetail();
        this.getStores();
    },
    methods: {
        async packageDetail() {
            try {
                const response = await ApiData.get(
                    `starter/package-detail/${this.$route.params.id}`
                );
                var data = response.data;
                this.transaction.package = data.detail;
                this.transaction.price.tax.amount = data.tax;
                this.transaction.price.final = data.detail.price;

                if (data.tax > 0 && this.transaction.package.price > 0) {
                    var taxFinal =
                        (data.tax / 100) * this.transaction.package.price;
                    this.transaction.price.final =
                        taxFinal + this.transaction.package.price;
                }
            } catch (error) {
                console.log(error);
            }
        },

        async getStores() {
            this.loader.data = true;

            try {
                const response = await ApiData.get(`app/stores`);
                var data = response.data;
                this.stores = data.stores;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },

        chooseStore(store) {
            this.transaction.store = {
                id: store.id,
                name: store.name,
            };
        },

        submitTransaction() {
            this.loader.submit = true;
            NProgress.start();
            NProgress.set(0.1);
            ApiData.post("starter/transactions/store", this.transaction)
                .then((response) => {
                    this.$handleSuccessResponse(response.message);
                    NProgress.done();
                    setTimeout(() => {
                        return this.$router.push({
                            name: "transactions",
                        });
                    }, 1000);
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },
    },
};
</script>
