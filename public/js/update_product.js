$(document).ready(function() {
    $('.dropify').dropify();

    $('#summernote').summernote({
        tabsize: 2,
        height: 150,
        width: '100%',
    });

    // Category Selected
    $("select[name='category']").on("change",function() {
        var url = domainpath + "/pos-admin/product/getSub/" + $(this).val();
        $("select[name='subcategory']").load(url);
        return false;
    });
    
    // Delete Variation Dom
    $("body").on("click", ".delete_variant", function() {
        $(this).parents(".variant").remove();
    });
 
});


/**
 *  Add Variation Function
 */
 function add_variant() {
    var url = domainpath + '/pos-admin/product/get-attribute'
    $.ajax({
      url: domain + url,
      type: 'GET',
      data: '',
      success: function (data, json, errorThrown) {
        var unit = ''
        var rak = ''
  
        $.each(data.unit, function (i, u) {
          unit += '<option value="' + u.id + '">' + u.name + '</option>'
        })
  
        $.each(data.rak, function (i, r) {
          rak +=
            '<option value="' +
            r.id +
            '">' +
            r.rak +
            ' (' +
            r.floor +
            ' ' +
            rak.room +
            ')</option>'
        })
        
        var cloning = `<div class="row variant mt-3">
        <div class="col-12  d-flex justify-content-end">
        <button type="button" class="btn btn-sm btn-danger mb-3 delete_variant"><i class="fa fa-minus-circle"></i> Hapus Variant</button> 
    </div>
        <div class="col-3 mb-1">
            <div class="form-group has-icon-left">
                <label class="form-label">Nama Variasi Produk</label>
                <div class="position-relative">
                    <input type="hidden" name="variation_id[]">
                    <input type="hidden" name="value_id[]">
                    <input type="text" class="form-control" name="value[]" value="" id="value">
                </div>
            </div>
        </div>
     
        <div class="col-3 mb-1">
            <div class="form-group has-icon-left">
                <label class="form-label">Harga Modal</label>
                <div class="position-relative">
                    <input type="text" class="form-control" name="purchase_price[]" id="purchase_price">
                </div>
            </div>
        </div>
        
        <div class="col-3 mb-1">
            <div class="form-group has-icon-left">
                <label class="form-label">Harga Jual ( Umum / Eceran )</label>
                <div class="position-relative">
                    <input type="text" class="form-control" name="selling_price[]" id="selling_price">
                </div>
            </div>
        </div>
      
  
        <div class="col-3 mb-1">
            <div class="form-group has-icon-left">
                <label class="form-label">Tipe Pajak</label>
                <div class="position-relative">
                    <select class="form-control" name="tax_type[]">
                        <option value="exclusive">Exclusive</option>
                        <option value="inclusive">Inclusive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-3 mb-1">
            <div class="form-group has-icon-left">
                <label class="form-label">Persentase Pajak</label>
                <div class="position-relative">
                    <input type="number" class="form-control" max="100" min="0" value="0" name="taxrate[]" id="taxrate[]">
                </div>
            </div>
        </div>
        <div class="col-3 mb-1">
            <div class="form-group has-icon-left">
                <label class="form-label">Unit Produk</label>
                <div class="position-relative">
                    <select class="form-control" name="unit[]" id="unitVariant">
                        <option value="">Pilih Unit</option>
                        ` +  unit + `
                    </select>
                </div>
            </div>
        </div>
      
  
        <div class="col-3 mb-1">
            <div class="form-group has-icon-left">
                <label class="form-label">Upload Gambar Variant</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="im[]" id="image">
                    <label class="custom-file-label" for="customFile">Upload Gambar</label>
                </div> 
            </div>
        </div>
    </div>`
     
  
        $('.variant-001').before(cloning)
      },
  
      cache: false,
      contentType: false,
      processData: false,
    })
  }
/**
 *  Delete Variation Function
 */ 
function delete_variant(id)
{
    const href = $(this).attr("href");
    Swal.fire({
        title: confirmation,
        text: warning,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: yes_sure
    }).then(result => {
        if (result.value) {
            spinner.show();
            $.ajax({
                url: domain + domainpath + "/pos-admin/product/variation-delete/" + id,
                type: 'GET',
                data: '',
                success: function (data,json, errorThrown) {
                    toastr.success(success, {
                        timeOut: 5e3,
                        closeButton: !0,
                        debug: !1,
                        newestOnTop: !0,
                        progressBar: !0,
                        positionClass: "toast-top-right",
                        preventDuplicates: !0,
                        onclick: null,
                        showDuration: "100",
                        hideDuration: "1000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                        tapToDismiss: !1,
                    });
                    spinner.hide();
                   
                },
              
                cache: false,
                contentType: false,
                processData: false
            });
            $(".variant-" + id).remove();
        }
    });
    
}

/**
 *  Save Product Data Function
 */
 $("form#cProduct").on("submit",function (e) {
    spinner.show();
    e.preventDefault();
    var formData = new FormData(this);
    setTimeout(function () {
        $.ajax({
            url: domain + domainpath + "/pos-admin/product/store/update",
            type: 'POST',
            data: formData,
            success: function (data) {
                if(data.message == 'error') {
                    var errorsHtml = '';
                    $.each(data.errors, function (index, value) {
                        errorsHtml += '<ul class="list-group"><li class="list-group-item alert alert-danger">' + value + '</li></ul>';
                    });
                    Swal.fire({
                        title: data.message + " " + data.status,// this will output "Error 422: Unprocessable Entity"
                        html: errorsHtml,
                        width: 'auto',
                        confirmButtonText: 'Try again',
                        cancelButtonText: 'Cancel',
                        showCancelButton: false,
                    }, function(isConfirm) {
                        if (isConfirm) {
                             $('#openModal').on("click");//this is when the form is in a modal
                        }
                    });
                    spinner.hide();
                } else {
                   
                        toastr.success(success, {
                            timeOut: 5e3,
                            closeButton: !0,
                            debug: !1,
                            newestOnTop: !0,
                            progressBar: !0,
                            positionClass: "toast-top-right",
                            preventDuplicates: !0,
                            onclick: null,
                            showDuration: "100",
                            hideDuration: "1000",
                            extendedTimeOut: "1000",
                            showEasing: "swing",
                            hideEasing: "linear",
                            showMethod: "fadeIn",
                            hideMethod: "fadeOut",
                            tapToDismiss: !1,
                        });
                        spinner.hide();
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }, 250);
});

function changePercentase(id) {
    var id = $(".variant-" + id);
    var p_price = id.find("input#purchase_price").val(); 
    var s_price = id.find("input#selling_price").val();
  
    id.find("input#purchase_price").val(formatRupiah(p_price));
    id.find("input#selling_price").val(formatRupiah(s_price)); 
    var countPercentase = (parseInt(s_price.replace(/[^0-9]/g, '').toString()) / parseInt(p_price.replace(/[^0-9]/g, '').toString())) * 100 - 100;
    id.find("input#margin").val(countPercentase.toFixed(0));
}