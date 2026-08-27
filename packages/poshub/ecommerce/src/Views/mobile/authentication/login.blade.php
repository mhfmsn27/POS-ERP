@extends('ecommerce::layouts.mobile')

@section('content')
<div class="signin-area pb-30">
    <div class="tf-container">
        <h1 class="mt-20 text-center">Login</h1>

        <form method="post" class="mt-32" action="{{route('ecommerce.mobile.signin')}}">
            <x-admin.validation-component></x-admin.validation-component>
            <fieldset class="mt-3">
                <label>Email</label>
                <input type="email" name="email" placeholder="Masukkan Email Anda" class="form-control">
            </fieldset>
            <fieldset class="mt-12">
                <label>Password</label>
                <div class="box-view-hide">
                    <input type="password" placeholder="Masukkan Password Anda" name="password" class="form-control password-field">
                    <div class="show-pass">
                        <span class="icon-view">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M0.833496 10.0007C0.833496 10.0007 4.16683 3.33398 10.0002 3.33398C15.8335 3.33398 19.1668 10.0007 19.1668 10.0007C19.1668 10.0007 15.8335 16.6673 10.0002 16.6673C4.16683 16.6673 0.833496 10.0007 0.833496 10.0007Z" stroke="#787982" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10 12.5C11.3807 12.5 12.5 11.3807 12.5 10C12.5 8.61929 11.3807 7.5 10 7.5C8.61929 7.5 7.5 8.61929 7.5 10C7.5 11.3807 8.61929 12.5 10 12.5Z" stroke="#787982" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="icon-hide">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <g clip-path="url(#clip0_1_4474)">
                                    <path d="M0.833496 10.0007C0.833496 10.0007 4.16683 3.33398 10.0002 3.33398C15.8335 3.33398 19.1668 10.0007 19.1668 10.0007C19.1668 10.0007 15.8335 16.6673 10.0002 16.6673C4.16683 16.6673 0.833496 10.0007 0.833496 10.0007Z" stroke="#787982" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10 12.5C11.3807 12.5 12.5 11.3807 12.5 10C12.5 8.61929 11.3807 7.5 10 7.5C8.61929 7.5 7.5 8.61929 7.5 10C7.5 11.3807 8.61929 12.5 10 12.5Z" stroke="#787982" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15 1L6 19" stroke="#787982" stroke-width="1.5" stroke-linecap="round" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_1_4474">
                                        <rect width="20" height="20" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                    </div>
                </div>
            </fieldset>
            <a href="{{route('ecommerce.mobile.forget')}}" class="text-caption d-inline-block text-secondary mt-12">Lupa Password?</a>
            <button class="mt-32 tf-btn primary" type="submit">Login</button>
        </form>

        <p class="mt-32 text-center text-caption">Belum Punya Akun ? &nbsp; <a href="{{route('ecommerce.mobile.register')}}" class="text-caption fw-6">Daftar Disini!</a></p>
    </div>
</div>
@endsection