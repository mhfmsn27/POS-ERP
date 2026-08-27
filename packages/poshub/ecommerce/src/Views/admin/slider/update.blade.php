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
                        <form id="uSliders" method="POST" enctype="multipart/form-data" class="form form-horizontal">
                            @csrf
                            <div class="form-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Title Slider *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="hidden" id="idSlider" value="{{$slider->id}}">
                                        <input type="text" class="form-control" name="title" id="title" value="{{$slider->title}}" required placeholder="Masukkan Title">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Subtitle Slider </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="subtitle" value="{{$slider->subtitle}}" id="subtitle" placeholder="Masukkan Sub Title">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Aktifkan Button </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select class="form-control" name="button" id="buttonSlider">
                                            <option value="no" @if($slider->button == 'no') selected @endif >Tidak</option>
                                            <option value="yes" @if($slider->button == 'yes') selected @endif> Iya</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3 button-text @if($slider->button == 'no') d-none @endif">
                                    <div class="col-md-4">
                                        <label>Button Text </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="button_name"  value="{{$slider->button_text}}"  id="button_name" placeholder="Masukkan Nama Button">
                                    </div>
                                </div>

                                <div class="row mb-3 button-url @if($slider->button == 'no') d-none @endif">
                                    <div class="col-md-4">
                                        <label>Button Url </label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="url" class="form-control" name="button_url" id="button_url"  value="{{$slider->button_url}}"  placeholder="Masukkan Button Url">
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('general.upload_image')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input class="dropify" type="file" id="image" name="image" data-default-file="{{asset($slider->image)}}">
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