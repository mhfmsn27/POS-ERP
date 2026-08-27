<template>
    <div class="row d-flex align-items-center justify-content-center mt-4">
        <div
            class="col-sm-6 col-xl-3 col-md-6 col-lg-6"
            v-for="(pricing, index) in pricings"
            :key="index"
        >
            <div class="card p-3 border-primary pricing-card advanced">
                <div class="card-header d-block text-center pt-2">
                    <p class="fs-30 fw-semibold mb-1 pe-0">
                        {{ pricing.name }}
                    </p>
                    <p class="fw-semibold mb-1">
                        <span class="fs-25"
                            >{{ $formatAmount(pricing.price) }} </span
                        ><br />
                        <span>{{ pricing.limit_day }} Hari</span>
                    </p>
                    <p class="fs-13 mb-2">{{ pricing.description }}.</p>
                </div>
                <div class="card-body py-4">
                    <ul class="pricing-body ps-0">
                        <li v-for="(detail, d) in pricing.details" :key="d">
                            <i
                                class="fa fa-check py-2 text-primary p-2 fs-16"
                            ></i>
                            <strong> </strong> {{ detail.name }}
                        </li>
                    </ul>
                </div>
                <div class="card-footer text-center border-top-0 pt-1">
                    <router-link
                        :to="{
                            name: 'package_detail',
                            params: { id: pricing.id },
                        }"
                        class="btn btn-lg btn-primary-gradient text-white btn-block"
                    >
                        <span class="ms-4 me-4">Pilih Layanan</span>
                    </router-link>
                </div>
            </div>
        </div>
        <div v-if="loader.data" class="col-12 d-flex justify-content-between">
            <ProgressSpinner style="width: 150px" />
        </div>
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
            pricings: [],
            loader: {
                data: true,
            },
        };
    },
    mounted() {
        this.getPackages();
    },
    methods: {
        async getPackages() {
            this.loader.data = true;

            try {
                const response = await ApiData.get(`starter/packages`);
                var data = response.data;
                this.pricings = data.packages;
                this.loader.data = false;
            } catch (error) {
                console.log(error);
            }
        },
    },
};
</script>
