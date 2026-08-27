<template>
    <!-- List Data -->
    <div class="col-12">
        <Form @submit="NumberTaxValidation()" ref="taxNumberValidation">
            <div class="card custom-card">
                <div class="card-header p-4">
                    <h3>Generate Penomoran Pajak</h3>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Dari Nomor</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="number.from"
                                name="Dari Nomor"
                            >
                                <InputMask
                                    id="basic"
                                    class="form-control"
                                    v-model="number.from"
                                    mask="999.99.99999999"
                                    placeholder="700.24.00000000"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-lg-6 col-sm-12">
                            <label for="Unit-name-add" class="form-label mt-2"
                                >Sampai Nomor</label
                            >
                            <Field
                                :rules="{
                                    required: true,
                                }"
                                v-slot="{ errors }"
                                v-model="number.to"
                                name="Sampai Nomor"
                            >
                                <InputMask
                                    id="basic"
                                    class="form-control"
                                    v-model="number.to"
                                    mask="999.99.99999999"
                                    placeholder="700.24.00000010"
                                />
                                <div class="fs-sm text-danger">
                                    {{ errors[0] }}
                                </div>
                            </Field>
                        </div>

                        <div class="col-12 mt-2">
                            <label for="Unit-name-add" class="form-label"
                                >Tipe Penggunaan</label
                            >
                            <Dropdown
                                v-model="number.type"
                                :options="[
                                    {
                                        label: 'Utama',
                                        value: 1,
                                    },
                                    {
                                        label: 'Cadangan',
                                        value: 2,
                                    },
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Pilih Opsi"
                                style="width: 100%"
                                class="w-full md:w-14rem"
                            />
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end mt-4">
                    <button
                        type="submit"
                        :disabled="loader.submit"
                        class="btn label-btn label-end btn-primary"
                    >
                        {{
                            loader.submit
                                ? "Mohon Tunggu...."
                                : "Tambahkan Data"
                        }}
                        <i class="ti ti-plus label-btn-icon ms-2"></i>
                    </button>
                </div>
            </div>
        </Form>
    </div>
    <!-- End List Data -->
</template>

<script>
import NProgress from "nprogress";
import InputMask from "primevue/inputmask";
import { ApiData } from "@/api/server";
export default {
    name: "list_purchase",
    components: {
        InputMask,
    },
    data() {
        return {
            number: {
                from: "",
                to: "",
                type: 1,
            },
            loader: {
                submit: false,
            },
        };
    },
    computed: {},
    created() {},
    methods: {
        NumberTaxValidation() {
            this.$refs.taxNumberValidation.validate().then((success) => {
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
                    this.createData();
                }
            });
        },

        createData() {
            ApiData.post("app/taxes/number/create", this.number)
                .then((response) => {
                    this.$handleSuccessResponse(response.data.message);
                    NProgress.done();
                    this.loader.submit = false;
                    window.parent.postMessage({
                        action: "closeActiveMenu",
                        data: "",
                    });
                })
                .catch((err) => {
                    NProgress.done();
                    this.loader.submit = false;
                    this.$handleErrorResponse(err);
                });
        },
    },
    mounted: function () {},
    watch: {},
};
</script>
