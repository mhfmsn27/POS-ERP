@extends('layouts.welcome')
@section('content')
<div class="wrapper">
    <div class="p-4">
        <div class="page-content">
            <div class="container-fluid">
                <div class="page-title-box">
                    <div class="row align-items-center">
                        <div class="col-md-12 text-center">
                            <h2>{{$page}}</h2>
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center ">
                    <div class="col-xl-8 col-md-8">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-header d-flex justify-content-between">
                                <div class="iq-header-title">
                                    <h4 class="card-title">{{$page}}</h4>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <form id="updateLicense" method="POST" enctype="multipart/form-data" class="form form-horizontal">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row mb-4">
                                            <label>Email Anda</label>
                                            <div class="col-md-12 form-group">
                                                <input type="email" class="form-control" name="email" value="{{ old('email', $license->email) }}" id="email" required>
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <label>Masukkan Code Purchase Anda</label>
                                            <div class="col-md-12 form-group">
                                                <input type="text" class="form-control" name="purchase" id="purchase" required>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 d-flex justify-content-end">
                                            <button class="btn btn-info  mb-2 btn-block">Update & Verifikasi Ulang
                                                Licensi</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('assets/vendors/sweetalert/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/vendors/sweetalert/evolution.js') }}"></script>
<script src="{{ asset('js/connection.js') }}"></script>
<script src="{{ asset('js/installer.js') }}"></script>
@endsection