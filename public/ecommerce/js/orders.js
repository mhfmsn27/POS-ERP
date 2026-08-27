const ecommerceMethode = $('#paymentMethodeEcommerce').val()

function detailTransaction(id) {
  $.ajax({
    url: domain + domainpath + '/web/account/orders/detail/' + id,
    type: 'GET',

    success: function (data, json, errorThrown) {
      if (data.status == true) {
        $('#invoiceOrders').html('')

        var itemDetail = ''
        var buttonTracking = ''
        var buttonPayment = ''

        $.each(data.data.items, function (index, item) {
          itemDetail +=
            `  <tr><td><div class="item-desc-1"><span>` +
            item.product_name +
            `</span><small>SKU: FWM15VKT</small></div></td><td class="text-center">Rp ` +
            formatRupiah(item.price.toString()) +
            ` </td><td class="text-center">` +
            item.qty +
            `</td><td class="text-right">Rp ` +
            formatRupiah(item.subtotal.toString()) +
            ` </td></tr>`
        })

        if (data.data.status == 'transit') {
          buttonTracking =
            '<a id="invoice_download_btn" class="btn btn-lg btn-custom bg-info btn-download hover-up" onclick="trackingPesanan(' +
            data.data.id +
            ')"> Tracking </a>  <a id="invoice_download_btn" onclick="receivedOrder(' +
            data.data.id +
            ')" class="btn btn-lg btn-custom btn-download hover-up"> Konfirmasi Penerimaan </a>'
        }

        if (data.data.payment_status == 'due') {
          buttonPayment =
            '<a id="invoice_download_btn" onclick="payTransaction(' +
            data.data.id +
            ')" class="btn btn-lg btn-custom btn-download hover-up"> Bayar </a>'
        }

        var invoiceDetail =
          `<div class="invoice-inner">
                              <div class="invoice-info" id="invoice_wrapper">
                                    <div class="invoice-header">

                                          <div class="row align-items-center">
                                                <div class="col-md-6">
                                                      <div class="logo">
                                                            <a href="` +
          domain +
          `" class="mr-20"><img src="` +
          data.logo +
          `" alt="logo"></a>
                                                      </div>
                                                </div>
                                                <div class="col-md-6 text-end">
                                                      <h2 class="mb-0">INVOICE</h2>
                                                </div>
                                          </div>
                                          <div class="row align-items-center">
                                                <div class="col-md-6">
                                                      <div class="text">
                                                      <strong class="text-brand">Informasi Cabang</strong>
                                                            ` +
          data.data.store.name +
          `<br>
                                                            ` +
          data.data.store.address +
          `<br>
                                                            <abbr title="Phone">Phone:</abbr>` +
          data.data.store.phone +
          `<br>
                                                      </div>
                                                </div>
                                                <div class="col-md-6 text-end">
                                                      <strong class="text-brand">Informasi Pelanggan</strong> <br>
                                                      ` +
          data.data.customer.name +
          `<br> 
                                                      ` +
          data.data.customer.address +
          `<br>
                                                      <abbr title="Email">Email: </abbr>` +
          data.data.customer.email +
          ` <br>
                                                      <abbr title="Email">Phone: </abbr>` +
          data.data.customer.phone +
          ` <br> 
                                                </div>
                                          </div>
                                          <div class="row mt-20">
                                                <div class="col-12">
                                                      <div class="hr mb-10"></div>
                                                </div>
                                                <div class="col-lg-4">
                                                      <strong class="text-brand"> Nomor Referensi:</strong> ` +
          data.data.ref_no +
          `
                                                </div>
                                                <div class="col-lg-4">
                                                      <strong class="text-brand"> Tanggal Transaksi:</strong> ` +
          data.data.transaction_date +
          `
                                                </div>
                                                <div class="col-lg-4">
                                                      <strong class="text-brand"> Status :</strong> ` +
          data.data.status_text +
          `
                                                </div>
                                                <div class="col-12">
                                                      <div class="hr mt-10"></div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class="invoice-center">
                                          <div class="table-responsive">
                                                <table class="table table-striped invoice-table">
                                                      <thead class="bg-active">
                                                            <tr>
                                                                  <th>Nama Produk</th>
                                                                  <th class="text-center">Harga Satuan</th>
                                                                  <th class="text-center">Quantity</th>
                                                                  <th class="text-right">Subtotal</th>
                                                            </tr>
                                                      </thead>
                                                      <tbody>
                                                            ` +
          itemDetail +
          `
                                                            <tr>
                                                                  <td colspan="3" class="text-end f-w-600">SubTotal</td>
                                                                  <td class="text-right">Rp ` +
          formatRupiah(data.data.subtotal.toString()) +
          ` </td>
                                                            </tr>
                                                            <tr>
                                                                  <td colspan="3" class="text-end f-w-600">Pajak PPN</td>
                                                                  <td class="text-right">Rp ` +
          formatRupiah(data.data.tax_total.toString()) +
          ` </td>
                                                            </tr>
                                                            <tr>
                                                                  <td colspan="3" class="text-end f-w-600">Ongkos Kirim</td>
                                                                  <td class="text-right">Rp ` +
          formatRupiah(data.data.shipping_cost.toString()) +
          ` </td>
                                                            </tr>
                                                            <tr>
                                                                  <td colspan="3" class="text-end f-w-600">Jumlah Total</td>
                                                                  <td class="text-right f-w-600">Rp ` +
          formatRupiah(data.data.grand_total.toString()) +
          ` </td>
                                                            </tr>
                                                      </tbody>
                                                </table>
                                          </div>
                                    </div>
                                    <div class="invoice-bottom pb-80">
                                          <div class="row">
                                                <div class="col-md-6">
                                                      <h6 class="mb-15">Informasi Pengiriman</h6>
                                                      <p class="font-sm">
                                                            <strong>Layanan Kurir: </strong>` +
          data.data.pengiriman.curir_name +
          ` - ` +
          data.data.pengiriman.curir_service +
          ` <br>
                                                            <strong>Alamat Pengiriman: </strong> ` +
          data.data.pengiriman.address_detail +
          `, ` +
          data.data.pengiriman.district +
          `, ` +
          data.data.pengiriman.city +
          `, ` +
          data.data.pengiriman.province +
          `, (` +
          data.data.pengiriman.postal_code +
          `) <br>
                                                            <strong>Atas Nama: </strong> ` +
          data.data.pengiriman.name +
          ` <br>
                                                            <strong>Nomor Resi: </strong> ` +
          data.data.pengiriman.resi_no +
          `
                                                      </p>
                                                </div>
                                                <div class="col-md-6 text-end">
                                                      <h6 class="mb-15">Jumlah Total</h6>
                                                      <h3 class="mt-0 mb-0 text-brand">Rp ` +
          formatRupiah(data.data.grand_total.toString()) +
          `</h3>
                                                      <p class="mb-0 text-muted">Sudah Termasuk Pajak</p>
                                                </div>
                                          </div>

                                    </div>
                              </div>
                              <div class="invoice-btn-section clearfix d-print-none">
                                    <a href="javascript:void(0);" class="btn bg-warning btn-lg btn-custom btn-print hover-up" onclick="backToList()"> Kembali </a>
                                    ` +
          buttonTracking +
          `
                                   ` +
          buttonPayment +
          `
                                   
                              </div>
                        </div>`

        $('#invoiceOrders').html(invoiceDetail)
        $('#orders').removeClass('active show')
        $('#orderDetails').addClass('active show')
      }
    },

    cache: false,
    contentType: false,
    processData: false,
  })
}

function payTransaction(id) {
  if (ecommerceMethode == 'midtrans') {
    setTimeout(function () {
      $.ajax({
        url: domain + domainpath + '/web/account/orders/pay-transaction/' + id,
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          Accept: 'application/json',
          'Content-Type': 'application/json',
          timeout: 0,
        },
        data: '',
        success: function (data, json, errorThrown) {
          if (data.status == false) {
            Swal.fire({
              title: 'Error',
              html: data.message,
              width: 'auto',
              confirmButtonText: 'Ok Saya Mengerti',
              showCancelButton: false,
            })
            return false
          } else {
            snap.pay(data.snap, {
              onSuccess: function (result) {
                window.location = '/web/account/orders/'
              },
              onPending: function (result) {
                window.location = '/web/account/orders/'
              },
              onError: function (result) {
                window.location = '/web/account/orders/'
              },
              onClose: function (result) {
                window.location = '/web/account/orders/'
              },
            })
          }
        },

        cache: false,
        contentType: false,
        processData: false,
      })
    }, 130)
  } else {
    showPaymentModal(id)
  }
}

function showPaymentModal(id) {
  if (id != null) {
    $('#tri').val(id)
    $('#no_rek').val('')
    $('#payment_amount').val(0)
  }

  $('#addpayEcommerce').modal('show')
}

function showPayment() {
  $('#bank_modal').modal('toggle')
  $('#addpayEcommerce').modal('show')
}

function bankModal() {
  $('#addpayEcommerce').modal('toggle')
  $('#bank_modal').modal('show')
}

function closePayment() {
  $('#addpayEcommerce').modal('toggle')
}

function closeBank() {
  $('#bank_modal').modal('toggle')
}

function backToList() {
  $('#orders').addClass('active show')
  $('#orderDetails').removeClass('active show')
  $('#trackingOrders').removeClass('active show')
}

function trackingPesanan(id) {
  $.ajax({
    url: domain + domainpath + '/web/account/orders/get-tracking/' + id,
    type: 'POST',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      Accept: 'application/json',
      'Content-Type': 'application/json',
      timeout: 0,
    },
    data: '',
    success: function (data, json, errorThrown) {
      if (data.status == true) {
        $('#listTracking').html('')

        var itemTrack = ''
        $.each(data.trackings, function (index, item) {
          itemTrack +=
            `  <tr><td>` +
            item.desc +
            `</td><td>` +
            item.date +
            `</td><td>` +
            item.time +
            `</td><td>` +
            item.city +
            `</td></tr>`
        })

        $('#listTracking').html($itemTrack)
        $('#trackingOrders').addClass('active show')
        $('#orderDetails').removeClass('active show')
      } else {
        Swal.fire({
          title: 'Error',
          html: data.message,
          width: 'auto',
          confirmButtonText: 'Ok Saya Mengerti',
          showCancelButton: false,
        })
        return false
      }
    },

    cache: false,
    contentType: false,
    processData: false,
  })
}

function receivedOrder(id) {
  Swal.fire({
    title: 'Apakah Anda Yakin ?',
    text: 'Klik Konfirmasi untuk konfirmasi penerimaan pesanan ini!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Konfirmasi',
  }).then((result) => {
    if (result.value) {
      setTimeout(function () {
        $.ajax({
          url: domain + domainpath + '/web/account/orders/confirmation/' + id,
          type: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            Accept: 'application/json',
            'Content-Type': 'application/json',
            timeout: 0,
          },
          success: function (data, json, errorThrown) {
            if (data.status == true) {
              toastr.success(data.message, 'Berhasil', {
                timeOut: 5e3,
                closeButton: !0,
                debug: !1,
                newestOnTop: !0,
                progressBar: !0,
                positionClass: 'toast-top-right',
                preventDuplicates: !0,
                onclick: null,
                showDuration: '100',
                hideDuration: '1000',
                extendedTimeOut: '1000',
                showEasing: 'swing',
                hideEasing: 'linear',
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut',
                tapToDismiss: !1,
              })

              setTimeout(function () {
                window.location = '/web/account/orders/'
              }, 1000)
            } else {
              Swal.fire({
                title: 'Error',
                html: data.message,
                width: 'auto',
                confirmButtonText: 'Ok Saya Mengerti',
                showCancelButton: false,
              })
              return false
            }
          },

          cache: false,
          contentType: false,
          processData: false,
        })
      }, 130)
    }
  })
}

$('#payment_amount').on('keyup', function () {
  var nominal = $('#payment_amount').val()
  $('#payment_amount').val(formatRupiah(nominal.toString()))
})

$('form#addpayEcommercementPurchase').on('submit', function (e) {
     
      e.preventDefault()
      var formData = new FormData(this)
      setTimeout(function () {
        $.ajax({
          url:
            domain +
            domainpath +
            '/web/account/orders/add-payment/' +
            $('#tri').val(),
          type: 'POST',
          data: formData,
          success: function (data, json, errorThrown) {  
            if (data.status == false) {
             
              Swal.fire(
                {
                  title: 'Peringatan',
                  text: data.message,
                  width: 'auto',
                  confirmButtonText: 'Ok, Saya Mengerti', 
                  showCancelButton: false,
                },
                function (isConfirm) {
                  if (isConfirm) {
                    $('#openModal').on('click')
                  }
                },
              ) 
            } else {
              toastr.success(data.message, 'Berhasil', {
                timeOut: 5e3,
                closeButton: !0,
                debug: !1,
                newestOnTop: !0,
                progressBar: !0,
                positionClass: 'toast-top-right',
                preventDuplicates: !0,
                onclick: null,
                showDuration: '100',
                hideDuration: '1000',
                extendedTimeOut: '1000',
                showEasing: 'swing',
                hideEasing: 'linear',
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut',
                tapToDismiss: !1,
              })
              $('#addpayEcommerce').modal('toggle')
              $("#tri").val("")
              $('#no_rek').val('')
              $('#payment_amount').val(0)
            }
          },
    
          cache: false,
          contentType: false,
          processData: false,
        })
      }, 130)
    })
