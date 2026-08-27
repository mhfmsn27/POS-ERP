import { createRouter, createWebHistory } from "vue-router";
import NProgress from "nprogress";
import accountingRoutes from "./module/accounting";
import inventoryRoutes from "./module/inventory";
import purchaseRoutes from "./module/purchases";
import salesRoutes from "./module/sales";
import settingsRoutes from "./module/settings";
import hrmRoutes from "./module/hrm";
import cashBankRoutes from "./module/cash_bank";
import reportsRoute from "./module/reports";
import taxesRoute from "./module/taxes";
import jurnalRoutes from "./module/jurnal";
import rmaRoutes from "./module/rma";
import store from "../../../store"; // Sesuaikan path ke store Vuex Anda
import userRoutes from "./module/users";
import deviceRoutes from "./module/devices";
import companyRoutes from "./module/company";
import ecommerceRoutes from "./module/ecommerce";

// Router Module

const routes = [
    {
        path: "/app",
        redirect: "/app/home",
        children: [
            {
                name: "home",
                path: "home",
                meta: {
                    title: "Dashboard Pengguna",
                    parent_menu: "home",
                    icon: "fe fe-airplay",
                    url: "/panel/home",
                    parent: {
                        name: "home",
                        title: "Dashboard Pengguna",
                        icon: "fe fe-airplay",
                    },
                },
            },
            {
                name: "profile",
                path: "profile",
                meta: {
                    title: "Edit Profil",
                    parent_menu: "home",
                    icon: "fe fe-user",
                    url: "/panel/profile",
                    parent: {
                        name: "home",
                        title: "Dashboard Pengguna",
                        icon: "fe fe-user",
                    },
                },
            },

            // Preferensi
            ...settingsRoutes,

            // User Manager
            ...userRoutes,

            // Whatsapp Device
            ...deviceRoutes,

            // Company Routes
            ...companyRoutes,

            // Profile Router
            ...accountingRoutes,

            // Inventori Produk
            ...inventoryRoutes,

            // Purchase Transactions
            ...purchaseRoutes,

            // Sales Transaction
            ...salesRoutes,

            // Hrm Module
            ...hrmRoutes,

            // Cash and Bank Module
            ...cashBankRoutes,

            // Laporan
            ...reportsRoute,

            // Pajak
            ...taxesRoute,

            ...jurnalRoutes,

            ...rmaRoutes,

            // Module E-Commerce
            ...ecommerceRoutes,
        ],
    },
];

const router = createRouter({
    mode: "history",
    history: createWebHistory(),
    routes: routes,
    scrollBehavior(to, from, savedPosition) {
        return { left: 0, top: 0 };
    },
});

router.beforeEach((to, from, next) => {
    if (to.path) {
        var tabName = to.params?.name
            ? to.meta.title + " - " + to.params?.name
            : to.meta.title;
        store.dispatch("general/set_open_menu", {
            name: to.meta.parent.name,
            title: to.meta.parent.title,
            icon: to.meta.parent.icon,
            links: {
                name: to.name,
                url: to.meta.url ?? "",
                parent: to.meta.parent.name,
                title: tabName,
                icon: to.meta.icon,
                params: to.params ?? {},
            },
        });

        NProgress.start();
        NProgress.set(0.1);
    }
    next();
});

router.afterEach((to, from) => {
    setTimeout(() => NProgress.done(), 200);
});

export default router;
