@extends('ecommerce::layouts.mobile')

@section('content')
<div class="signin-area pb-30">
    <div class="tf-container">
        <h1 class="mt-20 text-center">Lupa Password</h1>

        <form method="post" class="mt-32" action="{{route('ecommerce.mobile.send_forget')}}">
            <x-admin.validation-component></x-admin.validation-component>
            <fieldset class="mt-3">
                <label>Email</label>
                <input type="email" name="email" placeholder="Masukkan Email Anda" class="form-control">
            </fieldset> 
            <button class="mt-32 tf-btn primary" type="submit">Minta Kode </button>
        </form>

        <p class="mt-32 text-center text-caption">Kembali Ke Halaman &nbsp; <a href="{{route('ecommerce.mobile.login')}}" class="text-caption fw-6">Login</a></p>
    </div>
</div>
@endsection