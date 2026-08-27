@extends('layouts.register_mobile')
@section('content')
<div class="poshub-main">
    <div class="p-3 border-bottom">
        <a class="text-primary" href="{{route('index')}}"><i class="feather-chevron-left"></i> Kembali Ke Dashboard</a>
    </div>
    <div class="shift_register p-4">
        <h2 class="mb-3">Shift Register<br>Kasir / POS</h2>
        <h6 class="text-black-50">Masukkan Nominal Uang Cash yang Anda Bawa pada Saat buka / jaga Toko</h6>
        <div class="row">
            <div class="col-12 text-center">
                <img src="{{asset('assets/images/register_mobile.png')}}" style="width: 80%;">
            </div>
        </div>
        <form id="registershiftpos" action="{{route('shift.store')}}" method="POST">
            @csrf
            <div class="row my-4 mx-0">
                <div class="col pr-1 pl-0" id="shiftform">
                    <input type="text" name="cash_amount" placeholder="10,000" id="inputform" class="form-control form-control-lg shiftregisterinput">
                </div>
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Simpan dan Lanjutkan</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var paytotal = document.getElementById("inputform");
    paytotal.addEventListener("keyup", function(e) {
        paytotal.value = formatRupiah(this.value);
    });

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^.\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? "," : "";
            rupiah += separator + ribuan.join(",");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return prefix == undefined ? rupiah : rupiah ? rupiah : "";
    }
</script>
@endsection