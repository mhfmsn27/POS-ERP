@extends('layouts.super')
@section('content')
<div class="container-fluid">
    <div class="row row-sm">
        <div class="col-12">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="mb-4 main-content-label">Edit Template Pesan</div>
                    <x-admin.validation-component></x-admin.validation-component>
                    <form class="form-horizontal" action="{{route('admin.template.edit',$template->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Nama Template</label> </div>
                                <div class="col-md-9"> <input type="text" required name="name" class="form-control" value="<?= old('name',$template->name); ?>" placeholder="Masukkan Nama Template"> </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">File / Gambar</label> </div>
                                <div class="col-md-9"> <input type="file" name="image" class="form-control"> </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Pesan</label> </div>
                                <div class="col-md-9">
                                    <textarea class="form-control" name="message" style="min-height: 200px;">{{old('message',$template->message)}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-success" type="submit">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection