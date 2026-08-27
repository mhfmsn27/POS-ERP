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
                            Tambah Device
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
                        <form class="form-horizontal" action="{{route('device.edit',$device->id)}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3"> <label class="form-label">Nama Device</label> </div>
                                    <div class="col-md-9"> <input type="text" required name="name" class="form-control" value="<?= $device->name; ?>" placeholder="Masukkan Nama Device"> </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3"> <label class="form-label">ApiKey Local</label> </div>
                                    <div class="col-md-9"> <input type="text" required name="api" class="form-control" value="<?= $device->apikey; ?>" placeholder="Masukkan Api Local"> </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3"> <label class="form-label">Device ID</label> </div>
                                    <div class="col-md-9"> <input type="text" name="device" required class="form-control" value="<?= $device->deviceid; ?>" placeholder="Masukkan Device ID"> </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-success" type="submit">
                                    Edit Device
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