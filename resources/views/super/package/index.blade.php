@extends('layouts.super')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card custom-card">

            <div class="card-header d-block d-flex justify-content-between">

                <div>
                    <div class="card-title mb-2">Daftar Paket Layanan</div>
                    <p class="mb-1">Anda dapat menambahkan atau memperbaharui paket layanan di bawah ini</p>

                    <x-admin.validation-component></x-admin.validation-component>
                </div>
                <div>
                    <a class="btn btn-info" href="{{route('admin.package.create')}}">
                        Tambah Paket
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row d-flex justify-content-center">
    @foreach ($packages as $package)
    <div class="col-xs-6 col-sm-6 col-lg-6 col-xl-3">
        <div class="panel price panel-color">
            <div class="panel-heading bg-primary p-0 text-center">
                <h3>{{$package->name}}</h3>
            </div>
            <div class="panel-body text-center">
                <p class="lead">Rp <strong>{{number_format($package->price)}}</strong></p>
                <p> {{number_format($package->limit_day)}} / Hari</p>
               @if($package->description != null) <p> {{$package->description}}</p> @endif
            </div>
            <ul class="list-group list-group-flush text-center">
                @foreach ($package->details as $detail)
                <li class="list-group-item">{{$detail->name}}</li>    
                @endforeach 
            </ul>
            <div class="panel-footer text-center"> 
                <a class="btn btn-warning" href="{{route('admin.package.update',$package->id)}}">
                    <i class="fe fe-edit"></i>
                </a> 
                <a class="btn btn-danger deletebutton" href="{{route('admin.package.delete',$package->id)}}">
                    <i class="fe fe-trash"></i>
                </a> 
            </div>
        </div>
    </div>
    @endforeach

   
</div>

@endsection