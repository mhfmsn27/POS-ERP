const reports = {
    path: "/app/taxes",
    redirect: "/app/taxes/numbers", 
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
        },
    ],
};

const reportsRoute = [reports];

export default reportsRoute;
