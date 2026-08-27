'use strict'
$.ajax({
  url: domain + domainpath + '/mobile/analityc/profit',
  type: 'GET',
  data: '',
  success: function (data) {
    var pro = data.profit
    var expense = data.expense
    var profit = {
      chart: {
        width: 300,
        type: 'pie',
        sparkline: {
          enabled: true,
        },
        dropShadow: {
          enabled: true,
        },
      },
      colors: ['#0134d4', '#ea4c62'],
      series: [pro, expense],
      labels: ['Keuntungan Bersih', 'Pengeluaran'],
    }

    var profit_loss = new ApexCharts(
      document.querySelector('#profitLoss'),
      profit,
    )
    profit_loss.render()
  },

  cache: false,
  contentType: false,
  processData: false,
})
