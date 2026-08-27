<template>
  <div class="p-5">
    <div class="row" v-if="!loader.starter">
      <div class="col-xl-4">
        <div class="row">
          <div
            class="col-12 mb-md-5 mb-3"
          >
            <div class="" v-if="gallery.length > 0">
              <Carousel
                :value="gallery"
                :numVisible="1"
                :numScroll="1"
                orientation="horizontal"
                verticalViewPortHeight="360px"
                contentClass="flex align-items-center"
              >
                <template #item="{ data }">
                  <img
                    class="img-fluid"
                    :src="data.url"
                    style="width: 100%"
                    alt="product-image"
                  />
                </template>
              </Carousel>
            </div>
            <div v-else>
              <img src="@/assets/images/no-image.png" style="width:100%" />
            </div>
          </div>
          <div
            class="col-lg-12  mb-md-5 mb-3"
          >
            <div
              class="card custom-card bg-transparent shadow-none border rounded"
              v-if="variations.length > 1"
            >
              <div class="card-header flex-between justify-content-between">
                <div class="card-title">Variant Produk</div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div
                    class="col-lg-6 mb-2 mt-2"
                    v-for="(variant2, v2) in variations"
                    :key="'variant-2' + v2"
                  >
                    <div class="text-center">
                      <h5 class="mb-1 fs-12 fw-semibold mt-1">
                        {{ variant2.name }}
                      </h5>
                      <p class="mb-0 text-muted fs-12 px-3">
                        {{ variant2.sku }}
                      </p>

                      <div class="d-flex justify-content-center mb-1 mt-2">
                        <h6 class="mb-0 fw-semibold">
                          Rp {{ formatNumber(variant2.selling_price) }}
                        </h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="border p-3 rounded-3 mb-3">
              <p class="fs-16 fw-semibold mb-2">Detail Produk :</p>
              <div class="row">
                <div class="col-xl-12">
                  <div class="row">
                    <div class="col-xl-5">
                      <span class="fs-14 fw-semibold">Kategori </span>
                    </div>
                    <div class="col-xl-7">
                      <p class="text-muted fs-14">
                        {{ product.category.name }}
                      </p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xl-5">
                      <span class="fs-14 fw-semibold">Brand</span>
                    </div>
                    <div class="col-xl-7">
                      <p class="text-muted fs-14">
                        {{ product.brand.name }}
                      </p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xl-5">
                      <span class="fs-14 fw-semibold">Tipe Barcode</span>
                    </div>
                    <div class="col-xl-7">
                      <p class="text-muted fs-14">
                        {{ product.barcode_type.toUpperCase() }}
                      </p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xl-5">
                      <span class="fs-14 fw-semibold">Tipe Satuan</span>
                    </div>
                    <div class="col-xl-7">
                      <p class="text-muted fs-14">
                        {{ product.is_unit == false ? "Biasa" : "Lanjutan" }}
                      </p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xl-5">
                      <span class="fs-14 fw-semibold">Tipe Stok</span>
                    </div>
                    <div class="col-xl-7">
                      <p class="text-muted fs-14">
                        {{
                          product.is_stock == false
                            ? "Tidak ada Stok"
                            : "Ada Stok"
                        }}
                      </p>
                    </div>
                  </div>

                  <div class="row" v-if="product.is_stock">
                    <div class="col-xl-5">
                      <span class="fs-14 fw-semibold">Peringatan Stok Qty</span>
                    </div>
                    <div class="col-xl-7">
                      <p class="text-muted fs-14">
                        {{ formatNumber(product.alert_qty) }}
                      </p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xl-5">
                      <span class="fs-14 fw-semibold">Berat</span>
                    </div>
                    <div class="col-xl-7">
                      <p class="text-muted fs-14">
                        {{ formatNumber(product.weight) }} Gram
                      </p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xl-5">
                      <span class="fs-14 fw-semibold">Mekanisme Harga</span>
                    </div>
                    <div class="col-xl-7">
                      <p
                        class="text-muted fs-14"
                        v-if="product.price_type == 'normal_price'"
                      >
                        Harga Normal
                      </p>
                      <p
                        class="text-muted fs-14"
                        v-if="product.price_type == 'grosir_price'"
                      >
                        Harga Grosir
                      </p>
                      <p
                        class="text-muted fs-14"
                        v-if="product.price_type == 'store_price'"
                      >
                        Harga Per Toko
                      </p>
                      <p
                        class="text-muted fs-14"
                        v-if="product.price_type == 'member_price'"
                      >
                        Harga Per Level Pelanggan
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <p class="fs-15 fw-semibold mb-1">Deskripsi :</p>
              <div v-html="product.description"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-8 col-lg-8">
        <div class="row product-scroll rounded-3 border p-3">
          <div class="col-xl-12 mt-xl-0 mt-3">
            <div>
              <p class="fs-18 fw-semibold mb-0">
                {{ product.name }}
                {{
                  variations.length > 1 ? " - " + variations[choose].name : ""
                }}
              </p>

              <div class="row mb-4 mt-4">
                <div class="col-xl-5 col-xl-12">
                  <p class="mb-1 lh-1 fs-11 text-success fw-semibold">
                    Harga Jual
                  </p>
                  <div class="d-flex align-items-center">
                    <h3 class="mb-1 fw-semibold">
                      <span
                        >Rp
                        {{ formatNumber(variations[choose].selling_price) }}
                      </span>
                    </h3>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <div class="row">
                  <div class="col-12" v-if="variations.length > 1">
                    <p class="fs-15 fw-semibold mb-2" >Variant :</p>
                    <div class="row gy-3 mb-4 mt-2 product-checkout">
                      <div
                        class="col-xl-6"
                        v-for="(variant, v) in variations"
                        :key="'variant' + v"
                      >
                        <div class="form-check shipping-method-container">
                          <input
                            :id="'choose-method' + m"
                            name="choose_store"
                            type="radio"
                            :value="v"
                            v-model="choose"
                            class="form-check-input"
                            style="margin-top: -5px !important"
                          />
                          <div class="form-check-label">
                            <div class="d-sm-flex justify-content-between">
                              <div
                                class="shipping-partner-details me-sm-5 me-0"
                              >
                                <p class="mb-0 fw-semibold">
                                  {{ variant.name }}
                                </p>
                              </div>
                              <div class="fw-semibold me-sm-3 me-0"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="border p-3 rounded-3 mb-3">
                <p class="fs-16 fw-semibold mb-2">Informasi Harga :</p>
                <div class="row">
                  <div class="col-xl-12">
                    <div class="row">
                      <div class="col-xl-5">
                        <span class="fs-14 fw-semibold">SKU </span>
                      </div>
                      <div class="col-xl-7">
                        <p class="text-muted fs-14">
                          {{ variations[choose].sku }}
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xl-5">
                        <span class="fs-14 fw-semibold">Barcode</span>
                      </div>
                      <div class="col-xl-7">
                        <p class="text-muted fs-14">
                          {{ variations[choose].barcode }}
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xl-5">
                        <span class="fs-14 fw-semibold">Harga Modal</span>
                      </div>
                      <div class="col-xl-7">
                        <p class="text-muted fs-14">
                          {{ formatNumber(variations[choose].purchase_price) }}
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xl-5">
                        <span class="fs-14 fw-semibold">Harga Jual</span>
                      </div>
                      <div class="col-xl-7">
                        <p class="text-muted fs-14">
                          {{ formatNumber(variations[choose].selling_price) }}
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xl-5">
                        <span class="fs-14 fw-semibold">Harga Grosir</span>
                      </div>
                      <div class="col-xl-7">
                        <p class="text-muted fs-14">
                          {{ formatNumber(variations[choose].grocery) }}
                        </p>
                      </div>
                    </div>
                   
                    <div class="row" v-if="variations[choose].include_tax">
                      <div class="col-xl-5">
                        <span class="fs-14 fw-semibold">Persentase Pajak</span>
                      </div>
                      <div class="col-xl-7">
                        <p class="text-muted fs-14">
                          {{ variations[choose].tax }}%
                        </p>
                      </div>
                    </div>

                    <div class="row" v-if="variations[choose].is_point">
                      <div class="col-xl-5">
                        <span class="fs-14 fw-semibold">Point</span>
                      </div>
                      <div class="col-xl-7">
                        <p class="text-muted fs-14">
                          {{ formatNumber(variations[choose].point) }}
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xl-5">
                        <span class="fs-14 fw-semibold">Satuan Dasar</span>
                      </div>
                      <div class="col-xl-7">
                        <p class="text-muted fs-14">
                          {{ variations[choose].unit_name }}
                        </p>
                      </div>
                    </div>
                    <div
                      class="row"
                      v-if="variations[choose].unit_sale != null"
                    >
                      <div class="col-xl-5">
                        <span class="fs-14 fw-semibold">Satuan Penjualan</span>
                      </div>
                      <div class="col-xl-7">
                        <p class="text-muted fs-14">
                          {{ variations[choose].unit_sale_name }}
                        </p>
                      </div>
                    </div>
                    <div
                      class="row"
                      v-if="variations[choose].unit_purchase != null"
                    >
                      <div class="col-xl-5">
                        <span class="fs-14 fw-semibold">Satuan Pembelian</span>
                      </div>
                      <div class="col-xl-7">
                        <p class="text-muted fs-14">
                          {{ variations[choose].unit_purchase_name }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="border p-3 rounded-3 mb-3" v-if="product.is_unit && variations[choose].units.length > 0">
                <p class="fs-16 fw-semibold mb-2">Satuan Multi Harga :</p>
                <div class="row">
                  <div class="col-xl-12">
                    <div class="table-responsive">
                      <table class="table text-nowrap table-bordered">
                        <thead>
                          <tr>
                            <th scope="col">Nama Satuan</th>
                            <th scope="col">Operator</th>
                            <th scope="col">Value</th>
                            <th scope="col">Perubahan Harga</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="(unit, u) in variations[choose].units"
                            :key="u + 'unit'"
                          >
                            <th scope="row">
                              {{ unit.name }}
                            </th>
                            <td>
                              {{
                                unit.operator == "*" ? "Perkalian" : "Pembagian"
                              }}
                            </td>
                            <td>{{ formatNumber(unit.value) }} Qty</td>
                            <td>Rp {{ formatNumber(unit.price) }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div
                class="border p-3 rounded-3 mb-3"
                v-if="product.price_type == 'store_price'"
              >
                <p class="fs-16 fw-semibold mb-2">Multi Harga :</p>
                <div class="row">
                  <div class="col-xl-12">
                    <div class="table-responsive">
                      <table class="table text-nowrap table-bordered">
                        <thead>
                          <tr>
                            <th scope="col">
                              {{
                                product.price_type == "store_price"
                                  ? "Toko / Cabang"
                                  : "Level Pelanggan"
                              }}
                            </th>
                            <th scope="col">Perubahan Harga</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-if="product.price_type == 'store_price'"
                            v-for="(price, p) in variations[choose]
                              .price_stores"
                            :key="p + 'price'"
                          >
                            <td>
                              {{ price.name }}
                            </td>
                            <td>Rp {{ formatNumber(price.price) }}</td>
                          </tr>
                          <tr
                            v-else
                            v-for="(price, pl) in variations[choose]
                              .price_levels"
                            :key="pl + 'price-level'"
                          >
                            <td>
                              {{ price.name }}
                            </td>
                            <td>Rp {{ formatNumber(price.price) }}</td>
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
    </div>
    <div class="row" v-else>
      <div class="col-12 d-flex justify-content-center">
        <ProgressSpinner />
      </div>
    </div>
  </div>
</template>

<script>
import { ApiData } from "@/api/server";
import Carousel from "primevue/carousel";
import SelectButton from "primevue/selectbutton";
var _ = require("lodash");

export default {
  name: "detail_product",
  components: {
    Carousel,
    SelectButton,
  },
  data() {
    return {
      choose: 0,
      loader: {
        starter: true,
      },
      product: {
        id: "",
        name: "",
        is_variant: true,
        is_stock: true,
        is_unit: false,
        price_type: "",
        barcode_type: "",
        alert_qty: 0,
        weight: 0,
        description: null,
        category: {
          id: "",
          name: "",
        },
        brand: {
          id: "",
          name: "",
        },
        media: [],
      },
      variations: [
        {
          id: "",
          barcode: "",
          name: "",
          sku: "",
          purchase_price: 0,
          selling_price: 0,
          include_tax: false,
          tax: 0,
          unit: "",
          unit_name: "",
          unit_sale: "",
          unit_sale_name: "",
          unit_purchase: "",
          unit_purchase_name: "",
          grocery: 0,
          get_point: false,
          point: 0,
          rak: {
            id: "",
            rak: "",
          },
          units: [],
          price_levels: [],
          price_stores: [],
        },
      ],
      gallery: [],
    };
  },
  computed: {},
  created() {
    this.getDetails();
  },
  methods: {
    async getDetails() {
      this.loader.starter = true;
      try {
        const response = await ApiData.get(
          `app/inventory/detail/${this.$route.params.id}`
        );
        var data = response.data;
        this.product = data.details;
        this.variations = data.variations;
        this.gallery = data.gallery;
        this.loader.starter = false;
      } catch (error) {
        console.log(error);
      }
    },

    formatNumber(number) {
     if (parseFloat(number) > 0) {
        return number.toLocaleString();
      } else {
        return 0;
      }
    },
  },
  mounted: function () {},
  watch: {},
};
</script>
