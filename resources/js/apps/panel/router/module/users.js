const users = {
    path: "/app/preferensi/users",
    redirect: "/app/preferensi/users/groups", 
    children: [
        {
            name: "group",
            path: "groups",
            component: () => import("../../index.vue"),
            meta: {
                title: "Group Pengguna",
                parent_menu: "group",
                icon: "fe fe-shield",
                url: "/panel/preferensi/users/groups",
                parent: {
                    name: "group",
                    title: "Group Pengguna",
                    icon: "fe fe-shield",
                },
            }, 
        },
        {
            name: "permissions",
            path: "permissions/:id/:name",
            component: () => import("../../index.vue"),
            meta: {
                title: "Permission",
                parent_menu: "group",
                icon: "fe fe-shield",
                url: "/panel/preferensi/users/permissions/:id/:name",
                parent: {
                    name: "group",
                    title: "Group Pengguna",
                    icon: "fe fe-shield",
                },
            }, 
        },
        {
            path: "users",
            children: [
                {
                    name: "user_list",
                    path: "users",
                    component: () => import("../../index.vue"),
                    meta: {
                        title: "Pengguna",
                        parent_menu: "user_list",
                        icon: "fe fe-user",
                        url: "/panel/preferensi/users/users/users",
                        parent: {
                            name: "user_list",
                            title: "Pengguna",
                            icon: "fe fe-user",
                        },
                    }, 
                },
                {
                    name: "user_create",
                    path: "create",
                    component: () => import("../../index.vue"),
                    meta: {
                        url: "/panel/preferensi/users/users/create",
                        closeTab: true,
                        title: "Tambah Pengguna",
                        parent_menu: "user_list",
                        icon: "fe fe-user",
                        parent: {
                            name: "user_list",
                            title: "Pengguna",
                            icon: "fe fe-user",
                        },
                    }, 
                },
                {
                    name: "user_update",
                    path: "update/:id/:name",
                    component: () => import("../../index.vue"),
                    meta: {
                        closeTab: true,
                        url: "/panel/preferensi/users/users/update/:id",
                        title: "Edit Pengguna",
                        parent_menu: "user_list",
                        icon: "fe fe-user",
                        parent: {
                            name: "user_list",
                            title: "Pengguna",
                            icon: "fe fe-user",
                        },
                    }, 
                },
            ],
        },
    ],
};

const userRoutes = [users];

export default userRoutes;
