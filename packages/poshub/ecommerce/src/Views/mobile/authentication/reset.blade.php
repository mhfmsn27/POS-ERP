@extends('ecommerce::layouts.mobile')

@section('content')
<div class="signin-area pb-30">
    <div class="tf-container">
        <h1 class="mt-20 text-center">Reset Password</h1>

        <form method="post" class="mt-32" action="{{route('ecommerce.mobile.reset_pass')}}">
            <x-admin.validation-component></x-admin.validation-component>
            <fieldset class="mt-3">
                <label>Kode Reset Email</label>
                <input type="number" name="code" required placeholder="Masukkan Kode" class="form-control">
            </fieldset>
            <fieldset class="mt-12">
                <label>Password Baru</label>
                <div class="box-view-hide">
                    <input type="password" placeholder="Masukkan Password Baru" name="password" class="form-control password-field">
                </div>
            </fieldset>
            <fieldset class="mt-12">
                <label>Konfirmasi Password Baru</label>
                <div class="box-view-hide">
                    <input type="password" placeholder="Masukkan Konfirmasi Password" name="password_confirmation" class="form-control password-field">
                </div>
            </fieldset>
            <button class="mt-32 tf-btn primary" type="submit">Ganti Password</button>
        </form>

        <p class="mt-32 text-center text-caption">Kembali Ke Halaman &nbsp; <a href="{{route('ecommerce.mobile.login')}}" class="text-caption fw-6">Login!</a></p>
    </div>
</div>
@endsection