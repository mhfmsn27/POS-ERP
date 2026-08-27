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
            <x-ecommerce-tab-media-component></x-ecommerce-tab-media-component>
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
                        <form id="uFeatured" method="POST" enctype="multipart/form-data" class="form form-horizontal">
                            @csrf
                            <div class="form-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Title *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="hidden" id="idFeatured" value="{{$featured->id}}">
                                        <input type="text" class="form-control" name="title" id="title" value="{{$featured->title}}" required placeholder="Masukkan Title">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Subtitle </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="subtitle" value="{{$featured->subtitle}}" id="subtitle" placeholder="Masukkan Sub Title">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Posisi </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select class="form-control" name="position">
                                            <option value="about" @if($featured->position == 'about') selected @endif >About </option>
                                            <option value="footer" @if($featured->position == 'footer') selected @endif> Footer</option>
                                        </select>
                                    </div>
                                </div>
 
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('general.upload_image')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input class="dropify" type="file" id="image" name="image" data-default-file="{{asset($featured->image)}}">
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