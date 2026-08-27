@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{asset('ecommerce/css/tab.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select3/dist/css/select2.min.css') }}" />
@endsection
<div class="content-page">
      <div class="container-fluid">

            <div class="row">

                  <!-- Component -->
                  <x-ecommerce-tab-setting-component></x-ecommerce-tab-setting-component>
                  <x-admin.validation-component></x-admin.validation-component>
                  <!-- End Component -->

                  <div class="col-md-12 col-12">
                        <div class="card card-block card-stretch card-height">
                              <div class="card-header d-flex justify-content-between">
                                    <div class="iq-header-title">
                                          <h4 class="card-title">{{$page}}</h4>
                                    </div>
                              </div>
                              <div class="card-body">
                                    <form action="{{route('ecommerce.admin.setting.store')}}" method="POST" enctype="multipart/form-data" class="form form-horizontal">
                                          @csrf
                                          <div class="form-body">
                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Rajaongkir *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" value="{{$data ? $data->rajaongkir : ''}}" name="rajaongkir" id="rajaongkir">
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Merchant ID *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="text"  class="form-control" value="{{$data ? $data->merchant_id : ''}}" name="merchant_id" id="merchant_id">
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Client KEY *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" value="{{$data ? $data->client_key : ''}}" name="client_key" id="client_key">
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Server KEY *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" value="{{$data ? $data->server_key : ''}}" name="server_key" id="server_key">
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Opsi Pembayaran *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <select class="form-control" id="payment_method" name="payment_method">
                                                                  <option value="">Pilih Opsi</option>
                                                                  <option value="midtrans" @if(($data ? $data->payment_method : '')== 'midtrans') selected @endif>Midtrans </option>
                                                                  <option value="manual" @if(($data ? $data->payment_method : '') == 'manual') selected @endif>Manual </option>
                                                            </select>
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Aktivasi Kurir Mandiri *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <select class="form-control" id="kurir_manual" name="kurir_manual">
                                                                  <option value="yes" @if(($data ? $data->kurir_manual : '') == 'yes') selected @endif>Aktifkan </option>
                                                                  <option value="no" @if(($data ? $data->kurir_manual : '') == 'no') selected @endif>Tidak </option>
                                                            </select>
                                                      </div>
                                                </div>

                                                <div class="row mb-3 formprice <?= ($data ? $data->kurir_manual : '') == 'no' ? 'd-none' : ''; ?> ">
                                                      <div class="col-md-4">
                                                            <label>Harga Kurir Manual Per KM *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="number" class="form-control" id="price_per_km" name="price_per_km" value="<?= (int)($data ? $data->price_per_km : 0); ?>" />
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Slug Domain *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" name="domain_site" required value="<?= $data ? $data->domain_site : ''; ?>" />
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Pilih Provinsi *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <select id='provinceData' required name='get_province' class='choose_province' style='width: 100%;'>
                                                                  <option value='{{$store->subdistrict->city->province->id ?? ""}}'>{{$store->subdistrict->city->province->name ?? 'Pilih Provinsi'}} </option>
                                                            </select>
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Pilih Kota / Kabupaten *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <select id='cityData' required name='get_city' class='choose_city' style='width: 100%;'>
                                                                  <option value='{{$store->subdistrict->city->id ?? ""}}'>{{$store->subdistrict->city->name ?? 'Pilih Kota'}} </option>
                                                            </select>
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Pilih Kecamatan *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <select id='districtData' required name='sub_district_id' class='choose_district' style='width: 100%;'>
                                                                  <option value='{{$store->subdistrict->id ?? ""}}'>{{$store->subdistrict->name ?? 'Pilih Kecamatan'}} </option>
                                                            </select>
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Tampilkan Stok *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <select class="form-control" id="stock" name="stock">
                                                                  <option value="yes" @if(($data ? $data->show_stock : '') == 'yes') selected @endif>Tampilkan </option>
                                                                  <option value="no" @if(($data ? $data->show_stock : '') == 'no') selected @endif>Jangan Tampilkan </option>
                                                            </select>
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Tampilkan Produk yang tidak memiliki stok *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <select class="form-control" id="with_stock" name="with_stock">
                                                                  <option value="yes" @if(($data ? $data->with_stock : '') == 'yes') selected @endif>Tampilkan </option>
                                                                  <option value="no" @if(($data ? $data->with_stock : '') == 'no') selected @endif>Jangan Tampilkan </option>
                                                            </select>
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Status Aktivasi Toko *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <select name='status' class="form-control">
                                                                  <option value='yes' @if(($data ? $store->ecommerce_activation : '') == 'yes') selected @endif >Iya </option>
                                                                  <option value='no' @if(($data ? $store->ecommerce_activation : '') == 'no') selected @endif>No </option>
                                                            </select>
                                                      </div>
                                                </div>


                                                <div class="col-sm-12 d-flex justify-content-end">
                                                      <button class="btn btn-info me-1 mb-1 mr-2">Simpan Perubahan</button>
                                                </div>
                                          </div>
                                    </form>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</div>


@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/select3/dist/js/select2.full.min.js') }}"></script>
<script>
      $(document).ready(function() {
            $('.choose_province').select2()
            $('.choose_city').select2()
            $('.choose_district').select2()

            $('#provinceData').select2({
                  placeholder: 'Pilih Provinsi...',
                  ajax: {
                        url: domainpath + '/web/location/provinces',
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                              return {
                                    results: $.map(data, function(item) {
                                          return {
                                                text: item.name,
                                                id: item.id,
                                          }
                                    }),
                              }
                        },
                        cache: false,
                  },
            })


      })

      $("select[name='get_province']").on('change', function(e) {
            $('#cityData').val('')
            $('#districtData').val('')
            $('#cityData').select2({
                  placeholder: 'Pilih Kota / Kabupaten...',
                  ajax: {
                        url: domainpath + '/web/location/cities?province=' + $(this).val(),
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                              return {
                                    results: $.map(data, function(item) {
                                          return {
                                                text: item.name,
                                                id: item.id,
                                          }
                                    }),
                              }
                        },
                        cache: false,
                  },
            })
      })

      $("select[name='get_city']").on('change', function(e) {
            $('#districtData').val('')

            $('#districtData').select2({
                  placeholder: 'Pilih Kecamatan...',
                  ajax: {
                        url: domainpath + '/web/location/district?city=' + $(this).val(),
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                              return {
                                    results: $.map(data, function(item) {
                                          return {
                                                text: item.name,
                                                id: item.id,
                                          }
                                    }),
                              }
                        },
                        cache: false,
                  },
            })
      })

      $("#kurir_manual").on("change", function() {
            if ($(this).val() == 'yes') {
                  $(".formprice").removeClass("d-none")
            } else {
                  $(".formprice").addClass("d-none");
            }
      });
</script>
@endsection