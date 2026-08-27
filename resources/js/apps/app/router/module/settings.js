const settings = {
    path: "/panel/preferensi/account-default",
    redirect: "/panel/preferensi/account-default/crm",
    component: () => import("../../pages/settings"),
    children: [
        {
            name: "setting_account_crm",
            path: "crm",
            meta: {
                url: "/panel/preferensi/account-default/crm",
                title: "Akun Crm",
                parent_menu: "setting_account_crm",
                icon: "fe fe-settings",
                parent: {
                    name: "setting_account_crm",
                    title: "Preferensi",
                    icon: "fe fe-settings",
                },
            },
            component: () => import("../../pages/settings/crm.vue"),
        },
        {
            name: "setting_account_product",
            path: "product",
            meta: {
                url: "/panel/preferensi/account-default/product",
                title: "Akun Produk",
                parent_menu: "setting_account_crm",
                icon: "fe fe-settings",
                parent: {
                    name: "setting_account_crm",
                    title: "Preferensi",
                    icon: "fe fe-settings",
                },
            },
            component: () => import("../../pages/settings/product.vue"),
        },
        {
            name: "setting_account_transaction",
            path: "transaction",
            meta: {
                url: "/panel/preferensi/account-default/transaction",
                title: "Akun Transaksi",
                parent_menu: "setting_account_crm",
                icon: "fe fe-settings",
                parent: {
                    name: "setting_account_crm",
                    title: "Preferensi",
                    icon: "fe fe-settings",
                },
            },
            component: () => import("../../pages/settings/transaction.vue"),
        },
        {
            name: "setting_account_taxrate",
            path: "tax",
            meta: {
                url: "/panel/preferensi/account-default/tax",
                title: "Akun Pajak",
                parent_menu: "setting_account_crm",
                icon: "fe fe-settings",
                parent: {
                    name: "setting_account_crm",
                    title: "Preferensi",
                    icon: "fe fe-settings",
                },
            },
            component: () => import("../../pages/settings/taxrate.vue"),
        },
        {
            name: "setting_key",
            path: "key",
            meta: {
                url: "/panel/preferensi/account-default/key",
                title: "Prefix Transaksi",
                parent_menu: "setting_account_crm",
                icon: "fe fe-settings",
                parent: {
                    name: "setting_account_crm",
                    title: "Preferensi",
                    icon: "fe fe-settings",
                },
            },
            component: () => import("../../pages/settings/key.vue"),
        },
        {
            name: "setting_hrm",
            path: "hrm",
            meta: {
                url: "/panel/preferensi/account-default/hrm",
                title: "Pengaturan Hrm",
                parent_menu: "setting_account_crm",
                icon: "fe fe-settings",
                parent: {
                    name: "setting_account_crm",
                    title: "Preferensi",
                    icon: "fe fe-settings",
                },
            },
            component: () => import("../../pages/settings/hrm.vue"),
        },
        {
            name: "setting_notification",
            path: "notification",
            meta: {
                url: "/panel/preferensi/account-default/notification",
                title: "Pengaturan Notifikasi",
                parent_menu: "setting_account_crm",
                icon: "fe fe-settings",
                parent: {
                    name: "setting_account_crm",
                    title: "Preferensi",
                    icon: "fe fe-settings",
                },
            },
            component: () => import("../../pages/settings/notification.vue"),
        },
        {
            name: "setting_store",
            path: "store",
            meta: {
                url: "/panel/preferensi/account-default/store",
                title: "Pengaturan Toko",
                parent_menu: "setting_account_crm",
                icon: "fe fe-settings",
                parent: {
                    name: "setting_account_crm",
                    title: "Preferensi",
                    icon: "fe fe-settings",
                },
            },
            component: () => import("../../pages/settings/store.vue"),
        },
    ],
};

const settingsRoutes = [settings];

export default settingsRoutes;
