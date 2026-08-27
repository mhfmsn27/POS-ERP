const devices = {
    path: "/app/preferensi/device",
    redirect: "/app/preferensi/device/whatsapp", 
    children: [
        {
            name: "whatsapp_device",
            path: "whatsapp",
            component: () => import("../../index.vue"),
            meta: {
                title: "Whatsapp Device",
                url: '/panel/preferensi/device/whatsapp',
                parent_menu: "group",
                icon: "fe fe-phone",
                parent: {
                    name: "group",
                    title: "Device",
                    icon: "fe fe-phone",
                },
            },
        },
       
        {
            path: "templates", 
            children: [
                {
                    name: "template_list",
                    path: "list",
                    component: () => import("../../index.vue"),
                    meta: {
                        title: "Template Notifikasi",
                        parent_menu: "whatsapp_device",
                        icon: "fe fe-layout",
                        url: '/panel/preferensi/device/templates/list',
                        parent: {
                            name: "whatsapp_device",
                            title: "Whatsapp Device",
                            icon: "fe fe-phone",
                        },
                    },
                },
                {
                    name: "template_create",
                    path: "create",
                    component: () => import("../../index.vue"),
                    meta: {
                        title: "Tambah Template",
                        parent_menu: "whatsapp_device",
                        icon: "fe fe-layout",
                        url: '/panel/preferensi/device/templates/create',
                        parent: {
                            name: "whatsapp_device",
                            title: "Whatsapp Device",
                            icon: "fe fe-phone",
                        },
                    },
                },
                {
                    name: "template_update",
                    path: "update/:id/:name",
                    component: () => import("../../index.vue"),
                    meta: {
                        title: "Edit Template",
                        parent_menu: "whatsapp_device",
                        icon: "fe fe-layout",
                        url: '/panel/preferensi/device/templates/update/:id/:name',
                        parent: {
                            name: "whatsapp_device",
                            title: "Whatsapp Device",
                            icon: "fe fe-phone",
                        },
                    },
                },
            ],
        },
    ],
};

const deviceRoutes = [devices];

export default deviceRoutes;
