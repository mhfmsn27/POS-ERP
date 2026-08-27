@extends('layouts.super')

@section('content')

<div class="row row-sm">
    <div class="col-lg-4">
        <div class="card mg-b-20">
            <div class="card-body">
                <div class="ps-0">
                    <div class="main-profile-overview">

                        <div class="d-flex justify-content-between mg-b-20">
                            <div>
                                <h5 class="main-profile-name">{{$merchant->name}}</h5>
                                <p class="main-profile-name-text">{{$merchant->owner->name ?? ''}} ( Owner ) </p>
                            </div>
                        </div>

                        <div class="main-profile-work-list">
                            <ul>
                                <li>Tanggal Bergabung {{$merchant->created_at->format('Y-m-d')}} </li>
                                <li>{{number_format($merchant->user->count())}} Pengguna Terdaftar </li>
                                <li>{{number_format($merchant->store->count())}} Cabang / Toko </li>
                                <li>{{number_format($merchant->supplier()->count())}} Supplier </li>
                            </ul>
                        </div>
                    </div><!-- main-profile-overview -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="main-content-body main-content-body-profile">
            <div class="">
                <div class="wideget-user-tab">
                    <div class="tab-menu-heading">
                        <div class="tabs-menu1">
                            <ul class="nav" role="tablist">
                                <li class=""><a href="#tab-51" class="active show" data-bs-toggle="tab" aria-selected="true" role="tab">Toko / Cabang</a></li>
                                <li><a href="#tab-61" data-bs-toggle="tab" class="" aria-selected="false" tabindex="-1" role="tab">Pengguna</a></li>
                                <li><a href="#tab-81" data-bs-toggle="tab" class="" aria-selected="false" tabindex="-1" role="tab">Suppliers</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane p-0 border-0 active show" id="tab-51" role="tabpanel">
                    <div class="row">
                        @foreach ($merchant->store as $store)
                        <div class="col-md-4">
                            <div class="card custom-card">
                                <div class="card-body">
                                    <h6 class="card-title fw-semibold mb-3">{{$store->name}}</h6>
                                    @if($store->store_package)
                                    <p class="card-text">{{$store->store_package->end_date ?? ''}}</p>
                                    @else
                                    <p class="card-text">Tidak ada paket</p>
                                    @endif
                                    <p class="card-text">{{$store->address}}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="tab-pane p-0 border-0" id="tab-61" role="tabpanel">
                    <ul class="widget-users row ps-0 mb-5">
                        @foreach ($merchant->user as $user)
                        <li class="col-xl-4 col-lg-6  col-md-6 col-sm-12 col-12">
                            <div class="card border p-0"> <a href="#">
                                    <div class="card-body text-center">
                                        <span class="avatar avatar-xxl brround cover-image">
                                            <img src="{{asset($user->image_data)}}" alt="img">
                                        </span>
                                        <h5 class="fs-16 mb-0 mt-3 text-dark fw-semibold">{{$user->name}}</h5> <span class="text-muted">{{$user->email}}</span>
                                    </div>
                                </a>
                                <div class="card-footer text-center">
                                    <div class="row user-social-detail">
                                        @if($user->status == 'active')
                                        <a href="{{route('admin.merchant.user.activation',$user->id)}}" class="btn btn-danger text-white text-center"> <i class="fe fe-power text-white"></i> </a>
                                        @else
                                        <a href="{{route('admin.merchant.user.activation',$user->id)}}" class="btn btn-info text-white text-center"> <i class="fe fe-check-circle text-white"></i> </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="tab-pane p-0 border-0" id="tab-81" role="tabpanel">
                    <div class="row">
                        @foreach ($merchant->supplier()->get() as $supplier)
                        <div class="col-xl-6 col-lg-12 col-md-12">
                            <div class="card border p-0 over-flow-hidden">
                                <div class="media card-body media-xs overflow-visible ">
                                    <div class="media-body valign-middle"> <a href="#" class=" fw-semibold text-dark">{{$supplier->name}}</a>
                                        <p class="text-muted mb-0 text-break">{{$supplier->email}}</p>
                                        <p class="text-muted mb-0 text-break">{{$supplier->phone}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection