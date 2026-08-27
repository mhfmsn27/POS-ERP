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
                                    <form action="{{route('ecommerce.blogs.store')}}" method="POST" enctype="multipart/form-data" class="form form-horizontal">
                                          @csrf
                                          <div class="form-body">
                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Title Slider *</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" name="title" id="title" required placeholder="Masukkan Title">
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Kategori </label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <select class="form-control" name="category_id" required>
                                                                  <option value=""> Pilih Kategori</option>
                                                                  @foreach($category as $c)
                                                                  <option value="{{$c->id}}">{{$c->name}}</option>
                                                                  @endforeach

                                                            </select>
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Deskripsi Singkat </label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <textarea class="form-control" name="short_description"></textarea>
                                                      </div>
                                                </div>

                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>Deskripsi </label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <textarea id="summernote" style="height: 350px" name="description">{{ old('description') }}</textarea>
                                                      </div>
                                                </div>




                                                <div class="row mb-3">
                                                      <div class="col-md-4">
                                                            <label>{{__('general.upload_image')}}</label>
                                                      </div>
                                                      <div class="col-md-8 form-group">
                                                            <input class="dropify" type="file" id="image" name="thumbnail" data-default-file="">
                                                      </div>
                                                </div>
                                                <div class="col-sm-12 d-flex justify-content-end">
                                                      <button class="btn btn-info me-1 mb-1 mr-2">{{__('general.add')}}</button>
                                                      <button type="reset" class="btn btn-secondary ml-1 mb-1">{{ __('general.reset') }}</button>
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