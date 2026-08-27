@extends('layouts.super')
@section('content')
<div class="main-content-body">
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-end">

                    <a class="btn btn-info" href="{{route('admin.template.create')}}">
                        <i class="fe fe-plus-circle me-2"></i> Tambah Template
                    </a>
                </div>
                <div class="card-body">
                    <x-admin.validation-component></x-admin.validation-component>
                    <div class="table-responsive">
                        <table class="table border-top-0 table-bordered text-nowrap border-bottom" id="basic-datatable">
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
                                        <a href="{{route('admin.template.update',$template->id)}}" class="btn btn-sm btn-warning">
                                            <i class="fe fe-edit"></i>
                                        </a>
                                        <a href="{{route('admin.template.delete',$template->id)}}" class="btn btn-sm btn-danger deletebutton">
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
            searchPlaceholder: 'Cari Template...',
            sSearch: '',
        }
    });
</script>
@endsection