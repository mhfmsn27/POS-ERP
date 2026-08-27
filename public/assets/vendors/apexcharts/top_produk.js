'use strict'
$.ajax({
  url: domain + domainpath + '/mobile/analityc/trend-produk',
  type: 'GET',
  data: '',
  success: function (data) {
    var selling = data.selling
    var name = data.label
    console.log(name,selling,data)
    var topProduk = {
      chart: {
        height: 240,
        type: 'bar',
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 1000,
        },
        dropShadow: {
          enabled: true,
          opacity: 0.1,
          blur: 2,
          left: -1,
          top: 5,
        },
        zoom: {
          enabled: false,
        },
        toolbar: {
          show: false,
        },
      },
      subtitle: {
        text: "5 Top Produk",
        align: 'left',
        margin: 0,
        offsetX: 0,
        offsetY: 0,
        floating: false,
        style: {
          fontSize: '14px',
          color: '#8480ae',
        },
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '40%',
          endingShape: 'rounded',
        },
      },
      colors: ['#0134d4'],
      dataLabels: {
        enabled: true,
      },
      grid: {
        borderColor: '#dbeaea',
        strokeDashArray: 4,
        xaxis: {
          lines: {
            show: true,
          },
        },
        yaxis: {
          lines: {
            show: false,
          },
        },
        padding: {
          top: 0,
          right: 0,
          bottom: 0,
          left: 0,
        },
      },
      tooltip: {
        theme: 'light',
        marker: {
          show: true,
        },
        x: {
          show: false,
        },
      },
      stroke: {
        show: true,
        colors: ['transparent'],
        width: 3,
      },
      labels: name,
      series: [
        {
          name: 'Terjual',
          data: selling,
        },
      ],
      xaxis: {
        crosshairs: {
          show: true,
        },
        labels: {
          offsetX: 0,
          offsetY: 0,
          style: {
            colors: '#8380ae',
            fontSize: '12px',
          },
        },
        tooltip: {
          enabled: false,
        },
      },
      yaxis: {
        labels: {
          offsetX: -10,
          offsetY: 0,
          style: {
            colors: '#8380ae',
            fontSize: '12px',
          },
        },
      },
    }

    var columnChart_02 = new ApexCharts(
      document.querySelector('#topProduk'),
      topProduk,
    )
    columnChart_02.render()
  },

  cache: false,
  contentType: false,
  processData: false,
})
