$('form#addCategoryStore').on('submit', function (e) {
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
              var urlchangecategory = domainpath + '/pos-admin/product/category'
              $("select[name='category']").load(urlchangecategory)  
              $("#createCategory").modal("hide")
            }
          },
    
          cache: false,
          contentType: false,
          processData: false,
        })
      }, 130)
    })