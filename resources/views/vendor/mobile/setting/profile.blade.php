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
                        <form id="mobileUpdateProfile" method="POST" enctype="multipart/form-data">
                              @csrf
                              <div class="form-group">
                                    <label class="form-label" for="name">Nama Lengkap *</label>
                                    <input class="form-control" id="name" type="text" name="name" value="{{ old('name',Auth()->user()->name) }}" required>
                              </div>

                              <div class="form-group">
                                    <label class="form-label" for="email">Alamat Email *</label>
                                    <input class="form-control" id="email" type="text" name="email" value="{{ old('email',Auth()->user()->email) }}" required>
                              </div>

                              <div class="file-upload-card mt-2 mb-3">
                                    <svg class="bi bi-file-earmark-arrow-up text-primary" width="48" height="48" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M4 0h5.5v1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h1V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"></path>
                                          <path d="M9.5 3V0L14 4.5h-3A1.5 1.5 0 0 1 9.5 3z"></path>
                                          <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V7.707l1.146 1.147a.5.5 0 0 0 .708-.708l-2-2a.5.5 0 0 0-.708 0l-2 2a.5.5 0 1 0 .708.708L7.5 7.707V11.5a.5.5 0 0 0 .5.5z"></path>
                                    </svg>
                                    <h5 class="mt-2 mb-4">Masukkan Photo</h5>
                                    <div>
                                          <div class="form-file">
                                                <input class="form-control d-none" type="file" id="customFile" name="document">
                                                <label class="form-file-label justify-content-center" for="customFile"><span class="form-file-button btn btn-primary shadow w-100">Upload File</span></label>
                                          </div>
                                    </div>
                                    <h6 class="mt-4 mb-0">Supported files</h6><small>.jpg .png .jpeg</small>
                              </div>

                              <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" type="submit">Edit Profile </button>
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
      $('form#mobileUpdateProfile').on('submit', function(e) {
            e.preventDefault()
            var formData = new FormData(this)
            setTimeout(function() {
                  $.ajax({
                        url: domain + domainpath + '/mobile/setting/profile-store',
                        type: 'POST',
                        data: formData,
                        success: function(data) {
                              toastr.success("Pengaturan Profile Berhasil diperbaharui", "Berhasil", {
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