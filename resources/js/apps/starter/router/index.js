/*
    |--------------------------------------------------------------------------
    | Authentication Router
    |--------------------------------------------------------------------------
    */

import { createRouter, createWebHistory } from "vue-router";
import { TokenService } from "@/services";
import NProgress from "nprogress";

const routes = [
    {
        path: "/starter",
        component: () => import("../pages/index.vue"),
        redirect: "/starter/choose-store",
        children: [
            {
                path: "choose-store",
                name: "choose_store",
                component: () => import("../pages/stores/list.vue"),
                meta: {
                    must_auth: false,
                    title: "Pilih Toko atau Cabang",
                },
            },
            {
                path: "create-store",
                name: "create_store",
                component: () => import("../pages/stores/create.vue"),
                meta: {
                    must_auth: false,
                    title: "Tambah Toko atau Cabang",
                },
            },
            {
                path: "packages",
                redirect: "packages/list",
                children: [
                    {
                        path: "list",
                        name: "packages",
                        component: () => import("../pages/packages/list.vue"),
                        meta: {
                            requiresAuth: true,
                            page_name: "Pilihan Layanan Berlangganan",
                        },
                    },
                    {
                        path: "detail/:id",
                        name: "package_detail",
                        component: () => import("../pages/packages/detail.vue"),
                        meta: {
                            requiresAuth: true,
                            page_name: "Buat Transaksi Berlangganan",
                        },
                    },
                ],
            },
            {
                path: "transactions",
                redirect: "transactions/list",
                children: [
                    {
                        path: "list",
                        name: "transactions",
                        component: () =>
                            import("../pages/transactions/list.vue"),
                        meta: {
                            requiresAuth: true,
                            page_name: "Transaksi Layanan Berlangganan",
                        },
                    },
                ],
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
    scrollBehavior(to, from, savedPosition) {
        return { left: 0, top: 0 };
    },
});

router.beforeEach((to, from, next) => {
    if (TokenService.getToken() == null) {
        return (window.location = "/authentication");
    }

    if (!TokenService.getVerify()) {
        return (window.location = "/authentication/verify");
    }

    if (!TokenService.getMerchant()) {
        return (window.location = "/authentication/business-register");
    }

    if (to.path) {
        NProgress.start();
        NProgress.set(0.1);
    }

    // Proceed with navigation
    next();
});

router.afterEach((to, from) => {
    setTimeout(() => NProgress.done(), 200);
});

export default router;
