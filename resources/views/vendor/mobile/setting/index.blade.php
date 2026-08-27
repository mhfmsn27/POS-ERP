@extends('layouts.m')
@section('content')

<div class="header-area" id="headerArea">
      <div class="container">
            <div class="header-content header-style-five position-relative d-flex align-items-center justify-content-between">
                  <div class="logo-wrapper"><a href="{{route('m.index')}}"><img src="{{asset('uploads/logo2.png')}}" alt=""></a></div>
                  <div class="page-heading"> </div>
                  <div>
                        <h6 class="mb-0">{{$page}}</h6>
                  </div>
            </div>
      </div>
</div>


<div class="page-content-wrapper py-3">
      <x-mobile.alert-component></x-mobile.alert-component>

      <x-admin.validation-component></x-admin.validation-component>

      <div class="container">
            <!-- Element Heading -->
            <div class="element-heading">
                  <h6>Tambah Pengeluaran</h6>
            </div>
      </div>


      <div class="container">
            <div class="card">
                  <div class="card-body">
                        <form id="settStore" method="POST" enctype="multipart/form-data">
                              @csrf
                              <div class="form-group">
                                    <label class="form-label" for="name">Nama Toko *</label>
                                    <input class="form-control" type="text" required name="name" value="{{old('name',$store->name)}}" id="name">
                              </div>

                              <div class="form-group">
                                    <label class="form-label" for="email">Alamat Email *</label>
                                    <input class="form-control" type="email" required name="email" value="{{old('email',$store->email)}}" id="email">
                              </div>

                              <div class="form-group">
                                    <label class="form-label" for="phone">Nomor Ponsel Toko *</label>
                                    <input class="form-control" type="text" required name="phone" value="{{old('phone',$store->phone)}}" id="phone">
                              </div>

                              <div class="form-group">
                                    <label class="form-label" for="address">Alamat Toko</label>
                                    <textarea class="form-control" id="address" name="address" cols="3" rows="5" placeholder="Masukkan Alamat Toko...">{{ old('address',$store->address) }}</textarea>
                              </div>

                              <div class="form-group">
                                    <label class="form-label" for="footer_text">Tulisan Dibawah Struk</label>
                                    <textarea class="form-control" id="footer_text" name="footer_text" cols="3" rows="5" placeholder="Masukkan Catatan...">{{ old('footer_text',$store->footer_text) }}</textarea>
                              </div>

                              <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" type="submit">Simpan Pengaturan</button>
                        </form>
                  </div>
            </div>
      </div>

</div>
<br>
<x-mobile.footer-component></x-mobile.footer-component>

@endsection

@section('scripts')
<script>
      /**
       *  Setting
       */
      $('form#settStore').on('submit', function(e) { 
            e.preventDefault()
            var formData = new FormData(this)
            setTimeout(function() {
                  $.ajax({
                        url: domain + domainpath + '/mobile/setting/store',
                        type: 'POST',
                        data: formData,
                        success: function(data) {
                              toastr.success("Pengaturan Toko Berhasil Disimpan", "Berhasil", {
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
                        },
                        cache: false,
                        contentType: false,
                        processData: false,
                  })
            }, 130)
      })
</script>
@endsection