@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/dropify/css/dropify.min.css')}}">
@endsection
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
                        Edit Espedisi
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
                            <a href="<?= route('courier.index'); ?>" class="btn btn-info"> Kembali ke Daftar Ekspedisi </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="<?= route('courier.edit', $courier->id); ?>" enctype="multipart/form-data" method="POST" class="form form-horizontal">
                            @csrf
                            <div class="form-body">


                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Logo Ekspedisi</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input class="dropify" type="file" id="logo" name="logo" data-default-file="<?= asset($courier->logo); ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Nama Ekspedisi</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="name" value="<?= $courier->name; ?>" placeholder="Masukkan Nama Ekspedisi">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Kode Ekspedisi</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="code" value="<?= $courier->code; ?>" placeholder="Masukkan Kode Ekspedisi">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12 d-flex justify-content-end">
                                        <div>
                                            <button class="btn btn-primary mr-3 mb-1">Simpan Perubahan Data</button>
                                            <button type="reset" class="btn btn-warning ml-1 mb-1">Reset Form</button>
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

@section('scripts')
<script src="{{ asset('assets/vendors/dropify/js/dropify.min.js')}}"></script>
<script>
    $(document).ready(function() {
        // Basic
        $('.dropify').dropify();
    });
</script>
@endsection