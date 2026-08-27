const hrm = {
    path: "/panel/buku-besar/salaries",
    redirect: "/panel/buku-besar/salaries/kasbon",
    component: () => import("../../pages/hrm"),
    children: [
        {
            name: "kasbon",
            path: "kasbon",
            meta: {
                title: "Kasbon Pegawai",
                parent_menu: "group",
                icon: "fe fe-dollar-sign",
                url: "/panel/buku-besar/salaries/kasbon",
                parent: {
                    name: "salary",
                    title: "Gaji dan Kasbon",
                    icon: "fa fa-money",
                },
            },
            component: () => import("../../pages/hrm/kasbon.vue"),
        },
        {
            name: "salary",
            path: "list-salary",
            meta: { 
                title: "Gaji Pegawai",
                parent_menu: "group",
                icon: "fe fe-dollar-sign",
                url: "/panel/buku-besar/salaries/salary",
                parent: {
                    name: "salary",
                    title: "Gaji dan Kasbon",
                    icon: "fa fa-money",
                },
            },
            component: () => import("../../pages/hrm/salary/list.vue"),
        },
        {
            name: "generate_salary",
            path: "generate-salary/:date/:department",
            meta: {
                title: "Generate Gaji",
                parent_menu: "group",
                icon: "fe fe-dollar-sign",
                url: "/panel/buku-besar/salaries/generate-salary/:date/:department",
                parent: {
                    name: "salary",
                    title: "Gaji dan Kasbon",
                    icon: "fa fa-money",
                },
            },
            component: () =>
                import("../../pages/hrm/salary/generate.vue"),
        },
        {
            name: "detail_salary",
            path: "detail-salary/:id",
            meta: {
                title: "Detail Gaji",
                parent_menu: "group",
                icon: "fe fe-dollar-sign",
                url: "/panel/buku-besar/salaries/detail-salary/:id",
                parent: {
                    name: "salary",
                    title: "Gaji dan Kasbon",
                    icon: "fa fa-money",
                },
            },
            component: () => import("../../pages/hrm/salary/detail.vue"),
        },
        {
            name: "commission",
            path: "list-commission",
            meta: {
                title: "Komisi Pegawai",
                parent_menu: "group",
                icon: "fe fe-dollar-sign",
                url: "/panel/buku-besar/salaries/list-commission",
                parent: {
                    name: "salary",
                    title: "Gaji dan Kasbon",
                    icon: "fa fa-money",
                },
            },
            component: () => import("../../pages/hrm/commission.vue"),
        },
    ],
};

const hrmRoutes = [hrm];

export default hrmRoutes;
