const sales = {
    path: "/app/sales",
    redirect: "/app/sales/shipping",
    children: [
        {
            path: "offer",
            redirect: "/app/sales/offer/list",
            children: [
                {
                    name: "sales_offer",
                    path: "list",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: true,
                        url: "/panel/sales/offer/list",
                        title: "Daftar Data",
                        parent_menu: "sales_offer",
                        icon: "fe fe-list",
                        parent: {
                            name: "sales_offer",
                            title: "Penawaran Barang",
                            icon: "fa fa-file",
                        },
                    },
                },
                {
                    name: "sales_offer_create",
                    path: "create",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: true,
                        closeTab: true,
                        url: "/panel/sales/offer/create",
                        title: "Buat Data",
                        parent_menu: "sales_offer",
                        icon: "fe fe-plus-circle",
                        parent: {
                            name: "sales_offer",
                            title: "Penawaran Barang",
                            icon: "fa fa-file",
                        },
                    },
                },
                {
                    name: "sales_offer_update",
                    path: "update/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: false,
                        closeTab: true,
                        url: "/panel/sales/offer/update/:id",
                        title: "Edit Data",
                        parent_menu: "sales_offer",
                        icon: "fe fe-edit",
                        parent: {
                            name: "sales_offer",
                            title: "Penawaran Barang",
                            icon: "fa fa-file",
                        },
                    },
                },
                {
                    name: "sales_offer_detail",
                    path: "detail/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: false,
                        url: "/panel/sales/offer/detail/:id",
                        title: "Detail Penawaran Barang",
                        parent_menu: "sales_offer",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "sales_offer",
                            title: "Penawaran Barang",
                            icon: "fa fa-file",
                        },
                    },
                },
            ],
        },
        {
            path: "shipping",
            redirect: "/app/sales/shipping/list",
            children: [
                {
                    name: "sales_shipping",
                    path: "list",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: true,
                        url: "/panel/sales/shipping/list",
                        title: "Daftar Data",
                        parent_menu: "sales_shipping",
                        icon: "fe fe-list",
                        parent: {
                            name: "sales_shipping",
                            title: "Pengiriman Barang",
                            icon: "fa fa-truck",
                        },
                    },
                },
                {
                    name: "sales_shipping_create",
                    path: "create",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: true,
                        closeTab: true,
                        url: "/panel/sales/shipping/create",
                        title: "Buat Data",
                        parent_menu: "sales_shipping",
                        icon: "fe fe-plus-circle",
                        parent: {
                            name: "sales_shipping",
                            title: "Pengiriman Barang",
                            icon: "fa fa-truck",
                        },
                    },
                },
                {
                    name: "sales_shipping_update",
                    path: "update/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: false,
                        closeTab: true,
                        url: "/panel/sales/shipping/update/:id",
                        title: "Edit Data",
                        parent_menu: "sales_shipping",
                        icon: "fe fe-edit",
                        parent: {
                            name: "sales_shipping",
                            title: "Pengiriman Barang",
                            icon: "fa fa-truck",
                        },
                    },
                },
                {
                    name: "sales_shipping_detail",
                    path: "detail/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: false,
                        url: "/panel/sales/shipping/detail/:id",
                        title: "Detail Pengiriman Barang",
                        parent_menu: "sales_shipping",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "sales_shipping",
                            title: "Pengiriman Barang",
                            icon: "fa fa-truck",
                        },
                    },
                },
            ],
        },
        {
            path: "faktur",
            redirect: "/app/sales/faktur/list",
            children: [
                {
                    name: "sales_list",
                    path: "list",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: true,
                        url: "/panel/sales/faktur/list",
                        title: "Faktur Penjualan",
                        parent_menu: "sales_list",
                        icon: "fe fe-list",
                        parent: {
                            name: "sales_list",
                            title: "Faktur Penjualan",
                            icon: "fe fe-shopping-cart",
                        },
                    },
                },
                {
                    name: "sales_create",
                    path: "create",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: true,
                        closeTab: true,
                        url: "/panel/sales/faktur/create",
                        title: "Tambah Faktur",
                        parent_menu: "sales_list",
                        icon: "fe fe-plus-circle",
                        parent: {
                            name: "sales_list",
                            title: "Faktur Penjualan",
                            icon: "fe fe-shopping-cart",
                        },
                    },
                },
                {
                    name: "sales_update",
                    path: "update/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: false,
                        closeTab: true,
                        url: "/panel/sales/faktur/update/:id",
                        title: "Edit Faktur Penjualan",
                        parent_menu: "sales_list",
                        icon: "fe fe-edit",
                        parent: {
                            name: "sales_list",
                            title: "Faktur Penjualan",
                            icon: "fe fe-shopping-cart",
                        },
                    },
                },
                {
                    name: "sales_detail",
                    path: "detail/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: false,
                        url: "/panel/sales/faktur/detail/:id",
                        title: "Detail Faktur Penjualan",
                        parent_menu: "sales_list",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "sales_list",
                            title: "Faktur Penjualan",
                            icon: "fe fe-shopping-cart",
                        },
                    },
                },
            ],
        },
        {
            path: "payment",
            redirect: "/app/sales/payment/list",
            children: [
                {
                    name: "sales_payment_list",
                    path: "list",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: true,
                        url: "/panel/sales/payment/list",
                        title: "Pembayaran Penjualan",
                        parent_menu: "sales_payment_list",
                        icon: "fe fe-list",
                        parent: {
                            name: "sales_payment_list",
                            title: "Penerimaan Penjualan",
                            icon: "fe fe-credit-card",
                        },
                    },
                },
                {
                    name: "sales_payment_create",
                    path: "create",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: true,
                        closeTab: true,
                        url: "/panel/sales/payment/create",
                        title: "Buat Penerimaan Penjualan",
                        parent_menu: "sales_payment_list",
                        icon: "fe fe-plus-circle",
                        parent: {
                            name: "sales_payment_list",
                            title: "Penerimaan Penjualan",
                            icon: "fe fe-credit-card",
                        },
                    },
                },
                {
                    name: "sales_payment_update",
                    path: "update/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: false,
                        closeTab: true,
                        url: "/panel/sales/payment/update/:id",
                        title: "Edit Penerimaan Penjualan",
                        parent_menu: "sales_payment_list",
                        icon: "fe fe-pencil",
                        parent: {
                            name: "sales_payment_list",
                            title: "Penerimaan Penjualan",
                            icon: "fe fe-credit-card",
                        },
                    },
                },
                {
                    name: "sales_payment_detail",
                    path: "detail/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: false,
                        url: "/panel/sales/payment/detail/:id",
                        title: "Detail Pembayaran Penjualan",
                        parent_menu: "sales_payment_list",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "sales_payment_list",
                            title: "Penerimaan Penjualan",
                            icon: "fe fe-credit-card",
                        },
                    },
                },
            ],
        },
        {
            path: "return",
            redirect: "/app/sales/return/list",
            children: [
                {
                    name: "sales_return_list",
                    path: "list",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: true,
                        url: "/panel/sales/return/list",
                        title: "Daftar Retur Penjualan",
                        parent_menu: "sales_return_list",
                        icon: "fa fa-cart-arrow-down",
                        parent: {
                            name: "sales",
                            title: "Penjualan",
                            icon: "fe fe-credit-card",
                        },
                    },
                },
                {
                    name: "sales_return_create",
                    path: "create/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: true,
                        closeTab: true,
                        url: "/panel/sales/return/create/:id",
                        title: "Buat Retur Penjualan",
                        parent_menu: "sales_return_list",
                        icon: "fa fa-cart-arrow-down",
                        parent: {
                            name: "sales",
                            title: "Penjualan",
                            icon: "fe fe-shopping-cart",
                        },
                    },
                },
                {
                    name: "sales_return_update",
                    path: "update/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: false,
                        closeTab: true,
                        url: "/panel/sales/return/update/:id",
                        title: "Daftar Pengiriman Barang",
                        parent_menu: "sales_return_list",
                        icon: "fa fa-cart-arrow-down",
                        parent: {
                            name: "sales",
                            title: "Penjualan",
                            icon: "fe fe-shopping-cart",
                        },
                    },
                },
                {
                    name: "sales_return_detail",
                    path: "detail/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        keepAlive: false,
                        url: "/panel/sales/return/detail/:id",
                        title: "Daftar Pengiriman Barang",
                        parent_menu: "sales_return_list",
                        icon: "fa fa-cart-arrow-down",
                        parent: {
                            name: "sales",
                            title: "Penjualan",
                            icon: "fe fe-shopping-cart",
                        },
                    },
                },
            ],
        },

        // Customer
        {
            path: "customer",
            redirect: "/app/sales/customer/list",
            children: [
                {
                    name: "customer",
                    path: "list",
                    component: () => import("../../index.vue"),
                    meta: {
                        title: "Pelanggan",
                        url: "/panel/sales/customer/list",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                },
                {
                    name: "customer_create",
                    path: "create",
                    component: () => import("../../index.vue"),
                    meta: {
                        closeTab: true,
                        url: "/panel/sales/customer/create",
                        title: "Tambah Pelanggan",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                },
                {
                    name: "customer_update",
                    path: "update:/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        closeTab: true,
                        url: "/panel/sales/customer/update/:id",
                        title: "Edit Pelanggan",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                },
                {
                    name: "customer_due",
                    path: "customer-due/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        url: "/panel/sales/customer/customer-due/:id",
                        title: "Hutang Pelanggan",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                },
                {
                    name: "customer_transaction",
                    path: "customer-history/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        url: "/panel/sales/customer/sales/customer-history/:id",
                        title: "Riwayat Transaksi",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                },
                {
                    name: "customer_saldo",
                    path: "customer-saldo/:id",
                    component: () => import("../../index.vue"),
                    meta: {
                        url: "/panel/sales/customer/sales/customer-saldo/:id",
                        title: "Piutang Pelanggan",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                },
            ],
        },
    ],
};

const salesRoutes = [sales];

export default salesRoutes;
