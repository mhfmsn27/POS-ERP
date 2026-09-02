// Loader

document.onreadystatechange = function() {
  if (document.readyState !== "complete") {
      document.querySelector("body").style.visibility = "hidden";
      document.querySelector("#loading").style.visibility = "visible";
  } else {
      document.querySelector("#loading").style.display = "none";
      document.querySelector("body").style.visibility = "visible";
  }
};

/**
 *  Offline & Online Detection
 */
window.addEventListener('online', function () {
  toastr.success(internet_connected, {
    positionClass: 'toast-bottom-left',
    timeOut: 5e3,
    closeButton: !0,
    debug: !1,
    newestOnTop: !0,
    progressBar: !0,
    preventDuplicates: !0,
    onclick: null,
    showDuration: '300',
    hideDuration: '1000',
    extendedTimeOut: '1000',
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut',
    tapToDismiss: !1,
  })
  playSound(domainpath + '/public/sound/connection')
})

window.addEventListener('offline', function () {
  toastr.error(offline_internet, {
    positionClass: 'toast-top-right',
    timeOut: 5e3,
    closeButton: !0,
    debug: !1,
    newestOnTop: !0,
    progressBar: !0,
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
  playSound(domainpath + '/public/sound/connection')
})

var pageName = $('#pageName').html()
$('.pageName').html(pageName)
// $('.subPageName').html(pageName)
/**
 *  DASHBOARD ATTENDANCE
 */
$('#checkint_attendance').on('click', function () {
  $.ajax({
    url: domain + domainpath + '/pos-admin/hrm/checkint',
    type: 'GET',
    data: '',
    success: function (data) {
      if (data.message == 'success') {
        toastr.success('Berhasil', data.success, {
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
        $('#checkint_attendance').addClass('d-none')
        $('#checkout_attendance').removeClass('d-none')
      }

      if (data.message == 'min-check-int') {
        toastr.error(data.errors, {
          positionClass: 'toast-top-right',
          timeOut: 5e3,
          closeButton: !0,
          debug: !1,
          newestOnTop: !0,
          progressBar: !0,
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

      if (data.message == 'late') {
        toastr.error(data.errors, {
          positionClass: 'toast-top-right',
          timeOut: 5e3,
          closeButton: !0,
          debug: !1,
          newestOnTop: !0,
          progressBar: !0,
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

      if (data.message == 'employee-not-found') {
        toastr.error(data.errors, {
          positionClass: 'toast-top-right',
          timeOut: 5e3,
          closeButton: !0,
          debug: !1,
          newestOnTop: !0,
          progressBar: !0,
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
  playSound('/sound/beep-29')
})

$('#checkout_attendance').on('click', function () {
  $.ajax({
    url: domain + domainpath + '/pos-admin/hrm/checkout',
    type: 'GET',
    data: '',
    success: function (data) {
      if (data.message == 'success') {
        toastr.success(data.success, {
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
        $('#checkout_attendance').addClass('d-none')
        $('#attendance_clear').removeClass('d-none')
      }

      if (data.message == 'min') {
        toastr.error(data.errors, {
          positionClass: 'toast-top-right',
          timeOut: 5e3,
          closeButton: !0,
          debug: !1,
          newestOnTop: !0,
          progressBar: !0,
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
  playSound('/sound/beep-29')
})

/**
 *  Update Profile
 */
$('form#uProfile').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/auth/change-profile',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: 'Error', 
              html: data.errors,
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
          toastr.success(success, {
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

// Update Profile
$('form#uPassword').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/auth/change-profile',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: 'Error', 
              html: data.errors,
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
          toastr.success(success, {
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

$('form#uPassword').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/auth/change-profile',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
        } else if (data.message == 'password') {
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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

/**
 *  Category function
 */

// Create category
$('form#cCategory').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/category/category-store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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
          $('#detail').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Category
$('form#uCategory').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/category/category-store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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

// Create Subcategory
$('form#cSubcategory').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/category/category-store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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
          $('#detail').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Subcategory
$('form#uSubcategory').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/category/category-store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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

/**
 *  Supplier
 */

// Create Supplier
$('form#cSupplier').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/supplier/supplier-store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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
          $('#phone').val('')
          $('#email').val('')
          $('#code').val('')
          $('#city').val('')
          $('#address').val('')
          $('#detail').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Supplier
$('form#uSupplier').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/supplier/supplier-store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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

/**
 *  Customer
 */

// Create Customer
$('form#cCustomer').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/customer/customer-store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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
          $('#phone').val('')
          $('#email').val('')
          $('#code').val('')
          $('#city').val('')
          $('#state').val('')
          $('#address').val('')
          $('#detail').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Customer
$('form#uCustomer').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/customer/customer-store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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

/**
 *  Expense Category function
 */

// Create Expense Category
$('form#cExca').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/expense-category/category-store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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
          $('#detail').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Expense Category
$('form#uExca').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/expense-category/category-store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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

// Create Expense Sub Category
$('form#cExsub').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/expense-category/category-store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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
          $('#detail').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Expense Sub Category
$('form#uExsub').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url:
        domain +
        domainpath +
        '/pos-admin/expense-category/category-store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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

 

// Update Expense Category
$('form#uExpense').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/expense/store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
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
          toastr.success(success, {
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
/**
 *  Brand
 */

// Create Brand
$('form#cBrandForm').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/product/brand-store/create',
      type: 'POST',
      data: formData,
      success: function (data) {
        toastr.success(success, {
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
        $('#detail').val('')
        spinner.hide()
      },
      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Brand
$('form#uBrandForm').submit(function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/product/brand-store/update',
      type: 'POST',
      data: formData,
      success: function (data) {
        toastr.success(success, {
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
      },
      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

/**
 *  Unit
 */

// Create Unit
$('form#cUnit').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/product/unit-store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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
          $('#detail').val('')
          $('#value').val('')

          var parent = $('#is_root_parent')
          if (parent.is(':checked')) {
            $('#parentUnit').addClass('d-none')
            parent.prop('checked', false)
          }

          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Unit
$('form#uUnit').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/product/unit-store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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

 

/**
 *  Rak
 */

// Create Rak
$('form#cRak').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/product/rak-store/create',
      type: 'POST',
      data: formData,
      success: function (data) {
        toastr.success(success, {
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
        $('#floor').val('')
        $('#room').val('')
        $('#rak').val('')
        spinner.hide()
      },
      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Brand
$('form#uRak').submit(function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/product/rak-store/update',
      type: 'POST',
      data: formData,
      success: function (data) {
        toastr.success(success, {
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
      },
      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

/**
 *  Taxrate
 */

// Create
$('form#cTax').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    console.log(try_again)
    $.ajax({
      url: domain + domainpath + '/pos-admin/company/taxrates/store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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
          $('#taxrate').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update
$('form#uTax').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/company/taxrates/store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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

/**
 *  Printer
 */

// Change Type Option
$("select[name='type']").on('change', function () {
  if ($(this).val() == 'online') {
    $('#label-url').removeClass('d-none')
    $('#form-url').removeClass('d-none')
  } else {
    $('#label-url').addClass('d-none')
    $('#form-url').addClass('d-none')
  }
})

// Create
$('form#cPrinter').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/company/printers/store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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
          $('#path').val('')
          $('#char_per_line').val('')
          $('#ip_address').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update
$('form#uPrinter').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/company/printers/store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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

/**
 *  Role Permission
 */
$('.edit_permission').on('click', '.role_permission', function () {
  permission_modal = $(this).closest('.edit_permission')
  $('#permission_id').val(permission_modal.find('#pi').html())
  $('#permission_name').val(permission_modal.find('#pn').html())
  document.getElementById('update_c').click()
})

/**
 *  Country
 */
$('.edit_country').on('click', '.country', function () {
  country_modal = $(this).closest('.edit_country')
  $('#country_id').val(country_modal.find('#ci').html())
  $('#country_name').val(country_modal.find('#cn').html())
  document.getElementById('update_c').click()
})

/**
 *  Timezone
 */
$('.edit_timezone').on('click', '.timezone', function () {
  country_modal = $(this).closest('.edit_timezone')
  $('#timezone_id').val(country_modal.find('#ti').html())
  $('#timezone_name').val(country_modal.find('#tn').html())
  document.getElementById('update_t').click()
})

/**
 *  Currcency
 */
$('.edit_currency').on('click', '.currency', function () {
  country_modal = $(this).closest('.edit_currency')
  $('#currency_id').val(country_modal.find('#ci').html())
  $('#currency_name').val(country_modal.find('#cn').html())
  $('#currency_code').val(country_modal.find('#cd').html())
  $('#currency_c').val(country_modal.find('#cr').html())
  $('#currency_cd').val(country_modal.find('#cdd').html())
  document.getElementById('update_c').click()
})

/**
 *  Permission
 */
$('.edit_permission').on('click', '.permission', function () {
  country_modal = $(this).closest('.edit_permission')
  $('#permission_id').val(country_modal.find('#pi').html())
  $('#permission_name').val(country_modal.find('#pn').html())
  document.getElementById('update_p').click()
})

/**
 *  Bank
 */
$('.bank').on('click', '.updatebank', function () {
  bank = $(this).closest('.bank')
  $('#bank_id').val(bank.find('#ci').html())
  $('#bank_name').val(bank.find('#cn').html())
  $('#bank_code').val(bank.find('#cd').html())
  document.getElementById('update_c').click()
})

/**
 *  Purchase Transaction
 */

// For Filter Search
 $("select[name='chooseFilter']").change(function() {
  if ($(this).val() == 'multiple') {
      $("#startDate").removeClass("d-none");
      $("#endDate").removeClass("d-none");
      $("#dateNow").addClass("d-none");
  } else if ($(this).val() == 'single') {
      $("#startDate").addClass("d-none");
      $("#endDate").addClass("d-none");
      $("#dateNow").removeClass("d-none");
  } else {
      $("#startDate").addClass("d-none");
      $("#endDate").addClass("d-none");
      $("#dateNow").addClass("d-none");
  }
});

// For Payment Purchase
function getpaymentmodal_purchase(id) {
  $.ajax({
      url: domain + domainpath + "/pos-admin/purchase/getElementpo/" + id,
      type: 'GET',
      data: '',
      success: function(data) {
          $("#tri").val(id);
          $("#maxPayment").val(data.max_amount); 
          $("#addpay").modal("show")
      },

      cache: false,
      contentType: false,
      processData: false
  }); 
}

// For Payment Return
function getpaymentmodalReturn(id) {
  $.ajax({
      url: domain + domainpath + "/pos-admin/return/getElementreturn/" + id,
      type: 'GET',
      data: '',
      success: function(data) {
          $("#tri").val(id);
          $("#maxPayment").val(data.max_amount); 
          $("#addpay").modal("show")
      },

      cache: false,
      contentType: false,
      processData: false
  }); 
}

// For Show Payment
function showPayment_(id) {
  $.ajax({
      url: domain + domainpath + "/pos-admin/purchase/show-payment/" + id,
      type: 'GET',
      data: '',
      success: function(data, json, errorThrown) {
          var dataContent = '';
          var color = ''
          var status = ''
          $.each(data.payment, function(index, value) {

              if (index % 2 === 0) {
                  color = 'table-info'
              } else {
                  color = 'table-success'
              }
              
              if (value.account == null) {
                  status = '<span class="badge bg-danger text-white">Belum Terkoneksi</span>'
              } else {
                  status = '<span class="badge bg-primary text-white">' + value.account + '</span>'
              }

              dataContent += `<tr class="` + color + `"><td>` + value.date + `</td><td>` + value.user + `</td><td>` + value.amount + `</td><td>` + value.method + `</td><td>` + status + `</td></tr>`;
          })
          $("#paymentList").append(dataContent);
          $("#showPayment").modal("show")
      },

      cache: false,
      contentType: false,
      processData: false
  });

}

/**
 *  Designation
 */

// Create Designation
$('form#cDesignation').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/company/designations/store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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

// Update Designation
$('form#uDesignation').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/company/designations/store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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

/**
 *  Allowance
 */

// Create Allowance
$('form#cAllowance').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/buku-besar/allowances/store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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
          $('#amount').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Allowance
$('form#uAllowance').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/buku-besar/allowances/store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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

/**
 *  CUTTING SALARY
 */

// Create Allowance
$('form#cCutting').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/buku-besar/cutting/store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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
          $('#amount').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Allowance
$('form#uCutting').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/buku-besar/cutting/store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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
 


$(".payment_modal_").on("keyup", function() {
  var nominal = $("#payment_amount").val();
  var amount = $("#maxPayment").val();
  $("#payment_amount").val(formatRupiah(nominal.toString()))

  if (parseInt(nominal.replace(/[^0-9]/g, '').toString()) > amount) {
      Swal.fire({
          title: "Peringatan",
          text: "Jumlah Pembayaran Tidak Boleh melebihi Harga Total",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Ok, Mengerti"
      }).then(result => {
          $(this).find("input#payment_amount").val(formatRupiah(amount.toString())); 
      });
  }  
});


/**
 *  Setting
 */
$('form#uSetting').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/preferensi/settings-store',
      type: 'POST',
      data: formData,
      success: function (data) {
        toastr.success(success, {
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
      },
      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

$('form#transactionKeyForm').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/system/key-store',
      type: 'POST',
      data: formData,
      success: function (data) {
        toastr.success(success, {
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
      },
      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

/**
 *  Store
 */

// Create
$('form#cStore').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/store/store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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
          $('#email').val('')
          $('#phone').val('')
          $('#zip_code').val('')
          $('#tax').val(0)
          $('#zakat').val(0)
          $('#address').val('')
          $('#footer_text').val('')
          $('#gst').val('')
          $('#vat').val('')
          spinner.hide()
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update
$('form#uStore').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/store/store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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

/**
 *  Users
 */

// Create Users
$('form#cUsers').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/preferensi/users/user-store',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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
          $('#email').val('')
          $('#password').val('')
          $('#commission_percentase').val(0)
          $('#max_commission').val(0)
          spinner.hide()
        }
      },
      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Users
$('form#uUsers').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/preferensi/users/user-update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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

/**
 *  Employee
 */

// Create Employee
$('form#cEmployee').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/company/employees/store/create',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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
          $('#date_birth').val('')
          $('#salary').val('')
          $('#phone').val('')
          $('#commission_percentase').val(0)
          $('#max_commission').val(0)
          $('#address').val('')
          $('#about').val('')
          spinner.hide()
        }
      },
      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Update Users
$('form#uEmployee').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/company/employees/store/update',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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

/**
 *  HRM SETTING
 */

$('form#uHrm').on('submit', function (e) {
  spinner.show()
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/pos-admin/preferensi/hrm-store',
      type: 'POST',
      data: formData,
      success: function (data, json, errorThrown) {
        if (data.message == 'error') {
          var errorsHtml = ''
          $.each(data.errors, function (index, value) {
            errorsHtml +=
              '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
              value +
              '</li></ul>'
          })
          Swal.fire(
            {
              title: data.message + ' ' + data.status, 
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: try_again,
              cancelButtonText: 'Cancel',
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
          toastr.success(success, {
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

// Datatable for get data column

function datatable_poshub_callback(data) {
  for (var i = 0, len = data.columns.length; i < len; i++) {
    if (!data.columns[i].search.value) delete data.columns[i].search
    if (data.columns[i].searchable === true) delete data.columns[i].searchable
    if (data.columns[i].orderable === true) delete data.columns[i].orderable
    if (data.columns[i].data === data.columns[i].name)
      delete data.columns[i].name
  }
  delete data.search.regex

  return data
}

function datatable_mdhpos_callback(data) {
  return datatable_poshub_callback(data);
}

/**
 *  Update Margin Percetace
 */

const rupiah = (total) => {
  return new Intl.NumberFormat({
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(total)
}

function formatRupiah(angka, prefix) {
  var number_string = angka.replace(/[^.\d]/g, '').toString(),
    split = number_string.split(','),
    sisa = split[0].length % 3,
    rupiah = split[0].substr(0, sisa),
    ribuan = split[0].substr(sisa).match(/\d{3}/gi)

  // tambahkan titik jika yang di input sudah menjadi angka ribuan
  if (ribuan) {
    separator = sisa ? ',' : ''
    rupiah += separator + ribuan.join(',')
  }

  rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah
  return prefix == undefined ? rupiah : rupiah ? rupiah : ''
}

$("select[name='unit_']").on('change', function () {
  var url = domainpath + '/pos-admin/product/getUnitSub/' + $(this).val()
  $("select[name='unit_penjualan_']").load(url)
  $("select[name='unit_pembelian_']").load(url)
  return false
})

$('body').on('change', '.variant-0', function () {
  var head = $(this)
  $(this)
    .find('select#unitVariant')
    .on('change', function (e) {
      var url = domainpath + '/pos-admin/product/getUnitSub/' + $(this).val()
      head.find('select#unitSaleVariant').load(url)
      head.find('select#unitPoVariant').load(url)
      return false
    })
  return false 
})

$('body').on('change', '.variant', function () {
    console.log("hai")
  var head = $(this)
  $(this)
    .find('select#unitVariant')
    .on('change', function (e) {
        
      var url = domainpath + '/pos-admin/product/getUnitSub/' + $(this).val()
      head.find('select#unitSaleVariant').load(url)
      head.find('select#unitPoVariant').load(url)
      return false
    })
  return false
})

$('.variant-0').on('keyup', function (e) {
  var p_price = $(this).find('input#purchase_price').val()
  var s_price = $(this).find('input#selling_price').val()
  var g_price = $(this).find('input#sp_grocery').val()

  var countPercentase =
    (parseInt(s_price.replace(/[^0-9]/g, '').toString()) /
      parseInt(p_price.replace(/[^0-9]/g, '').toString())) *
      100 -
    100

  var countPercentaseG =
    (parseInt(g_price.replace(/[^0-9]/g, '').toString()) /
      parseInt(p_price.replace(/[^0-9]/g, '').toString())) *
      100 -
    100
  $(this).find('input#margin').val(countPercentase.toFixed(0))
  $(this).find('input#margin_grocery').val(countPercentaseG.toFixed(0))
})

$('.single_product').on('keyup', function (e) {
  var p_price = $(this).find('input#p_price').val()
  var s_price = $(this).find('input#s_price').val()
  var g_price = $(this).find('input#s_price_grocery').val()

  $(this).find('input#p_price').val(formatRupiah(p_price))
  $(this).find('input#s_price').val(formatRupiah(s_price))
  $(this).find('input#s_price_grocery').val(formatRupiah(g_price))
  var countPercentase =
    (parseInt(s_price.replace(/[^0-9]/g, '').toString()) /
      parseInt(p_price.replace(/[^0-9]/g, '').toString())) *
      100 -
    100
  var countGroceryMargin =
    (parseInt(g_price.replace(/[^0-9]/g, '').toString()) /
      parseInt(p_price.replace(/[^0-9]/g, '').toString())) *
      100 -
    100
  $(this).find('input#margin').val(countPercentase.toFixed(0))
  $(this).find('input#margin_grocery').val(countGroceryMargin.toFixed(0))
})

$('body').on('keyup', '.variant', function () {
  var p_price = $(this).find('input#purchase_price').val()
  var s_price = $(this).find('input#selling_price').val()
  var g_price = $(this).find('input#sp_grocery').val()
  $(this).find('input#purchase_price').val(formatRupiah(p_price))
  $(this).find('input#selling_price').val(formatRupiah(s_price))
  $(this).find('input#sp_grocery').val(formatRupiah(g_price))
  var countPercentase =
    (parseInt(s_price.replace(/[^0-9]/g, '').toString()) /
      parseInt(p_price.replace(/[^0-9]/g, '').toString())) *
      100 -
    100
  var countPercentaseG =
    (parseInt(g_price.replace(/[^0-9]/g, '').toString()) /
      parseInt(p_price.replace(/[^0-9]/g, '').toString())) *
      100 -
    100
  $(this).find('input#margin').val(countPercentase.toFixed(0))
  $(this).find('input#margin_grocery').val(countPercentaseG.toFixed(0))
})

$('#get_sku').on('click', function (length) {
  $('#product_sku').val(getSku(6))
})

$('#getrest').on('click', function (length) {
  $('#restapi').val(getSku(12))
})

function getSku(length) {
  var result = []
  var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
  var charactersLength = characters.length
  for (var i = 0; i < length; i++) {
    result.push(characters.charAt(Math.floor(Math.random() * charactersLength)))
  }
  return result.join('')
}

/**
 *  Playsound Function
 */
function playSound(filename) {
  var mp3Source = '<source src="' + filename + '.mp3" type="audio/mpeg">'
  var embedSource =
    '<embed hidden="true" autostart="true" loop="false" src="' +
    filename +
    '.mp3">'
  document.getElementById('sound').innerHTML =
    '<audio autoplay="autoplay">' + mp3Source + embedSource + '</audio>'
}

/**
 * Global DataTable Helper for POSHUB Enterprise Backoffice
 * Seamlessly manages filter parameters, pagination state, and search queries
 */
function datatable_poshub_callback(d) {
  if (!d) d = {};
  if (typeof $ !== 'undefined') {
    if ($('#store').length && $('#store').val()) d.store = $('#store').val();
    if ($('#status').length && $('#status').val()) d.status = $('#status').val();
    if ($('#start_date').length && $('#start_date').val()) d.start_date = $('#start_date').val();
    if ($('#end_date').length && $('#end_date').val()) d.end_date = $('#end_date').val();
    if ($('#date_now').length && $('#date_now').val()) d.date_now = $('#date_now').val();
  }
  return d;
}

// Backward compatibility alias
var datatable_mdhpos_callback = datatable_poshub_callback;

