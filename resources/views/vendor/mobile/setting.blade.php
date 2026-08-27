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

      <div class="container">
            <div class="card">
                  <div class="card-body p-3">
                        <p class="ps-2">Pengaturan Umum</p>
                        @can("Update Toko")
                        <div class="poshub-page-item">
                              <div class="form-check form-switch">
                                    <input class="form-check-input" id="shiftRegister" name="shiftRegister" type="checkbox" @if($store->shift_register == 'active') checked="" @endif>
                                    <label class="form-check-label" for="shiftRegister">On / Off Shift Register</label>
                              </div>
                        </div>
                        @endcan
                        @can("Setting")
                        <div class="poshub-page-item">
                              <div class="form-check form-switch">
                                    <input class="form-check-input" id="mobileVersion" name="mobileVersion" type="checkbox" checked="">
                                    <label class="form-check-label" for="mobileVersion">On / Off Mobile Version</label>
                              </div>
                        </div>
                        @endcan
                        @can("Update Toko")
                        <a class="poshub-page-item" href="{{route('m.store')}}">
                              <div class="icon-wrapper"><i class="fas fa-cog"></i></div>Pengaturan Toko<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                  </div>
            </div>

            <div class="card mt-2">
                  <div class="card-body p-3">
                        <p class="ps-2">Pengaturan Akun</p>
                        <a class="poshub-page-item" href="{{route('m.profile')}}">
                              <div class="icon-wrapper"><i class="fas fa-user-circle"></i></div>Edit Profile<i class="bi bi-chevron-right"></i>
                        </a>
                        <a class="poshub-page-item" href="{{route('m.password')}}">
                              <div class="icon-wrapper"><i class="fas fa-unlock"></i></div>Edit Password<i class="bi bi-chevron-right"></i>
                        </a>
                        <a class="poshub-page-item" href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                              <div class="icon-wrapper"><i class="fas fa-sign-out-alt"></i></div>Keluar<i class="bi bi-chevron-right"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                              @csrf
                        </form>
                  </div>
            </div>

      </div>

</div>
<br>
<x-mobile.footer-component></x-mobile.footer-component>

@endsection

@section("scripts")
<script>
      $("#mobileVersion").on("click", function() {
            Swal.fire({
                  title: 'Apakah Anda Yakin ?',
                  text: 'Setelah meng-nonaktifkan fitur ini, Anda akan otomatis diarahkan ke versi Website walau sedang menggunakan ponsel Anda',
                  width: 'auto',
                  confirmButtonText: 'Ya',
                  cancelButtonText: 'Tidak',
                  showCancelButton: true,
            }).then((result) => {
                  if (result.value) {
                        location.href = domain + domainpath + '/mobile/setting/turn-off-mobile';
                  }
            })
      });

      $("#shiftRegister").on("click", function() {
            var pname = $("input[name='shiftRegister']:checked").length;
            $.ajax({
                  url: domain + domainpath + '/mobile/setting/option-shift/' + pname,
                  type: 'GET',
                  data: '',
                  success: function(data, json, errorThrown) {
                        toastr.success('Pengaturan Berhasil diperbaharui', 'Berhasil', {
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
      });
</script>
@endsection