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
                            Edit Printer
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
                        <form id="uPrinter" method="POST" class="form form-horizontal">
                            @csrf
                            <div class="form-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('settings.printer_name')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="name" value="{{ old('name',$data->name) }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('general.type')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select class="form-control" name="type" id="type">
                                            @if($data->type == 'offline')
                                            <option value="offline">Sharing Printer</option>
                                            <option value="online">Rest Api</option>
                                            @elseif($data->type == 'online')
                                            <option value="online">Rest Api</option>
                                            <option value="offline">Sharing Printer</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 @if($data->type != 'online') d-none @endif" id="label-url">
                                        <label>Url Rest Api</label>
                                    </div>
                                    <div class="col-md-8 form-group @if($data->type != 'online') d-none @endif" id="form-url">
                                        <input type="hidden" name="id" value="{{ $data->id }}" id="id">
                                        <input type="url" name="url" class="form-control" value="{{ old('url',$data->url) }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('settings.char_by_line')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="number" name="char_per_line" class="form-control" value="{{ old('char_per_line',$data->char_per_line) }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('settings.ip_address')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address',$data->ip_address) }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12 d-flex justify-content-between">
                                        <div>
                                            <a href="{{route('printer.index')}}" class="btn btn-info mr-3 mb-1">Kembali ke Daftar Printer</a>
                                        </div>
                                        <div>
                                            <button class="btn btn-primary mr-3 mb-1">Simpan Perubahan</button>
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