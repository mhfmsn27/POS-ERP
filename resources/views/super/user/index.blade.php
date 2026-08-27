@extends('layouts.super')
@section('content')
<div class="main-content-body">
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-end">
                    
                    <a class="btn btn-info" href="{{route('administrator.user.create')}}">
                        <i class="fe fe-plus-circle me-2"></i> Tambah Pengguna
                    </a>
                </div>
                <div class="card-body">
                    <x-admin.validation-component></x-admin.validation-component>
                    <div class="table-responsive">
                        <table class="table border-top-0 table-bordered text-nowrap border-bottom" id="basic-datatable">
                            <thead>
                                <tr>
                                    <th class="wd-15p border-bottom-0">Nama</th>
                                    <th class="wd-15p border-bottom-0">Email</th>
                                    <th class="wd-20p border-bottom-0">Phone</th> 
                                    <th class="wd-10p border-bottom-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <th>
                                        {{$user->name}}
                                    </th>
                                    <th>
                                        {{$user->email}}
                                    </th>
                                    <th>
                                        {{$user->phone}}
                                    </th> 
                                  
                                    <th>
                                        <a href="{{route('administrator.user.update',$user->id)}}" class="btn btn-sm btn-warning">
                                            <i class="fe fe-edit"></i>
                                        </a>
                                        <a href="{{route('administrator.user.delete',$user->id)}}" class="btn btn-sm btn-danger deletebutton">
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
            searchPlaceholder: 'Cari Pengguna...',
            sSearch: '',
        }
    });
</script>
@endsection