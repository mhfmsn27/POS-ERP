@extends('ecommerce::layouts.mobile')

@section('content')
<div class="signin-area pb-30">
    <div class="tf-container">
        <h1 class="mt-20 text-center">Register Akun</h1>

        <form method="post" class="mt-32" action="{{route('ecommerce.mobile.signup')}}">
            <x-admin.validation-component></x-admin.validation-component>
            <fieldset class="mt-3">
                <label>Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Masukkan Nama Lengkap" class="form-control">
            </fieldset>
            <fieldset class="mt-3">
                <label>Nomor Hp</label>
                <input type="number" name="phone" required placeholder="Masukkan Nomor Hp" class="form-control">
            </fieldset>
            <fieldset class="mt-3">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Masukkan Alamat Email" class="form-control">
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
            <button class="mt-32 tf-btn primary" type="submit">Daftarkan Akun</button>
        </form>

        <p class="mt-32 text-center text-caption">Kembali Ke Halaman &nbsp; <a href="{{route('ecommerce.mobile.login')}}" class="text-caption fw-6">Login!</a></p>
    </div>
</div>
@endsection