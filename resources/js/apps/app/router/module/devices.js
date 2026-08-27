const devices = {
    path: "/panel/preferensi/device",
    redirect: "/panel/preferensi/device/whatsapp",
    component: () => import("../../pages/devices"),
    children: [
        {
            name: "whatsapp_device",
            path: "whatsapp",
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
            component: () => import("../../pages/devices/list.vue"),
        },
       
        {
            path: "templates", 
            children: [
                {
                    name: "template_list",
                    path: "list",
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
                    component: () => import("../../pages/devices/templates/index.vue"),
                },
                {
                    name: "template_create",
                    path: "create",
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
                    component: () => import("../../pages/devices/templates/create.vue"),
                },
                {
                    name: "template_update",
                    path: "update/:id/:name",
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
                    component: () => import("../../pages/devices/templates/update.vue"),
                },
            ],
        },
    ],
};

const deviceRoutes = [devices];

export default deviceRoutes;
