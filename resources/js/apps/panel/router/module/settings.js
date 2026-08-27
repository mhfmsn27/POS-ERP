const settings = {
    path: "/app/preferensi/account-default",
    redirect: "/app/preferensi/account-default/crm", 
    children: [
        {
            name: "setting_account_crm",
            path: "crm",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "setting_account_product",
            path: "product",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "setting_account_transaction",
            path: "transaction",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "setting_account_taxrate",
            path: "tax",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "setting_key",
            path: "key",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "setting_hrm",
            path: "hrm",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "setting_notification",
            path: "notification",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "setting_store",
            path: "store",
            component: () => import("../../index.vue"),
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
        },
    ],
};

const settingsRoutes = [settings];

export default settingsRoutes;
