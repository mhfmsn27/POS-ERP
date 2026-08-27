@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/dropify/css/dropify.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection
<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="row">

            <div class="col-12 mb-3">
                <nav aria-label="breadcrumb mt-4">
                    <ol class="breadcrumb iq-bg-primary">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="ri-home-4-line mr-1 float-left"></i>Pengaturan</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Pengguna
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
                            <a href="{{route('user.index')}}" class="btn btn-info">Daftar Pengguna</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="uUsers" method="POST" enctype="multipart/form-data" class="form form-horizontal">
                            @csrf
                            <div class="form-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('user.name')}} *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="hidden" name="id" value="{{ $users->id }}">
                                        <input type="text" class="form-control" name="name" value="{{ old('name',$users->name) }}" id="name" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('general.email')}} *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="email" class="form-control" name="email" value="{{ old('email',$users->email) }}" id="email" required placeholder="{{__('general.email')}}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__("user.password")}} *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="{{__("user.password")}}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('store.choose_store')}} *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        @foreach ($store as $s)
                                        <div class="custom-control custom-checkbox custom-checkbox-color custom-control-inline">
                                            <input type="checkbox" name="store[]" value="{{$s->id}}" class="custom-control-input bg-primary" id="store-{{$s->id}}" @if(!empty($access[$s->id])) checked @endif  >
                                            <label class="custom-control-label" for="store-{{$s->id}}">{{$s->name}} </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Pilih Group Role *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select class="form-control" name="role_id">
                                            @foreach ($data as $d)
                                            <option value="{{ $d->id }}" @if($d->id == $users->role) selected @endif>{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Option is Sell -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Akses Penjual ? *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select class="form-control" id="is_sell" name="is_sell">
                                            <option value="no" @if($users->is_sell == 'no') selected @endif>Tidak</option>
                                            <option value="yes" @if($users->is_sell == 'yes') selected @endif>Iya</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- End Option is Sell -->

                                <div class="row mb-3 percentaseamount @if($users->is_sell == 'no') d-none @endif">
                                    <div class="col-md-4">
                                        <label>Nominal Komisi ( Persentase ) *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="number" class="form-control" name="commission_percentase" value="{{$users->commission_percentase}}" id="commission_percentase" required value="0" min="0" max="100">
                                    </div>
                                </div>
                                <div class="row mb-3 maxpersentase @if($users->is_sell == 'no') d-none @endif">
                                    <div class="col-md-4">
                                        <label>Maximal Komisi Per-transaksi ( Jumlah Fix ) *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="max_commission" value="{{number_format($users->max_commission)}}" id="max_commission" required value="0">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('general.image')}} *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input class="dropify" type="file" id="photo" name="photo" data-default-file="{{ asset(old('photo',$users->photo ?? ''))}}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12 d-flex justify-content-end">
                                        <button class="btn btn-info me-1 mb-1">{{ __('general.save') }}</button>
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
<script src="{{ asset('assets/vendors/dropify/js/dropify.min.js') }}"></script>
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script>
    $('#uUsers').on('keyup', function(e) {
        pp = formatRupiah($(this).find('#max_commission').val())
        $(this).find('#max_commission').val(pp)
    })

    $("#is_sell").on("change", function(e) {
        var value = $(this).val();

        if (value == 'yes') {
            $(".percentaseamount").removeClass("d-none");
            $(".maxpersentase").removeClass("d-none");
        } else {
            $(".percentaseamount").addClass("d-none");
            $(".maxpersentase").addClass("d-none");
        }
    })

    $(document).ready(function() {
        $('.dropify').dropify();
    });
</script>
@endsection
@endsection