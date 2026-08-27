$(document).ready(function () {
  $('.dropify').dropify()

  $('#summernote').summernote({
    tabsize: 2,
    height: 150,
    width: '100%',
  })

  $("select[name='category']").on("change",function () {  
    var url = domainpath + '/pos-admin/product/getSub/' + $(this).val()
    $("select[name='subcategory']").load(url)
    return false
  })

  $("select[name='type']").on('change', function () {
    if ($(this).val() == 'single') {
      $('#variable').addClass('d-none')
      $('#single').removeClass('d-none')
      $('.productvariationchoose').addClass('d-none')
    } else if ($(this).val() == 'variable') {
      $('#single').addClass('d-none')
      $('#variable').removeClass('d-none')
      $('.productvariationchoose').removeClass('d-none')
    } else {
      $('#variable').addClass('d-none')
      $('#single').addClass('d-none')
      $('.productvariationchoose').addClass('d-none')
    }
  })

  $('body').on('click', '.delete_variant', function () {
    $(this).parents('.variant').remove()
  })
})

$("select[name='variation']").change(function (e) {
  if ($(this).val() != '0') {
    var url = domainpath + '/pos-admin/product/getVariant/' + $(this).val()
    spinner.show()
    e.preventDefault()
    setTimeout(function () {
      $.ajax({
        url: domain + url,
        type: 'GET',
        data: '',
        success: function (data, json, errorThrown) {
          var dataContent = ''
          var buttonContent = ''
          $.each(data.variant, function (index, value) {
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

            buttonContent =
              '<button type="button" class="btn btn-sm btn-danger mb-3" id="' +
              index +
              '" onclick="delete_variant(this.id)"><i  class="fa fa-minus-circle"></i> Hapus Variant</button>'

            dataContent +=
              `<div class="row variant-` +
              index +
              ` mt-3" onchange="changePercentase(this.id)" id="` +
              index +
              `"> 
            <div class="col-12  d-flex justify-content-end">
            ` +
              buttonContent +
              ` 
            <hr style="border: 1px solid black;">
        </div>
            <div class="col-3 mb-1">
                <div class="form-group has-icon-left">
                    <label class="form-label">Nama Variasi Produk</label>
                    <div class="position-relative">
                    <input type="hidden" name="variation_id[]"> 
                    <input type="hidden" name="value_id[]" value="` +
              value.id +
              `"> 
                    <input type="text" class="form-control" name="value[]" value="` +
              value.name +
              `" id="value">
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
                    <label class="form-label">Harga Jual</label>
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
                            ` +
              unit +
              `
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
          })
          $('.variant-0').remove()
          $('.variant-001').before(dataContent)
          spinner.hide()
        },

        cache: false,
        contentType: false,
        processData: false,
      })
    }, 100)
  }
})

$('.variant-0').on('keyup', function (e) {
  pp = formatRupiah($(this).find('#purchase_price').val())
  $(this).find('#purchase_price').val(pp)
  sp = formatRupiah($(this).find('#selling_price').val())
  gp = formatRupiah($(this).find('#sp_grocery').val())
  $(this).find('#sp_grocery').val(gp)
  $(this).find('#selling_price').val(sp)
})

/**
 *  Save Product Data Function
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

      var cloning =
        `<div class="row variant mt-3 ">
      <div class="col-12  d-flex justify-content-end">
      <button type="button" class="btn btn-sm btn-danger mb-3 delete_variant"><i class="fa fa-minus-circle"></i> Hapus Variant </button>
      <hr style="border: 2px solid black;">
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
              <label class="form-label">Harga Jual</label>
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
                      ` +
        unit +
        `
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

function delete_variant(id) {
  $('.variant-' + id).remove()
}

function changePercentase(id) {
  var id = $('.variant-' + id)
  var p_price = id.find('input#purchase_price').val()
  var s_price = id.find('input#selling_price').val()
  var g_price = id.find('input#sp_grocery').val()

  id.find('input#purchase_price').val(formatRupiah(p_price))
  id.find('input#selling_price').val(formatRupiah(s_price))
  id.find('input#sp_grocery').val(formatRupiah(g_price))

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

  id.find('input#margin').val(countPercentase.toFixed(0))
  id.find('input#margin_grocery').val(countPercentaseG.toFixed(0))
}


