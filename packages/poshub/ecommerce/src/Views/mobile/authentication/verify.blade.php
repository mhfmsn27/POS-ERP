@extends('ecommerce::layouts.mobile')

@section('content')
<div class="signin-area pb-30">
    <div class="tf-container">
        <h1 class="mt-20 text-center">Register Akun</h1>

        <form method="post" class="mt-32" action="{{route('ecommerce.mobile.verifymail')}}">
            <x-admin.validation-component></x-admin.validation-component>
            <fieldset class="mt-3">
                <label>Masukkan Kode Verifikasi</label>
                <input type="number" name="code" required placeholder="Masukkan Kode Verifikasi" class="form-control">
            </fieldset>

            <p class="mt-12 text-center">Belum Menerima Email ?</p>
            <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('re-send-mail').submit();" class="d-flex justify-content-center mt-4 text-center fw-6 text-primary text-decoration-underline ">
                Minta Pengiriman Email Kembali
            </a>

            <button class="mt-32 tf-btn primary" type="submit">Verifikasi Email</button>
        </form>

        <form id="re-send-mail" action="{{ route('ecommerce.mobile.resend') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>
@endsection