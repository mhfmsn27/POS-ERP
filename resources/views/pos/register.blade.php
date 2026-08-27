@extends('layouts.register')

@section('content')
<div class="row">
    <div class="col-12 p-1 m-1 ">
        <div class="card register-card">
            <div class="card-header header-modal">
                <h4 class="text-white">Shift Register POS</h4>
            </div>
            <div class="card-body">
                <form id="registershiftpos" action="{{route('shift.store')}}" class="text-center" method="POST">
                    @csrf
                    <div class="form-group formshift">
                        <h4>Cash di Tangan* : </h4>
                        <div class="input-group" id="shiftform">
                            <input type="text" class="form-control " placeholder="10,000" required name="cash_amount" id="shiftregisterinput">
                            <button class="icon-shift" type="submit">
                                <i class="fas fa-arrow-alt-circle-right text-center"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var paytotal = document.getElementById("shiftregisterinput");
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