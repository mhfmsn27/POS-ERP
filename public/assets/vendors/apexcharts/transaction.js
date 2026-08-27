'use strict'
$.ajax({
  url: domain + domainpath + '/mobile/analityc/transaction',
  type: 'GET',
  data: '',
  success: function (data) {
    var sell = data.sell
    var sell_return = data.sell_return
    var purchase = data.purchase
    var purchase_return = data.purchase_return
    var adjustment = data.adjustment
    var transfer = data.transfer

    var transactionData = {
      chart: {
        width: 300,
        type: 'donut',
        sparkline: {
          enabled: true,
        },
        dropShadow: {
          enabled: true,
        },
      },
      colors: ['#0134d4', '#2ecc4a', '#ea4c62', '#1787b8','#ee4920','#b81fb8'],
      series: [sell, sell_return, purchase, purchase_return,adjustment,transfer],
      labels: ['Penjualan', 'Return Penjualan', 'Pembelian', 'Return Pembelian','Perapihan Stok','Transfer Stok'],
    }

    var Transaction_data = new ApexCharts(
      document.querySelector('#transactionData'),
      transactionData,
    )
    Transaction_data.render()
  },

  cache: false,
  contentType: false,
  processData: false,
})
