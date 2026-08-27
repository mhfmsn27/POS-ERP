const company = {
    path: "/panel/company",
    redirect: "/panel/company/couerier",
    component: () => import("../../pages/company"),
    children: [
        {
            name: "couerier",
            path: "couerier",
            meta: {
                title: "Ekspedisi",
                parent_menu: "group",
                icon: "fe fe-truck",
                url: '/panel/company/courier',
                parent: {
                    name: "couerier",
                    title: "Perusahaan",
                    icon: "fa fa-building",
                },
            },
            component: () => import("../../pages/company/couerier.vue"),
        },
        {
            name: "devision",
            path: "devision",
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
            component: () => import("../../pages/company/department.vue"),
        },
        {
            name: "designation",
            path: "designation",
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
            component: () => import("../../pages/company/designation.vue"),
        },
        {
            name: "allowance",
            path: "allowance",
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
            component: () => import("../../pages/company/allowance.vue"),
        },
        {
            name: "potongan",
            path: "potongan",
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
            component: () => import("../../pages/company/cutting.vue"),
        },
        {
            name: "printer",
            path: "printer",
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
            component: () => import("../../pages/company/printer.vue"),
        },
        {
            name: "taxrate",
            path: "taxrate",
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
            component: () => import("../../pages/company/taxrate.vue"),
        },
        {
            name: "term",
            path: "term",
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
            component: () => import("../../pages/master/term.vue"),
        },
        {
            path: "employee",
            children: [
                {
                    name: "employee_list",
                    path: "employees",
                    meta: {
                        url: '/panel/company/employee/employees',
                        title: "Pegawai",
                        parent_menu: "employee_list",
                        icon: "fe fe-users",
                        parent: {
                            name: "employee_list",
                            title: "Pegawai",
                            icon: "fe fe-users",
                        },
                    },
                    component: () =>
                        import("../../pages/company/employee/index.vue"),
                },
                {
                    name: "employee_create",
                    path: "create",
                    meta: {
                        closeTab: true,
                        url: '/panel/company/employee/create',
                        title: "Tambah Pegawai",
                        parent_menu: "employee_list",
                        icon: "fe fe-users",
                        parent: {
                            name: "employee_list",
                            title: "Pegawai",
                            icon: "fe fe-users",
                        },
                    },
                    component: () =>
                        import("../../pages/company/employee/create.vue"),
                },
                {
                    name: "employee_update",
                    path: "update/:id/:name",
                    meta: {
                        closeTab: true,
                        title: "Edit Pegawai",
                        url: '/panel/company/employee/update:/id',
                        parent_menu: "employee_list",
                        icon: "fe fe-users",
                        parent: {
                            name: "employee_list",
                            title: "Pegawai",
                            icon: "fe fe-users",
                        },
                    },
                    component: () =>
                        import("../../pages/company/employee/update.vue"),
                },
            ],
        },
    ],
};

const companyRoutes = [company];

export default companyRoutes;
