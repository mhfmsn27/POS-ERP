"use strict";
$(function () {
    popularProduct();
});

function popularProduct() {
    $.ajax({
        url: domain + domainpath + "/pos-admin/five-top-product",
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
