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
            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">{{$page}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('hrm.employee_name')}}</th>
                                        <th>{{__('attendance.entry')}}</th>
                                        <th>{{__('attendance.out')}}</th>
                                        <th>{{__('attendance.late')}}</th>
                                        <th>{{__('attendance.total')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $c)
                                    <tr>
                                        <td>#</td>
                                        <td>{{ $c->user->name }}</td>
                                        <td>{{ $c->check_int }}</td>
                                        <td>{{ $c->check_out }}</td>
                                        <td>{{ $c->late }}</td>
                                        <td>{{ $c->total_work }}</td>
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