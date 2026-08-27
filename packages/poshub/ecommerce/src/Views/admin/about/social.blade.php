@extends('layouts.admin')
@section('content')

@section('styles') 
<link rel="stylesheet" href="{{asset('ecommerce/css/tab.css')}}"> 
@endsection
<div class="content-page">
      <div class="container-fluid">

            <div class="row">

                  <!-- Component -->
                  <x-ecommerce-tab-blog-component></x-ecommerce-tab-blog-component>
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
                                    <form action="{{route('ecommerce.admin.about.social_store')}}" method="POST" enctype="multipart/form-data" class="form form-horizontal">
                                          @csrf
                                          <div class="form-body">
                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Facebook URL *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="url" class="form-control" value="{{$data->facebook_url}}" name="facebook_url" id="facebook_url" >
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Instagram URL *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="url" class="form-control" value="{{$data->instagram_url}}" name="instagram_url" id="instagram_url" >
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Twitter URL *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="url" class="form-control" value="{{$data->twitter_url}}" name="twitter_url" id="twitter_url" >
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Youtube URL *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="url" class="form-control" value="{{$data->youtube_url}}" name="youtube_url" id="youtube_url" >
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

 
@endsection