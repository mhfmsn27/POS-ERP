<template>
  <div class="p-4">
    <div class="row">
      <div class="col-12 d-flex justify-content-between">
        <div>
          <label class="form-label">Cari</label>
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fa fa-search"></i> </span>
            <input
              type="text"
              v-model="filter.name"
              v-tooltip="
                'Masukkan Nama Variant atau Nama Toko Untuk Mulai Mencari Data Berdasarkan Variant atau Toko'
              "
              @keyup="searchData()"
              class="form-control"
              placeholder="Cari ...."
              aria-describedby="basic-addon1"
            />
          </div>
        </div>
      </div>
      <div class="col-12 mt-2">
        <div class="table-responsive">
          <DataTable
            :value="stocks"
            :paginator="true"
            :rows="limit"
            :rowsPerPageOptions="[20, 50, 100]"
            paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :lazy="true"
            :totalRecords="totalRows"
            @page="onPageChange($event)"
            class="table text-nowrap"
            :loading="loader.data"
            responsiveLayout="scroll"
            sortField="dynamicSortField"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
          >
            <Column header="Nama Produk" field="product"></Column>
            <Column header="Variant" field="variation"></Column>
            <Column header="Gudang" field="warehouse.name"></Column>
            <Column header="Jumlah Stok">
              <template #body="{ data }">
                {{ formatNumber(data.stock) }}
              </template>
            </Column>
          </DataTable>
        </div>
      </div>
    </div>
  </div>
</template>

<script>


var _ = require("lodash");
import { ApiData } from "@/api/server";
export default {
  name: "stocks",
  components: {},
  data() {
    return {
      stocks: [],
      loader: {
        data: false,
      },
      limit: 20,
      totalRows: 0,
      page: 1,
      filter: {
        name: "",
      },
    };
  },
  computed: {},
  created() {
    this.getData();
  },
  methods: {
    async getData(page = 1) {
      this.loader.data = true;
      this.page = page;

      try {
        const response = await ApiData.get(
          `app/reports/stocks/stocks?limit=${this.limit}&page=${this.page}&name=${this.filter.name}&product=${this.$route.params.id}`
        );
        var data = response.data;
        this.stocks = data.stocks;
        this.totalRows = data.totalRows;
        this.loader.data = false;
      } catch (error) {
        console.log(error);
      }
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

    resetFilter() {
      this.filter = {
        name: "",
      };
      this.searchData();
    },
  },
  mounted: function () {},
  watch: {},
};
</script>
