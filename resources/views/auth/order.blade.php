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
<div class="wrapper">
    <div class="p-4">
        <div class="page-content">
            <div class="container-fluid">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col-lg-12 tabheader mb-4">
                            <ul class="iq-edit-profile d-flex nav nav-pills">
                                <!-- Type Account -->
                                <li class="col item-nav">
                                    <a class="nav-link item-tab active" href="{{route('store.choose')}}">
                                        Pilih Toko / Cabang
                                    </a>
                                </li>
                                <!-- End Type Account -->

                                <!-- Purchase Package -->
                                <li class="col item-nav">
                                    <a class="nav-link item-tab" href="{{route('choose.package')}}">
                                        Pilihan Paket
                                    </a>
                                </li>
                                <!-- End Purchase Package -->

                                <!-- Account -->
                                <li class="col item-nav">
                                    <a class="nav-link item-tab" href="{{route('package.order')}}">
                                        Riwayat Transaksi Pembelian Paket
                                    </a>
                                </li>
                                <!-- End Account -->

                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-8">
                        <x-admin.validation-component></x-admin.validation-component>
                        @foreach ($packages as $package)
                        <div class="card" id="package_<?= $package->id; ?>">
                            <div class="card-body">
                                <div class="ckeckout-product-lists">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class=" ml-3 checkout-product-details">
                                                <h5 id="packagename">{{$package->name}}</h5>
                                                <input type="hidden" id="packageprice" value="<?= (int)$package->price; ?>">
                                                <input type="hidden" id="idPackage" value="<?= $package->id; ?>">
                                                <h5>Rp {{number_format($package->price)}} </h5>
                                                <p class="text-success">{{number_format($package->limit_day)}} / Hari</p>
                                                <p class="mb-0 mt-2">{{$package->description}} </p>
                                            </div>
                                        </div>
                                        <div class="checkout-amount-data text-center">
                                            <div class="checkout-button">
                                                <a href="#" onclick="choosePackage(<?= $package->id; ?>)" type="submit" class="btn btn-light d-block packagebutton">Pilih Paket</a>
                                                <!-- <a href="#" type="submit" class="btn btn-primary d-block mt-2"><i class="ri-heart-line mr-1"></i>Wishlist</a> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <form action="<?= route('package.order.store', $store->id); ?>" method="POST" class="card-body">
                                <p>Informasi Pembelian</p>
                                <div class="d-flex justify-content-between">
                                    <span>Nama Toko</span>
                                    <span>{{$store->name}}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Nama Paket</span>
                                    <span class="packagename"></span>
                                </div>
                                <hr>
                                <p><b>Ringkasan Harga</b></p>
                                <div class="d-flex justify-content-between">
                                    <span>Harga Paket</span>
                                    <span class="packageprice">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Pajak</span>
                                    <span class="text-success packagetax">{{(int)$settings->tax}}% ( Rp 0)</span>
                                </div>

                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="text-dark"><strong>Total</strong></span>
                                    <span class="text-dark packagetotal"><strong>Rp 0</strong></span>
                                </div>
                                <input type="hidden" name="package" value="" id="packageId">
                                <input type="hidden" id="taxrateId" value="<?= (int)$settings->tax; ?>">
                                <button type="submit" class="btn btn-primary d-block mt-1 next">Proses Pembelian</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    function choosePackage(id) {
        var packageCore = $("#package_" + id);
        var packagePricing = packageCore.find("#packageprice").val();
        var packageName = packageCore.find("#packagename").html();
        var packageId = packageCore.find("#idPackage").val();
        var taxrate     = parseInt($("#taxrateId").val())
        var taxtotal = taxrate > 0 ? taxrate / 100 * parseInt(packagePricing) : parseInt(packagePricing);
        var totalPrice = parseInt(packagePricing) + parseInt(taxtotal);

        var choosedPackage = $(".packagebutton");
        choosedPackage.html('Pilih Paket');
        choosedPackage.addClass('btn-light');
        choosedPackage.removeClass('btn-primary terpilih');

        packageCore.find(".packagebutton").removeClass('btn-light')
        packageCore.find('.packagebutton').addClass('btn-primary terpilih');
        packageCore.find('.packagebutton').html('Terpilih');

        $("#packageId").val(packageId);

        $(".packagename").html(packageName);
        $(".packageprice").html("Rp " + formatRupiah(packagePricing.toString()))
        $(".packagetax").html("11% ( Rp " + formatRupiah(taxtotal.toString()) + " )");
        $(".packagetotal").html("Rp " + formatRupiah(totalPrice.toString()))



    }

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^.\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi)

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? ',' : ''
            rupiah += separator + ribuan.join(',')
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah
        return prefix == undefined ? rupiah : rupiah ? rupiah : ''
    }
</script>
@endsection