const cashBank = {
    path: "/panel/cash-bank",
    redirect: "/panel/cash-bank/payment-method",
    component: () => import("../../pages/cashbank"),
    children: [
        {
            name: "payment_method",
            path: "payment-method",
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
            component: () => import("../../pages/master/payment_method.vue"),
        },
        {
            name: "detail_payment_method",
            path: "history-saldo/:id",
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
            component: () => import("../../pages/master/history_payment.vue"),
        },
        {
            name: "smartlink",
            path: "smart-link",
            component: () => import("../../pages/master/smartlink.vue"),
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
            component: () =>
                import("../../pages/cash-int-out/expense/list.vue"),
        },
        {
            name: "create_expense",
            path: "create-expense",
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
            component: () =>
                import("../../pages/cash-int-out/expense/create.vue"),
        },
        {
            name: "update_expense",
            path: "update-expense/:id",
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
            component: () =>
                import("../../pages/cash-int-out/expense/update.vue"),
        },

        // Cash Int
        {
            name: "cash_int",
            path: "cashint",
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
            component: () =>
                import("../../pages/cash-int-out/cashint/list.vue"),
        },
        {
            name: "create_cash_int",
            path: "create-cashint",
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
            component: () =>
                import("../../pages/cash-int-out/cashint/create.vue"),
        },
        {
            name: "update_cash_int",
            path: "update-cashint/:id",
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
            component: () =>
                import("../../pages/cash-int-out/cashint/update.vue"),
        },

        // Categories
        {
            name: "expense_category",
            path: "categories",
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
            component: () => import("../../pages/cash-int-out/category.vue"),
        },

        {
            name: "bank_mutation",
            path: "mutasi-bank",
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
            component: () =>
                import("../../pages/reports/commission/mutation.vue"),
        },

        {
            name: "rekonsiliasi",
            path: "rekonsiliasi",
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
            component: () => import("../../pages/accounting/rekonsiliasi.vue"),
        },
    ],
};

const cashBankRoutes = [cashBank];

export default cashBankRoutes;
