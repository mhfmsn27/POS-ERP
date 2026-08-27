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
                        <a href="#"
                            ><i class="ri-home-4-line mr-1 float-left"></i
                            >Perusahaan</a
                        >
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Daftar Pegawai
                    </li>
                </ol>
            </nav>
        </div>

            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">{{$page}}</h4>
                        </div>
                        @can('add_employee')
                        <div>
                            <a href="<?=route('employee.create');?>" class="btn btn-info">Tambah Data</a>
                        </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('hrm.employee_name')}}</th>
                                        <th>{{__('hrm.designation_name')}}</th>
                                        <th>{{__('general.store')}}</th>
                                        <th>{{__('general.phone')}}</th>
                                        <th>Hutang</th>
                                        <th width="110px"><span class="fa fa-cogs"></span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $no = 1;
                                    @endphp
                                    @foreach ($data as $c)
                                    <tr class="data-product">
                                        <td> {{ $no++ }} </td>
                                        <td> {{ $c->user->name ?? '' }} </td>
                                        <td> {{ $c->designation->name ?? '' }} </td>
                                        <td> {{ $c->user->store->name ?? __('user.all_store') }} </td>
                                        <td> {{ $c->phone ?? '' }} </td>
                                        <td> {{ number_format(abs($c->due_total)) }} </td>
                                        <td>
                                            @can("update_employee")
                                            <a href="{{ route('employee.update',$c->id) }}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                            @endcan
                                            <a href="{{ route('employee.history',$c->id) }}" class="btn btn-sm btn-info"><i class="fa fa-list"></i></a>
                                            @can("delete_employee")
                                            <a href="{{ route('employee.delete',$c->id) }}" class="btn btn-sm btn-danger deletebutton"><i class="fa fa-trash"></i></a>
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