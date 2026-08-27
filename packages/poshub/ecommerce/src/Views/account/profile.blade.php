@extends("ecommerce::layouts.web")

@section('content')

<main class="main pages">
      <div class="page-header breadcrumb-wrap">
            <div class="container">
                  <div class="breadcrumb">
                        <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                        <span></span> Account <span></span> Dashboard
                  </div>
            </div>
      </div>
      <div class="page-content pt-150 pb-150">
            <div class="container">
                  <div class="row">
                        <div class="col-lg-12 m-auto">
                              <div class="row">
                                    <x-ecommerce-sidebar-account-component></x-ecommerce-sidebar-account-component>
                                    <div class="col-md-9">
                                          <div class="tab-content account dashboard-content pl-50">

                                                <div class="tab-pane fade active show" id="account-detail" role="tabpanel" aria-labelledby="account-detail-tab">
                                                      <div class="row">
                                                            <div class="col-12">
                                                                  <x-admin.validation-component></x-admin.validation-component>
                                                            </div>
                                                            <div class="col-lg-8 col-sm-12 col-12">
                                                                  <div class="card">
                                                                        <div class="card-header">
                                                                              <h5>Edit Profil</h5>
                                                                        </div>
                                                                        <div class="card-body">
                                                                              <form method="post" action="{{route('ecommerce.change_profile')}}">
                                                                                    <div class="row">
                                                                                          <div class="form-group col-md-12">
                                                                                                <label>Nama Lengkap <span class="required">*</span></label>
                                                                                                <input required="" value="{{auth()->guard('customers')->user()->name}}" class="form-control" name="name" type="text" />
                                                                                          </div>
                                                                                          <div class="form-group col-md-12">
                                                                                                <label>Alamat Email <span class="required">*</span></label>
                                                                                                <input required="" value="{{auth()->guard('customers')->user()->email}}" class="form-control" name="email" type="email" />
                                                                                          </div>
                                                                                          <div class="form-group col-md-12">
                                                                                                <label>Nomor Ponsel <span class="required">*</span></label>
                                                                                                <input required="" value="{{auth()->guard('customers')->user()->phone}}" class="form-control" name="phone" type="number" />
                                                                                          </div>
                                                                                          <div class="form-group col-md-12">
                                                                                                <label>Alamat Lengkap <span class="required">*</span></label>
                                                                                                <textarea class="form-control h-100" name="address" required>{{auth()->guard('customers')->user()->address}}</textarea>
                                                                                          </div>
 
                                                                                          <div class="col-md-12 mt-50">
                                                                                                <button type="submit" class="btn btn-fill-out submit font-weight-bold" name="submit" value="Submit">Simpan Perubahan</button>
                                                                                          </div>
                                                                                    </div>
                                                                              </form>
                                                                        </div>
                                                                  </div>
                                                            </div>
                                                            <div class="col-lg-4 col-sm-12 col-12">
                                                                  <div class="card">
                                                                        <div class="card-header">
                                                                              <h5>Edit Password</h5>
                                                                        </div>
                                                                        <div class="card-body">
                                                                              <form method="post" action="{{route('ecommerce.change_password')}}">
                                                                                    <div class="row">

                                                                                          <div class="form-group col-md-12">
                                                                                                <label>Password Saat ini <span class="required">*</span></label>
                                                                                                <input required="" class="form-control" name="old_password" type="password" />
                                                                                          </div>
                                                                                          <div class="form-group col-md-12">
                                                                                                <label>Password Baru <span class="required">*</span></label>
                                                                                                <input required="" class="form-control" name="new_password" type="password" />
                                                                                          </div>
                                                                                          <div class="form-group col-md-12">
                                                                                                <label>Konfirmasi Password <span class="required">*</span></label>
                                                                                                <input required="" class="form-control" name="con_password" type="password" />
                                                                                          </div>
                                                                                          <div class="col-md-12">
                                                                                                <button type="submit" class="btn btn-fill-out submit font-weight-bold" name="submit" value="Submit">Ubah Password</button>
                                                                                          </div>
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
                        </div>
                  </div>
            </div>
      </div>
</main>

@endsection