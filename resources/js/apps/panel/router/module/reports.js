const reports = {
    path: "/app/reports",
    redirect: "/app/reports/list", 
    children: [
        {
            name: "report_list",
            path: "list",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "commission_reports",
            path: "commission",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "commission_detail",
            path: "commission-detail/:id",
            component: () => import("../../index.vue"),
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
        },

        {
            name: "piutang_customer",
            path: "piutang-customer",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "saldo_customer",
            path: "saldo-customer",
            component: () => import("../../index.vue"),
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
        },

        {
            name: "hutang_supplier",
            path: "hutang-supplier",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "saldo_supplier",
            path: "saldo-supplier",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "profit",
            path: "profit",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "profit_priode",
            path: "profit-priode",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "neraca",
            path: "neraca",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "report_products",
            path: "products",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "product_minus",
            path: "product-minus",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "taxrate_purchase",
            path: "tax-purchase",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "taxrate_r_purchase",
            path: "tax-return-purchase",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "taxrate_sales",
            path: "tax-sales",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "taxrate_r_sales",
            path: "tax-return-sales",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "spt",
            path: "spt",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "create_spt",
            path: "create-spt",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "detail_spt",
            path: "detail-spt/:id",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "activities",
            path: "activity",
            component: () => import("../../index.vue"),
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
        },
    ],
};

const reportsRoute = [reports];

export default reportsRoute;
