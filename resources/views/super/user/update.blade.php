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
                    <div class="mb-4 main-content-label">Edit Penguna</div>
                    <x-admin.validation-component></x-admin.validation-component>
                    <form class="form-horizontal" action="{{route('administrator.user.edit',$user->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Photo</label> </div>
                                <div class="col-md-9">
                                    <input class="dropify" type="file" id="photo" name="photo" data-default-file="<?= asset($user->image_data); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Nama Lengkap</label> </div>
                                <div class="col-md-9"> <input type="text" required name="name" class="form-control" value="<?=$user->name;?>" placeholder="Masukkan Nama Lengkap"> </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Alamat Email</label> </div>
                                <div class="col-md-9"> <input type="email" required name="email" class="form-control" value="<?=$user->email;?>" placeholder="Masukkan Alamat"> </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Nomor Ponsel</label> </div>
                                <div class="col-md-9"> <input type="number" name="phone" required class="form-control" value="<?=$user->phone;?>" placeholder="Masukkan nomor ponsel"> </div>
                            </div>
                        </div> 
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3"> <label class="form-label">Jenis Kelamin</label> </div>
                                <div class="col-md-9">
                                    <select class="form-control" name="jk">
                                        <option value="pria" @if($user->jk == 'pria') selected @endif >Laki - Laki</option>
                                        <option value="wanita" @if($user->jk == 'wanita') selected @endif>Perempuan</option>
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