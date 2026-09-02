@extends('layouts.welcome')

@section('styles')
<style>
    .tabheader {
        background-color: #fff;
        margin-top: 0;
        margin: 0 !important;
        padding: 0 !important;
    }

    .item-nav {
        margin: 0 !important;
        padding: 0 !important;
    }

    .item-tab {
        width: 100% !important;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        color: black;
    }

    .item_tab {
        text-align: center !important;
    }
</style>

@endsection
@section('content')
<div class="row d-flex align-items-center justify-content-center mt-4">

    @foreach ($packages as $package)
    <div class="col-sm-6 col-xl-3 col-md-6 col-lg-6">
        <div class="card p-3 border-primary pricing-card advanced">
            <div class="card-header d-block text-justified pt-2">
                <p class="fs-30 fw-semibold mb-1 pe-0">{{$package->name}}
                    <!-- <span class="tag bg-primary text-white float-end">Limited Deal</span> -->
                </p>
                <p class="text-justify fw-semibold mb-1">
                    <span class="fs-25">{{number_format($package->price)}} / {{number_format($package->limit_day)}} Hari</span>
                </p>
                <p class="fs-13 mb-2">{{$package->description}}.</p>
            </div>
            <div class="card-body py-4">
                <ul class="pricing-body ps-0">
                    @foreach ($package->details as $detail)
                    <li><i class="fa fa-check py-2 text-primary p-2 fs-16"></i> <strong> </strong> {{$detail->name}}</li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer text-center border-top-0 pt-1">
                <button class="btn btn-lg btn-primary text-white btn-block shadow-sm">
                    <span class="ms-4 me-4">Pilih Layanan</span>
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection