@extends('layouts.welcome')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/maps/leaflet.css') }}" />
@endsection

<div class="wrapper">
    <div class="p-4">
        <div class="page-content">
            <div class="container-fluid">
                <div class="page-title-box">
                    <div class="row align-items-center">
                        <div class="col-md-12 text-center">
                            <h2>Buat Toko / Cabang Baru</h2>
                            <p>Lengkapi informasi toko anda di bawah ini</p>
                            <x-admin.validation-component></x-admin.validation-component>
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center ">
                    <div class="col-sm-12 col-lg-8">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">Informasi Bisnis Dan Toko</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="row" action="{{route('store.add')}}">
                                    @csrf

                                    <div class="col-md-6 mb-4">
                                        <h6>{{__('store.store_name')}}</h6>
                                        <div class="form-group">
                                            <input type="text" name="name" value="{{old('name')}}" id="name" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>{{__('general.email')}}</h6>
                                        <div class="form-group">
                                            <input type="email" name="email" value="{{old('email')}}" id="email" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>{{__('general.phone')}}</h6>
                                        <div class="form-group">
                                            <input type="number" name="phone" value="{{old('phone')}}" id="phone" class="form-control">
                                        </div>
                                    </div>


                                    <div class="col-md-6 mb-4">
                                        <h6>Opsi Penggunaan Akutansi</h6>
                                        <div class="form-group">
                                            <select class="form-control" name="accountant_use" required>
                                                <option value="yes">Menggunakan</option>
                                                <option value="no">Tidak Gunakan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Opsi Penggunaan Pajak</h6>
                                        <div class="form-group">
                                            <select class="form-control" name="tax_option" id="taxoption" required>
                                                <option value="no">Tidak Gunakan Pajak</option>
                                                <option value="active">Menggunakan Pajak</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Kode Pos</h6>
                                        <div class="form-group">
                                            <input type="text" name="zip_code" value="{{old('zip_code')}}" id="zip_code" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h6>Default Gudang</h6>
                                        <div class="form-group">
                                            <select class="form-control" name="warehouse_default_id">
                                                <option value="">Gudang Utama</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <h6>Gunakan Shift Register</h6>
                                        <div class="form-group">
                                            <select class="form-control" name="shift_register" id="shift_register">
                                                <option value="active">Active</option>
                                                <option value="no">Tidak</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-12 mb-4">
                                        <h6>{{__('general.address')}}</h6>
                                        <div class="form-group">
                                            <textarea class="form-control" name="address" id="address">{{ old('address') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end mt-4">
                                        <button class="btn btn-primary">{{__('general.save')}}</button>
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
@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/maps/store_create.js') }}"></script>
@endsection