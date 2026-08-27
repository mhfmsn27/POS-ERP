@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/vendors/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('ecommerce/css/tab.css')}}">
@endsection
<div class="content-page">
      <div class="container-fluid">

            <div class="row">

                  <!-- Component -->
                  <x-ecommerce-tab-setting-component></x-ecommerce-tab-setting-component>
                  <x-admin.validation-component></x-admin.validation-component>
                  <!-- End Component -->

                  <div class="col-md-12 col-12">
                        <div class="card card-block card-stretch card-height">
                              <div class="card-header d-flex justify-content-between">
                                    <div class="iq-header-title">
                                           <h4>Data Provinsi</h4>
                                    </div>
                              </div>
                              <div class="card-body">
                                    <div class="table-responsive">
                                          <table class="table table-striped" id="table-1">
                                                <thead>
                                                      <tr>
                                                            <th>Nama</th>
                                                            <th>Jumlah Kota</th>
                                                            <th>Status</th>
                                                            <th width="110px"><span class="fa fa-cogs"></span></th>
                                                      </tr>
                                                </thead>
                                                <tbody>
                                                      @foreach ($data as $c)
                                                      <tr class="data-product">

                                                            <td>{{$c->name}}</td>
                                                            <td>{{count($c->city)}}</td>
                                                            <td>
                                                                  @if($c->status == 'yes')
                                                                  Aktif
                                                                  @else
                                                                  Tidak Aktif
                                                                  @endif
                                                            </td>
                                                            <td>
                                                                  @if($c->status == 'yes')
                                                                  <a href="{{ route('ecommerce.sett.province.status',$c->id) }}" class="btn btn-sm btn-danger"><i class="fa fa-times-circle"></i> Non Aktif</a>
                                                                  @else
                                                                  <a href="{{ route('ecommerce.sett.province.status',$c->id) }}" class="btn btn-sm btn-success"><i class="fa fa-check-circle"></i> Aktifkan</a>
                                                                  @endif
                                                                 
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

@section('scripts')
<script src="{{asset('assets/vendors/datatables/datatables.min.js')}}"></script>
<script src="{{asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/vendors/datatables/datatables.js')}}"></script>
@endsection
@endsection