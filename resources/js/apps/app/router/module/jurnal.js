const jurnal = {
    path: "/panel/buku-besar",
    redirect: "/panel/buku-besar/jurnal",
    component: () => import("../../pages/accounting/jurnal"),
    children: [
        {
            name: "jurnal",
            path: "jurnal",
            meta: {
                url: "/panel/buku-besar/jurnal",
                title: "Jurnal Umum",
                parent_menu: "jurnal",
                icon: "fe fe-list",
                parent: {
                    name: "jurnal",
                    title: "Jurnal Umum",
                    icon: "fa fa-list",
                },
            },
            component: () => import("../../pages/accounting/jurnal/list.vue"),
        },
        {
            name: "create_jurnal",
            path: "create-jurnal",
            meta: {
                closeTab: true,
                url: "/panel/buku-besar/create-jurnal",
                title: "Tambah Jurnal Umum",
                parent_menu: "jurnal",
                icon: "fe fe-plus-circle",
                parent: {
                    name: "jurnal",
                    title: "Jurnal Umum",
                    icon: "fa fa-list",
                },
            },
            component: () => import("../../pages/accounting/jurnal/create.vue"),
        },
        {
            name: "update_jurnal",
            path: "update-jurnal/:id",
            meta: {
                url: "/panel/buku-besar/update-jurnal/:id",
                closeTab: true,
                title: "Edit Jurnal Umum",
                parent_menu: "jurnal",
                icon: "fe fe-pencil",
                parent: {
                    name: "jurnal",
                    title: "Jurnal Umum",
                    icon: "fa fa-list",
                },
            },
            component: () => import("../../pages/accounting/jurnal/update.vue"),
        },
    ],
};

const jurnalRoutes = [jurnal];

export default jurnalRoutes;
