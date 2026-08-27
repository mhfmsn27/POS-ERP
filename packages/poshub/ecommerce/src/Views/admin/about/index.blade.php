@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/dropify/css/dropify.min.css')}}">
<link rel="stylesheet" href="{{asset('ecommerce/css/tab.css')}}">
<link href="{{ asset('assets/vendors/summernote/summernote.min.css') }}" rel="stylesheet">
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
                                    <form action="{{route('ecommerce.admin.about.store')}}" method="POST" enctype="multipart/form-data" class="form form-horizontal">
                                          @csrf
                                          <div class="form-body">
                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Title  *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" value="{{$data->about_title}}" name="about_title" id="title" required placeholder="Masukkan Title">
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Copyright  *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" value="{{$data->copyright}}" name="copyright" id="copyright" required>
                                                      </div>
                                                </div> 

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Deskripsi </label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <textarea id="summernote" style="height: 350px" name="about_text">{{ old('description',$data->about_text) }}</textarea>
                                                      </div>
                                                </div> 


                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>{{__('general.upload_image')}}</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input class="dropify" type="file" id="image" name="about_image" data-default-file="{{asset($data->about_image)}}">
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
<script src="{{ asset('assets/vendors/summernote/summernote.min.js') }}"></script>
<script src="{{ asset('assets/vendors/dropify/js/dropify.min.js')}}"></script>
<script src="{{ asset('ecommerce/js/admin.js')}}"></script>
<script>
      $(document).ready(function() {
            $('.dropify').dropify();

            $('#summernote').summernote({
                  tabsize: 2,
                  height: 150,
                  width: '100%',
            })
      });
</script>
@endsection
@endsection