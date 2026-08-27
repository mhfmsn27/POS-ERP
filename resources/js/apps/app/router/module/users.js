const users = {
    path: "/panel/preferensi/users",
    redirect: "/panel/preferensi/users/groups",
    component: () => import("../../pages/users"),
    children: [
        {
            name: "group",
            path: "groups",
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
            component: () => import("../../pages/users/groups/index.vue"),
        },
        {
            name: "permissions",
            path: "permissions/:id/:name",
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
            component: () => import("../../pages/users/groups/permissions.vue"),
        },
        {
            path: "users",
            children: [
                {
                    name: "user_list",
                    path: "users",
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
                    component: () => import("../../pages/users/user/index.vue"),
                },
                {
                    name: "user_create",
                    path: "create",
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
                    component: () =>
                        import("../../pages/users/user/create.vue"),
                },
                {
                    name: "user_update",
                    path: "update/:id/:name",
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
                    component: () =>
                        import("../../pages/users/user/update.vue"),
                },
            ],
        },
    ],
};

const userRoutes = [users];

export default userRoutes;
