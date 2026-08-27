const jurnal = {
    path: "/app/buku-besar",
    redirect: "/app/buku-besar/jurnal", 
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
        },
    ],
};

const jurnalRoutes = [jurnal];

export default jurnalRoutes;
