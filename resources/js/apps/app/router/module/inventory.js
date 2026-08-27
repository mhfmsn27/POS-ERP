const inventory = {
    path: "/panel/inventori",
    redirect: "/panel/inventori/products",
    children: [
        {
            path: "products",
            redirect: "/panel/inventori/products/products",
            component: () => import("../../pages/inventori/products"),
            children: [
                {
                    path: "products",
                    redirect: "/panel/inventori/products/products/list",
                    children: [
                        {
                            name: "products",
                            path: "list",
                            meta: {
                                url: "/panel/inventori/products/products/list",
                                title: "Daftar Produk",
                                parent_menu: "products",
                                icon: "fe fe-box",
                                parent: {
                                    name: "products",
                                    title: "Barang dan Jasa",
                                    icon: "fe fe-box",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/inventori/products/list.vue"
                                ),
                        },
                        {
                            name: "create_product",
                            path: "create-product",
                            meta: {
                                closeTab: true,
                                url: "/panel/inventori/products/products/create-product",
                                title: "Tambah Produk",
                                parent_menu: "products",
                                icon: "fe fe-box",
                                parent: {
                                    name: "products",
                                    title: "Barang dan Jasa",
                                    icon: "fe fe-box",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/inventori/products/create.vue"
                                ),
                        },
                        {
                            name: "update_product",
                            path: "update-product/:id",
                            meta: {
                                url: "/panel/inventori/products/products/update-product/:id",
                                title: "Edit Produk",
                                parent_menu: "products",
                                icon: "fe fe-box",
                                parent: {
                                    name: "products",
                                    title: "Barang dan Jasa",
                                    icon: "fe fe-box",
                                },
                            },
                            component: () =>
                                import(
                                    "../../pages/inventori/products/update/variation.vue"
                                ),
                        },
                        {
                            path: "product-detail",
                            component: () => import("../../pages/inventori/products/detail"),
                            children: [
                                {
                                    name: "product_details",
                                    path: "product-detail/:id",
                                    meta: {
                                        url: "/panel/inventori/products/products/product-detail/product-detail/:id",
                                        title: "Detail Produk",
                                        parent_menu: "products",
                                        icon: "fe fe-box",
                                        parent: {
                                            name: "products",
                                            title: "Barang dan Jasa",
                                            icon: "fe fe-box",
                                        },
                                    },
                                    component: () =>
                                        import(
                                            "../../pages/inventori/products/detail/detail.vue"
                                        ),
                                },
                                {
                                    name: "stock_histories_product",
                                    path: "histori-stock/:id",
                                    meta: {
                                        url: "/panel/inventori/products/products/product-detail/histori-stock/:id",
                                        title: "Riwayat Stok Produk",
                                        parent_menu: "products",
                                        icon: "fe fe-box",
                                        parent: {
                                            name: "products",
                                            title: "Barang dan Jasa",
                                            icon: "fe fe-box",
                                        },
                                    },
                                    component: () =>
                                        import(
                                            "../../pages/inventori/products/detail/histories.vue"
                                        ),
                                },
                                {
                                    name: "stock_product",
                                    path: "stok-product/:id",
                                    meta: {
                                        url: "/panel/inventori/products/products/product-detail/stok-product/:id",
                                        title: "Stok Produk",
                                        parent_menu: "products",
                                        icon: "fe fe-box",
                                        parent: {
                                            name: "products",
                                            title: "Barang dan Jasa",
                                            icon: "fe fe-box",
                                        },
                                    },
                                    component: () =>
                                        import(
                                            "../../pages/inventori/products/detail/stocks.vue"
                                        ),
                                },
                            ],
                        },
                    ],
                },
                {
                    name: "categories",
                    path: "categories",
                    meta: {
                        title: "Daftar Kategori",
                        parent_menu: "products",
                        icon: "fe fe-grid",
                        url: "/panel/inventori/products/products/categories",
                        parent: {
                            name: "products",
                            title: "Persediaan",
                            icon: "fe fe-grid",
                        },
                    },
                    component: () =>
                        import("../../pages/inventori/categories.vue"),
                },
                {
                    name: "brands",
                    path: "brands",
                    meta: {
                        title: "Daftar Kategori",
                        parent_menu: "products",
                        icon: "fe fe-grid",
                        url: "/panel/inventori/products/products/brands",
                        parent: {
                            name: "products",
                            title: "Persediaan",
                            icon: "fe fe-grid",
                        },
                    },
                    component: () => import("../../pages/inventori/brands.vue"),
                },
                {
                    name: "units",
                    path: "units",
                    meta: {
                        url: "/panel/inventori/products/products/units",
                        title: "Daftar Kategori",
                        parent_menu: "products",
                        icon: "fe fe-grid",
                        parent: {
                            name: "products",
                            title: "Persediaan",
                            icon: "fe fe-grid",
                        },
                    },
                    component: () => import("../../pages/inventori/units.vue"),
                },
                {
                    name: "raks",
                    path: "raks",
                    meta: {
                        url: "/panel/inventori/products/products/raks",
                        title: "Daftar Kategori",
                        parent_menu: "products",
                        icon: "fe fe-grid",
                        parent: {
                            name: "products",
                            title: "Persediaan",
                            icon: "fe fe-grid",
                        },
                    },
                    component: () => import("../../pages/inventori/raks.vue"),
                },
                {
                    name: "warehouse",
                    path: "warehouse",
                    meta: {
                        url: "/panel/inventori/products/products/warehouse",
                        title: "Gudang",
                        parent_menu: "warehouse",
                        icon: "fe fe-box",
                        parent: {
                            name: "products",
                            title: "Barang dan Jasa",
                            icon: "fe fe-box",
                        },
                    },
                    component: () => import("../../pages/master/warehouse.vue"),
                },
            ],
        },

        {
            path: "stock-opname",
            redirect: "/panel/inventori/stock-opname/list",
            component: () => import("../../pages/stock-opname"),
            children: [
                {
                    name: "stock_opname_list",
                    path: "list",
                    meta: {
                        url: "/panel/inventori/stock-opname/list",
                        title: "Stok Opname",
                        parent_menu: "stock_opname_list",
                        icon: "fe fe-box",
                        parent: {
                            name: "stock_opname_list",
                            title: "Stok Opname",
                            icon: "fe fe-box",
                        },
                    },
                    component: () =>
                        import("../../pages/stock-opname/list.vue"),
                },
                {
                    name: "stock_opname_create",
                    path: "create",
                    meta: {
                        closeTab: true,
                        url: "/panel/inventori/stock-opname/create",
                        title: "Buat Stok Opname",
                        parent_menu: "stock_opname_list",
                        icon: "fe fe-box",
                        parent: {
                            name: "stock_opname_list",
                            title: "Stok Opname",
                            icon: "fe fe-box",
                        },
                    },
                    component: () =>
                        import("../../pages/stock-opname/create.vue"),
                },
                {
                    name: "stock_opname_detail",
                    path: "detail/:id",
                    meta: {
                        url: "/panel/inventori/stock-opname/detail/:id",
                        title: "Detail Stok Opname",
                        parent_menu: "stock_opname_list",
                        icon: "fe fe-box",
                        parent: {
                            name: "stock_opname_list",
                            title: "Stok Opname",
                            icon: "fe fe-box",
                        },
                    },
                    component: () =>
                        import("../../pages/stock-opname/detail.vue"),
                },
                // Transfer Warehouse
                {
                    name: "warehouse_transfer",
                    path: "warehouse-list",
                    meta: {
                        url: "/panel/inventori/stock-opname/warehouse-list",
                        title: "Transfer Stok",
                        parent_menu: "warehouse_transfer",
                        icon: "fe fe-send",
                        parent: {
                            name: "warehouse_transfer",
                            title: "Transfer Stok",
                            icon: "fe fe-send",
                        },
                    },
                    component: () =>
                        import("../../pages/stock-opname/transfer/list.vue"),
                },
                {
                    name: "warehouse_transfer_create",
                    path: "warehouse-create",
                    meta: {
                        url: "/panel/inventori/stock-opname/warehouse-create",
                        closeTab: true,
                        title: "Transfer Stok",
                        parent_menu: "warehouse_transfer",
                        icon: "fe fe-send",
                        parent: {
                            name: "warehouse_transfer",
                            title: "Transfer Stok",
                            icon: "fe fe-send",
                        },
                    },
                    component: () =>
                        import("../../pages/stock-opname/transfer/create.vue"),
                },
                {
                    name: "warehouse_transfer_detail",
                    path: "warehouse-detail/:id",
                    meta: {
                        url: "/panel/inventori/stock-opname/warehouse-detail/:id",
                        closeTab: true,
                        title: "Transfer Stok",
                        parent_menu: "warehouse_transfer",
                        icon: "fe fe-send",
                        parent: {
                            name: "warehouse_transfer",
                            title: "Transfer Stok",
                            icon: "fe fe-send",
                        },
                    },
                    component: () =>
                        import("../../pages/stock-opname/transfer/detail.vue"),
                },
            ],
        },

        // Stock Opname
    ],
};

const inventoryRoutes = [inventory];

export default inventoryRoutes;
