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
    <h6>Edit Alamat</h6>
    <a href="{{route('ecommerce.mobile.address.delete',$address->id)}}" class="right">
        <i class="fa fa-trash text-danger"></i>
    </a>
</div>
<x-admin.validation-component></x-admin.validation-component>
<div class="app-content">
    <div class="card">
        <form class="card-body">
            <fieldset class="mt-20 input-fill">
                <label>Nama Lengkap <span class="required">*</span></label>
                <input type="text" placeholder="Nama Lengkap" value="<?= $address->name; ?>" id="addressName" class="form-control">
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Nomor Telpon <span class="required">*</span></label>
                <input type="text" placeholder="Nomor Telpon" value="<?= $address->phone; ?>" id="phoneAddress" class="form-control">
            </fieldset>
            <fieldset class="mt-20">
                <label>Provinsi <span class="required">*</span></label>
                <select id='provinceData' required name='get_province' class='form-control choose_province'>
                    <option value='<?= $address->subdistrict->city->province->id ?? ''; ?>'><?= $address->subdistrict->city->province->name ?? ''; ?> </option>
                </select>
            </fieldset>
            <fieldset class="mt-20">
                <label>Kota / Kabupaten <span class="required">*</span></label>
                <input type="hidden" id="addressID" value="<?= $address->id; ?>">
                <select id='cityData' required name='get_city' class='form-control choose_city' style='width: 100%;'>
                    <option value='<?= $address->subdistrict->city->id ?? ''; ?>'><?= $address->subdistrict->city->name ?? ''; ?> </option>
                </select>
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Kecamatan <span class="required">*</span></label>
                <select id='districtData' required class='form-control choose_district' style='width: 100%;'>
                    <option value='<?= $address->subdistrict->id ?? ''; ?>'><?= $address->subdistrict->name ?? ''; ?></option>
                </select>
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Kode POS <span class="required">*</span></label>
                <input type="text" placeholder="Kode POS" value="<?= $address->postal_code; ?>" id="postalCode" class="form-control">
            </fieldset>
            <fieldset class="mt-20 input-fill">
                <label>Alamat Lengkap <span class="required">*</span></label>
                <textarea class="form-control h-100" id="fullAddress" required>
                    <?= $address->address; ?>
                </textarea>
            </fieldset>
            <fieldset class="mt-20 input-fill mb-20">
                <label>Jadikan Default Pengiriman<span class="required">*</span></label>
                <select class="form-control" id="defaultOption">
                    <option value="no" @if($address->default == 'no') selected @endif >Tidak</option>
                    <option value="yes" @if($address->default == 'yes') selected @endif>Iya</option>
                </select>
            </fieldset>
            <input type="hidden" value="<?= $address->long; ?>" id="long">
            <input type="hidden" value="<?= $address->lang; ?>" id="lang">


            <div class="mt-4">
                <div class=" mt-4" id="map"></div>
            </div>
        </form>
    </div> 
</div>
 
<div class="footer-fixed p-16">
    <button id="updateDataAddress" type="button" class="tf-btn primary">Tambahkan</button>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/select3/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/vendors/maps/leaflet.js') }}"></script>

<script>
    $(document).ready(function() {

        // use below if you want to specify the path for leaflet's images
        //L.Icon.Default.imagePath = '@Url.Content("~/Content/img/leaflet")';

        var curLocation = [0, 0];
        // use below if you have a model
        // var curLocation = [@Model.Location.lang, @Model.Location.long];

        if (curLocation[0] == 0 && curLocation[1] == 0) {
            console.log($("#lang").val())
            curLocation = [$("#lang").val(), $("#long").val()];
        }

        var map = L.map('map').setView(curLocation, 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© MDHDigital',
        }).addTo(map);
        current_accuracy = L.circle(curLocation, 15000).addTo(map);
        map.attributionControl.setPrefix(false);

        var marker = new L.marker(curLocation, {
            draggable: 'true'
        });



        marker.on('dragend', function(event) {
            var position = marker.getLatLng();
            marker.setLatLng(position, {
                draggable: 'true'
            }).bindPopup(position).update();
            $("#lang").val(position.lat);
            $("#long").val(position.lng).keyup();
            removeCircle();
            current_accuracy = L.circle(position, 15000);
            map.addLayer(current_accuracy);
        });

        $("#lang, #long").change(function() {
            var position = [parseInt($("#lang").val()), parseInt($("#long").val())];
            marker.setLatLng(position, {
                draggable: 'true'
            }).bindPopup(position).update();
            map.panTo(position);
        });

        function removeCircle() {
            map.removeLayer(current_accuracy);
        }

        map.addLayer(marker);

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

        setTimeout(function() {
            $('#cityData').select2({
                placeholder: 'Pilih Kota / Kabupaten...',
                ajax: {
                    url: domainpath + '/web/location/cities?province=' + $("#provinceData").val(),
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

            $('#districtData').select2({
                placeholder: 'Pilih Kecamatan...',
                ajax: {
                    url: domainpath + '/web/location/district?city=' + $("#cityData").val(),
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
        }, 100)
    });




    $("select[name='get_province']").on('change', function(e) {
        $('#cityData').val('')
        $('#districtData').val('')

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

    $('#updateDataAddress').on('click', function() {
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
                url: domain +
                    domainpath +
                    '/m-ecommerce/account/address/update/' +
                    $('#addressID').val(),
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
                        return false
                    } else {
                        $('.listDataAddress').html(
                            '<div class="col-12 actionButtonAddress"></div>',
                        )


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
</script>
<!-- <script src="{{asset('ecommerce/js/address.js')}}"></script> -->
<script>

</script>
@endsection