$.ajax({
  url: domain + domainpath + '/pos-admin/transaction-data',
  type: 'GET',
  data: '',
  success: function (data) {
    var sell = data.sell

    var purchase = data.purchase

    var p_return = data.purchase_return

    var adjustment = data.adjustment

    var transfer = data.transfer

    Highcharts.chart('transactiondata', {
      chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie',
      },
      colorAxis: {},
      title: {
        text: 'Laporan Per Transaksi',
      },
      tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f} </b>%',
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: 'pointer',
          dataLabels: {
            enabled: true,
            format: '<b>{point.name}</b>: {point.percentage:.1f}  %',
          },
        },
      },
      series: [
        {
          name: 'Transaksi',
          colorByPoint: true,
          data: [
            {
              name: 'Penjualan',
              y: sell,
              sliced: true,
              selected: true,
              color: '#0084ff',
            },
            {
              name: 'Pembelian ( PO ) ',
              y: purchase,
              color: '#00ca00',
            },
            {
              name: 'PO Return ',
              y: p_return,
              color: '#e64141',
            },
            {
              name: 'Atur Stok',
              y: adjustment,
              color: '#ffd400',
            },
            {
              name: 'Transfer Stok',
              y: transfer,
              color: '#00d0ff',
            },
          ],
        },
      ],
    })
  },

  cache: false,
  contentType: false,
  processData: false,
})

$.ajax({
  url: domain + domainpath + '/pos-admin/sell-month',
  type: 'GET',
  data: '',
  success: function (data) {
    var date = data.selling.map(function (e) {
      return e.date
    })
    var sell = data.selling.map(function (e) {
      return e.total
    })

    var options = {
      series: [
        {
          name: 'Penjualan',
          data: sell,
        },
      ],
      chart: {
        height: 470,
        type: 'line',
        zoom: {
          enabled: false,
        },
      },
      dataLabels: {
        enabled: false,
      },
      stroke: {
        curve: 'straight',
      },
      title: {
        text: 'Penjualan 30 Hari Terakhir',
        align: 'left',
      },
      grid: {
        row: {
          colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
          opacity: 0.5,
        },
      },
      xaxis: {
        categories: date,
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return moneyformatter + ' ' + formatRupiah(val.toString()) + ' '
          },
        },
      },
    }

    var chart = new ApexCharts(document.querySelector('#sellMonth'), options)
    chart.render()
  },

  cache: false,
  contentType: false,
  processData: false,
})

$.ajax({
  url: domain + domainpath + '/pos-admin/income-expense',
  type: 'GET',
  data: '',
  success: function (data) {
    am4core.useTheme(am4themes_animated)
    var chart = am4core.create('incomeExpense', am4charts.PieChart)

    var income = data.income.map(function (e) {
      return e.jumlah
    })

    var expense = data.expense.map(function (e) {
      return e.jumlah
    })

    chart.data = [
      {
        value: 'Income',
        nominal: income,
      },
      {
        value: 'Expense',
        nominal: expense,
      },
    ]
 
    chart.innerRadius = am4core.percent(50) 
    var pieSeries = chart.series.push(new am4charts.PieSeries())
    pieSeries.dataFields.value = 'nominal'
    pieSeries.dataFields.category = 'value'
    pieSeries.slices.template.stroke = am4core.color('#fff')
    pieSeries.slices.template.strokeWidth = 2
    pieSeries.slices.template.strokeOpacity = 1
    pieSeries.labels.template.fill = am4core.color('#8e8da4') 
    pieSeries.hiddenState.properties.opacity = 1
    pieSeries.hiddenState.properties.endAngle = -90
    pieSeries.hiddenState.properties.startAngle = -90
  },

  cache: false,
  contentType: false,
  processData: false,
})
