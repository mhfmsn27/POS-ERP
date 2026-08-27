$(document).ready(function () {
 
  $('#amount').on('keyup', function () {
    var amount = $('#amount').val()
    $('#amount').val(formatRupiah(amount.toString()))
    var amount = $('#amount').val()
  })
})

/**
 *  Expense Category function
 */

// Create Expense Category
$('form#mobileExpense').on('submit', function (e) {
  console.log('hai')
  e.preventDefault()
  var formData = new FormData(this)
  setTimeout(function () {
    $.ajax({
      url: domain + domainpath + '/mobile/expense/store/create',
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
              title: 'Terjadi Kesalahan', // this will output "Error 422: Unprocessable Entity"
              html: errorsHtml,
              width: 'auto',
              confirmButtonText: 'Coba Lagi',
              cancelButtonText: 'Tutup',
              showCancelButton: false,
            },
            function (isConfirm) {
              if (isConfirm) {
                $('#openModal').on('click') //this is when the form is in a modal
              }
            },
          )
        } else if (data.message == 'shift_error') {
          toastr.error(data.errors, 'Failed', {
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
        } else {
          toastr.success('Penambahan Data Pengeluaran berhasil', 'Berhasil', {
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
          $('#detail').val('')
        }
      },

      cache: false,
      contentType: false,
      processData: false,
    })
  }, 130)
})

// Create Expense Category
$('form#mobileExpenseUpdate').on('submit', function (e) {
      console.log('hai')
      e.preventDefault()
      var formData = new FormData(this)
      setTimeout(function () {
        $.ajax({
          url: domain + domainpath + '/mobile/expense/store/update',
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
                  title: 'Terjadi Kesalahan', // this will output "Error 422: Unprocessable Entity"
                  html: errorsHtml,
                  width: 'auto',
                  confirmButtonText: 'Coba Lagi',
                  cancelButtonText: 'Tutup',
                  showCancelButton: false,
                },
                function (isConfirm) {
                  if (isConfirm) {
                    $('#openModal').on('click') //this is when the form is in a modal
                  }
                },
              )
            } else if (data.message == 'shift_error') {
              toastr.error(data.errors, 'Failed', {
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
            } else {
              toastr.success('Edit Data Pengeluaran berhasil', 'Berhasil', {
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

   