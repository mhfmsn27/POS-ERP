const accounting = {
    path: "/panel/buku-besar/accounting",
    redirect: "/panel/buku-besar/accounting/type",
    component: () => import("../../pages/accounting"),
    children: [
        {
            name: "account_type",
            path: "type",
            meta: {
                title: "Tipe Akun",
                url: "/panel/buku-besar/accounting/type",
                parent_menu: "accountant",
                icon: "fe fe-grid",
                parent: {
                    name: "jurnal",
                    title: "Buku Besar",
                    icon: "fe fe-book",
                },
            },
            component: () => import("../../pages/accounting/type/list.vue"),
        },
        {
            name: "accountant",
            path: "account",
            meta: {
                title: "Akun Perkiraan",
                parent_menu: "accountant",
                icon: "fe fe-book",
                url: "/panel/buku-besar/accounting/account",
                parent: {
                    name: "jurnal",
                    title: "Buku Besar",
                    icon: "fe fe-book",
                },
            },
            component: () => import("../../pages/accounting/list.vue"),
        },
        {
            name: "account_history",
            path: "account-history/:id",
            meta: {
                title: "Riwayat",
                parent_menu: "accountant",
                icon: "fe fe-list",
                url: "/panel/buku-besar/accounting/account-history/:id",
                parent: {
                    name: "jurnal",
                    title: "Buku Besar",
                    icon: "fe fe-book",
                },
            },
            component: () => import("../../pages/accounting/history.vue"),
        },
    ],
};

const accountingRoutes = [accounting];

export default accountingRoutes;
