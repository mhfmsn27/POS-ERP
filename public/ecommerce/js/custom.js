;('use strict')


checkCart()

$('#ourCategory').select2({
  placeholder: 'Semua Kategori',
  ajax: {
    url: domainpath + '/web/shop/categories',
    dataType: 'json',
    delay: 250,
    processResults: function (data) {
      return {
        results: $.map(data, function (item) {
          return {
            text: item.name,
            id: item.id,
          }
        }),
      }
    },
    cache: false,
  },
})

$('#selectedStoreLocation').on('change', function () {
  window.location = '/web/change-session/' + $(this).val()
})

$('#addToCartProduct').on('click', function () {
  var quantity = $('#qtyCart').val()
  var variationid = $('#variationID').val()

  if (quantity < 1) {
    Swal.fire({
      title: 'Peringatan!',
      text: 'Qty Harus lebih dari satu',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#435ebe',
      cancelButtonColor: '#198754',
      confirmButtonText: 'Ok Saya Mengerti',
    }).then((result) => {})
    return
  }

  var sendData = {
    variationid: variationid,
    quantity: quantity,
  }
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/web/account/cart/add',
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
          $('#qtyCart').val(1)

          // For Website
          addCartForWebsite(data)
          addCartForMobile(data)

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
})


function addCartForWebsite(data) {
  $('#proCount').html(data.total)
  $('#totalinCart').html(formatRupiah(data.subtotal.toString()))

  if (data.total > 0) {
    $('#shopCartWebsite').removeClass('d-none')
  }

  var cart = data.cart

  var check = $('#cartProducts')
    .find('#cartwebsite' + cart.id)
    .html()

  if (check == undefined) {
    var cartProducts =
      ` <li id="cartwebsite` +
      cart.id +
      `">
      <div class="shopping-cart-img">
            <a href="` +
      cart.url_product +
      `"><img alt="` +
      cart.product_name +
      `" src="` +
      cart.image +
      `" /></a>
      </div>
      <div class="shopping-cart-title">
            <h4 id="titleDatacart"><a href="` +
      cart.url_product +
      `">` +
      cart.product_name +
      ` ` +
      cart.variation_name +
      `</a></h4>
            <h4><span id="qtyKeranjang">` +
      cart.quantity +
      ` × </span>` +
      formatRupiah(cart.price.toString()) +
      `</h4>
      </div>
      <div class="shopping-cart-delete">
            <a href="javascript:void(0);" onclick="deleteCart(` +
      cart.id +
      `)"><i class="fi-rs-cross-small"></i></a>
      </div>
</li>`

    $('.cart_website_no').after(cartProducts)
  } else {
    $('#cartwebsite' + cart.id)
      .find('#qtyKeranjang')
      .html('' + cart.quantity + ' x ')
  }
}

function addCartForMobile(data) {
  $('#proCountMobile').html(data.total)
  $('#totalinCartMobile').html(formatRupiah(data.subtotal.toString()))

  if (data.total > 0) {
    $('#shopCartMobile').removeClass('d-none')
  }

  var cart = data.cart

  var check = $('#mobileCartData')
    .find('#cartwebsite' + cart.id)
    .html()

  if (check == undefined) {
    var cartProducts =
      ` <li id="cartwebsite` +
      cart.id +
      `">
              <div class="shopping-cart-img">
                    <a href="` +
      cart.url_product +
      `"><img alt="` +
      cart.product_name +
      `" src="` +
      cart.image +
      `" /></a>
              </div>
              <div class="shopping-cart-title">
                    <h4 id="titleDatacart"><a href="` +
      cart.url_product +
      `">` +
      cart.product_name +
      ` ` +
      cart.variation_name +
      `</a></h4>
                    <h4><span id="qtyKeranjang">` +
      cart.quantity +
      ` × </span>` +
      formatRupiah(cart.price.toString()) +
      `</h4>
              </div>
              <div class="shopping-cart-delete">
                    <a href="javascript:void(0);" onclick="deleteCart(` +
      cart.id +
      `)"><i class="fi-rs-cross-small"></i></a>
              </div>
        </li>`

    $('.cart_website_no_mobile').after(cartProducts)
  } else {
    $('#mobileCartData')
    .find('#cartwebsite' + cart.id)
    .find('#qtyKeranjang')
    .html(cart.quantity + ' x');
  }
}

function changeVariation(id) {
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/web/shop/variation-detail/' + id,
      type: 'GET',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        Accept: 'application/json',
        'Content-Type': 'application/json',
        timeout: 0,
      },
      success: function (data, json, errorThrown) {
        if (data.status == true) {
          $('.in-stock').html(
            formatRupiah(data.data.stock.toString()) + ' Tersedia',
          )
          $('#variationID').val(data.data.id)
          $('.list-filter').find('.active').removeClass('active')
          $('#listVariant' + data.data.id).addClass('active')
          $('.current-price').html(
            'Rp ' + formatRupiah(data.data.price.toString()),
          )
          $('.title-detail').html(
            data.data.product_name + ' - ' + data.data.name,
          )
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
}

function deleteCart(id) {
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
          // For Website
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

function checkCart() {
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/web/shop/cart',
      type: 'GET',
      success: function (data, json, errorThrown) {
        if (data.status == true) {
          cartWebsite(data)
          cartMobile(data)
        }
      },
    })
  }, 130)
}

function cartWebsite(data) {
  var cartProducts = ''
  $('#proCount').html(data.total)
  $('#totalinCart').html(formatRupiah(data.subtotal.toString()))

  if (data.total > 0) {
    $('#shopCartWebsite').removeClass('d-none')
  }

  $.each(data.cart, function (index, value) {
    cartProducts +=
      ` <li id="cartwebsite` +
      value.id +
      `">
          <div class="shopping-cart-img">
                <a href="` +
      value.url_product +
      `"><img alt="` +
      value.product_name +
      `" src="` +
      value.image +
      `" /></a>
          </div>
          <div class="shopping-cart-title">
                <h4 id="titleDatacart"><a href="` +
      value.url_product +
      `">` +
      value.product_name +
      ` ` +
      value.variation_name +
      `</a></h4>
                <h4><span id="qtyKeranjang">` +
      value.quantity +
      ` × </span>` +
      formatRupiah(value.price.toString()) +
      `</h4>
          </div>
          <div class="shopping-cart-delete">
          <a href="javascript:void(0);" onclick="deleteCart(` +
      value.id +
      `)"><i class="fi-rs-cross-small"></i></a>
          </div>
    </li>`
  })

  $('.cart_website_no').after(cartProducts)
}

function cartMobile(data) {
  var cartProducts = ''
  $('#proCountMobile').html(data.total)
  $('#totalinCartMobile').html(formatRupiah(data.subtotal.toString()))

  if (data.total > 0) {
    $('#shopCartMobile').removeClass('d-none')
  }

  $.each(data.cart, function (index, value) {
    cartProducts +=
      ` <li id="cartwebsite` +
      value.id +
      `">
          <div class="shopping-cart-img">
                <a href="` +
      value.url_product +
      `"><img alt="` +
      value.product_name +
      `" src="` +
      value.image +
      `" /></a>
          </div>
          <div class="shopping-cart-title">
                <h4 id="titleDatacart"><a href="` +
      value.url_product +
      `">` +
      value.product_name +
      ` ` +
      value.variation_name +
      `</a></h4>
                <h4><span id="qtyKeranjang">` +
      value.quantity +
      ` × </span>` +
      formatRupiah(value.price.toString()) +
      `</h4>
          </div>
          <div class="shopping-cart-delete">
          <a href="javascript:void(0);" onclick="deleteCart(` +
      value.id +
      `)"><i class="fi-rs-cross-small"></i></a>
          </div>
    </li>`
  })

  $('.cart_website_no_mobile').after(cartProducts)
}

function formatRupiah(angka, prefix) {
  var number_string = angka.replace(/[^0-9\.]/g, '').toString(),
    titik = number_string.split('.'),
    split = titik[0].split(','),
    sisa = split[0].length % 3,
    rupiah = split[0].substr(0, sisa),
    ribuan = split[0].substr(sisa).match(/\d{3}/gi)

  if (ribuan) {
    separator = sisa ? ',' : ''
    rupiah += separator + ribuan.join(',')
  }

  rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah
  rupiah = titik[1] != undefined ? rupiah + '.' + titik[1] : rupiah
  return prefix == undefined ? rupiah : rupiah ? rupiah : ''
}
