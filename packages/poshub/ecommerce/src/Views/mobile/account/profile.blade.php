@extends('ecommerce::layouts.mobile')

@section('content')
<div class="header fixed-top line4-bt">
    <div class="left">
        <a href="{{ route('ecommerce.mobile.dashboard') }}" class="icon back-btn"><i class="icon-left-btn"></i></a>
    </div>
    <h6>Ubah Profil</h6>
</div>
<form action="{{route('ecommerce.mobile.change_profile')}}" method="POST" class="app-content">
    @csrf
    <div class="card">
        <div class="card-body">
            <fieldset class="mt-20 input-fill">
                <label>Nama Lengkap <span class="required">*</span></label>
                <input type="name" placeholder="Nama Lengkap" value="<?= auth()->guard('customers')->user()->name; ?>" name="name" class="form-control">
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Nomor Telpon <span class="required">*</span></label>
                <input type="phone" placeholder="Nomor Telpon" value="<?= auth()->guard('customers')->user()->phone; ?>" name="phone" class="form-control">
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Email <span class="required">*</span></label>
                <input type="email" placeholder="Email" value="<?= auth()->guard('customers')->user()->email; ?>" name="email" class="form-control">
            </fieldset>
        </div>
    </div>

    <div class="footer-fixed p-16">
        <button type="submit" class="tf-btn primary">Simpan Perubahan</button>
    </div>
</form>
<x-admin.validation-component></x-admin.validation-component>
@endsection