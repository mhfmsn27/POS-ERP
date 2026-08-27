$(document).ready(function () {
  getShipping(null)

  $('#getPayment').on('click', function () {
    setTimeout(function () {
      var sendData = data()
      $.ajax({
        url: domain + domainpath + '/web/shop/checkout/transactions',
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
            return false
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

            window.location = '/web/account/orders/' 
          }
        },

        cache: false,
        contentType: false,
        processData: false,
      })
    }, 130)
  })
})

function getShipping(id) {
  $('.listcostshipping').html('')

  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/web/shop/checkout/get-shipping-cost?address_id=' +
        id,
      type: 'GET',
      success: function (data, json, errorThrown) {
        var shippingData = ''
        if (data.status == true) {
          let no = 1
          $.each(data.data, function (index, value) {
            var defaultData = ''

            var idNumber = no++

            shippingData +=
              `<tr id="shipping_option` +
              idNumber +
              `">
                                                <td class="custome-radio pl-30">

                                                      <input class="form-check-input" type="radio" name="currier_option" onclick="chooseShipping(` +
              idNumber +
              `)" id="optionShipping` +
              idNumber +
              `" value="` +
              idNumber +
              `">
                                                      <label class="form-check-label" for="optionShipping` +
              idNumber +
              `"></label>
                                                </td>
                                                <td class="image product-thumbnail pt-40">
                                                      <img src="` +
              value.image +
              `" alt="#">
                                                      <input type="hidden" value="` +
              value.price +
              `" id="shippingPrice" name="shipping_price">
                                                      <input type="hidden" value="` +
              value.code +
              `" id="shippingCode" name="shipping_code">
                                                      <input type="hidden" value="` +
              value.service +
              `" id="shippingService" name="shipping_service">
              <input type="hidden" value="` +
              value.curir_id +
              `" id="courierId">
                                                </td>
                                                <td class="product-des product-name">
                                                      <h6 class="mb-5">
                                                            <a class="product-name mb-10 text-heading" href="javascript:void(0;)">` +
              value.name +
              ` </a>
                                                      </h6>
                                                      <div class="product-rate-cover">

                                                            <div class="product-rate-cover">
                                                                  <span class="font-small ml-5 text-muted">Layanan : ` +
              value.service +
              ` </span>
                                                            </div>

                                                            <div class="product-rate-cover">
                                                                  <span class="font-small ml-5 text-muted">Estimasi Pengiriman : ` +
              value.etd +
              ` </span>
                                                            </div>

                                                      </div>
                                                </td>
                                                <td>
                                                      <h6 class="w-160 mb-5"><a href="javascript:void(0);" class="text-heading">` +
              formatRupiah(value.price.toString()) +
              `</a></h6></span>
                                                </td>
                                          </tr>`
          })

          $('.listcostshipping').html(shippingData)
        }
      },
    })
  }, 130)
}

function chooseShipping(iddata) {
  var price = $('#shipping_option' + iddata)
    .find('input#shippingPrice')
    .val()
  var code = $('#shipping_option' + iddata)
    .find('input#shippingCode')
    .val()
  var service = $('#shipping_option' + iddata).find('input#shippingService').val()
  var courierid = $("#shipping_option" + iddata).find("input#courierId").val();
  var subtotal = $('#subtotalCart').val()
  var taxTotal = $('#subtotalTax').val()

  if (price == undefined) {
    price = 0
  }

  $('#sp').val(price)
  $('#sc').val(code)
  $('#ss').val(service)
  $("#ci").val(courierid)

  var grandTotal = parseInt(price) + parseInt(subtotal) + parseInt(taxTotal)

  $('.shippingCost').html('Rp ' + formatRupiah(price.toString()))
  $('.grandTotal').html('Rp ' + formatRupiah(grandTotal.toString()))
}

$("input[name='address_option']").on('change', function () {
  var addressId = $(this).val()
  var subtotal = $('#subtotalCart').val()
  var taxTotal = $('#subtotalTax').val()

  $('#sp').val(0)
  $('#sc').val('')
  $('#ss').val('')
  $("#ci").val('');

  var grandTotal = parseInt(subtotal) + parseInt(taxTotal)
  $('.shippingCost').html('Rp ' + 0)
  $('.grandTotal').html('Rp ' + formatRupiah(grandTotal.toString()))

  getShipping(addressId)
})

function data() {
  var items = []

  var ongkir = {
    id: $("#ci").val(),
    price: $('#sp').val(),
    code: $('#sc').val(),
    service: $('#ss').val(),
    from: $('input[name="address_option"]:checked').val(),
  }

  $($('input#cartIdVariation')).each(function () {
    items.push({
      cart: $(this).val(),
    })
  })

  var data = {
    details: items,
    ongkir: ongkir,
  }

  return data
}
