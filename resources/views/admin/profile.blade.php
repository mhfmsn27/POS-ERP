@extends('layouts.admin')
@section('content')
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/dropify/css/dropify.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection
<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">{{__("sidebar.update_profile")}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="uProfile" method="POST" enctype="multipart/form-data" class="form">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="form-group has-icon-left">
                                            <label for="system_name">{{__('general.image')}}</label>
                                            <div class="position-relative">
                                                <input class="dropify" type="file" id="photo" name="photo" data-default-file="{{asset(Auth()->user()->photo)}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-xl-6 col-sm-12 mb-3">
                                        <div class="form-group has-icon-left">
                                            <label for="email-id-icon">{{ __('general.name') }}</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" name="name" value="{{ old('name',Auth()->user()->name) }}" id="name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-xl-6 col-sm-12 mb-3">
                                        <div class="form-group has-icon-left">
                                            <label for="email-id-icon">{{ __('general.email') }}</label>
                                            <div class="position-relative">
                                                <input type="email" class="form-control" name="email" value="{{ old('email',Auth()->user()->email) }}" id="email" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-12 col-sm-12 mb-3">
                                        <div class="form-group has-icon-left">
                                            <label for="email-id-icon">{{ __('sidebar.timezone') }}</label>
                                            <div class="position-relative">
                                                <select class="form-control choices" name="timezone">
                                                    @foreach($timezone as $t => $value)
                                                    <option value="{{$value}}" @if($value==Auth()->user()->timezone) selected @endif>{{$t}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 d-flex justify-content-end mt-4">
                                        <button class="btn btn-info me-1 mb-1">{{__('general.save')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">{{__('sidebar.update_password')}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('change.password')}}" method="POST" enctype="multipart/form-data" class="form form-horizontal">
                            @csrf
                            <div class="form-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('user.password')}} *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="password" class="form-control" name="password" id="password" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('general.confirm_password')}}</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="password" class="form-control" name="confirm" id="password" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-info me-1 mb-1">{{__('general.save')}}</button>
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
@section('scripts')
<script src="{{ asset('assets/vendors/dropify/js/dropify.min.js')}}"></script>
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script>
    $(document).ready(function() {
        // Basic
        $('.dropify').dropify();
    });
</script>
@endsection
@endsection