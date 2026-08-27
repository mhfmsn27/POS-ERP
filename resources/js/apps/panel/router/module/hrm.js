const hrm = {
    path: "/app/buku-besar/salaries",
    redirect: "/app/buku-besar/salaries/kasbon", 
    component: () => import("../../index.vue"),
    children: [
        {
            name: "kasbon",
            path: "kasbon",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "salary",
            path: "list-salary",
            component: () => import("../../index.vue"),
            meta: { 
                title: "Gaji Pegawai",
                parent_menu: "group",
                icon: "fe fe-dollar-sign",
                url: "/panel/buku-besar/salaries/list-salary",
                parent: {
                    name: "salary",
                    title: "Gaji dan Kasbon",
                    icon: "fa fa-money",
                },
            }, 
        },
        {
            name: "generate_salary",
            path: "generate-salary/:date/:department",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "detail_salary",
            path: "detail-salary/:id",
            component: () => import("../../index.vue"),
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
        },
        {
            name: "commission",
            path: "list-commission",
            component: () => import("../../index.vue"),
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
        },
    ],
};

const hrmRoutes = [hrm];

export default hrmRoutes;
