@extends("ecommerce::layouts.web")



@section('content')

<main class="main pages">
      <div class="page-header breadcrumb-wrap">
            <div class="container">
                  <div class="breadcrumb">
                        <a href="{{route('ecommerce.home')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                        <span></span> Account <span></span> My Account
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

                                                <!-- List Address -->
                                                <div class="tab-pane fade active show " id="addressTabs">
                                                      <div class="row">
                                                            <div class="col-12 mb-4">
                                                                  <div class="cart-action d-flex justify-content-end" role="tablist">
                                                                        <a class="btn  mr-10 mb-sm-15 nav-link" href="javascript:void(0);" onclick="addAddress();"><i class="fi-rs-plus mr-10"></i>Tambah Alamat</a>
                                                                  </div>
                                                            </div>
                                                            <div class="row listDataAddress">
                                                                  <div class="col-12 actionButtonAddress"></div>
                                                            </div>
                                                      </div>
                                                </div>
                                                <!-- End List Address -->

                                                <!-- Form Address -->
                                                <div class="tab-pane fade" id="listAddressTabs" role="tabpanel" aria-labelledby="orders-tab">
                                                      <div class="row">
                                                            <div class="col-12 mb-4">
                                                                  <div class="cart-action d-flex justify-content-end" role="tablist">
                                                                        <a class="btn  mr-10 mb-sm-15 nav-link" href="javascript:void(0);" onclick="listAddress();"><i class="fi-rs-list mr-10"></i>Daftar Alamat</a>
                                                                  </div>
                                                            </div>
                                                            <div class="col-12">
                                                                  <div class="card">
                                                                        <div class="card-header">
                                                                              <h5 id="textTitleAddress">Tambah Alamat Baru</h5>
                                                                        </div>
                                                                        <div class="card-body">

                                                                              <form method="post" class="shipping_calculator" name="enq">
                                                                                    <div class="row ">
                                                                                          <div class="form-group col-md-12">
                                                                                                <label>Nama <span class="required">*</span></label>
                                                                                                <input type="hidden" id="addressID" value="">
                                                                                                <input required="" class="form-control" id="addressName" type="text" />
                                                                                          </div>

                                                                                          <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                                                                <label>Provinsi <span class="required">*</span></label>
                                                                                                <div class="custom_select">
                                                                                                      <select id='provinceData' required name='get_province' class='choose_province' style='width: 100%;'>
                                                                                                            <option value=''>Pilih Provinsi </option>
                                                                                                      </select>
                                                                                                </div>
                                                                                          </div>

                                                                                          <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                                                                <label>Kota / Kabupaten <span class="required">*</span></label>
                                                                                                <div class="custom_select">
                                                                                                      <select id='cityData' required name='get_city' class='choose_city' style='width: 100%;'>
                                                                                                            <option value=''>Pilih Kota / Kabupaten </option>
                                                                                                      </select>
                                                                                                </div>
                                                                                          </div>

                                                                                          <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                                                                <label>Kecamatan <span class="required">*</span></label>
                                                                                                <div class="custom_select">
                                                                                                      <select id='districtData' required class='choose_district' style='width: 100%;'>
                                                                                                            <option value=''>Pilih Kecamatan </option>
                                                                                                      </select>
                                                                                                </div>
                                                                                          </div>

                                                                                          <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                                                                <label>Kode POS <span class="required">*</span></label>
                                                                                                <input required="" class="form-control" id="postalCode" type="number" />
                                                                                          </div>

                                                                                          <div class="form-group col-md-12">
                                                                                                <label>Nomor Ponsel <span class="required">*</span></label>
                                                                                                <input required="" class="form-control" id="phoneAddress" type="number" />
                                                                                          </div>

                                                                                          <div class="form-group col-md-12 mb-4">
                                                                                                <label>Alamat Lengkap<span class="required">*</span></label>
                                                                                                <textarea class="form-control h-100" id="fullAddress" required></textarea>
                                                                                          </div>


                                                                                          <div class="form-group col-md-12 mt-4">
                                                                                                <label>Jadikan Default Pengiriman<span class="required">*</span></label>
                                                                                                <select class="form-control" id="defaultOption">
                                                                                                      <option value="no">Tidak</option>
                                                                                                      <option value="yes">Iya</option>
                                                                                                </select>
                                                                                          </div>

                                                                                          <div class="col-md-12">
                                                                                                <button type="button" id="updateDataAddress" class="btn btn-fill-out submit font-weight-bold d-none">Simpan Perubahan Alamat</button>
                                                                                                <button type="button" id="createDataAddress" class="btn btn-fill-out submit font-weight-bold">Tambah Alamat</button>
                                                                                          </div>
                                                                                    </div>
                                                                              </form>
                                                                        </div>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </div>
                                                <!-- End Form Address -->
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</main>

@endsection

@section('scripts')
<script src="{{asset('ecommerce/js/address.js')}}"></script>
@endsection