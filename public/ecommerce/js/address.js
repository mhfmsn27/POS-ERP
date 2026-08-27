$(document).ready(function() {
  $('.choose_province').select2()
  $('.choose_city').select2()
  $('.choose_district').select2()

  $('#provinceData').select2({
        placeholder: 'Pilih Provinsi...',
        ajax: {
              url: domainpath + '/web/location/provinces',
              dataType: 'json',
              delay: 250,
              processResults: function(data) {
                    return {
                          results: $.map(data, function(item) {
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

  getAddress()
})

$("select[name='get_province']").on('change', function(e) {
  $('#cityData').val('')
  $('#districtData').val('')

  console.log("ini ke trigger cuk", $(this).val())
  $('#cityData').select2({
        placeholder: 'Pilih Kota / Kabupaten...',
        ajax: {
              url: domainpath + '/web/location/cities?province=' + $(this).val(),
              dataType: 'json',
              delay: 250,
              processResults: function(data) {
                    return {
                          results: $.map(data, function(item) {
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
})

$("select[name='get_city']").on('change', function(e) {
  $('#districtData').val('')

  $('#districtData').select2({
        placeholder: 'Pilih Kecamatan...',
        ajax: {
              url: domainpath + '/web/location/district?city=' + $(this).val(),
              dataType: 'json',
              delay: 250,
              processResults: function(data) {
                    return {
                          results: $.map(data, function(item) {
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
})

$('#createDataAddress').on('click', function() {
  var sendData = {
        name: $('#addressName').val(),
        sub_district_id: $('#districtData').val(),
        address: $('#fullAddress').val(),
        postal_code: $('#postalCode').val(),
        phone: $('#phoneAddress').val(),
        default: $('#defaultOption').val(),
  }

  setTimeout(function() {
        $.ajax({
              url: domain + domainpath + '/web/account/address/store',
              type: 'POST',
              headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    timeout: 0,
              },
              data: JSON.stringify(sendData),
              success: function(data, json, errorThrown) {
                    if (data.status == false) {
                          Swal.fire({
                                title: 'Error',
                                html: data.message,
                                width: 'auto',
                                confirmButtonText: 'Ok Saya Mengerti',
                                showCancelButton: false,
                          })
                    } else {
                          $('.listDataAddress').html(
                                '<div class="col-12 actionButtonAddress"></div>',
                          )

                          getAddress()
                          refreshData()
                          listAddress()

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

$('#updateDataAddress').on('click', function() {
  var sendData = {
        name: $('#addressName').val(),
        sub_district_id: $('#districtData').val(),
        address: $('#fullAddress').val(),
        postal_code: $('#postalCode').val(),
        phone: $('#phoneAddress').val(),
        default: $('#defaultOption').val(),
  }

  setTimeout(function() {
        $.ajax({
              url: domain +
                    domainpath +
                    '/web/account/address/update/' +
                    $('#addressID').val(),
              type: 'POST',
              headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    timeout: 0,
              },
              data: JSON.stringify(sendData),
              success: function(data, json, errorThrown) {
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
                          $('.listDataAddress').html(
                                '<div class="col-12 actionButtonAddress"></div>',
                          )

                          getAddress()
                          refreshData()
                          listAddress()

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

function getAddress() {
  setTimeout(function() {
        $.ajax({
              url: domain + domainpath + '/web/account/address',
              type: 'GET',
              success: function(data, json, errorThrown) {
                    var addressData = ''
                    if (data.status == true) {
                          $.each(data.data, function(index, value) {
                                var defaultData = ''

                                if (value.default == 'yes') {
                                      defaultData = ` <div class="vendor-img-action-wrap">
                                                              <div class="product-badges product-badges-position product-badges-mrg">
                                                                    <span class="hot">Utama</span>
                                                              </div>
                                                        </div>`
                                }
                                addressData +=
                                      `<div class="col-lg-6 col-md-6 col-12 col-sm-6" id="addressData` +
                                      value.id +
                                      `">
                                                  <div class="vendor-wrap mb-40">
                                                       ` +
                                      defaultData +
                                      `
                                                        <div class="vendor-content-wrap">
                                                              <div class="d-flex justify-content-between align-items-end mb-30">
                                                                    <div>
                                                                          <h4 class="mb-5"><a href="javascript:void(0);" id="addressDataName">` +
                                      value.name +
                                      `</a></h4>
                                                                    </div>
                                                                    <div class="mb-10">
                                                                          <span class="font-small postalDataCode">Kode Pos : ` +
                                      value.postal_code +
                                      `</span>
                                                                    </div>
                                                              </div>
                                                              <div class="vendor-info mb-30">
                                                                    <ul class="contact-infor text-muted">
                                                                          <li><img src="assets/imgs/theme/icons/icon-location.svg" alt=""><strong>Provinsi: </strong> <span id="provinceDataAddress">` +
                                      value.province.name +
                                      `</span></li>
                                                                          <li><img src="assets/imgs/theme/icons/icon-location.svg" alt=""><strong>Kota / Kab: </strong> <span id="cityDataAddress">` +
                                      value.city.name +
                                      `</span></li>
                                                                          <li><img src="assets/imgs/theme/icons/icon-location.svg" alt=""><strong>Kecamatan: </strong> <span id="kecamatanDataAddress">` +
                                      value.sub_district.name +
                                      `</span></li>
                                                                          <li><img src="assets/imgs/theme/icons/icon-location.svg" alt=""><strong>Alamat Lengkap: </strong> <span id="addressDataAddress">` +
                                      value.address +
                                      `</span></li>
                                                                          <li><img src="assets/imgs/theme/icons/icon-contact.svg" alt=""><strong>Nomor Ponsel:</strong><span id="phoneDataAddress">` +
                                      value.phone +
                                      `</span></li>
                                                                    </ul>
                                                              </div>
                                                              <a href="javascript:void(0);" onclick="updateDataAddress(` +
                                      value.id +
                                      `)" class="btn bg-warning btn-xs"><i class="fi-rs-pencil mr-4"></i> Edit Alamat </a>
                                                              <a href="javascript:void(0);" onclick="deleteDataAddress(` +
                                      value.id +
                                      `)" class="btn bg-danger btn-xs"><i class="fi-rs-trash mr-4"></i> Hapus Alamat </a>
                                                        </div>
                                                  </div>
                                            </div>`
                          })

                          $('.actionButtonAddress').after(addressData)
                    }
              },
        })
  }, 130)
}

function deleteDataAddress(id) {
  Swal.fire({
        title: 'Apakah Anda Yakin ?',
        text: 'Data Alamat yang telah di hapus tidak dapat di kembalikan lagi',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Asyiaap',
  }).then((result) => {
        if (result.value) {
              setTimeout(function() {
                    $.ajax({
                          url: domain + domainpath + '/web/account/address/delete/' + id,
                          type: 'DELETE',
                          headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                timeout: 0,
                          },
                          success: function(data, json, errorThrown) {
                                if (data.status == true) {
                                      $('#addressData' + id).remove()

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

function updateDataAddress(id) {
  $.ajax({
        url: domain + domainpath + '/web/account/address/detail/' + id,
        type: 'GET',

        success: function(data, json, errorThrown) {
              if (data.status == true) {
                    
                    var address = data.data

                    $('#textTitleAddress').html('Edit Alamat')
                    $('#addressID').val(address.id)
                    $('#addressName').val(address.name)

                    $('#postalCode').val(address.postal_code)
                    $('#phoneAddress').val(address.phone)
                    $('#fullAddress').val(address.address)
                    $('#updateDataAddress').removeClass('d-none')
                    $('#createDataAddress').addClass('d-none')

                    $('#addressTabs').removeClass('active')
                    $('#addressTabs').removeClass('show')
                    $('#listAddressTabs').addClass('active')
                    $('#listAddressTabs').addClass('show')


                    $("select[name='get_province']").val(address.province.id).trigger('change');
                    $("select[name='get_city']").val(address.city.id).trigger('change');
                    $("#districtData").val(address.sub_district.id);


              }
        },

        cache: false,
        contentType: false,
        processData: false,
  })
}

function addAddress() {
  $('#addressTabs').removeClass('active')
  $('#addressTabs').removeClass('show')
  $('#listAddressTabs').addClass('active')
  $('#listAddressTabs').addClass('show')

  refreshData()
}

function listAddress() {
  $('#listAddressTabs').removeClass('active')
  $('#listAddressTabs').removeClass('show')
  $('#addressTabs').addClass('active')
  $('#addressTabs').addClass('show')
}

function refreshData() {
  $('#textTitleAddress').html('Tambah Alamat Baru')
  $('#addressID').val('')
  $('#addressName').val('')
  $('#provinceData').val('')
  $('#cityData').val('')
  $('#districtData').val('')
  $('#postalCode').val('')
  $('#phoneAddress').val('')
  $('#fullAddress').val('')
  $('#updateDataAddress').addClass('d-none')
  $('#createDataAddress').removeClass('d-none')

  $("select[name='get_province']").trigger('change')
  $("select[name='get_city']").trigger('change')
}