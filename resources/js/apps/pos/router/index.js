import { createRouter, createWebHistory } from "vue-router";
import NProgress from "nprogress";
// Router Module

const routes = [
    {
        path: "/pos",
        redirect: "/pos/layer",
        component: () => import("../pages"),
        children: [
            {
                name: "pos_layer",
                path: "layer",
                meta: {
                    title: "POS",
                    parent: "",
                    route_parent: "pos_layer",
                },
                component: () => import("../pages/pos.vue"),
            },
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
        NProgress.start();
        NProgress.set(0.1);
    }
    next();
});

router.afterEach((to, from) => {
    setTimeout(() => NProgress.done(), 200);
});

export default router;
