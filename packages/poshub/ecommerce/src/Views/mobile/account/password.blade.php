@extends('ecommerce::layouts.mobile')

@section('content')
<div class="header fixed-top line4-bt">
    <div class="left">
        <a href="{{ route('ecommerce.mobile.dashboard') }}" class="icon back-btn"><i class="icon-left-btn"></i></a>
    </div>
    <h6>Ubah Password</h6>
</div>

<form action="{{route('ecommerce.mobile.change_password')}}" method="POST" class="app-content">
    @csrf
    <div class="card">
        <div class="card-body">
            <fieldset class="mt-20 input-fill">
                <label>Password Lama <span class="required">*</span></label>
                <input type="password" placeholder="Masukkan Password Lama" name="old_password" class="form-control">
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Password Baru <span class="required">*</span></label>
                <input type="password" placeholder="Masukkan Password Baru" name="new_password" class="form-control">
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Konfirmasi Password <span class="required">*</span></label>
                <input type="password" placeholder="Konfirmasi Password" name="con_password" class="form-control">
            </fieldset>
        </div>
    </div>

    <div class="footer-fixed p-16">
        <button type="submit" class="tf-btn primary">Simpan Perubahan</button>
    </div>
</form>
<x-admin.validation-component></x-admin.validation-component>
@endsection