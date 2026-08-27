@extends('layouts.admin')
@section('content')
<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="row">

            <div class="col-12 mb-3">
                <nav aria-label="breadcrumb mt-4">
                    <ol class="breadcrumb iq-bg-primary">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="ri-home-4-line mr-1 float-left"></i>Perusahaan</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Daftar Pajak
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">{{$page}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="cTax" class="form form-horizontal">
                            @csrf
                            <div class="form-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('settings.taxrate_name')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="name">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('settings.taxrate_code')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" name="code" class="form-control">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('settings.percentase_tax')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" name="taxrate" class="form-control">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12 d-flex justify-content-between">
                                        <div>
                                            <a href="{{route('taxrate.index')}}" class="btn btn-info mr-3 mb-1">Kembali ke Daftar Pajak</a>
                                        </div>
                                        <div>
                                            <button class="btn btn-primary mr-3 mb-1">Tambah Rate Pajak</button>
                                            <button type="reset" class="btn btn-warning ml-1 mb-1">{{ __('general.reset') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection