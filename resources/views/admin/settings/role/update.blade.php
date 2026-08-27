@extends('layouts.admin')
@section('content')
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
                        Edit Group Pengguna
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
                        <div>
                            <a href="{{route('role.index')}}" class="btn btn-info">Daftar Akses Group</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('role.store', 'update') }}" method="POST" enctype="multipart/form-data" class="form form-horizontal">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>{{__('user.role_name')}} *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="hidden" name="id" value="{{ $role->id }}" id="role_id">
                                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name',$role->name) }}" required>
                                    </div>
 
                                    <div class="table-responsive">
                                        <table class="table table" id="table-1">
                                            <thead>
                                                <tr>
                                                    <th>{{__('user.permission_name')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($used as $p)
                                                <tr class="permission_change">
                                                    <td>
                                                        <div class="form-check">
                                                            <div class="custom-control custom-checkbox">
                                                                @php
                                                                if($p['used'] == 'yes') {
                                                                $check = 'checked id="permission_id"';
                                                                } else {
                                                                $check = '';
                                                                }
                                                                @endphp
                                                                <input type="hidden" value="{{ $p['id'] }}" id="id_permission">
                                                                <input type="checkbox" class="form-check-input form-check-primary" <?= $check; ?> name="permission_id[]" value="{{ $p['id'] }}">
                                                                <label class="form-check-label" for="permission_id">{{ $p['name'] }}</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-sm-12 d-flex justify-content-end">
                                        <button type="submit" id="send" class="btn btn-info me-1 mb-1">{{ __('save') }}</button> 
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(".permission_change").on("click", "#permission_id", function() {
        permission = $(this).closest(".permission_change");
        var id = permission.find("#id_permission").val()
        var role = $("#role_id").val()
        $.ajax({
            url: domain + domainpath + "/pos-admin/preferensi/roles/role-permission-delete/" + id + "/" + role,
            type: "GET",
            data: "",
            success: function(data, json, errorThrown) {
                var dataContent = "";
                var buttonContent = "";
                 console.log(data);
            },

            cache: false,
            contentType: false,
            processData: false,
        });
    })
</script>
@endsection
@endsection