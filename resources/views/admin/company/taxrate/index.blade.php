@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/vendors/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="row">

            <div class="col-12 mb-3">
                <nav aria-label="breadcrumb mt-4">
                    <ol class="breadcrumb iq-bg-primary">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="ri-home-4-line mr-1 float-left"></i>Perusahaan</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Daftar Pajak
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            @can("add_tax")
                            <a class="btn btn-md btn-primary float-end" href="{{route('taxrate.create')}}"><i class="fa fa-plus"></i> {{__('settings.add_taxrate')}}</a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th class="text-center"> No </th>
                                        <th>{{__('settings.taxrate_name')}}</th>
                                        <th>{{__('settings.taxrate_code')}}</th>
                                        <th>{{__('settings.percentase_tax')}}</th>
                                        <th>{{__('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $no = 1;
                                    @endphp
                                    @foreach($data as $d)
                                    <tr>
                                        <td>{{$no++}} </td>
                                        <td> {{$d->name}} </td>
                                        <td> {{$d->code}} </td>
                                        <td> {{$d->taxrate}} </td>
                                        <td>
                                            @can("update_tax")
                                            <a href="{{route('taxrate.update',$d->id)}}" class="btn btn-warning">{{__('update')}}</a>
                                            @endcan
                                            @can("delete_tax")
                                            <a href="{{route('taxrate.delete',$d->id)}}" class="btn btn-danger deletebutton">{{__('delete')}}</a>
                                            @endcan
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