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
                                    
                                    <div class="col-lg-12 col-md-8">
                                          <div class="login_wrap widget-taber-content background-white">
                                                <div class="padding_eight_all bg-white">
                                                      <div class="heading_s1">
                                                            <h1 class="mb-5">Minta Reset Password</h1> 
                                                      </div>
                                                      <x-admin.validation-component></x-admin.validation-component>
                                                      <form method="post" action="{{route('ecommerce.send_forget')}}">
                                                            <div class="form-group">
                                                                  <input type="text" required="" name="email" placeholder="Masukkan Email *" />
                                                            </div> 
                                                             
                                                            <div class="form-group">
                                                                  <button type="submit" class="btn btn-heading btn-block hover-up" name="login">Kirim Permintaan</button>
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