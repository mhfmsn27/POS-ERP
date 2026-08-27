"use strict";
$(function () {
    typetransaction();
    todaypayment();
    topProduct();
});

var colors = ["#886ab5", "#1dc9b7", "#2196f3", "#fd3995", "#ffc241"];

function typetransaction() {
    $.ajax({
        url:
            domain + domainpath + "/pos-admin/shift-register/today-transaction",
        type: "GET",
        data: "",
        success: function (data) {
            console.log(data);

            var sell = data.sell.map(function (e) {
                return e.total;
            });

            var opening = data.cash.map(function (e) {
                return e.total;
            });

            var refund = data.return.map(function (e) {
                return e.total;
            });

            var expense = data.expense.map(function (e) {
                return e.total;
            });

            var pieChart = c3.generate({
                bindto: "#summaryTransaction",
                data: {
                    // iris data from R
                    columns: [
                        ["Penjualan", sell],
                        ["Cash di Tangan", opening],
                        ["Return Penjualan", refund],
                        ["Pengeluaran", expense],
                    ],
                    type: "pie", //,
                },
                color: {
                    pattern: colors,
                },
            });
        },

        cache: false,
        contentType: false,
        processData: false,
    });
}

function todaypayment() {
    $.ajax({
        url: domain + domainpath + "/pos-admin/shift-register/today-payment",
        type: "GET",
        data: "",
        success: function (data) {
            var cash = data.cash.map(function (e) {
                return e.total;
            });

            var bank = data.bank.map(function (e) {
                return e.total;
            });

            var other = data.other.map(function (e) {
                return e.total;
            });

            var donutChart = c3.generate({
                bindto: "#payment",
                data: {
                    // iris data from R
                    columns: [
                        ["Via Cash", cash],
                        ["Via Bank", bank],
                        ["Lainnya", other],
                    ],
                    type: "donut", //,
                },
                donut: {
                    title: "Pembayaran",
                },
                color: {
                    pattern: colors,
                },
            });
        },

        cache: false,
        contentType: false,
        processData: false,
    });
}

function topProduct() {
    $.ajax({
        url: domain + domainpath + "/pos-admin/shift-register/top-product",
        type: "GET",
        data: "",
        success: function (data) {
            am4core.useTheme(am4themes_animated);
            var chart = am4core.create("popularproduct", am4charts.XYChart);
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "name";
            categoryAxis.renderer.grid.template.disabled = false;
            categoryAxis.renderer.minGridDistance = 20;
            categoryAxis.renderer.inside = false;
            categoryAxis.renderer.labels.template.fill = am4core.color("#fff");
            categoryAxis.renderer.labels.template.fontSize = 14;

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.renderer.grid.template.strokeDasharray = "4,4";
            valueAxis.renderer.labels.template.disabled = false;
            valueAxis.min = 0;

            // Do not crop bullets
            chart.maskBullets = true;

            // Remove padding
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "selling";
            series.dataFields.categoryX = "name";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText =
                "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "#";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());
        },

        cache: false,
        contentType: false,
        processData: false,
    });
}
