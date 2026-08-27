const rma = {
    path: "/app/rma",
    redirect: "/app/rma/list", 
    children: [
        {
            name: "rma_list",
            path: "list",
            component: () => import("../../index.vue"),
            meta: {
                url: "/panel/rma/list",
                keepAlive: false,
                title: "Daftar Rma",
                parent_menu: "rma_list",
                icon: "fe fe-tool",
                parent: {
                    name: "rma_list",
                    title: "Rma",
                    icon: "fe fe-tool",
                },
            }, 
        },
        {
            name: "rma_create",
            path: "create",
            component: () => import("../../index.vue"),
            meta: {
                url: "/panel/rma/create",
                keepAlive: true,
                closeTab: true,
                title: "Tambah Rma",
                parent_menu: "rma_list",
                icon: "fe fe-tool",
                parent: {
                    name: "rma_list",
                    title: "Rma",
                    icon: "fe fe-tool",
                },
            }, 
        },
        {
            name: "rma_update",
            path: "update/:id",
            component: () => import("../../index.vue"),
            meta: {
                url: "/panel/rma/update/:id",
                keepAlive: true,
                closeTab: true,
                title: "Edit Rma",
                parent_menu: "rma_list",
                icon: "fe fe-tool",
                parent: {
                    name: "rma_list",
                    title: "Rma",
                    icon: "fe fe-tool",
                },
            }, 
        },
        {
            name: "rma_detail",
            path: "detail/:id",
            component: () => import("../../index.vue"),
            meta: {
                url: "/panel/rma/detail/:id",
                keepAlive: false,
                title: "Detail Rma",
                parent_menu: "rma_list",
                icon: "fe fe-tool",
                parent: {
                    name: "rma_list",
                    title: "Rma",
                    icon: "fe fe-tool",
                },
            }, 
        },
    ],
};

const rmaRoutes = [rma];

export default rmaRoutes;
