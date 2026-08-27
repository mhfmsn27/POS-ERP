'use strict'
$.ajax({
  url: domain + domainpath + '/mobile/sell-week',
  type: 'GET',
  data: '',
  success: function (data) {
    var date = data.selling.map(function (e) {
      return e.date
    })
    var sell = data.selling.map(function (e) {
      return e.total
    })
    var penjualan2Week = {
      chart: {
        height: 240,
        type: 'area',
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 1000,
        },
        dropShadow: {
          enabled: true,
          opacity: 0.1,
          blur: 1,
          left: -5,
          top: 5,
        },
        zoom: {
          enabled: false,
        },
        toolbar: {
          show: false,
        },
      },
      colors: ['#0134d4'],
      dataLabels: {
        enabled: false,
      },
      fill: {
        type: 'gradient',
        gradient: {
          type: 'vertical',
          shadeIntensity: 1,
          inverseColors: true,
          opacityFrom: 0.15,
          opacityTo: 0.05,
          stops: [40, 100],
        },
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
      legend: {
        position: 'top',
        horizontalAlign: 'right',
        offsetY: -60,
        fontSize: '14px',
        markers: {
          width: 9,
          height: 9,
          strokeWidth: 0,
          radius: 20,
        },
        itemMargin: {
          horizontal: 5,
          vertical: 0,
        },
      },
      title: {
        text: '',
        align: 'left',
        margin: 0,
        offsetX: 0,
        offsetY: 20,
        floating: false,
        style: {
          fontSize: '16px',
          color: '#8480ae',
        },
      },
      tooltip: {
        theme: 'dark',
        marker: {
          show: true,
        },
        x: {
          show: false,
        },
      },
      subtitle: {
        text: 'Hasil Dari 7 Hari Penjualan Terakhir',
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
      stroke: {
        show: true,
        curve: 'smooth',
        width: 3,
      },
      labels: date,
      series: [
        {
          name: 'Penjualan',
          data: sell,
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
            colors: '#8480ae',
            fontSize: '10px',
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
            colors: '#8480ae',
            fontSize: '12px',
          },
        },
      },
    }

    var penjualan2_Week = new ApexCharts(
      document.querySelector('#penjualan2Week'),
      penjualan2Week,
    )
    penjualan2_Week.render()
  },

  cache: false,
  contentType: false,
  processData: false,
})
