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
                            >Pengaturan</a
                        >
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Akses Group
                    </li>
                </ol>
            </nav>
        </div>

            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            @can("add_role")
                            <a class="btn btn-md btn-primary float-end" href="{{ route('role.create') }}"><i class="fa fa-plus"></i> {{__('user.add_role')}}</a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th class="text-center"> No </th>
                                        <th>{{__('user.role_name')}}</th>
                                        <th>{{__('user.permission_get')}} : </th>
                                        <th>{{__('action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $no = 1;
                                    @endphp
                                    @foreach($data as $d)
                                    <tr>
                                        <td>{{$no++}} </td>
                                        <td>{{$d->name}}</p>
                                        </td>
                                        <td>
                                            <?php $count = 0; ?>
                                            @foreach($d->permissions as $p)
                                            <?php if ($count == 10) break; ?>
                                            <span class="badge bg-primary text-white">{{ $p['name'] }}</span>
                                            <?php $count++; ?>
                                            @endforeach
                                        </td>
                                        <td>
                                            @can('update_role')
                                            <a href="{{ route('role.update',$d->id) }}" class="btn btn-sm btn-warning "><i class="fa fa-edit"></i></a>
                                            @endcan
                                            @can('delete_role')
                                            <a href="{{route('role.delete',$d->id)}}" class="btn btn-sm btn-danger deletebutton"><i class="fa fa-trash"></i></a>
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