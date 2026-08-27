@extends('vendor.license.layouts.master')


@section('template_title')
    {{ trans('installer_messages.welcome.templateTitle') }}
@endsection

@section('title')
    POSHUB VERIFY INSTALL
@endsection

@section('container')
    <p class="text-center">
        Anda sudah berhasil menginstall Aplikasi POSHUB, Kini hanya tinggal beberapa langkah lagi untuk mengaktifkan Lisensi pembelian kamu, hingga POSHUB benar-benar dapat digunakan maksimal
    </p>
    <p class="text-center">
      <a href="{{ route('license.validation') }}" class="button">
        Lanjutkan Verifikasi Licensi
        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
      </a>
    </p>
@endsection
