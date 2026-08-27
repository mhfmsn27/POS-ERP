const rma = {
    path: "/panel/rma",
    redirect: "/panel/rma/list",
    component: () => import("../../pages/rma"),
    children: [
        {
            name: "rma_list",
            path: "list",
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
            component: () => import("../../pages/rma/list.vue"),
        },
        {
            name: "rma_create",
            path: "create",
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
            component: () => import("../../pages/rma/create.vue"),
        },
        {
            name: "rma_update",
            path: "update/:id",
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
            component: () => import("../../pages/rma/update.vue"),
        },
        {
            name: "rma_detail",
            path: "detail/:id",
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
            component: () => import("../../pages/rma/detail.vue"),
        },
    ],
};

const rmaRoutes = [rma];

export default rmaRoutes;
