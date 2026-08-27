$('body').on('change', '.cartitems', function () {
  var price = $(this).find('input#productPrice').val()
  var qty = $(this).find('input.qty-val').val()
  var maxqty = $(this).find('input.qty-val').attr('max')
  var cartid = $(this).find('input#cartIdData').val()
  var subtotal = 0

  if (qty == null || qty == '') {
    qty = 1
    $(this).find('input.qty-val').val(1)
  }

  if (parseInt(qty) > parseInt(maxqty)) {
    $(this).find('input.qty-val').val(maxqty)
    qty = maxqty
  }

  subtotal = parseInt(qty) * parseInt(price)

  $(this)
    .find('.text-brand')
    .html('Rp ' + formatRupiah(subtotal.toString()))
  $(this).find('input#subtotalPriceCart').val(subtotal)
 
  setTimeout(function () {
    var sendData = {
      quantity: qty,
    }

    console.log(sendData)

    $.ajax({
      url: domain + domainpath + '/web/account/cart/update/' + cartid,
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        Accept: 'application/json',
        'Content-Type': 'application/json',
        timeout: 0,
      },
      data: JSON.stringify(sendData),
      success: function (data, json, errorThrown) {
        if (data.status == false) {
          Swal.fire({
            title: 'Error',
            html: data.message,
            width: 'auto',
            confirmButtonText: 'Ok Saya Mengerti',
            showCancelButton: false,
          })
        } else {
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)

  $('.cartdata').trigger('change')
})

$('.cartdata').on('change', function () {
  var subtotal = 0
  var tax = $('#taxstores').val()
  var tax_total = 0
  var grandTotal = 0

  $(this)
    .find('input#subtotalPriceCart')
    .each(function () {
      var cartid = $(this).attr('datacart')
      if ($('#chooseCart' + cartid + ':checked').val() != undefined) {
        subtotal += parseInt(
          $(this)
            .val()
            .replace(/[^0-9]/g, '')
            .toString(),
        )
      }
    })

  if (parseInt(tax) > 0) {
    tax_total = (parseInt(tax) / 100) * parseInt(subtotal)
  }

  grandTotal = parseInt(subtotal) + parseInt(tax_total)

  $('.subtotalCart').html('Rp ' + formatRupiah(subtotal.toString()))
  $('.taxtotalCart').html(
    'Rp ' + formatRupiah(tax_total.toString()) + ' (' + tax + ' %)',
  )
  $('.grandTotalCart').html('Rp ' + formatRupiah(grandTotal.toString()))
})

function removeCart(id) {
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/web/account/cart/delete/' + id,
      type: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        Accept: 'application/json',
        'Content-Type': 'application/json',
        timeout: 0,
      },
      success: function (data, json, errorThrown) {
        if (data.status == true) {
          // Dor Website
          $('#cartwebsite' + id).remove()
          $('#proCount').html(data.total)
          $('#totalinCart').html(formatRupiah(data.subtotal.toString()))

          if (data.total < 1) {
            $('#shopCartWebsite').addClass('d-none')
          }

          // For Mobile
          $('#mobileCartData')
            .find($('#cartwebsite' + id))
            .remove()
          $('#proCountMobile').html(data.total)
          $('#totalinCartMobile').html(formatRupiah(data.subtotal.toString()))

          if (data.total < 1) {
            $('#shopCartMobile').addClass('d-none')
          }

          $('#cartid' + id).remove()
          $('.cartdata').trigger('change')

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
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
}

function removeAll() {
  Swal.fire({
    title: 'Apakah Anda Yakin ?',
    text: '',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Asyiaap',
  }).then((result) => {
    if (result.value) {
      setTimeout(function () {
        $.ajax({
          url: domain + domainpath + '/web/account/cart/delete-all',
          type: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            Accept: 'application/json',
            'Content-Type': 'application/json',
            timeout: 0,
          },
          success: function (data, json, errorThrown) {
            if (data.status == true) {
              $('#cartProducts').html("<li class='cart_website_no'></li>")
              $('#proCount').html(data.total)
              $('#totalinCart').html(formatRupiah(data.subtotal.toString()))

              if (data.total < 1) {
                $('.shopping-cart-button').addClass('d-none')
              }

              $('.cartdata').html('')
              $('.cartdata').trigger('change')

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
