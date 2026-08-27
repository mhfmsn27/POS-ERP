// Create Slider
$('form#cSliders').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/ecommerce/media-content/sliders/store',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        console.log(data)
        if (data.status == false) {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message,
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: close_lang,
              showCancelButton: false,
            },
            function (isConfirm) {
              if (isConfirm) {
                $('#openModal').on('click')
              }
            },
          )
          spinner.hide()
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
          $('#title').val('')
          $('#subtitle').val('')
          $('#button_url').val('')
          $('#button_name').val('')
          $('#buttonSlider').val('no').trigger('change')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

$('#buttonSlider').on('change', function () {
  if ($(this).val() == 'yes') {
    $('.button-text').removeClass('d-none')
    $('.button-url').removeClass('d-none')
  } else {
    $('.button-text').addClass('d-none')
    $('.button-url').addClass('d-none')
  }
})

// Update Slider
$('form#uSliders').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/ecommerce/media-content/sliders/edit/' +
        $('#idSlider').val(),
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        console.log(data)
        if (data.status == false) {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message,
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: close_lang,
              showCancelButton: false,
            },
            function (isConfirm) {
              if (isConfirm) {
                $('#openModal').on('click')
              }
            },
          )
          spinner.hide()
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

          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Create Banner
$('form#cBanner').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/ecommerce/media-content/banners/store',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        console.log(data)
        if (data.status == false) {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message,
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: close_lang,
              showCancelButton: false,
            },
            function (isConfirm) {
              if (isConfirm) {
                $('#openModal').on('click')
              }
            },
          )
          spinner.hide()
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
          $('#title').val('')
          $('#button_url').val('')
          $('#button_name').val('')
          $('#buttonSlider').val('no').trigger('change')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update banners
$('form#uBanner').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/ecommerce/media-content/banners/edit/' +
        $('#idBanner').val(),
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        console.log(data)
        if (data.status == false) {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message,
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: close_lang,
              showCancelButton: false,
            },
            function (isConfirm) {
              if (isConfirm) {
                $('#openModal').on('click')
              }
            },
          )
          spinner.hide()
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

          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Create Banner
$('form#cFeatured').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/ecommerce/media-content/featured/store',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        console.log(data)
        if (data.status == false) {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message,
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: close_lang,
              showCancelButton: false,
            },
            function (isConfirm) {
              if (isConfirm) {
                $('#openModal').on('click')
              }
            },
          )
          spinner.hide()
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
          $('#title').val('')
          $('#subtitle').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update banners
$('form#uFeatured').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/ecommerce/media-content/featured/edit/' +
        $('#idFeatured').val(),
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        console.log(data)
        if (data.status == false) {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message,
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: close_lang,
              showCancelButton: false,
            },
            function (isConfirm) {
              if (isConfirm) {
                $('#openModal').on('click')
              }
            },
          )
          spinner.hide()
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

          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})


// Create Category Blog
$('form#cBlog').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/ecommerce/blogs/categories/store',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        console.log(data)
        if (data.status == false) {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message,
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: close_lang,
              showCancelButton: false,
            },
            function (isConfirm) {
              if (isConfirm) {
                $('#openModal').on('click')
              }
            },
          )
          spinner.hide()
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
          $('#name').val('') 
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update banners
$('form#uBlog').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/ecommerce/blogs/categories/edit/' +
        $('#idBlog').val(),
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        console.log(data)
        if (data.status == false) {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message,
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: close_lang,
              showCancelButton: false,
            },
            function (isConfirm) {
              if (isConfirm) {
                $('#openModal').on('click')
              }
            },
          )
          spinner.hide()
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

          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})


// Create Category Blog
$('form#cKurir').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/ecommerce/settings/curir/store',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        console.log(data)
        if (data.status == false) {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message,
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: close_lang,
              showCancelButton: false,
            },
            function (isConfirm) {
              if (isConfirm) {
                $('#openModal').on('click')
              }
            },
          )
          spinner.hide()
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
          $('#name').val('') 
          $('#code').val('') 

          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update banners
$('form#uKurir').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/ecommerce/settings/curir/edit/' +
        $('#idKurir').val(),
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        console.log(data)
        if (data.status == false) {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message,
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: close_lang,
              showCancelButton: false,
            },
            function (isConfirm) {
              if (isConfirm) {
                $('#openModal').on('click')
              }
            },
          )
          spinner.hide()
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

          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})
