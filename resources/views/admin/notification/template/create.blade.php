@extends('layouts.admin')
@section('content')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">

            <div class="col-12 mb-3">
                <nav aria-label="breadcrumb mt-4">
                    <ol class="breadcrumb iq-bg-primary">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="ri-home-4-line mr-1 float-left"></i>Notifikasi</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Tambah Template
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
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal" action="{{route('template.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3"> <label class="form-label">Nama Template</label> </div>
                                    <div class="col-md-9"> <input type="text" required name="name" class="form-control" value="<?= old('name'); ?>" placeholder="Masukkan Nama Template"> </div>
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
                                        <textarea class="form-control" name="message" style="min-height: 200px;">{{old('message')}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-success" type="submit">
                                    Tambahkan Template
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection