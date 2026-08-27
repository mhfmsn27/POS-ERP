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
        path: "/authentication",
        component: () => import("../pages/index.vue"),
        redirect: "/authentication/login",
        children: [
            {
                path: "login",
                name: "login",
                component: () => import("../pages/basic/login.vue"),
                meta: {
                    must_auth: false,
                    title: "Login Akun",
                },
            },
            {
                path: "register",
                name: "register",
                component: () => import("../pages/basic/register.vue"),
                meta: {
                    must_auth: false,
                    title: "Registrasi Akun",
                },
            },
            {
                path: "forget-password",
                name: "forgetpass",
                component: () => import("../pages/password/index.vue"),
                meta: {
                    must_auth: false,
                    title: "Minta Reset Password",
                },
            },
            {
                path: "verify",
                name: "verify",
                component: () => import("../pages/verify.vue"),
                meta: {
                    must_auth: true,
                    title: "Verifikasi Alamat Email",
                },
            },
            {
                path: "business-register",
                name: "business_register",
                component: () => import("../pages/business_register.vue"),
                meta: {
                    must_auth: true,
                    must_verify: true,
                    title: "Daftarkan Bisnis Anda",
                },
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

    if (to.meta.must_auth) {
        if (TokenService.getToken() == null) {
            return next({ name: "login" });
        } 
 
        if(to.name == 'verify' && TokenService.getVerify()) {
            return next({ name: "business_register" });
        }
    } else {
 

        if(TokenService.getToken() != null) {

            if(!TokenService.getVerify()) {
                return next({ name: "verify" });
            }

            if(!TokenService.getMerchant()) {
                return next({ name: "business_register" });
            }

            
            return (window.location = "/app");   
        } 
    }

    if(to.meta.must_verify) {
        if(!TokenService.getVerify()) {
            return next({ name: "verify" });
        }

        if(TokenService.getMerchant()) {
            return (window.location = "/app");   
        }
    }
      
    // Start progress bar if the route path exists
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
