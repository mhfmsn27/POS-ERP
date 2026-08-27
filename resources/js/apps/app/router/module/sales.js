const sales = {
    path: "/panel/sales",
    redirect: "/panel/sales/shipping",
    children: [
        {
            path: "offer",
            redirect: "/panel/sales/offer/list",
            component: () => import("../../pages/sales/offer"),
            children: [
                {
                    name: "sales_offer",
                    path: "list",
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
                    component: () => import("../../pages/sales/offer/list.vue"),
                },
                {
                    name: "sales_offer_create",
                    path: "create",
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
                    component: () =>
                        import("../../pages/sales/offer/create.vue"),
                },
                {
                    name: "sales_offer_update",
                    path: "update/:id",
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
                    component: () =>
                        import("../../pages/sales/offer/update.vue"),
                },
                {
                    name: "sales_offer_detail",
                    path: "detail/:id",
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
                    component: () =>
                        import("../../pages/sales/offer/detail.vue"),
                },
            ],
        },
        {
            path: "shipping",
            redirect: "/panel/sales/shipping/list",
            component: () => import("../../pages/sales/shipping"),
            children: [
                {
                    name: "sales_shipping",
                    path: "list",
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
                    component: () =>
                        import("../../pages/sales/shipping/list.vue"),
                },
                {
                    name: "sales_shipping_create",
                    path: "create",
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
                    component: () =>
                        import("../../pages/sales/shipping/create.vue"),
                },
                {
                    name: "sales_shipping_update",
                    path: "update/:id",
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
                    component: () =>
                        import("../../pages/sales/shipping/update.vue"),
                },
                {
                    name: "sales_shipping_detail",
                    path: "detail/:id",
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
                    component: () =>
                        import("../../pages/sales/shipping/detail.vue"),
                },
            ],
        },
        {
            path: "faktur",
            redirect: "/panel/sales/faktur/list",
            component: () => import("../../pages/sales"),
            children: [
                {
                    name: "sales_list",
                    path: "list",
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
                    component: () => import("../../pages/sales/list.vue"),
                },
                {
                    name: "sales_create",
                    path: "create",
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
                    component: () => import("../../pages/sales/create.vue"),
                },
                {
                    name: "sales_update",
                    path: "update/:id",
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
                    component: () => import("../../pages/sales/update.vue"),
                },
                {
                    name: "sales_detail",
                    path: "detail/:id",
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
                    component: () => import("../../pages/sales/detail.vue"),
                },
            ],
        },
        {
            path: "payment",
            redirect: "/panel/sales/payment/list",
            component: () => import("../../pages/sales/faktur"),
            children: [
                {
                    name: "sales_payment_list",
                    path: "list",
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
                    component: () =>
                        import("../../pages/sales/faktur/list.vue"),
                },
                {
                    name: "sales_payment_create",
                    path: "create",
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
                    component: () =>
                        import("../../pages/sales/faktur/create.vue"),
                },
                {
                    name: "sales_payment_update",
                    path: "update/:id",
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
                    component: () =>
                        import("../../pages/sales/faktur/update.vue"),
                },
                {
                    name: "sales_payment_detail",
                    path: "detail/:id",
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
                    component: () =>
                        import("../../pages/sales/faktur/detail.vue"),
                },
            ],
        },
        {
            path: "return",
            redirect: "/panel/sales/return/list",
            component: () => import("../../pages/sales/return"),
            children: [
                {
                    name: "sales_return_list",
                    path: "list",
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
                    component: () =>
                        import("../../pages/sales/return/list.vue"),
                },
                {
                    name: "sales_return_create",
                    path: "create/:id",
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
                    component: () =>
                        import("../../pages/sales/return/create.vue"),
                },
                {
                    name: "sales_return_update",
                    path: "update/:id",
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
                    component: () =>
                        import("../../pages/sales/return/update.vue"),
                },
                {
                    name: "sales_return_detail",
                    path: "detail/:id",
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
                    component: () =>
                        import("../../pages/sales/return/detail.vue"),
                },
            ],
        },

        // Customer
        {
            path: "customer",
            redirect: "/panel/customer/list",
            component: () => import("../../pages/crm/customers"),
            children: [
                {
                    name: "customer",
                    path: "list",
                    meta: {
                        title: "Pelanggan",
                        url: "/panel/customer/list",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/customers/list.vue"),
                },
                {
                    name: "customer_create",
                    path: "create",
                    meta: {
                        closeTab: true,
                        url: "/panel/customer/create",
                        title: "Tambah Pelanggan",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/customers/create.vue"),
                },
                {
                    name: "customer_update",
                    path: "update:/:id",
                    meta: {
                        closeTab: true,
                        url: "/panel/customer/update/:id",
                        title: "Edit Pelanggan",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/customers/update.vue"),
                },
                {
                    name: "customer_due",
                    path: "customer-due/:id",
                    meta: {
                        url: "/panel/customer/customer-due/:id",
                        title: "Hutang Pelanggan",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/customers/due.vue"),
                },
                {
                    name: "customer_transaction",
                    path: "customer-history/:id",
                    meta: {
                        url: "/panel/customer/customer-history/:id",
                        title: "Riwayat Transaksi",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/customers/history.vue"),
                },
                {
                    name: "customer_saldo",
                    path: "customer-saldo/:id",
                    meta: {
                        url: "/panel/customer/customer-saldo/:id",
                        title: "Piutang Pelanggan",
                        parent_menu: "customer",
                        icon: "fa fa-users",
                        parent: {
                            name: "customer",
                            title: "Pelanggan",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/customers/saldo.vue"),
                },
            ],
        },
    ],
};

const salesRoutes = [sales];

export default salesRoutes;
