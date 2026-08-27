@extends('ecommerce::layouts.mobile')

@section('styles')
<link rel="stylesheet" href="{{ asset('ecommerce/css/plugins/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendors/maps/leaflet.css') }}" />

<style>
    #map {
        height: 30vh;
    }

    #accuracyButton {
        position: absolute;
        top: 10vh;
        left: 80%;
        width: 50px;
        z-index: 1000;
        background-color: #e55223;
        color: white;
    }
</style>
@endsection


@section('content')
<div class="header fixed-top line4-bt">
    <div class="left">
        <a href="{{ route('ecommerce.mobile.address') }}" class="icon back-btn"><i class="icon-left-btn"></i></a>
    </div>
    <h6>Tambah Alamat</h6>
</div>
<div class="app-content">
    <div class="card">
        <form class="card-body formaddress">
            <fieldset class="mt-20 input-fill">
                <label>Nama Lengkap <span class="required">*</span></label>
                <input type="text" placeholder="Nama Lengkap" id="addressName" class="form-control">
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Nomor Telpon <span class="required">*</span></label>
                <input type="text" placeholder="Nomor Telpon" id="phoneAddress" class="form-control">
            </fieldset>
            <fieldset class="mt-20">
                <label>Provinsi <span class="required">*</span></label>
                <select id='provinceData' required name='get_province' class='form-control choose_province'>
                    <option value=''>Pilih Provinsi </option>
                </select>
            </fieldset>
            <fieldset class="mt-20">
                <label>Kota / Kabupaten <span class="required">*</span></label>
                <select id='cityData' required name='get_city' class='form-control choose_city' style='width: 100%;'>
                    <option value=''>Pilih Kota / Kabupaten </option>
                </select>
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Kecamatan <span class="required">*</span></label>
                <select id='districtData' required class='form-control choose_district' style='width: 100%;'>
                    <option value=''>Pilih Kecamatan </option>
                </select>
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Kode POS <span class="required">*</span></label>
                <input type="text" placeholder="Kode POS" id="postalCode" class="form-control">
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Alamat Lengkap <span class="required">*</span></label>
                <textarea class="form-control h-100" id="fullAddress" required></textarea>
            </fieldset>
            <fieldset class="mt-20 input-fill mb-20">
                <label>Jadikan Default Pengiriman<span class="required">*</span></label>
                <select class="form-control" id="defaultOption">
                    <option value="no">Tidak</option>
                    <option value="yes">Iya</option>
                </select>
            </fieldset>
            <input type="hidden" id="long">
            <input type="hidden" id="lang">

            <div class="mt-4">
                <div class=" mt-4" id="map"></div>
            </div>
            
        </form>

    </div>
</div>

<div class="footer-fixed p-16">
    <button id="createDataAddress" type="button" class="tf-btn primary">Tambahkan</button>
    <button type="button" onclick="closeMap()" class="tf-btn primary d-none button-lokasi">Konfirmasi Lokasi</button>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/select3/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/vendors/maps/leaflet.js') }}"></script>
<script src="{{ asset('assets/vendors/maps/store_create.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.choose_province').select2();
        $('.choose_city').select2();
        $('.choose_district').select2();

        $('#provinceData').select2({
            placeholder: 'Pilih Provinsi...',
            ajax: {
                url: domainpath + '/web/location/provinces',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id,
                            }
                        }),
                    }
                },
                cache: false,
            },
        })
    });

    $("select[name='get_province']").on('change', function(e) {
        $('#cityData').val('')
        $('#districtData').val('')

        console.log("ini ke trigger cuk", $(this).val())
        $('#cityData').select2({
            placeholder: 'Pilih Kota / Kabupaten...',
            ajax: {
                url: domainpath + '/web/location/cities?province=' + $(this).val(),
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id,
                            }
                        }),
                    }
                },
                cache: false,
            },
        })
    })

    $("select[name='get_city']").on('change', function(e) {
        $('#districtData').val('')

        $('#districtData').select2({
            placeholder: 'Pilih Kecamatan...',
            ajax: {
                url: domainpath + '/web/location/district?city=' + $(this).val(),
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id,
                            }
                        }),
                    }
                },
                cache: false,
            },
        })
    })

    $('#createDataAddress').on('click', function() {
        var sendData = {
            name: $('#addressName').val(),
            sub_district_id: $('#districtData').val(),
            address: $('#fullAddress').val(),
            postal_code: $('#postalCode').val(),
            phone: $('#phoneAddress').val(),
            default: $('#defaultOption').val(),
            long: $("#long").val(),
            lang: $("#lang").val()
        }

        setTimeout(function() {
            $.ajax({
                url: domain + domainpath + '/m-ecommerce/account/address/store',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    timeout: 0,
                },
                data: JSON.stringify(sendData),
                success: function(data, json, errorThrown) {
                    if (data.status == false) {
                        Swal.fire({
                            title: 'Error',
                            html: data.message,
                            width: 'auto',
                            confirmButtonText: 'Ok Saya Mengerti',
                            showCancelButton: false,
                        })
                    } else {
                        $('.listDataAddress').html(
                            '<div class="col-12 actionButtonAddress"></div>',
                        )

                        // getAddress()
                        refreshData()
                        // listAddress()

                        toastr.success(data.message, 'Berhasil', {
                            timeOut: 5e3,
                            closeButton: !0,
                            debug: !1,
                            newestOnTop: !0,
                            progressBar: !0,
                            positionClass: 'toast-top-right',
                            preventDuplicates: !0,
                            onclick: null,
                            showDuration: '100',
                            hideDuration: '1000',
                            extendedTimeOut: '1000',
                            showEasing: 'swing',
                            hideEasing: 'linear',
                            showMethod: 'fadeIn',
                            hideMethod: 'fadeOut',
                            tapToDismiss: !1,
                        })
                    }
                },

                cache: false,
                contentType: false,
                processData: false,
            })
        }, 130)
    })

    function refreshData() {
        // $('#addressID').val('')
        $('#addressName').val('')
        $('#provinceData').val('')
        $('#cityData').val('')
        $('#districtData').val('')
        $('#postalCode').val('')
        $('#phoneAddress').val('')
        $('#fullAddress').val('')
        // $('#updateDataAddress').addClass('d-none')
        // $('#createDataAddress').removeClass('d-none')

        $("select[name='get_province']").trigger('change')
        $("select[name='get_city']").trigger('change')
    }

    function showMap() {
        $(".map-lokasi").removeClass("d-none");
        $(".button-lokasi").removeClass("d-none");
        $(".formaddress").addClass("d-none");
        $("#createDataAddress").addClass("d-none")
    }

    function closeMap() {
        $(".map-lokasi").addClass("d-none");
        $(".button-lokasi").addClass("d-none");
        $(".formaddress").removeClass("d-none");
        $("#createDataAddress").removeClass("d-none")
    }
</script>
<!-- <script src="{{asset('ecommerce/js/address.js')}}"></script> -->
<script>

</script>
@endsection