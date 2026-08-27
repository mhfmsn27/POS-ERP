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
                            <a class="btn btn-md btn-primary float-end" href="{{ route('ecommerce.curir.add') }}"><i class="fa fa-plus-circle"></i> Tambah Baru</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th style="width:70px;text-align: center;"><span class="fa fa-image"></span></th>
                                        <th>Nama</th>
                                        <th>Kode</th>
                                        <th>Status</th>
                                        <th width="110px"><span class="fa fa-cogs"></span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $c)
                                    <tr class="data-product">
                                        <td style="width:70px;">
                                            <a href="javascript:void(0)" data-lity="">
                                                <img width="50px" src="{{ asset($c->logo) }}">
                                            </a>
                                        </td>
                                        <td>{{$c->name}}</td>
                                        <td>{{$c->code}}</td>
                                        <td>
                                            @if($c->status == 'yes')
                                            Aktif
                                            @else
                                            Tidak Aktif
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('ecommerce.curir.update',$c->id) }}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                            <a href="{{ route('ecommerce.curir.delete',$c->id) }}" class="btn btn-sm btn-danger deletebutton"><i class="fa fa-trash"></i></a>
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