const purchase = {
    path: "/panel/purchase",
    redirect: "/panel/purchase/received",
    children: [
        {
            path: "po",
            redirect: "/panel/purchase/po/list",
            component: () => import("../../pages/purchase/po"),
            children: [
                {
                    name: "purchase_po",
                    path: "list",
                    meta: {
                        url: "/panel/purchase/po/list",
                        keepAlive: true,
                        title: "Daftar Pesanan Barang",
                        parent_menu: "purchase_po",
                        icon: "fe fe-list",
                        parent: {
                            name: "purchase_po",
                            title: "Pesanan Barang",
                            icon: "fe fe-box",
                        },
                    },
                    component: () => import("../../pages/purchase/po/list.vue"),
                },
                {
                    name: "purchase_po_create",
                    path: "create",
                    meta: {
                        url: "/panel/purchase/po/create",
                        keepAlive: true,
                        closeTab: true,
                        title: "Buat Pesanan Barang",
                        parent_menu: "purchase_po",
                        icon: "fe fe-plus-circle",
                        parent: {
                            name: "purchase_po",
                            title: "Pesanan Barang",
                            icon: "fe fe-box",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/po/create.vue"),
                },
                {
                    name: "purchase_po_update",
                    path: "update/:id",
                    meta: {
                        url: "/panel/purchase/po/update/:id",
                        keepAlive: false,
                        closeTab: true,
                        title: "Edit Pesanan Barang",
                        parent_menu: "purchase_po",
                        icon: "fe fe-edit",
                        parent: {
                            name: "purchase_po",
                            title: "Pesanan Barang",
                            icon: "fe fe-box",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/po/update.vue"),
                },
                {
                    name: "purchase_po_detail",
                    path: "detail/:id",
                    meta: {
                        url: "/panel/purchase/po/detail/:id",
                        keepAlive: false,
                        title: "Detail Pesanan Barang",
                        parent_menu: "purchase_po",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "purchase_po",
                            title: "Pesanan Barang",
                            icon: "fe fe-box",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/po/detail.vue"),
                },
            ],
        },
        {
            path: "received",
            redirect: "/panel/purchase/received/list",
            component: () => import("../../pages/purchase/received"),
            children: [
                {
                    name: "purchase_received",
                    path: "list",
                    meta: {
                        url: "/panel/purchase/received/list",
                        keepAlive: true,
                        title: "Daftar Penerimaan Barang",
                        parent_menu: "purchase_received",
                        icon: "fe fe-list",
                        parent: {
                            name: "purchase_received",
                            title: "Penerimaan Barang",
                            icon: "fe fe-box",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/received/list.vue"),
                },
                {
                    name: "purchase_received_create",
                    path: "create",
                    meta: {
                        url: "/panel/purchase/received/create",
                        keepAlive: true,
                        closeTab: true,
                        title: "Buat Penerimaan Barang",
                        parent_menu: "purchase_received",
                        icon: "fe fe-plus-circle",
                        parent: {
                            name: "purchase_received",
                            title: "Penerimaan Barang",
                            icon: "fe fe-box",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/received/create.vue"),
                },
                {
                    name: "purchase_received_update",
                    path: "update/:id",
                    meta: {
                        url: "/panel/purchase/received/update/:id",
                        keepAlive: false,
                        closeTab: true,
                        title: "Edit Penerimaan Barang",
                        parent_menu: "purchase_received",
                        icon: "fe fe-edit",
                        parent: {
                            name: "purchase_received",
                            title: "Penerimaan Barang",
                            icon: "fe fe-box",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/received/update.vue"),
                },
                {
                    name: "purchase_received_detail",
                    path: "detail/:id",
                    meta: {
                        url: "/panel/purchase/received/detail/:id",
                        keepAlive: false,
                        title: "Detail Penerimaan Barang",
                        parent_menu: "purchase_received",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "purchase_received",
                            title: "Penerimaan Barang",
                            icon: "fe fe-box",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/received/detail.vue"),
                },
            ],
        },
        {
            path: "faktur",
            redirect: "/panel/purchase/faktur/list",
            component: () => import("../../pages/purchase"),
            children: [
                {
                    name: "purchase_list",
                    path: "list",
                    meta: {
                        url: "/panel/purchase/faktur/list",
                        keepAlive: false,
                        title: "Faktur Pembelian",
                        parent_menu: "purchase_list",
                        icon: "fe fe-list",
                        parent: {
                            name: "purchase_list",
                            title: "Faktur Pembelian",
                            icon: "fe fe-shopping-bag",
                        },
                    },
                    component: () => import("../../pages/purchase/list.vue"),
                },
                {
                    name: "purchase_create",
                    path: "create",
                    meta: {
                        url: "/panel/purchase/faktur/create",
                        keepAlive: true,
                        closeTab: true,
                        title: "Buat Faktur Pembelian",
                        parent_menu: "purchase_list",
                        icon: "fe fe-plus-circle",
                        parent: {
                            name: "purchase_list",
                            title: "Faktur Pembelian",
                            icon: "fe fe-shopping-bag",
                        },
                    },
                    component: () => import("../../pages/purchase/create.vue"),
                },
                {
                    name: "purchase_update",
                    path: "update/:id",
                    meta: {
                        url: "/panel/purchase/faktur/update/:id",
                        keepAlive: false,
                        closeTab: true,
                        title: "Edit Faktur Pembelian",
                        parent_menu: "purchase_list",
                        icon: "fe fe-edit",
                        parent: {
                            name: "purchase_list",
                            title: "Faktur Pembelian",
                            icon: "fe fe-shopping-bag",
                        },
                    },
                    component: () => import("../../pages/purchase/update.vue"),
                },
                {
                    name: "purchase_detail",
                    path: "detail/:id",
                    meta: {
                        url: "/panel/purchase/faktur/detail/:id",
                        keepAlive: false,
                        title: "Detail Faktur Pembelian",
                        parent_menu: "purchase_list",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "purchase_list",
                            title: "Faktur Pembelian",
                            icon: "fe fe-shopping-bag",
                        },
                    },
                    component: () => import("../../pages/purchase/detail.vue"),
                },
            ],
        },
        {
            path: "payment",
            redirect: "/panel/purchase/payment/list",
            component: () => import("../../pages/purchase/faktur"),
            children: [
                {
                    name: "purchase_payment_list",
                    path: "list",
                    meta: {
                        url: "/panel/purchase/payment/list",
                        keepAlive: false,
                        title: "Pembayaran Pembelian",
                        parent_menu: "purchase_payment_list",
                        icon: "fe fe-list",
                        parent: {
                            name: "purchase_payment_list",
                            title: "Pembayaran Pembelian",
                            icon: "fa fa-money",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/faktur/list.vue"),
                },
                {
                    name: "purchase_payment_create",
                    path: "create",
                    meta: {
                        url: "/panel/purchase/payment/create",
                        keepAlive: true,
                        closeTab: true,
                        title: "Buat Pembayaran Pembelian",
                        parent_menu: "purchase_payment_list",
                        icon: "fe fe-plus-circle",
                        parent: {
                            name: "purchase_payment_list",
                            title: "Pembayaran Pembelian",
                            icon: "fa fa-money",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/faktur/create.vue"),
                },
                {
                    name: "purchase_payment_update",
                    path: "update/:id",
                    meta: {
                        url: "/panel/purchase/payment/update/:id",
                        keepAlive: false,
                        closeTab: true,
                        title: "Edit Pembayaran Pembelian",
                        parent_menu: "purchase_payment_list",
                        icon: "fe fe-pencil",
                        parent: {
                            name: "purchase_payment_list",
                            title: "Pembayaran Pembelian",
                            icon: "fa fa-money",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/faktur/update.vue"),
                },
                {
                    name: "purchase_payment_detail",
                    path: "detail/:id",
                    meta: {
                        url: "/panel/purchase/payment/detail/:id",
                        keepAlive: false,
                        title: "Detail Pembayaran Pembelian",
                        parent_menu: "purchase_payment_list",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "purchase_payment_list",
                            title: "Pembayaran Pembelian",
                            icon: "fa fa-money",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/faktur/detail.vue"),
                },
            ],
        },
        {
            path: "return",
            redirect: "/panel/purchase/return/list",
            component: () => import("../../pages/purchase/return"),
            children: [
                {
                    name: "purchase_return_list",
                    path: "list",
                    meta: {
                        url: "/panel/purchase/return/list",
                        keepAlive: false,
                        title: "Retur Pembelian",
                        parent_menu: "purchase_return_list",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "purchase_return_list",
                            title: "Retur Pembelian",
                            icon: "fa fa-money",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/return/list.vue"),
                },
                {
                    name: "purchase_return_create",
                    path: "create/:id",
                    meta: {
                        url: "/panel/purchase/return/create",
                        keepAlive: false,
                        closeTab: true,
                        title: "Retur Pembelian",
                        parent_menu: "purchase_return_list",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "purchase_return_list",
                            title: "Retur Pembelian",
                            icon: "fa fa-money",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/return/create.vue"),
                },
                {
                    name: "purchase_return_update",
                    path: "update/:id",
                    meta: {
                        url: "/panel/purchase/return/update/:id",
                        keepAlive: false,
                        closeTab: true,
                        title: "Buat Retur Pembelian",
                        parent_menu: "purchase_return_list",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "purchase_return_list",
                            title: "Retur Pembelian",
                            icon: "fa fa-money",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/return/update.vue"),
                },
                {
                    name: "purchase_return_detail",
                    path: "detail/:id",
                    meta: {
                        url: "/panel/purchase/return/detail/:id",
                        keepAlive: false,
                        title: "Detail Retur Pembelian",
                        parent_menu: "purchase_return_list",
                        icon: "fe fe-file-text",
                        parent: {
                            name: "purchase_return_list",
                            title: "Retur Pembelian",
                            icon: "fa fa-money",
                        },
                    },
                    component: () =>
                        import("../../pages/purchase/return/detail.vue"),
                },
            ],
        },
        {
            path: "supplier",
            redirect: "/panel/purchase/supplier/list",
            component: () => import("../../pages/crm/suppliers"),
            children: [
                {
                    name: "supplier",
                    path: "list",
                    meta: {
                        url: "/panel/purchase/supplier/list",
                        title: "Pemasok",
                        parent_menu: "supplier",
                        icon: "fa fa-users",
                        parent: {
                            name: "supplier",
                            title: "Pemasok",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/suppliers/list.vue"),
                },
                {
                    name: "supplier_create",
                    path: "create",
                    meta: {
                        url: "/panel/purchase/supplier/create",
                        closeTab: true,
                        title: "Tambah Pemasok",
                        parent_menu: "supplier",
                        icon: "fa fa-users",
                        parent: {
                            name: "supplier",
                            title: "Pemasok",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/suppliers/create.vue"),
                },
                {
                    name: "supplier_update",
                    path: "update/:id",
                    meta: {
                        url: "/panel/purchase/supplier/update/:id",
                        closeTab: true,
                        title: "Edit Pemasok",
                        parent_menu: "supplier",
                        icon: "fa fa-users",
                        parent: {
                            name: "supplier",
                            title: "Pemasok",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/suppliers/update.vue"),
                },
                {
                    name: "supplier_due",
                    path: "supplier-due/:id",
                    meta: {
                        url: "/panel/purchase/supplier/purchase/supplier-due/:id",
                        title: "Hutang Pemasok",
                        parent_menu: "supplier",
                        icon: "fa fa-users",
                        parent: {
                            name: "supplier",
                            title: "Pemasok",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/suppliers/due.vue"),
                },
                {
                    name: "supplier_saldo",
                    path: "supplier-saldo/:id",
                    meta: {
                        url: "/panel/purchase/supplier/purchase/supplier-saldo/:id",
                        title: "Saldo Pemasok",
                        parent_menu: "supplier",
                        icon: "fa fa-users",
                        parent: {
                            name: "supplier",
                            title: "Pemasok",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/suppliers/saldo.vue"),
                },
                {
                    name: "supplier_transaction",
                    path: "supplier-history/:id",
                    meta: {
                        url: "/panel/purchase/supplier/purchase/supplier-history/:id",
                        title: "Transaksi Pemasok",
                        parent_menu: "supplier",
                        icon: "fa fa-users",
                        parent: {
                            name: "supplier",
                            title: "Pemasok",
                            icon: "fe fe-user",
                        },
                    },
                    component: () =>
                        import("../../pages/crm/suppliers/history.vue"),
                },
            ],
        },
    ],
};

const purchaseRoutes = [purchase];

export default purchaseRoutes;
