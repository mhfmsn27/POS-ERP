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
                        <a href="#"
                            ><i class="ri-home-4-line mr-1 float-left"></i
                            >Perusahaan</a
                        >
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Tambah Jabatan
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
                        <div>
                            <a href="{{route('designation.index')}}" class="btn btn-info">Daftar Designation</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="cDesignation" method="POST" class="row">
                            <div class="col-md-6 mb-4">
                                <h6>{{__('hrm.department_name')}}</h6>
                                <div class="form-group">
                                    <select class="form-control" name="department_id" id="department">
                                        <option value="">{{__('hrm.choose_department')}}</option>
                                        @foreach($data as $d)
                                        <option value="{{$d->id}}" @if($d->id == old('department_id')) selected @endif>{{$d->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h6>{{__('hrm.designation_name')}}</h6>
                                <div class="form-group">
                                    <input type="text" name="name" value="{{old('name')}}" id="name" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button class="btn btn-primary me-1 mb-1">{{__('general.add')}}</button>
                                <a href="{{route('designation.index')}}" class="btn btn-light-secondary me-1 mb-1">{{__('general.back')}}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection