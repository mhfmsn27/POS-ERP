@extends('layouts.super')
@section('content')
<div class="main-content-body">
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-end">

                    <a class="btn btn-info" href="{{route('admin.device.create')}}">
                        <i class="fe fe-plus-circle me-2"></i> Tambah Device
                    </a>
                </div>
                <div class="card-body">
                    <x-admin.validation-component></x-admin.validation-component>
                    <div class="table-responsive">
                        <table class="table border-top-0 table-bordered text-nowrap border-bottom" id="basic-datatable">
                            <thead>
                                <tr>
                                    <th class="wd-15p border-bottom-0">Nama</th>
                                    <th class="wd-15p border-bottom-0">ApiKey</th>
                                    <th class="wd-20p border-bottom-0">Device ID</th>
                                    <th class="wd-10p border-bottom-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($devices as $device)
                                <tr>
                                    <th>
                                        {{$device->name}}
                                    </th>
                                    <th>
                                        {{$device->apikey}}
                                    </th>
                                    <th>
                                        {{$device->deviceid}}
                                    </th>

                                    <th>
                                        <a href="{{route('admin.device.update',$device->id)}}" class="btn btn-sm btn-warning">
                                            <i class="fe fe-edit"></i>
                                        </a>
                                        <a href="{{route('admin.device.delete',$device->id)}}" class="btn btn-sm btn-danger deletebutton">
                                            <i class="fe fe-trash"></i>
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


@endsection

@section('scripts')
<script src="{{asset('admin/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>

<script>
    $('#basic-datatable').DataTable({
        language: {
            searchPlaceholder: 'Cari Device...',
            sSearch: '',
        }
    });
</script>
@endsection