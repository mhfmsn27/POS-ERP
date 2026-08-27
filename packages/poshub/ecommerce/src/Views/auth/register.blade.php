@extends('ecommerce::layouts.web')

@section('content')


<!--End header-->
<main class="main pages">

      <div class="page-header breadcrumb-wrap">
            <div class="container">
                  <div class="breadcrumb">
                        <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                        <span></span> Auth <span></span> Login
                  </div>
            </div>
      </div>
      <div class="page-content pt-150 pb-150">
            <div class="container">
                  <div class="row">
                        <div class="col-xl-8 col-lg-10 col-md-12 m-auto">
                              <div class="row">
                                    <div class="col-lg-6 pr-30 d-none d-lg-block">
                                          <img class="border-radius-15" src="{{asset('ecommerce/imgs/page/login-1.png')}}" alt="" />
                                    </div>
                                    <div class="col-lg-6 col-md-8">
                                          <div class="login_wrap widget-taber-content background-white">
                                                <div class="padding_eight_all bg-white">
                                                      <div class="heading_s1">
                                                            <h1 class="mb-5">Register</h1>
                                                            <p class="mb-30">Sudah Punya Akun ? <a href="{{route('ecommerce.login')}}">Login disini</a></p>
                                                      </div>
                                                      <x-admin.validation-component></x-admin.validation-component>
                                                      <form method="post" action="{{route('ecommerce.signup')}}">

                                                            <div class="form-group">
                                                                  <input type="text" required="" name="name" placeholder="Masukkan Nama Lengkap *" />
                                                            </div>

                                                            <div class="form-group">
                                                                  <input type="number" required="" name="phone" placeholder="Masukkan Nomor Ponsel *" />
                                                            </div>

                                                            <div class="form-group">
                                                                  <input type="text" required="" name="email" placeholder="Masukkan Email *" />
                                                            </div>
                                                            <div class="form-group">
                                                                  <input required="" type="password" name="password" placeholder="Masukkan password *" />
                                                            </div>
                                                            <div class="form-group">
                                                                  <input required="" type="password" name="password_confirmation" placeholder="Konfirmasi Password *" />
                                                            </div>
                                                            <div class="login_footer form-group">

                                                            </div>

                                                            <div class="form-group">
                                                                  <button type="submit" class="btn btn-heading btn-block hover-up" name="login">Register</button>
                                                            </div>
                                                      </form>
                                                </div>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>

</main>

@endsection