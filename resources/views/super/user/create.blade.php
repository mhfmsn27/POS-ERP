@extends('layouts.super')


@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/dropify/css/dropify.min.css')}}">
@endsection

@section('content')
<div class="container-fluid"> <!-- row -->
    <div class="row row-sm"> <!-- Col -->
        <div class="col-12">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="mb-4 main-content-label">Tambah Penguna</div>
                    <x-admin.validation-component></x-admin.validation-component>
                    <form class="form-horizontal" action="{{route('administrator.user.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Photo</label> </div>
                                <div class="col-md-9">
                                    <input class="dropify" type="file" id="photo" name="photo" data-default-file="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Nama Lengkap</label> </div>
                                <div class="col-md-9"> <input type="text" required name="name" class="form-control" placeholder="Masukkan Nama Lengkap" > </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Alamat Email</label> </div>
                                <div class="col-md-9"> <input type="email" required name="email" class="form-control" placeholder="Masukkan Alamat" > </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Nomor Ponsel</label> </div>
                                <div class="col-md-9"> <input type="number" name="phone" required class="form-control" placeholder="Masukkan nomor ponsel" > </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Password Pengguna</label> </div>
                                <div class="col-md-9"> <input type="password" name="password" required class="form-control" placeholder="Masukkan Password" > </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Jenis Kelamin</label> </div>
                                <div class="col-md-9">
                                    <select class="form-control" name="jk">
                                        <option value="pria" >Laki - Laki</option>
                                        <option value="wanita" >Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-success" type="submit">
                                Tambahkan Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div> <!-- /Col --> <!-- Col --> 
    </div> <!-- row closed -->
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/dropify/js/dropify.min.js')}}"></script>
<script>
    $(document).ready(function() {
        // Basic
        $('.dropify').dropify();
    });
</script>
@endsection