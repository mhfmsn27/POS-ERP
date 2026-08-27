const cashBank = {
    path: "/app/cash-bank",
    redirect: "/app/cash-bank/payment-method", 
    children: [
        {
            name: "payment_method",
            path: "payment-method",
            component: () => import("../../index.vue"),
            meta: {
                title: "Metode Pembayaran",
                parent_menu: "payment_method",
                icon: "fe fe-folder",
                url: '/panel/cash-bank/payment-method',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },
        {
            name: "detail_payment_method",
            path: "history-saldo/:id",
            component: () => import("../../index.vue"),
            meta: {
                title: "Detail Metode Pembayaran",
                parent_menu: "payment_method",
                icon: "fe fe-folder",
                url: '/panel/cash-bank/history-saldo/:id',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },
        {
            name: "smartlink",
            path: "smart-link",
            component: () => import("../../index.vue"),
            meta: {
                title: "SmartLink e-Banking",
                parent_menu: "payment_method",
                icon: "fe fe-folder",
                url: '/panel/cash-bank/smart-link',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },
        // Cash Out
        {
            name: "expense",
            path: "expense",
            component: () => import("../../index.vue"),
            meta: {
                title: "Pembayaran",
                parent_menu: "payment_method",
                icon: "fa fa-money",
                url: '/panel/cash-bank/expense',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },
        {
            name: "create_expense",
            path: "create-expense",
            component: () => import("../../index.vue"),
            meta: {
                closeTab: true,
                title: "Pembayaran",
                parent_menu: "payment_method",
                icon: "fa fa-money",
                url: '/panel/cash-bank/create-expense',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },
        {
            name: "update_expense",
            path: "update-expense/:id",
            component: () => import("../../index.vue"),
            meta: {
                closeTab: true,
                title: "Pembayaran",
                parent_menu: "payment_method",
                icon: "fa fa-money",
                url: '/panel/cash-bank/update-expense/:id',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },

        // Cash Int
        {
            name: "cash_int",
            path: "cashint",
            component: () => import("../../index.vue"),
            meta: {
                title: "Penerimaan",
                parent_menu: "payment_method",
                icon: "fa fa-credit-card",
                url: '/panel/cash-bank/cashint',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },
        {
            name: "create_cash_int",
            path: "create-cashint",
            component: () => import("../../index.vue"),
            meta: {
                closeTab: true,
                title: "Penerimaan",
                parent_menu: "payment_method",
                icon: "fa fa-credit-card",
                url: '/panel/cash-bank/create-cashint',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },
        {
            name: "update_cash_int",
            path: "update-cashint/:id",
            component: () => import("../../index.vue"),
            meta: {
                closeTab: true,
                title: "Penerimaan",
                parent_menu: "payment_method",
                icon: "fa fa-credit-card",
                url: '/panel/cash-bank/update-cashint/:id',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },

        // Categories
        {
            name: "expense_category",
            path: "categories",
            component: () => import("../../index.vue"),
            meta: {
                title: "Kategori",
                parent_menu: "payment_method",
                icon: "fa fa-credit-card",
                url: '/panel/cash-bank/categories',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },

        {
            name: "bank_mutation",
            path: "mutasi-bank",
            component: () => import("../../index.vue"),
            meta: {
                title: "Mutasi Bank",
                parent_menu: "payment_method",
                icon: "fe fe-folder",
                url: '/panel/cash-bank/mutasi-bank',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },

        {
            name: "rekonsiliasi",
            path: "rekonsiliasi",
            component: () => import("../../index.vue"),
            meta: {
                title: "Rekonsiliasi Bank",
                parent_menu: "payment_method",
                icon: "fe fe-folder",
                url: '/panel/cash-bank/rekonsiliasi',
                parent: {
                    name: "payment_method",
                    title: "Kas dan Bank",
                    icon: "fa fa-money",
                },
            },
        },
    ],
};

const cashBankRoutes = [cashBank];

export default cashBankRoutes;
