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
                    <div class="mb-4 main-content-label">Pengaturan Sistem</div>
                    <x-admin.validation-component></x-admin.validation-component>
                    <form class="form-horizontal" action="{{route('administrator.update')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6 col-sm-12 mt-2">
                                    <label class="form-label">White Logo</label>
                                    <input class="dropify" type="file" id="photo" name="white_logo" data-default-file="<?= asset($settings->white_logo); ?>">
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-2">
                                    <label class="form-label">Drak Logo</label>
                                    <input class="dropify" type="file" id="photo" name="dark_logo" data-default-file="<?= asset($settings->dark_logo); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-lg-6 col-sm-12 mt-2">
                                    <label class="form-label">Pajak Pembelian Paket</label>
                                    <input type="number"  name="tax" class="form-control" value="<?=(int)$settings->tax;?>">
                                </div> 
                                <div class="col-lg-6 col-sm-12 mt-2">
                                    <label class="form-label">Midtrans Merchant</label>
                                    <input type="text"  name="midtrans_key" class="form-control" value="<?=$settings->midtrans_key;?>">
                                </div> 
                                <div class="col-lg-6 col-sm-12 mt-2">
                                    <label class="form-label">Midtrans Client Key</label>
                                    <input type="text"  name="midtrans_client" class="form-control" value="<?=$settings->midtrans_client;?>">
                                </div> 
                                <div class="col-lg-6 col-sm-12 mt-2">
                                    <label class="form-label">Midtrans Server Key</label>
                                    <input type="text"  name="midtrans_server" class="form-control" value="<?=$settings->midtrans_server;?>">
                                </div> 
                                <div class="col-lg-6 col-sm-12 mt-2">
                                    <label class="form-label">Whatsapp Server Key</label>
                                    <input type="text"  name="whatsapp_server" class="form-control" value="<?=$settings->whatsapp_server;?>">
                                </div> 
                                <div class="col-lg-6 col-sm-12 mt-2">
                                    <label class="form-label">Whatsapp Phone Key</label>
                                    <input type="text"  name="whatsapp_phone" class="form-control" value="<?=$settings->whatsapp_phone;?>">
                                </div> 
                               
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-success" type="submit">
                                Simpan Pengaturan
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