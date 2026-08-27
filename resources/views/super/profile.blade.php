@extends('layouts.super')


@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/dropify/css/dropify.min.css')}}">
@endsection

@section('content')
<div class="container-fluid"> <!-- row -->
    <div class="row row-sm"> <!-- Col -->
        <div class="col-lg-8">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="mb-4 main-content-label">Edit Profil</div>
                    <x-admin.validation-component></x-admin.validation-component>
                    <form class="form-horizontal" action="{{route('admin.profile.change')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Photo Profil</label> </div>
                                <div class="col-md-9">
                                    <input class="dropify" type="file" id="photo" name="photo" data-default-file="{{asset(Auth()->user()->photo)}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Nama Lengkap</label> </div>
                                <div class="col-md-9"> <input type="text" required name="name" class="form-control" placeholder="Masukkan Nama Lengkap" value="{{auth()->user()->name}}"> </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Alamat Email</label> </div>
                                <div class="col-md-9"> <input type="email" required name="email" class="form-control" placeholder="Masukkan Alamat" value="{{auth()->user()->email}}"> </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Nomor Ponsel</label> </div>
                                <div class="col-md-9"> <input type="number" name="phone" required class="form-control" placeholder="Masukkan nomor ponsel" value="{{auth()->user()->phone}}"> </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Jenis Kelamin</label> </div>
                                <div class="col-md-9">
                                    <select class="form-control" name="jk">
                                        <option value="pria" @if(auth()->user()->jk == 'pria') selected @endif>Laki - Laki</option>
                                        <option value="wanita" @if(auth()->user()->jk == 'wanita') selected @endif>Perempuan</option>
                                    </select>
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

        </div> <!-- /Col --> <!-- Col -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4 main-content-label">Ubah Password</div>
                    <form class="form-horizontal" action="{{route('admin.password.change')}}" method="POST">
                        @csrf
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">Password Lama</label>
                                    <input type="password" class="form-control" name="old_password" placeholder="Masukkan password lama anda">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label">Password baru</label>
                                    <input type="password" class="form-control" name="new_password" placeholder="Masukkan Password baru anda">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label">Konfirmasi Password baru</label>
                                    <input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi password baru anda">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-success" type="submit">
                                Ubah Password
                            </button>
                        </div>
                    </form>
                </div> 
            </div>
        </div> <!-- /Col -->
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