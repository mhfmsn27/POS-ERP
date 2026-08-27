const reports = {
    path: "/panel/taxes",
    redirect: "/panel/taxes/numbers",
    component: () => import("../../pages/taxrates/numbers"),
    children: [
        {
            name: "tax_number",
            path: "numbers",
            meta: {
                title: "SmartLink",
                parent_menu: "tax_number",
                icon: "fe fe-percent",
                parent: {
                    name: "tax_number",
                    title: "SmartLink",
                    icon: "fe fe-percent",
                },
            },
            component: () => import("../../pages/taxrates/numbers/list.vue"),
        },
        {
            name: "create_tax_number",
            path: "create-tax-number",
            meta: {
                title: "SmartLink",
                parent_menu: "tax_number",
                icon: "fe fe-percent",
                parent: {
                    name: "tax_number",
                    title: "SmartLink",
                    icon: "fe fe-percent",
                },
            },
            component: () => import("../../pages/taxrates/numbers/create.vue"),
        },
        {
            name: "detail_tax_number",
            path: "detail-tax-number/:id",
            meta: {
                title: "SmartLink",
                parent_menu: "tax_number",
                icon: "fe fe-percent",
                parent: {
                    name: "tax_number",
                    title: "SmartLink",
                    icon: "fe fe-percent",
                },
            },
            component: () => import("../../pages/taxrates/numbers/detail.vue"),
        },
    ],
};

const reportsRoute = [reports];

export default reportsRoute;
