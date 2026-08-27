@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/dropify/css/dropify.min.css')}}">
<link rel="stylesheet" href="{{asset('ecommerce/css/tab.css')}}">
@endsection
<div class="content-page">
    <div class="container-fluid">

        <div class="row">

            <!-- Component --> 
            <x-admin.validation-component></x-admin.validation-component>
            <!-- End Component -->

            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">{{$page}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('ecommerce.bank.edit',$bank->id)}}" method="POST" enctype="multipart/form-data" class="form form-horizontal">
                            @csrf
                            <div class="form-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Nama Bank *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="bank_name" value="{{$bank->bank_name}}" required placeholder="Masukkan Nama Bank">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Nomor Rekening *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="no_rek" value="{{$bank->no_rek}}" required placeholder="Masukkan Nomor Rekening">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Atas Nama *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="an" value="{{$bank->an}}" required placeholder="Atas Nama">
                                    </div>
                                </div>



                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Logo</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input class="dropify" type="file" id="image" name="image" data-default-file="{{asset($bank->logo)}}">
                                    </div>
                                </div>
                                <div class="col-sm-12 d-flex justify-content-end">
                                    <button class="btn btn-info me-1 mb-1 mr-2">Simpan Perubahan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@section('scripts')
<script src="{{ asset('assets/vendors/dropify/js/dropify.min.js')}}"></script>
<script src="{{ asset('ecommerce/js/admin.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.dropify').dropify();
    });
</script>
@endsection
@endsection