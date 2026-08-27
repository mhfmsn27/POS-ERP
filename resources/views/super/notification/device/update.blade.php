@extends('layouts.super')
@section('content')
<div class="container-fluid">
    <div class="row row-sm">
        <div class="col-12">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="mb-4 main-content-label">Edit Whatsapp Device</div>
                    <x-admin.validation-component></x-admin.validation-component>
                    <form class="form-horizontal" action="{{route('admin.device.edit',$device->id)}}" method="POST" enctype="multipart/form-data">
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
@endsection