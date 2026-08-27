const inventory = {
    path: "/app/inventori",
    redirect: "/app/inventori/products",
    children: [
        {
            path: "products",
            redirect: "/app/inventori/products/products",
            children: [
                {
                    path: "products",
                    redirect: "/app/inventori/products/products/list",
                    children: [
                        {
                            name: "products",
                            path: "list",
                            component: () => import("../../index.vue"),
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
                        },
                        {
                            name: "create_product",
                            path: "create-product",
                            component: () => import("../../index.vue"),
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
                        },
                        {
                            name: "update_product",
                            path: "update-product/:id",
                            component: () => import("../../index.vue"),
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
                        },
                        {
                            path: "product-detail",
                            children: [
                                {
                                    name: "product_details",
                                    path: "product-detail/:id",
                                    component: () => import("../../index.vue"),
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
                                },
                                {
                                    name: "stock_histories_product",
                                    path: "histori-stock/:id",
                                    component: () => import("../../index.vue"),
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
                                },
                                {
                                    name: "stock_product",
                                    path: "stok-product/:id",
                                    component: () => import("../../index.vue"),
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
                                },
                            ],
                        },
                    ],
                },
                {
                    name: "categories",
                    path: "categories",
                    component: () => import("../../index.vue"),
                    meta: {
                        title: "Daftar Kategori",
                        parent_menu: "products",
                        icon: "fe fe-grid",
                        url: "/panel/inventori/products/categories",
                        parent: {
                            name: "products",
                            title: "Persediaan",
                            icon: "fe fe-grid",
                        },
                    },
                },
                {
                    name: "brands",
                    path: "brands",
                    component: () => import("../../index.vue"),
                    meta: {
                        title: "Daftar Kategori",
                        parent_menu: "products",
                        icon: "fe fe-grid",
                        url: "/panel/inventori/products/brands",
                        parent: {
                            name: "products",
                            title: "Persediaan",
                            icon: "fe fe-grid",
                        },
                    },
                },
                {
                    name: "units",
                    path: "units",
                    component: () => import("../../index.vue"),
                    meta: {
                        url: "/panel/inventori/products/units",
                        title: "Daftar Kategori",
                        parent_menu: "products",
                        icon: "fe fe-grid",
                        parent: {
                            name: "products",
                            title: "Persediaan",
                            icon: "fe fe-grid",
                        },
                    },
                },
                {
                    name: "raks",
                    path: "raks",
                    component: () => import("../../index.vue"),
                    meta: {
                        url: "/panel/inventori/products/raks",
                        title: "Daftar Kategori",
                        parent_menu: "products",
                        icon: "fe fe-grid",
                        parent: {
                            name: "products",
                            title: "Persediaan",
                            icon: "fe fe-grid",
                        },
                    },
                },
                {
                    name: "warehouse",
                    path: "warehouse",
                    component: () => import("../../index.vue"),
                    meta: {
                        url: "/panel/inventori/products/warehouse",
                        title: "Gudang",
                        parent_menu: "warehouse",
                        icon: "fe fe-box",
                        parent: {
                            name: "products",
                            title: "Barang dan Jasa",
                            icon: "fe fe-box",
                        },
                    },
                },
            ],
        },

        {
            path: "stock-opname",
            redirect: "/app/inventori/stock-opname/list",
            children: [
                {
                    name: "stock_opname_list",
                    path: "list",
                    component: () => import("../../index.vue"),
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
                },
                {
                    name: "stock_opname_create",
                    path: "create",
                    component: () => import("../../index.vue"),
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
                },
                {
                    name: "stock_opname_detail",
                    path: "detail/:id",
                    component: () => import("../../index.vue"),
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
                },
                // Transfer Warehouse
                {
                    name: "warehouse_transfer",
                    path: "warehouse-list",
                    component: () => import("../../index.vue"),
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
                },
                {
                    name: "warehouse_transfer_create",
                    path: "warehouse-create",
                    component: () => import("../../index.vue"),
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
                },
                {
                    name: "warehouse_transfer_detail",
                    path: "warehouse-detail/:id",
                    component: () => import("../../index.vue"),
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
                },
            ],
        },

        // Stock Opname
    ],
};

const inventoryRoutes = [inventory];

export default inventoryRoutes;
