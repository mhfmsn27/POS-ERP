const reports = {
    path: "/panel/reports",
    redirect: "/panel/reports/list",
    component: () => import("../../pages/reports"),
    children: [
        {
            name: "report_list",
            path: "list",
            meta: {
                title: "Daftar Laporan",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/list",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/reports/list.vue"),
        },
        {
            name: "commission_reports",
            path: "commission",
            meta: {
                title: "Laporan Komisi",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/commission",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/reports/commission/list.vue"),
        },
        {
            name: "commission_detail",
            path: "commission-detail/:id",
            meta: {
                title: "Detail Komisi",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/commission-detail/:id",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () =>
                import("../../pages/reports/commission/detail.vue"),
        },

        {
            name: "piutang_customer",
            path: "piutang-customer",
            meta: {
                title: "Laporan Piutang Pelanggan",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/piutang-customer",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () =>
                import("../../pages/reports/crm/customers/piutang.vue"),
        },
        {
            name: "saldo_customer",
            path: "saldo-customer",
            meta: {
                title: "Laporan Saldo Pelanggan",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/saldo-customer",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () =>
                import("../../pages/reports/crm/customers/saldo.vue"),
        },

        {
            name: "hutang_supplier",
            path: "hutang-supplier",
            meta: {
                title: "Laporan Hutang Pemasok",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/hutang-supplier",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () =>
                import("../../pages/reports/crm/suppliers/hutang.vue"),
        },
        {
            name: "saldo_supplier",
            path: "saldo-supplier",
            meta: {
                title: "Laporan Saldo Pemasok",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/saldo-supplier",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () =>
                import("../../pages/reports/crm/suppliers/saldo.vue"),
        },
        {
            name: "profit",
            path: "profit",
            meta: {
                title: "Laporan Laba Rugi Standart",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/profit",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/reports/profit/standart.vue"),
        },
        {
            name: "profit_priode",
            path: "profit-priode",
            meta: {
                title: "Laporan Laba Rugi Per Priode",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/profit-priode",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/reports/profit/priode.vue"),
        },
        {
            name: "neraca",
            path: "neraca",
            meta: {
                title: "Laporan Neraca",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/neraca",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/reports/neraca/standart.vue"),
        },
        {
            name: "report_products",
            path: "products",
            meta: {
                title: "Laporan Produk",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/products",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/reports/products/list.vue"),
        },
        {
            name: "product_minus",
            path: "product-minus",
            meta: {
                title: "Laporan Produk Minus",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/product-minus",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/reports/products/minus.vue"),
        },
        {
            name: "taxrate_purchase",
            path: "tax-purchase",
            meta: {
                title: "Pajak Masukan",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/tax-purchase",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/taxrates/list.vue"),
        },
        {
            name: "taxrate_r_purchase",
            path: "tax-return-purchase",
            meta: {
                title: "Retur Pajak Masukan",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/tax-return-purchase",

                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/taxrates/r_purchase.vue"),
        },
        {
            name: "taxrate_sales",
            path: "tax-sales",
            meta: {
                title: "Pajak Keluaran",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/tax-sales",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/taxrates/sales.vue"),
        },
        {
            name: "taxrate_r_sales",
            path: "tax-return-sales",
            meta: {
                title: "Retur Pajak Keluaran",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/tax-return-sales",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/taxrates/r_sales.vue"),
        },
        {
            name: "spt",
            path: "spt",
            meta: {
                title: "SPT Pajak",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/spt",
                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/taxrates/spt.vue"),
        },
        {
            name: "create_spt",
            path: "create-spt",
            meta: {
                title: "Buat SPT",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/create-spt",

                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/taxrates/create_spt.vue"),
        },
        {
            name: "detail_spt",
            path: "detail-spt/:id",
            meta: {
                title: "Detail SPT",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/detail-spt/:id",

                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/taxrates/detail_spt.vue"),
        },
        {
            name: "activities",
            path: "activity",
            meta: {
                title: "Laporan Aktivitas",
                parent_menu: "report_list",
                icon: "fe fe-list",
                url: "/panel/reports/activity",

                parent: {
                    name: "report_list",
                    title: "Laporan",
                    icon: "fe fe-list",
                },
            },
            component: () => import("../../pages/reports/activity.vue"),
        },
    ],
};

const reportsRoute = [reports];

export default reportsRoute;
