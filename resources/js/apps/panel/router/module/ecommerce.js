const ecommerce = {
    path: "/app/e-commerce",
    component: () => import("../../index.vue"),
    children: [
        {
            path: "media-content",
            redirect: "/app/e-commerce/media-content/blank",
            children: [
                {
                    name: "e_commerce_blank",
                    path: "blank",
                    component: () => import("../../index.vue"),
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
                },
                {
                    path: "slider",
                    redirect: "/app/e-commerce/media-content/slider/list",
                    children: [
                        {
                            name: "slider_ecommerce",
                            path: "list",
                            component: () => import("../../index.vue"),
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
                        },
                        {
                            name: "create_slider_ecommerce",
                            path: "create",
                            component: () => import("../../index.vue"),
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
                        },
                        {
                            name: "update_slider_ecommerce",
                            path: "update/:id",
                            component: () => import("../../index.vue"),
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
                        },
                    ],
                },
                {
                    path: "banner",
                    redirect: "/app/e-commerce/media-content/banner/list",
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
                        },
                    ],
                },
                {
                    path: "featured",
                    redirect: "/app/e-commerce/media-content/featured/list",
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
                },
            ],
        },
        {
            path: "blog-content",
            redirect: "/app/e-commerce/blog-content/blank", 
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
                },
                {
                    path: "category",
                    redirect: "/app/e-commerce/blog-content/category/list",
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
                        },
                    ],
                },
                {
                    path: "blog",
                    redirect: "/app/e-commerce/blog-content/blog/list",
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
                },
            ],
        },
        {
            path: "settings",
            redirect: "/app/e-commerce/settings/general",
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
                },
            ],
        },
        {
            path: "transaction",
            redirect: "/app/e-commerce/transaction/blank", 
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
                },
                {
                    path: "order",
                    redirect: "/app/e-commerce/transaction/order/pending",
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
                        },
                    ],
                },
            ],
        },
    ],
};

const ecommerceRoutes = [ecommerce];

export default ecommerceRoutes;
