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
                            Daftar Printer
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            @can("add_printer")
                            <a class="btn btn-md btn-primary float-end" href="{{route('printer.create')}}"><i class="fa fa-plus"></i> {{__('settings.add_printer')}}</a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th class="text-center"> No </th>
                                        <th>{{__('settings.printer_name')}}</th>
                                        <th>{{__('general.type')}}</th>
                                        <th>Url Rest Api</th>
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
                                        <td> {{$d->type}} </td>
                                        <td> {{$d->url}} </td>
                                        <td>

                                            @can("update_printer")
                                            <a href="{{route('printer.update',$d->id)}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                            @endcan

                                            @can("delete_printer")
                                            <a href="{{route('printer.delete',$d->id)}}" class="btn btn-danger deletebutton"><i class="fa fa-edit"></i></a>
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