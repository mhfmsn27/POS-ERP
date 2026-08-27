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
                            <a href="#"><i class="ri-home-4-line mr-1 float-left"></i>Notifikasi</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Daftar Template Notifikasi
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <a class="btn btn-md btn-primary float-end" href="{{ route('template.create') }}"><i class="fa fa-plus"></i> Tambah Device</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table border-top-0 table-bordered text-nowrap border-bottom" id="table-1">
                                <thead>
                                    <tr>
                                        <th class="wd-15p border-bottom-0">Nama Template</th>
                                        <th class="wd-15p border-bottom-0">Photo / File</th>
                                        <th class="wd-10p border-bottom-0">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($templates as $template)
                                    <tr>
                                        <th>
                                            {{$template->name}}
                                        </th>
                                        <th>
                                            @if($template->file)
                                            <img src="{{asset($template->file)}}" class="w-25" />
                                            @endif
                                        </th>
                                        <th>
                                            <a href="{{route('template.update',$template->id)}}" class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{route('template.delete',$template->id)}}" class="btn btn-sm btn-danger deletebutton">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </th>
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