@extends('layouts.app')
@section('content')
<div class="">
    <div class="col col-login mx-auto mt-7">
        <div class="text-center">
            <a href="{{url('index')}}">
                <img src="{{asset('assets/images/logo-signin.png')}}" class="header-brand-img" alt="logo">
            </a>
        </div>
    </div>
    <div class="container-login100">
        <div class="wrap-login100 p-6" style="width: 1000px;">
            <span class="login100-form-title">
                Registrasi Bisnis dan Toko
            </span> 
            <x-admin.validation-component></x-admin.validation-component>
            <form class="validate-form row" method="POST" action="{{route('business.register.create')}}">
                <div class="col-sm-12 col-lg-6">
                    <label>Nama Bisnis *</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" id="name" required placeholder="Masukkan Nama Bisnis Anda">
                    </div>
                </div>
                <div class="col-sm-12 col-lg-6">
                    <label>Email Bisnis *</label>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" id="email" required placeholder="Masukkan Email Bisnis Anda">
                    </div>
                </div>
                <div class="col-sm-12 col-lg-6">
                    <label>Nomor Ponsel *</label>
                    <div class="form-group">
                        <input type="number" class="form-control" name="phone" value="{{ old('phone') }}" id="phone" required placeholder="Masukkan Nomor Ponsel Bisnis Anda">
                    </div>
                </div>
                <div class="col-sm-12 col-lg-6">
                    <label>Opsi Penggunaan Akutansi *</label>
                    <div class="form-group">
                        <select class="form-control" name="accountant_use" required>
                            <option value="yes">Menggunakan</option>
                            <option value="no">Tidak Gunakan</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-6">
                    <label>Opsi Penggunaan Pajak *</label>
                    <div class="form-group">
                        <select class="form-control" name="tax_option" id="taxoption" required>
                            <option value="no">Tidak Gunakan Pajak</option>
                            <option value="active">Menggunakan Pajak</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <label>Alamat Lengkap *</label>
                    <div class="form-group">
                        <textarea class="form-control" name="address" id="address">{{ old('address') }}</textarea>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-lg btn-info" type="submit">
                        Simpan dan Buat Bisnis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection