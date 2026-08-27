const accounting = {
    path: "/app/buku-besar/accounting",
    redirect: "/app/buku-besar/accounting/type", 
    children: [
        {
            name: "account_type",
            path: "type",
            component: () => import("../../index.vue"),
            meta: {
                title: "Tipe Akun",
                url: "/panel/buku-besar/accounting/type",
                parent_menu: "accountant",
                icon: "fe fe-grid",
                parent: {
                    name: "accountant",
                    title: "Buku Besar",
                    icon: "fe fe-book",
                },
            },
        },
        {
            name: "accountant",
            path: "account",
            component: () => import("../../index.vue"),
            meta: {
                title: "Akun Perkiraan",
                parent_menu: "accountant",
                icon: "fe fe-book",
                url: "/panel/buku-besar/accounting/account",
                parent: {
                    name: "accountant",
                    title: "Buku Besar",
                    icon: "fe fe-book",
                },
            },
        },
        {
            name: "account_history",
            path: "account-history/:id",
            component: () => import("../../index.vue"),
            meta: {
                title: "Riwayat",
                parent_menu: "accountant",
                icon: "fe fe-list",
                url: "/panel/buku-besar/accounting/account-history/:id",
                parent: {
                    name: "accountant",
                    title: "Buku Besar",
                    icon: "fe fe-book",
                },
            },
        },
    ],
};

const accountingRoutes = [accounting];

export default accountingRoutes;
