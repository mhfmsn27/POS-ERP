@extends('vendor.license.layouts.master')

@section('title')
    Form Validation License
@endsection

@section('container')
    <div class="tabs tabs-full">

        <input id="tab1" type="radio" name="tabs" class="tab-input" checked />
        <label for="tab1" class="tab-label">
            <i class="fa fa-cog fa-2x fa-fw" aria-hidden="true"></i>
            <br />
            {{ trans('installer_messages.environment.wizard.tabs.environment') }}
        </label>


        <form method="post" id="activasiLicensi" class="tabs-wrap">
            <div class="tab" id="tab1content">
                @csrf

                <div class="form-group {{ $errors->has('purchase') ? ' has-error ' : '' }}">
                    <label for="purchase">
                        Kode Purchase Pembelian
                    </label>
                    <input type="text" name="purchase" required id="purchase" value=""
                        placeholder="Masukkan Kode Purchase Pembelian Anda" />
                    @if ($errors->has('purchase'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('purchase') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('email') ? ' has-error ' : '' }}">
                    <label for="email">
                        Kode Purchase Pembelian
                    </label>
                    <input type="text" name="email" id="email" required value="" placeholder="Masukkan Email Akun POSHUB Anda" />
                    @if ($errors->has('email'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('email') }}
                        </span>
                    @endif
                </div>


                <div class="buttons">
                    <button class="button" type="submit">
                        Check Licensi
                        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

        </form>

    </div>
@endsection

@section('scripts')
 <script src="{{ asset('assets/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/evolution.js') }}"></script>
    <script src="{{ asset('js/connection.js') }}"></script>
    <script src="{{ asset('js/installer.js') }}"></script>
@endsection
