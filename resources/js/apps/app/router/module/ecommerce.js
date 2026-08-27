const ecommerce = {
    path: "/panel/e-commerce",
    children: [
        {
            path: "media-content",
            redirect: "/panel/e-commerce/media-content/blank",
            component: () => import("../../pages/ecommerce/media"),
            children: [
                {
                    name: "e_commerce_blank",
                    path: "blank",
                    meta: {
                        title: "Media Konten E-Commerce",
                        parent_menu: "e_commerce_blank",
                        icon: "fe fe-layout",
                        url: "/panel/e-commerce/media-content/blank",
                        parent: {
                            name: "e_commerce_blank",
                            title: "Media Konten E-Commerce",
                            icon: "fe fe-layout",
                        },
                    },
                    component: () =>
                        import("../../pages/ecommerce/media/blank.vue"),
                },
                {
                    path: "slider",
                    redirect: "/panel/e-commerce/media-content/slider/list",
                    children: [
                        {
                            name: "slider_ecommerce",
                            path: "list",
                            meta: {
                                title: "Slider Website",
                                url: "/panel/e-commerce/media-content/slider/list",
                                parent_menu: "slider_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "slider_ecommerce",
                                    title: "Slider Website",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/media/slider/list.vue"
                                ),
                        },
                        {
                            name: "create_slider_ecommerce",
                            path: "create",
                            meta: {
                                title: "Tambah Slider",
                                url: "/panel/e-commerce/media-content/slider/create",
                                parent_menu: "slider_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "slider_ecommerce",
                                    title: "Slider Website",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/media/slider/create.vue"
                                ),
                        },
                        {
                            name: "update_slider_ecommerce",
                            path: "update/:id",
                            meta: {
                                url: "/panel/e-commerce/media-content/slider/update/:id",
                                title: "Edit Slider",
                                parent_menu: "slider_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "slider_ecommerce",
                                    title: "Daftar Slider",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/media/slider/update.vue"
                                ),
                        },
                    ],
                },
                {
                    path: "banner",
                    redirect: "/panel/e-commerce/media-content/banner/list",
                    children: [
                        {
                            name: "banner_ecommerce",
                            path: "list",
                            meta: {
                                url: "/panel/e-commerce/media-content/banner/list",
                                title: "banner Website",
                                parent_menu: "banner_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "banner_ecommerce",
                                    title: "banner Website",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/media/banner/list.vue"
                                ),
                        },
                        {
                            name: "create_banner_ecommerce",
                            path: "create",
                            meta: {
                                url: "/panel/e-commerce/media-content/banner/create",
                                title: "Tambah banner",
                                parent_menu: "banner_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "banner_ecommerce",
                                    title: "banner Website",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/media/banner/create.vue"
                                ),
                        },
                        {
                            name: "update_banner_ecommerce",
                            path: "update/:id",
                            meta: {
                                url: "/panel/e-commerce/media-content/banner/update/:id",
                                title: "Edit banner",
                                parent_menu: "banner_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "banner_ecommerce",
                                    title: "Daftar banner",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/media/banner/update.vue"
                                ),
                        },
                    ],
                },
                {
                    path: "featured",
                    redirect: "/panel/e-commerce/media-content/featured/list",
                    children: [
                        {
                            name: "featured_ecommerce",
                            path: "list",
                            meta: {
                                url: "/panel/e-commerce/media-content/featured/list",
                                title: "Featured Website",
                                parent_menu: "featured_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "featured_ecommerce",
                                    title: "Featured Icon Website",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/media/featured/list.vue"
                                ),
                        },
                        {
                            name: "create_featured_ecommerce",
                            path: "create",
                            meta: {
                                url: "/panel/e-commerce/media-content/featured/create",
                                title: "Tambah featured",
                                parent_menu: "featured_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "featured_ecommerce",
                                    title: "Featured Icon Website",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/media/featured/create.vue"
                                ),
                        },
                        {
                            name: "update_featured_ecommerce",
                            path: "update/:id",
                            meta: {
                                url: "/panel/e-commerce/media-content/featured/update/:id",
                                title: "Edit featured Icon",
                                parent_menu: "featured_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "featured_ecommerce",
                                    title: "Featured Icon Website",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/media/featured/update.vue"
                                ),
                        },
                    ],
                },
                {
                    name: "ecommerce_category",
                    path: "category",
                    meta: {
                        title: "E-Commerce Category",
                        parent_menu: "e_commerce_blank",
                        icon: "fe fe-grid",
                        url: "/panel/e-commerce/media-content/category",
                        parent: {
                            name: "e_commerce_blank",
                            title: "Media Konten E-Commerce",
                            icon: "fe fe-grid",
                        },
                    },
                    component: () =>
                        import("../../pages/ecommerce/media/categories.vue"),
                },
            ],
        },
        {
            path: "blog-content",
            redirect: "/panel/e-commerce/blog-content/blank",
            component: () => import("../../pages/ecommerce/content"),
            children: [
                {
                    name: "e_commerce_blank_content",
                    path: "blank",
                    meta: {
                        url: "/panel/e-commerce/blog-content/blank",
                        title: "Blog dan Page",
                        parent_menu: "e_commerce_blank_content",
                        icon: "fe fe-file",
                        parent: {
                            name: "e_commerce_blank_content",
                            title: "Blog dan Page",
                            icon: "fe fe-file",
                        },
                    },
                    component: () =>
                        import("../../pages/ecommerce/content/blank.vue"),
                },
                {
                    path: "category",
                    redirect: "/panel/e-commerce/blog-content/category/list",
                    children: [
                        {
                            name: "category_blog_ecommerce",
                            path: "list",
                            meta: {
                                url: "/panel/e-commerce/blog-content/category/list",
                                title: "category Website",
                                parent_menu: "category_blog_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "category_blog_ecommerce",
                                    title: "category Website",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/content/category/list.vue"
                                ),
                        },
                        {
                            name: "create_category_blog_ecommerce",
                            path: "create",
                            meta: {
                                url: "/panel/e-commerce/blog-content/category/create",
                                title: "Tambah category",
                                parent_menu: "category_blog_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "category_blog_ecommerce",
                                    title: "category Website",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/content/category/create.vue"
                                ),
                        },
                        {
                            name: "update_category_blog_ecommerce",
                            path: "update/:id",
                            meta: {
                                url: "/panel/e-commerce/blog-content/category/update/:id",
                                title: "Edit category",
                                parent_menu: "category_blog_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "category_blog_ecommerce",
                                    title: "Daftar category",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/content/category/update.vue"
                                ),
                        },
                    ],
                },
                {
                    path: "blog",
                    redirect: "/panel/e-commerce/blog-content/blog/list",
                    children: [
                        {
                            name: "blog_ecommerce",
                            path: "list",
                            meta: {
                                url: "/panel/e-commerce/blog-content/blog/list",
                                title: "blog Website",
                                parent_menu: "blog_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "blog_ecommerce",
                                    title: "blog Website",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/content/blog/list.vue"
                                ),
                        },
                        {
                            name: "create_blog_ecommerce",
                            path: "create",
                            meta: {
                                url: "/panel/e-commerce/blog-content/blog/create",
                                title: "Tambah blog",
                                parent_menu: "blog_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "blog_ecommerce",
                                    title: "blog Website",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/content/blog/create.vue"
                                ),
                        },
                        {
                            name: "update_blog_ecommerce",
                            path: "update/:id",
                            meta: {
                                url: "/panel/e-commerce/blog-content/blog/update/:id",
                                title: "Edit blog",
                                parent_menu: "blog_ecommerce",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "blog_ecommerce",
                                    title: "Daftar blog",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/content/blog/update.vue"
                                ),
                        },
                    ],
                },
                {
                    name: "e_commerce_about",
                    path: "about-us",
                    meta: {
                        url: "/panel/e-commerce/blog-content/about-us",
                        title: "About Us",
                        parent_menu: "e_commerce_blank_content",
                        icon: "fe fe-file",
                        parent: {
                            name: "e_commerce_blank_content",
                            title: "Blog dan Page",
                            icon: "fe fe-file",
                        },
                    },
                    component: () =>
                        import("../../pages/ecommerce/content/about.vue"),
                },
                {
                    name: "e_commerce_sosmed",
                    path: "social-media",
                    meta: {
                        url: "/panel/e-commerce/blog-content/social-media",
                        title: "Social Media",
                        parent_menu: "e_commerce_blank_content",
                        icon: "fe fe-file",
                        parent: {
                            name: "e_commerce_blank_content",
                            title: "Blog dan Page",
                            icon: "fe fe-file",
                        },
                    },
                    component: () =>
                        import("../../pages/ecommerce/content/sosmed.vue"),
                },
            ],
        },
        {
            path: "settings",
            redirect: "/panel/e-commerce/settings/general",
            children: [
                {
                    name: "e_commerce_setting",
                    path: "general",
                    meta: {
                        title: "Pengaturan General",
                        parent_menu: "e_commerce_setting",
                        icon: "fe fe-settings",
                        url: "/panel/e-commerce/settings/general",
                        parent: {
                            name: "e_commerce_setting",
                            title: "Pengaturan Website",
                            icon: "fe fe-settings",
                        },
                    },
                    component: () =>
                        import("../../pages/ecommerce/setting/setting.vue"),
                },
            ],
        },
        {
            path: "transaction",
            redirect: "/panel/e-commerce/transaction/blank",
            component: () => import("../../pages/ecommerce/transaction"),
            children: [
                {
                    name: "e_commerce_transaction_blank",
                    path: "blank",
                    meta: {
                        url: "/panel/e-commerce/settings/transaction/blank",
                        title: "Transaksi E-Commerce",
                        parent_menu: "e_commerce_transaction_blank",
                        icon: "fe fe-shopping-cart",
                        parent: {
                            name: "e_commerce_transaction_blank",
                            title: "Transaksi E-Commerce",
                            icon: "fe fe-shopping-cart",
                        },
                    },
                    component: () =>
                        import("../../pages/ecommerce/media/blank.vue"),
                },
                {
                    path: "order",
                    redirect: "/panel/e-commerce/transaction/order/pending",
                    children: [
                        {
                            name: "e_commerce_order_pending",
                            path: "pending",
                            meta: {
                                title: "Pending Transaksi",
                                url: "/panel/e-commerce/settings/transaction/order/pending",
                                parent_menu: "e_commerce_transaction_blank",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "e_commerce_transaction_blank",
                                    title: "Transaksi E-Commerce",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/transaction/list.vue"
                                ),
                        },
                        {
                            name: "e_commerce_order_process",
                            path: "process",
                            meta: {
                                title: "Dalam Pengemasan",
                                url: "/panel/e-commerce/settings/transaction/order/process",
                                parent_menu: "e_commerce_transaction_blank",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "e_commerce_transaction_blank",
                                    title: "Transaksi E-Commerce",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/transaction/process.vue"
                                ),
                        },
                        {
                            name: "e_commerce_order_shipping",
                            path: "shipping",
                            meta: {
                                url: "/panel/e-commerce/settings/transaction/order/shipping",
                                title: "Dalam Pengiriman",
                                parent_menu: "e_commerce_transaction_blank",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "e_commerce_transaction_blank",
                                    title: "Transaksi E-Commerce",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/transaction/shipping.vue"
                                ),
                        },
                        {
                            name: "e_commerce_order_received",
                            path: "received",
                            meta: {
                                url: "/panel/e-commerce/settings/transaction/order/received",
                                title: "Pesanan di Terima",
                                parent_menu: "e_commerce_transaction_blank",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "e_commerce_transaction_blank",
                                    title: "Transaksi E-Commerce",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/transaction/complete.vue"
                                ),
                        },
                        {
                            name: "e_commerce_order_detail",
                            path: "detail/:id",
                            meta: {
                                url: "/panel/e-commerce/settings/transaction/order/detail/:id",
                                title: "Detail Transaksi",
                                parent_menu: "e_commerce_transaction_blank",
                                icon: "fe fe-layout",
                                parent: {
                                    name: "e_commerce_transaction_blank",
                                    title: "Transaksi E-Commerce",
                                    icon: "fe fe-layout",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/ecommerce/transaction/detail.vue"
                                ),
                        },
                    ],
                },
            ],
        },
        // {
        //     name: "whatsapp_device",
        //     path: "whatsapp",
        //     meta: {
        //         title: "Whatsapp Device",
        //         parent_menu: "group",
        //         icon: "fe fe-phone",
        //         parent: {
        //             name: "group",
        //             title: "Device",
        //             icon: "fe fe-phone",
        //         },
        //     },
        //     component: () => import("../../pages/devices/list.vue"),
        // },

        // {
        //     path: "templates",
        //     children: [
        //         {
        //             name: "template_list",
        //             path: "list",
        //             meta: {
        //                 title: "Template Notifikasi",
        //                 parent_menu: "whatsapp_device",
        //                 icon: "fe fe-layout",
        //                 parent: {
        //                     name: "whatsapp_device",
        //                     title: "Whatsapp Device",
        //                     icon: "fe fe-phone",
        //                 },
        //             },
        //             component: () => import("../../pages/devices/templates/index.vue"),
        //         },
        //         {
        //             name: "template_create",
        //             path: "create",
        //             meta: {
        //                 title: "Tambah Template",
        //                 parent_menu: "whatsapp_device",
        //                 icon: "fe fe-layout",
        //                 parent: {
        //                     name: "whatsapp_device",
        //                     title: "Whatsapp Device",
        //                     icon: "fe fe-phone",
        //                 },
        //             },
        //             component: () => import("../../pages/devices/templates/create.vue"),
        //         },
        //         {
        //             name: "template_update",
        //             path: "update/:id/:name",
        //             meta: {
        //                 title: "Edit Template",
        //                 parent_menu: "whatsapp_device",
        //                 icon: "fe fe-layout",
        //                 parent: {
        //                     name: "whatsapp_device",
        //                     title: "Whatsapp Device",
        //                     icon: "fe fe-phone",
        //                 },
        //             },
        //             component: () => import("../../pages/devices/templates/update.vue"),
        //         },
        //     ],
        // },
    ],
};

const ecommerceRoutes = [ecommerce];

export default ecommerceRoutes;
