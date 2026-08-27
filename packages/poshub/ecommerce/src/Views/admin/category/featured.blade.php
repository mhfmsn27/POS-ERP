@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/vendors/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('ecommerce/css/tab.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select3/dist/css/select2.min.css') }}" />
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
                                          <a class="btn btn-md btn-primary float-end text-white" role="button" data-toggle="modal" data-target="#addCategory"><i class="fa fa-plus-circle"></i> Tambah Baru</a>
                                    </div>
                              </div>
                              <div class="card-body">
                                    <div class="table-responsive">
                                          <table class="table table-striped" id="table-1">
                                                <thead>
                                                      <tr>
                                                            <th style="width:70px;text-align: center;"><span class="fa fa-image"></span></th>
                                                            <th>Nama Kategori</th>
                                                            <th width="110px"><span class="fa fa-cogs"></span></th>
                                                      </tr>
                                                </thead>
                                                <tbody>
                                                      @foreach ($data as $c)
                                                      <tr class="data-product">
                                                            <td style="width:70px;">
                                                                  <a href="javascript:void(0)" data-lity="">
                                                                        <img width="50px" src="{{ asset($c->image) }}">
                                                                  </a>
                                                            </td>
                                                            <td>{{$c->name}}</td>

                                                            <td>
                                                                  <a href="{{ route('ecommerce.content.fcategory.delete',$c->id) }}" class="btn btn-sm btn-danger deletebutton"><i class="fa fa-trash"></i></a>
                                                            </td>
                                                      </tr>
                                                      @endforeach
                                                </tbody>
                                          </table>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</div>

<div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="add-category" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-lg " role="document">
            <form method="POST" action="{{route('ecommerce.content.fcategory.store')}}" class="modal-content">
                  @csrf
                  <div class="modal-header header-modal ">
                        <h5 class="modal-title" id="">Opsi Kategori Unggulan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <i data-feather="x"></i> </button>
                  </div>
                  <div class="modal-body">
                        <div class="form form-horizontal">
                              <div class="form-body p-2">
                                    <div class="row" id="">

                                          <div class="col-md-12 form-group">
                                                <label>Masukkan Kategori yang Akan Ditampilkan Di Website</label>
                                                <select id='categoryData' required name='category[]' multiple style='width: 100%;'>
                                                      @foreach($category as $c)
                                                      <option value='{{$c->id}}'>{{$c->name}} </option>
                                                      @endforeach
                                                </select>
                                          </div>

                                    </div>
                                    <br>
                              </div>
                        </div>
                  </div>
                  <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                              <i class="bx bx-x d-block d-sm-none"></i>
                              <span class="d-none d-sm-block">{{ __('general.close') }}</span>
                        </button>
                        <button type="submit" class="btn btn-primary ml-1">
                              <i class="bx bx-check d-block d-sm-none"></i>
                              <span class="d-none d-sm-block">Tampilkan Kategori</span>
                        </button>
                  </div>
            </form>
      </div>
</div>
@section('scripts')
<script src="{{asset('assets/vendors/datatables/datatables.min.js')}}"></script>
<script src="{{asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/vendors/datatables/datatables.js')}}"></script>
<script src="{{ asset('assets/vendors/select3/dist/js/select2.full.min.js') }}"></script>
<script>
      $(document).ready(function() {
            $('#categoryData').select2();
      });
</script>
@endsection
@endsection