@extends('layouts.super')

@section('content')

<div class="row">
    @foreach ($merchants as $merchant)
    <div class="col-xl-3 col-md-6">
        <div class="card custom-card bg-info">
            <div class="card-body">
                <div class="d-flex align-items-center w-100">
                    <div class="me-2"> <span class="avatar avatar-rounded"> <img src="{{asset($merchant->owner->image_data ?? '')}}" alt="img"> </span> </div>
                    <div class="">
                        <div class="fs-15 fw-semibold">{{$merchant->name}}</div>
                        <p class="mb-0 text-fixed-white op-7 fs-12">Sejak {{$merchant->created_at->format('Y-m-d')}} </p>
                    </div>
                    <div class="ms-auto"> 
                        <a aria-label="anchor" href="{{route('admin.merchant.detail',$merchant->id)}}" class="text-white"><i class="fe fe-eye"></i></a> 
                    </div>
                </div>
            </div>
        </div>
    </div>    
    @endforeach
    
</div>

@endsection