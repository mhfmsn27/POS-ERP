<template>
    <div class="row mt-3">
        <!-- 4 Executive KPI Summary Cards -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
            <div class="card poshub-kpi-card h-100 p-3 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Total Pendapatan</span>
                        <h4 class="fw-bold mb-0 text-success mt-1">
                            Rp {{ formatNumber(profit.pendapatan || 0) }}
                        </h4>
                        <small class="text-muted">Akumulasi Omset Tahun Ini</small>
                    </div>
                    <div class="poshub-kpi-icon bg-success-subtle text-success">
                        <i class="fa fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
            <div class="card poshub-kpi-card h-100 p-3 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Estimasi Laba Bersih</span>
                        <h4 class="fw-bold mb-0 mt-1" :class="(profit.profit || 0) >= 0 ? 'text-primary' : 'text-danger'">
                            Rp {{ formatNumber(profit.profit || 0) }}
                        </h4>
                        <small class="text-muted">Net Margin Operasional</small>
                    </div>
                    <div class="poshub-kpi-icon bg-primary-subtle text-primary">
                        <i class="fa fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
            <div class="card poshub-kpi-card h-100 p-3 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Penjualan Harian</span>
                        <h4 class="fw-bold mb-0 text-info mt-1">
                            {{ daily_sales.amount || 'Rp 0' }}
                        </h4>
                        <small class="text-muted">{{ daily_sales.transactions || 0 }} Transaksi ({{ daily_sales.qty || 0 }} Qty)</small>
                    </div>
                    <div class="poshub-kpi-icon bg-info-subtle text-info">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
            <div class="card poshub-kpi-card h-100 p-3 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Piutang Belum Lunas</span>
                        <h4 class="fw-bold mb-0 text-warning mt-1">
                            {{ piutang.unpaid || 'Rp 0' }}
                        </h4>
                        <small class="text-muted">Jatuh tempo: {{ piutang.overdue || 'Rp 0' }}</small>
                    </div>
                    <div class="poshub-kpi-icon bg-warning-subtle text-warning">
                        <i class="fa fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Executive KPI Cards -->

        <!-- Monthly Sales -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 px-3">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="fa fa-chart-area text-primary me-2"></i>Tren Penjualan Bulanan
                    </h5>
                    <div style="max-width: 200px;">
                        <input
                            type="month"
                            v-model="monthly_sales.date"
                            @input="getMonthlySales()"
                            class="form-control form-control-sm border-secondary-subtle rounded-pill"
                        />
                    </div>
                </div>
                <div class="card-body">
                    <Chart
                        type="line"
                        :data="monthly_sales.data"
                        :options="monthly_sales.options"
                        style="height: 360px !important; width: 100%"
                    />
                </div>
            </div>
        </div>
        <!-- End Monthly Sales -->

        <div class="col-lg-6 col-sm-12 row">
            <!-- Activity -->
            <div class="col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Log Aktivitas ( {{ activity.user.name }} )</h5>
                        <button
                            type="button"
                            @click="activity.modal = true"
                            v-tooltip="'Filter Data'"
                            class="btn btn-icon btn-outline-info btn-wave waves-effect waves-light"
                        >
                            <i class="fe fe-filter me-2"></i> Filter
                        </button>
                    </div>
                    <div
                        class="card-body"
                        style="height: 300px; overflow: scroll"
                    >
                        <Timeline
                            :value="activity.list"
                            align="alternate"
                            class="w-full md:w-20rem"
                        >
                            <template #content="slotProps">
                                <div class="media-body my-auto">
                                    <a
                                        href="javascript:void(0);"
                                        class="text-dark fs-12"
                                        >{{ slotProps.item.user }}</a
                                    >
                                    <div class="text-muted small">
                                        {{ slotProps.item.date }} -
                                        {{ slotProps.item.desc }}
                                    </div>
                                </div>
                            </template>
                        </Timeline>
                    </div>
                </div>
            </div>
            <!-- End Activity -->

            <!-- Stock Minus -->
            <div class="col-12">
                <div class="card Visitors">
                    <div class="card-header">
                        <h5 class="card-title">Stok Minus</h5>
                    </div>
                    <div
                        class="card-body"
                        style="height: 300px; overflow: scroll"
                    >
                        <div
                            class="media mt-0"
                            v-for="(item, index) in stock_minus"
                            :key="index"
                        >
                            <div class="media-body my-auto">
                                <a
                                    href="javascript:void(0);"
                                    class="text-dark"
                                    >{{ item.name }}</a
                                >
                                <div class="text-muted small">
                                    Minimum : {{ item.alert }}
                                </div>
                            </div>
                            <button
                                type="button"
                                class="btn btn-icon btn-outline-primary rounded-pill btn-wave waves-effect waves-light mb-2"
                            >
                                {{ item.qty }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Stock Minus -->

            <!-- Customer Due -->
            <div class="col-12">
                <div class="card Visitors">
                    <div class="card-header">
                        <h5 class="card-title">Pelanggan Berutang</h5>
                    </div>
                    <div
                        class="card-body"
                        style="height: 300px; overflow: scroll"
                    >
                        <div
                            class="mb-4"
                            v-for="(customerdue, cd) in dues.customers"
                            :key="cd"
                        >
                            <p class="mb-0">
                                {{ customerdue.customer
                                }}<span class="float-end text-muted"
                                    >{{ customerdue.day_left }} Hari</span
                                >
                            </p>
                            <p class="mb-1">
                                {{ formatNumber(customerdue.amount) }}
                                <span class="float-end text-muted">{{
                                    customerdue.due_date
                                }}</span>
                            </p>
                            <div class="progress h-2">
                                <div
                                    class="progress-bar"
                                    :class="
                                        customerdue.day_left < 7
                                            ? 'bg-danger ' +
                                              'w-' +
                                              customerdue.progress
                                            : 'bg-info ' +
                                              'w-' +
                                              customerdue.progress
                                    "
                                    role="progressbar"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Customer Due -->

            <!-- Top Products -->
            <div class="col-12">
                <div class="card Visitors">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title">Penjualan Teratas</h5>
                        <Dropdown
                            v-model="top_products.priode"
                            :options="[
                                {
                                    name: 'Harian',
                                    value: 'day',
                                },
                                {
                                    name: 'Bulanan',
                                    value: 'month',
                                },
                                {
                                    name: 'Tahunan',
                                    value: 'year',
                                },
                            ]"
                            optionLabel="name"
                            optionValue="value"
                            placeholder="Pilih Priode"
                            @change="getTopProducts"
                            style="width: 50%"
                        />
                    </div>
                    <div
                        class="card-body"
                        style="height: 300px; overflow: scroll"
                    >
                        <div
                            class="clearfix row mb-4"
                            v-for="(tops, tp) in top_products.list"
                            :key="tp"
                        >
                            <div class="col">
                                <div class="float-start">
                                    <h5 class="mb-0">
                                        <strong>{{ tops.product }}</strong>
                                    </h5>
                                    <small class="text-muted"
                                        >Terjual {{ tops.quantity }} Qty
                                    </small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="float-end">
                                    <h4
                                        class="font-weight-bold mb-0 mt-2 text-blue"
                                    >
                                        {{ tops.total }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Top Products -->

            <!-- Daily Sales -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <input
                            type="date"
                            v-model="daily_sales.date"
                            @input="getDailySale()"
                            class="form-control"
                        />
                    </div>
                    <div class="card-body">
                        <div class="card-widget">
                            <h6 class="mb-2">Penjualan Perhari</h6>
                            <h2 class="text-end">
                                <i
                                    class="fa fa-cart-plus icon-size float-start text-danger text-danger-shadow"
                                ></i
                                ><span>{{ daily_sales.amount }}</span>
                            </h2>
                            <p class="mb-0">
                                Jumlah Transaksi<span class="float-end"
                                    >{{ daily_sales.transactions }} Kali
                                </span>
                            </p>
                            <p class="mb-0">
                                Jenis Barang<span class="float-end"
                                    >{{ daily_sales.type }} Barang
                                </span>
                            </p>
                            <p class="mb-0">
                                Jumlah Barang<span class="float-end"
                                    >{{ daily_sales.qty }} Qty
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Daily Sales -->

            <!-- Piutang -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Piutang Usaha</h5>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">Total Pendapatan</small>
                        <h2 class="number-font">{{ piutang.revenue }}</h2>
                        <div class="progress grouped h-3">
                            <div
                                class="progress-bar w-25 bg-primary"
                                role="progressbar"
                            ></div>
                            <div
                                class="progress-bar w-30 bg-danger"
                                role="progressbar"
                            ></div>
                            <div
                                class="progress-bar w-20 bg-warning"
                                role="progressbar"
                            ></div>
                        </div>
                        <div class="row mt-3 pt-3">
                            <div class="col border-end">
                                <p class="number-font1 mb-0">
                                    <span class="dot-label bg-success"></span
                                    >Lunas
                                </p>
                                <h6 class="mt-2 fw-semibold mb-0">
                                    {{ piutang.paid }}
                                </h6>
                            </div>
                            <div class="col border-end">
                                <p class="number-font1 mb-0">
                                    <span class="dot-label bg-primary"></span
                                    >Belum Lunas
                                </p>
                                <h6 class="mt-2 fw-semibold mb-0">
                                    {{ piutang.unpaid }}
                                </h6>
                            </div>
                            <div class="col border-end">
                                <p class="number-font mb-0">
                                    <span class="dot-label bg-danger"></span
                                    >Belum lewat
                                </p>
                                <h6 class="mt-2 fw-semibold mb-0">
                                    {{ piutang.not_due }}
                                </h6>
                            </div>
                            <div class="col">
                                <p class="number-font1 mb-0">
                                    <span class="dot-label bg-warning"></span
                                    >Sudah Lewat
                                </p>
                                <h6 class="mt-2 fw-semibold mb-0">
                                    {{ piutang.overdue }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Piutang -->
        </div>

        <div class="col-lg-6 col-sm-12 row">
            <!-- Stock Minimum -->
            <div class="col-12">
                <div class="card Visitors">
                    <div class="card-header">
                        <h5 class="card-title">Stok Minimum</h5>
                    </div>
                    <div
                        class="card-body"
                        style="height: 300px; overflow: scroll"
                    >
                        <div
                            class="media mt-0"
                            v-for="(item, index) in stock_alerts"
                            :key="index"
                        >
                            <div class="media-body my-auto">
                                <a
                                    href="javascript:void(0);"
                                    class="text-dark"
                                    >{{ item.name }}</a
                                >
                                <div class="text-muted small">
                                    Minimum : {{ item.alert }}
                                </div>
                            </div>
                            <button
                                type="button"
                                class="btn btn-icon btn-outline-primary rounded-pill btn-wave waves-effect waves-light mb-2"
                            >
                                {{ item.qty }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Stock Minimum -->

            <!-- Rugi -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h2 class="card-title">Laba Rugi Tahun ini</h2>
                    </div>
                    <div class="card-body">
                        <Chart
                            type="pie"
                            class="d-flex justify-content-center"
                            :data="profit_chart.data"
                            :options="profit_chart.style"
                            style="height: 200px !important; width: 100%"
                        />
                        <div class="text-center row mt-3">
                            <div class="col mt-4">
                                <h4 class="ms-5">
                                    <i
                                        class="fa fa-caret-up fa-1x text-primary me-1"
                                    ></i
                                    >Rp {{ formatNumber(profit.pendapatan) }}
                                </h4>
                                <h6 class="ms-5 pb-0 mb-0">Pendapatan</h6>
                            </div>

                            <div class="col mt-4">
                                <h4 class="me-5">
                                    <i
                                        class="fa fa-caret-down fa-1x text-primary me-1"
                                    ></i
                                    >Rp {{ formatNumber(profit.pengeluaran) }}
                                </h4>
                                <h6 class="me-5 mt-0 mb-0">Pengeluaran</h6>
                            </div>

                            <div class="col mt-4">
                                <h4 class="ms-5">
                                    {{ formatNumber(profit.hpp) }}
                                </h4>
                                <h6 class="ms-5 pb-0 mb-0">Hpp</h6>
                            </div>
                            <div class="col mt-4">
                                <h4 class="me-5">
                                    {{ formatNumber(profit.profit) }}
                                </h4>
                                <h6 class="me-5 mt-0 mb-0">Laba Tahun ini</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Laba Rugi -->

            <!-- Customer Active -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pengguna Aktif</h3>
                    </div>
                    <div style="height: 300px; overflow: scroll">
                        <div
                            class="list-group-item d-flex align-items-center border-top-0 border-start-0 border-end-0"
                            v-for="(user, index) in user_last_active"
                            :key="index"
                        >
                            <div class="me-2">
                                <img
                                    :src="user.photo"
                                    class="avatar avatar-md brround cover-image"
                                />
                            </div>
                            <div class="">
                                <div class="fw-semibold">{{ user.name }}</div>
                                <small class="text-muted"
                                    >{{ user.last_active }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Customer Active -->

            <!-- Top Customers -->
            <div class="col-12">
                <div class="card Visitors">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title">Pelanggan Teratas</h5>
                        <Dropdown
                            v-model="top_customers.priode"
                            :options="[
                                {
                                    name: 'Harian',
                                    value: 'day',
                                },
                                {
                                    name: 'Bulanan',
                                    value: 'month',
                                },
                                {
                                    name: 'Tahunan',
                                    value: 'year',
                                },
                            ]"
                            optionLabel="name"
                            optionValue="value"
                            placeholder="Pilih Priode"
                            @change="getTopCustomers"
                            style="width: 50%"
                        />
                    </div>
                    <div
                        class="card-body"
                        style="height: 300px; overflow: scroll"
                    >
                        <div
                            class="clearfix row mb-4"
                            v-for="(tops, tp) in top_customers.list"
                            :key="tp"
                        >
                            <div class="col">
                                <div class="float-start">
                                    <h5 class="mb-0">
                                        <strong>{{ tops.name }}</strong>
                                    </h5>
                                    <small class="text-muted"
                                        >{{ tops.amount }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Top Customers -->

            <!-- Bank Rekonsiliasi -->
            <div class="col-12">
                <div class="card Visitors">
                    <div class="card-header">
                        <h5 class="card-title">Rekonsiliasi Bank</h5>
                    </div>
                    <div
                        class="card-body"
                        style="height: 300px; overflow: scroll"
                    >
                        <div
                            class="mb-4"
                            v-for="(rekonsiliasi, cd) in rekonsiliations"
                            :key="cd"
                        >
                            <p class="mb-0">
                                {{ rekonsiliasi.bank
                                }}<span class="float-end text-muted"
                                    >{{ rekonsiliasi.rekening.total }}/{{
                                        rekonsiliasi.fakturco.total
                                    }}</span
                                >
                            </p>
                            <p class="mb-1">
                                Faktur
                                {{ formatNumber(rekonsiliasi.fakturco.amount) }}
                                <span class="float-end text-muted"
                                    >Rekening
                                    {{ rekonsiliasi.rekening.amount }}</span
                                >
                            </p>
                            <div class="progress h-2">
                                <div
                                    class="progress-bar bg-info"
                                    :class="`w-${rekonsiliasi.percent}`"
                                    role="progressbar"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Rekonsiliasi -->

            <!-- Hutang Usaha -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Hutang Usaha</h5>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">Pembayaran</small>
                        <h2 class="number-font">{{ hutang.revenue }}</h2>
                        <div class="progress grouped h-3">
                            <div
                                class="progress-bar w-25 bg-primary"
                                role="progressbar"
                            ></div>
                            <div
                                class="progress-bar w-30 bg-danger"
                                role="progressbar"
                            ></div>
                            <div
                                class="progress-bar w-20 bg-warning"
                                role="progressbar"
                            ></div>
                        </div>
                        <div class="row mt-3 pt-3">
                            <div class="col border-end">
                                <p class="number-font1 mb-0">
                                    <span class="dot-label bg-success"></span
                                    >Lunas
                                </p>
                                <h6 class="mt-2 fw-semibold mb-0">
                                    {{ hutang.paid }}
                                </h6>
                            </div>
                            <div class="col border-end">
                                <p class="number-font1 mb-0">
                                    <span class="dot-label bg-primary"></span
                                    >Belum Lunas
                                </p>
                                <h6 class="mt-2 fw-semibold mb-0">
                                    {{ hutang.unpaid }}
                                </h6>
                            </div>
                            <div class="col border-end">
                                <p class="number-font mb-0">
                                    <span class="dot-label bg-danger"></span
                                    >Belum lewat
                                </p>
                                <h6 class="mt-2 fw-semibold mb-0">
                                    {{ hutang.not_due }}
                                </h6>
                            </div>
                            <div class="col">
                                <p class="number-font1 mb-0">
                                    <span class="dot-label bg-warning"></span
                                    >Sudah Lewat
                                </p>
                                <h6 class="mt-2 fw-semibold mb-0">
                                    {{ hutang.overdue }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Hutang Usaha -->
        </div>
    </div>

    <!-- Modal For Filter -->

    <Dialog
        v-model:visible="activity.modal"
        class="filter-data"
        modal
        header="Aktivitas"
        :style="{ width: '30vw' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
        <div class="row p-3">
            <div class="col-lg-12 mb-3">
                <label for="user-date" class="form-label">Pilih Pengguna</label>
                <Multiselect
                    v-model="activity.user"
                    :options="users"
                    :multiple="false"
                    :allowEmpty="false"
                    :close-on-select="true"
                    :clear-on-select="true"
                    :preserve-search="true"
                    :searchable="true"
                    :internal-search="false"
                    :options-limit="50"
                    :loading="loader.user"
                    placeholder="Pilih Pengguna"
                    open-direction="bottom"
                    label="name"
                    id="id"
                    track-by="name"
                    @search-change="getUsers"
                ></Multiselect>
            </div>
        </div>
        <template #footer>
            <button
                type="button"
                @click="getActivity()"
                class="btn btn-outline-info btn-wave waves-effect waves-light"
            >
                {{ loader.activity ? "Mohon Tunggu...." : "Terapkan Filter" }}
            </button>
        </template>
    </Dialog>
    <!-- End Modal -->
</template>

<script>
import { ApiData } from "@/api/server";
import Timeline from "primevue/timeline";
import Chart from "primevue/chart";
import { formatNumber } from "chart.js/helpers";
var _ = require("lodash");

export default {
    name: "KeySetting",
    components: {
        Timeline,
        Chart,
    },
    data() {
        return {
            users: [],
            piutang: {
                revenue: 0,
                paid: 0,
                unpaid: 0,
                not_due: 0,
                overdue: 0,
            },
            hutang: {
                revenue: 0,
                paid: 0,
                unpaid: 0,
                not_due: 0,
                overdue: 0,
            },
            rekonsiliations: [],
            stock_alerts: [],
            monthly_sales: {
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: "Penjualan Bulanan",
                            data: [],
                            fill: true,
                            backgroundColor: "rgba(59, 130, 246, 0.12)",
                            borderColor: "#3b82f6",
                            borderWidth: 2.5,
                            tension: 0.35,
                            pointBackgroundColor: "#3b82f6",
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    aspectRatio: 0.6,
                    plugins: {
                        legend: {
                            labels: {
                                color: "#334155",
                                font: {
                                    weight: "600"
                                }
                            },
                        },
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: "#64748b",
                            },
                            grid: {
                                color: "rgba(226, 232, 240, 0.6)",
                            },
                        },
                        y: {
                            ticks: {
                                color: "#64748b",
                            },
                            grid: {
                                color: "rgba(226, 232, 240, 0.6)",
                            },
                        },
                    },
                },
                date: "",
            },
            top_products: {
                list: [],
                priode: "day",
            },
            top_customers: {
                list: [],
                priode: "year",
            },
            daily_sales: {
                amount: 0,
                transactions: 0,
                type: 0,
                qty: 0,
                date: "",
            },
            stock_minus: [],
            user_last_active: [],
            dues: {
                customers: [],
            },
            activity: {
                modal: false,
                user: {
                    id: "",
                    name: "",
                },
                list: [],
            },
            profit: {
                pendapatan: 0,
                pengeluaran: 0,
                hpp: 0,
                profit: 0,
            },
            profit_chart: {
                data: {
                    labels: ["Pendapatan", "Pengeluaran", "Hpp"],
                    datasets: [
                        {
                            data: [0, 0, 0],
                            backgroundColor: ["#77d023", "#ea3e56", "#3ebdea"],
                            hoverBackgroundColor: [
                                "#77d023",
                                "#ea3e56",
                                "#3ebdea",
                            ],
                        },
                    ],
                },
                style: {
                    plugins: {
                        legend: {
                            labels: {
                                usePointStyle: true,
                                color: "#000",
                            },
                        },
                    },
                },
            },
            loader: {
                activity: false,
                user: false,
            },
        };
    },
    methods: {
        formatNumber(number) {
            const num = parseFloat(number);
            if (isNaN(num)) return "0";
            if (num >= 0) {
                return num.toLocaleString();
            } else {
                return "-" + (-num).toLocaleString();
            }
        },

        async getUsers(query) {
            this.loader.user = true;
            try {
                const response = await ApiData.get(
                    `app/master/components/users?name=${query}`
                );
                var data = response.data;
                this.users = data.users;
                this.loader.user = false;
            } catch (error) {
                console.log(error);
            }
        },

        async getUserActive() {
            try {
                const response = await ApiData.get(`app/dashboard/active-user`);
                var data = response.data;
                this.user_last_active = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getActivity() {
            try {
                const response = await ApiData.get(
                    `app/dashboard/activity?user=${this.activity.user.id}&user_name=${this.activity.user.name}`
                );
                var data = response.data;
                this.activity = {
                    modal: false,
                    user: {
                        id: data.user.id,
                        name: data.user.name,
                    },
                    list: data.list,
                };
            } catch (error) {
                console.log(error);
            }
        },

        async getAlertStock() {
            try {
                const response = await ApiData.get(`app/dashboard/alerts`);
                var data = response.data;
                this.stock_alerts = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getMinusStock() {
            try {
                const response = await ApiData.get(
                    `app/dashboard/alerts?type=minus`
                );
                var data = response.data;
                this.stock_minus = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getTopProducts() {
            try {
                const response = await ApiData.get(
                    `app/dashboard/top-products?priode=${this.top_products.priode}`
                );
                var data = response.data;
                this.top_products.list = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getTopCustomers() {
            try {
                const response = await ApiData.get(
                    `app/dashboard/top-customers?priode=${this.top_customers.priode}`
                );
                var data = response.data;
                this.top_customers.list = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getDailySale() {
            try {
                const response = await ApiData.get(
                    `app/dashboard/daily-sales?date=${this.daily_sales.date}`
                );
                var data = response.data;
                this.daily_sales = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getCustomerDues() {
            try {
                const response = await ApiData.get(
                    `app/dashboard/dues/customer`
                );
                var data = response.data;
                this.dues.customers = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getRekonsiliasi() {
            try {
                const response = await ApiData.get(
                    `app/dashboard/rekonsiliastions`
                );
                var data = response.data;
                this.rekonsiliations = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getPiutangUsaha() {
            try {
                const response = await ApiData.get(
                    `app/dashboard/piutang?type=customer`
                );
                var data = response.data;
                this.piutang = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getHutangUsaha() {
            try {
                const response = await ApiData.get(
                    `app/dashboard/piutang?type=supplier`
                );
                var data = response.data;
                this.hutang = data;
            } catch (error) {
                console.log(error);
            }
        },

        async getProfitable() {
            try {
                const response = await ApiData.get(`app/dashboard/profitable`);
                var data = response.data;
                this.profit = {
                    pendapatan: data.pendapatan,
                    pengeluaran: data.pengeluaran,
                    hpp: data.hpp,
                    profit: data.profit,
                };

                this.profit_chart.data = {
                    labels: ["Pendapatan", "Pengeluaran", "Hpp"],
                    datasets: [
                        {
                            data: [data.pendapatan, data.pengeluaran, data.hpp],
                            backgroundColor: ["#77d023", "#ea3e56", "#3ebdea"],
                            hoverBackgroundColor: [
                                "#77d023",
                                "#ea3e56",
                                "#3ebdea",
                            ],
                        },
                    ],
                };
            } catch (error) {
                console.log(error);
            }
        },

        async getMonthlySales() {
            try {
                const response = await ApiData.get(
                    `app/dashboard/monthly-analisis?date=${this.monthly_sales.date}`
                );
                var data = response.data;
                this.monthly_sales.date = data.date;
                this.monthly_sales.data = {
                    labels: data.data.date,
                    datasets: [
                        {
                            label: "Penjualan Bersih",
                            data: data.data.amount,
                            fill: true,
                            backgroundColor: "rgba(59, 130, 246, 0.12)",
                            borderColor: "#3b82f6",
                            borderWidth: 2.5,
                            tension: 0.35,
                            pointBackgroundColor: "#3b82f6",
                        },
                    ],
                };
            } catch (error) {
                console.log(error);
            }
        },
    },
    mounted: function () {
        this.getActivity();
        this.getAlertStock();
        this.getMinusStock();
        this.getProfitable();
        this.getCustomerDues();
        this.getUserActive();
        this.getUsers("");
        this.getTopProducts();
        this.getTopCustomers();
        this.getDailySale();
        this.getRekonsiliasi();
        this.getMonthlySales();
        this.getPiutangUsaha();
        this.getHutangUsaha();
    },
    watch: {},
};
</script>
