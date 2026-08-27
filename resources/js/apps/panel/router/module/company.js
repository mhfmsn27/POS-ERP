const company = {
    path: "/app/company",
    redirect: "/app/company/couerier", 
    children: [
        {
            name: "couerier",
            path: "couerier",
            component: () => import("../../index.vue"),
            meta: {
                title: "Ekspedisi",
                parent_menu: "group",
                icon: "fe fe-truck",
                url: '/panel/company/couerier',
                parent: {
                    name: "couerier",
                    title: "Perusahaan",
                    icon: "fa fa-building",
                },
            },
        },
        {
            name: "devision",
            path: "devision",
            component: () => import("../../index.vue"),
            meta: {
                title: "Devisi",
                url: '/panel/company/devision',
                parent_menu: "group",
                icon: "fe fe-grid",
                parent: {
                    name: "couerier",
                    title: "Perusahaan",
                    icon: "fa fa-building",
                },
            },
        },
        {
            name: "designation",
            path: "designation",
            component: () => import("../../index.vue"),
            meta: {
                title: "Jabatan",
                parent_menu: "group",
                icon: "fe fe-grid",
                url: '/panel/company/designation',
                parent: {
                    name: "couerier",
                    title: "Perusahaan",
                    icon: "fa fa-building",
                },
            },
        },
        {
            name: "allowance",
            path: "allowance",
            component: () => import("../../index.vue"),
            meta: {
                title: "Tunjangan",
                parent_menu: "group",
                icon: "fe fe-grid",
                url: '/panel/company/allowance',
                parent: {
                    name: "couerier",
                    title: "Perusahaan",
                    icon: "fa fa-building",
                },
            },
        },
        {
            name: "potongan",
            path: "potongan",
            component: () => import("../../index.vue"),
            meta: {
                title: "Potongan",
                parent_menu: "group",
                icon: "fe fe-grid",
                url: '/panel/company/potongan',
                parent: {
                    name: "couerier",
                    title: "Perusahaan",
                    icon: "fa fa-building",
                },
            },
        },
        {
            name: "printer",
            path: "printer",
            component: () => import("../../index.vue"),
            meta: {
                title: "Printer",
                parent_menu: "group",
                icon: "fe fe-printer",
                url: '/panel/company/printer',
                parent: {
                    name: "couerier",
                    title: "Perusahaan",
                    icon: "fa fa-building",
                },
            },
        },
        {
            name: "taxrate",
            path: "taxrate",
            component: () => import("../../index.vue"),
            meta: {
                title: "Pajak",
                parent_menu: "group",
                icon: "fe fe-percent",
                url: '/panel/company/taxrate',
                parent: {
                    name: "couerier",
                    title: "Perusahaan",
                    icon: "fa fa-building",
                },
            },
        },
        {
            name: "term",
            path: "term",
            component: () => import("../../index.vue"),
            meta: {
                title: "Syarat Pembayaran",
                parent_menu: "group",
                icon: "fe fe-percent",
                url: '/panel/company/term',
                parent: {
                    name: "couerier",
                    title: "Perusahaan",
                    icon: "fa fa-building",
                },
            },
        },
        {
            path: "employee",
            children: [
                {
                    name: "employee_list",
                    path: "employees",
                    component: () => import("../../index.vue"),
                    meta: {
                        url: '/panel/company/employee/employees',
                        title: "Pegawai",
                        parent_menu: "couerier",
                        icon: "fe fe-users",
                        parent: {
                            name: "couerier",
                            title: "Perusahaan",
                            icon: "fa fa-building",
                        },
                    },
                },
                {
                    name: "employee_create",
                    path: "create",
                    component: () => import("../../index.vue"),
                    meta: {
                        closeTab: true,
                        url: '/panel/company/employee/create',
                        title: "Tambah Pegawai",
                        parent_menu: "couerier",
                        icon: "fe fe-users",
                        parent: {
                            name: "couerier",
                            title: "Perusahaan",
                            icon: "fa fa-building",
                        },
                    },
                },
                {
                    name: "employee_update",
                    path: "update/:id/:name",
                    component: () => import("../../index.vue"),
                    meta: {
                        closeTab: true,
                        title: "Edit Pegawai",
                        url: '/panel/company/employee/update:/id',
                        parent_menu: "couerier",
                        icon: "fe fe-users",
                        parent: {
                            name: "couerier",
                            title: "Perusahaan",
                            icon: "fa fa-building",
                        },
                    },
                },
            ],
        },
    ],
};

const companyRoutes = [company];

export default companyRoutes;
