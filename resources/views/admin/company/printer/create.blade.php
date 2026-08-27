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
                            Tambah Printer
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
                        <form id="cPrinter" method="POST" class="form form-horizontal">
                            @csrf
                            <div class="form-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('settings.printer_name')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="name" placeholder="{{__('settings.printer_name')}} ">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('general.type')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select class="form-control" name="type" id="type">
                                            <option value="offline">Sharing Printer</option>
                                            <option value="online">Rest Api</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 d-none" id="label-url">
                                        <label>Url Rest Api</label>
                                    </div>
                                    <div class="col-md-8 form-group d-none" id="form-url">
                                        <input type="url" name="url" class="form-control">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <!-- END URL -->
                                    <div class="col-md-4">
                                        <label>{{__('settings.char_by_line')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="number" name="char_per_line" class="form-control">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('settings.ip_address')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" name="ip_address" class="form-control" placeholder="{{__('settings.ip_address')}}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12 d-flex justify-content-between">
                                        <div>
                                            <a href="{{route('printer.index')}}" class="btn btn-info mr-3 mb-1">Kembali ke Daftar Printer</a>
                                        </div>
                                        <div>
                                            <button class="btn btn-primary mr-3 mb-1">{{__('settings.add_printer')}}</button>
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