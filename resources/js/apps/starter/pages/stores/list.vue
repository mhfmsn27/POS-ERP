<template>
    <div class="row mt-4">
        <div
            class="col-md-4 col-xl-4"
            v-for="(store, index) in stores"
            :key="index"
        >
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {{
                            store.package != null
                                ? store.package.end_date
                                : "Tidak ada Paket"
                        }}
                    </h3>
                </div>
                <div class="card-body text-center">
                    <span
                        class="avatar avatar-xxl brround cover-image cover-image"
                        :data-bs-image-src="asset.store"
                        v-bind:style="{
                            'background-image': 'url(' + asset.store + ')',
                        }"
                    ></span>
                    <h4 class="h4 mb-0 mt-3">{{ store.name }}</h4>
                    <p class="card-text">{{ store.address }}</p>
                </div>
                <div class="card-footer text-center">
                    <div class="user-social-detail">
                        <button
                            v-if="store.package != null" 
                            @click="chooseStore(store)"
                            type="button"
                            class="btn btn-success waves-effect waves-light me-2"
                            >Pilih Toko</button>
                    </div>
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
import { TokenService } from "@/services";
import storeImage from "@/assets/images/store.png";
import ProgressSpinner from "primevue/progressspinner";
import { ApiData } from "@/api/server";
import Swal from "sweetalert2";
export default {
    name: "business_register",
    components: {
        ProgressSpinner,
    },
    data() {
        return {
            asset: {
                store: storeImage,
            },
            stores: [],
            loader: {
                data: true,
            },
        };
    },
    mounted() {
        this.getStores();
    },
    methods: {
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
            if (store.package == null) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Sepertinya Toko anda tidak memiliki paket langganan',
                    icon: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ok",
                });
                return false;
            } else {
                TokenService.saveStore(store);
                setTimeout(() => {
                    return (window.location = "/app/home");
                }, 1000);
            }
        },
    },
};
</script>
