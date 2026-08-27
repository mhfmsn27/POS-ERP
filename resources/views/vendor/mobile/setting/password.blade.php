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
                        <form id="mobileUpdatePassword" method="POST" enctype="multipart/form-data">
                              @csrf
                              <div class="form-group">
                                    <label class="form-label" for="password">Password Baru</label>
                                    <input class="form-control" id="password" type="password" name="password" required>
                              </div>

                              <div class="form-group">
                                    <label class="form-label" for="confirm">Konfirmasi Password Baru</label>
                                    <input class="form-control" id="confirm" type="password" name="confirm" required>
                              </div>


                              <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" type="submit">Edit Password </button>
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
      $('form#mobileUpdatePassword').on('submit', function(e) {
            e.preventDefault()
            var formData = new FormData(this)
            setTimeout(function() {
                  $.ajax({
                        url: domain + domainpath + '/mobile/setting/password-store',
                        type: 'POST',
                        data: formData,
                        success: function(data) {
                              if (data.message == 'combine') {
                                    toastr.warning(data.errors, "Gagal", {
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
                                    toastr.success("Password berhasil disimpan", "Berhasil", {
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
</script>
@endsection