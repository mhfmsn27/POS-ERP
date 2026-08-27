@extends('layouts.rma')
@section('content')
<div class="sign-in-from">
    <div class="text-center">
        <img src="{{asset('images/logo.png')}}" style="max-height: 50px; width: auto;" class="mb-4" alt="POSHUB Logo" />
        <h1 class="mb-0">Tracking Progress RMA</h1>
        <p>Silahkan Masukkan Kode Referensi layanan anda di bawah ini</p>
        <x-admin.validation-component></x-admin.validation-component>
    </div>
    <form class="mt-4" method="POST" action="{{ route('check.rma') }}">
        <div class="form-group">
            <label for="emailAddress">No.Referensi</label>
            <input type="text" name="referensi" placeholder="Masukan No.Referensi disini" value="{{old('referensi')}}" class="form-control mb-0">
        </div>

        <div class="d-inline-block w-100">
            <button type="submit" class="btn btn-primary btn-block">Tracking Sekarang</button>
        </div> 
    </form>
</div>
@endsection