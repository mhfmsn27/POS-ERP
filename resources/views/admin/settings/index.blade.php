@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/dropify/css/dropify.min.css') }}">
@endsection

<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="row">

            <div class="col-12 mb-3">
                <nav aria-label="breadcrumb mt-4">
                    <ol class="breadcrumb iq-bg-primary">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="ri-home-4-line mr-1 float-left"></i>Pengaturan</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Preferensi
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="iq-edit-list">
                            <ul class="iq-edit-profile d-flex nav nav-pills">
                                @if(auth()->user()->can('setting_view'))
                                <li class="col">
                                    <a class="nav-link active" data-toggle="pill" href="#transactionkey">
                                        Transaksi Key
                                    </a>
                                </li>
                                <li class="col">
                                    <a class="nav-link" data-toggle="pill" href="#hrmsett">
                                        Pengaturan HRM
                                    </a>
                                </li>
                                @endif
                                <li class="col">
                                    <a class="nav-link @if(!auth()->user()->can('setting_view')) active @endif" data-toggle="pill" href="#storesetting">
                                        Informasi Bisnis
                                    </a>
                                </li>
                                <li class="col">
                                    <a class="nav-link" data-toggle="pill" href="#notificationsetting">
                                        Pemberitahuan
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="iq-edit-list-data">
                    <div class="tab-content">
                        @if(auth()->user()->can('setting_view'))
                        
                        <div class="tab-pane fade" id="hrmsett" role="tabpanel">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="iq-header-title">
                                        <h4 class="card-title">Pengaturan Absensi dan Gaji Pegawai</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data" id="uHrm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-4 mb-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="system_name">{{__('settings.time_min')}}</label>
                                                    <div class="position-relative">
                                                        <input type="time" class="form-control" name="min_check_int" value="{{ old('min_check_int',$hrm->min_check_int ?? '') }}" required id="min_check_int">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4 mb-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="system_name">{{__('settings.time_max')}}</label>
                                                    <div class="position-relative">
                                                        <input type="time" class="form-control" name="max_check_int" value="{{ old('max_check_int',$hrm->max_check_int ?? '') }}" required id="max_check_int">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4 mb-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="system_name">{{__('settings.time_out')}}</label>
                                                    <div class="position-relative">
                                                        <input type="time" class="form-control" name="min_check_out" value="{{ old('min_check_out',$hrm->min_check_out ?? '') }}" required id="min_check_out">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="form-group has-icon-left">
                                                    <label for="system_name">{{__('settings.late_loss')}}</label>
                                                    <div class="position-relative">
                                                        <select class="form-control" name="attendance_in_late">
                                                            @if($hrm->attendance_in_late == 'yes')
                                                            <option value="yes">{{__('settings.connect')}}</option>
                                                            <option value="no">{{__('settings.no')}}</option>
                                                            @else
                                                            <option value="no">{{__('settings.no')}}</option>
                                                            <option value="yes">{{__('settings.connect')}}</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="form-group has-icon-left">
                                                    <label for="system_name">{{__('settings.allowance_to_attendance')}}</label>
                                                    <div class="position-relative">
                                                        <select class="form-control" name="attendance_to_salary">
                                                            @if($hrm->attendance_to_salary == 'yes')
                                                            <option value="yes">{{__('settings.connect')}}</option>
                                                            <option value="no">{{__('settings.no')}}</option>
                                                            @else
                                                            <option value="no">{{__('settings.no')}}</option>
                                                            <option value="yes">{{__('settings.connect')}}</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="form-group has-icon-left">
                                                    <label for="system_name">{{__('settings.deduction_to_attendance')}}</label>
                                                    <div class="position-relative">
                                                        <select class="form-control" name="attendance_to_cutting">
                                                            @if($hrm->attendance_to_cutting == 'yes')
                                                            <option value="yes">{{__('settings.connect')}}</option>
                                                            <option value="no">{{__('settings.no')}}</option>
                                                            @else
                                                            <option value="no">{{__('settings.no')}}</option>
                                                            <option value="yes">{{__('settings.connect')}}</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4 mt-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="system_name">{{__('settings.salary_tax')}} ( % )</label>
                                                    <div class="position-relative">
                                                        <input type="number" class="form-control" name="salary_tax" value="{{ old('salary_tax',$hrm->salary_tax ?? '') }}" required id="salary_tax">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 d-flex justify-content-end mt-4">
                                                <button class="btn btn-info me-1 mb-1">{{ __('save') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="tab-pane fade @if(!auth()->user()->can('setting_view')) active show @endif" id="storesetting" role="tabpanel">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="iq-header-title">
                                        <h4 class="card-title">Informasi Toko</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form method="POST" class="row" id="uStore" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-4 mb-4">
                                            <h6>{{__('store.choose_printer')}} </h6>
                                            <div class="form-group">
                                                <select class="form-control" name="printer_id" id="printer" required>
                                                    <option value="">{{__('store.choose_printer')}}</option>
                                                    @foreach($data['printer'] as $p)
                                                    <option value="{{$p->id}}" @if($p->id == old('printer_id',$store->printer_id)) selected @endif>{{$p->name}} - {{ $p->type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <h6>{{__('store.store_name')}}</h6>
                                            <div class="form-group">
                                                <input type="text" name="name" value="{{old('name',$store->name)}}" id="name" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <h6>{{__('general.email')}}</h6>
                                            <div class="form-group">
                                                <input type="email" name="email" value="{{old('email',$store->email)}}" id="email" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-4">
                                            <h6>{{__('general.phone')}}</h6>
                                            <div class="form-group">
                                                <input type="number" name="phone" value="{{old('phone',$store->phone)}}" id="phone" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <h6>{{__('store.zip_code')}}</h6>
                                            <div class="form-group">
                                                <input type="text" name="zip_code" value="{{old('zip_code',$store->zip_code)}}" id="zip_code" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <h6>Opsi Penggunaan Pajak</h6>
                                            <div class="form-group">
                                                <select class="form-control" name="tax_option" id="taxoption" required>
                                                    <option value="no" @if($store->tax_option == 'no') selected @endif>Tidak Gunakan Pajak</option>
                                                    <option value="active" @if($store->tax_option == 'active') selected @endif>Gunakan Pajak</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-4 <?= $store->tax_option != 'active' ? 'd-none' : ''; ?> taxrate_one">
                                            <h6>Pajak 1</h6>
                                            <div class="form-group">
                                                <select class="form-control" name="tax_one">
                                                    @foreach ($data['taxrate'] as $tax)
                                                    <option value="<?= $tax->taxrate; ?>" @if($tax->taxrate == $store->tax_one) selected @endif ><?= $tax->name; ?></option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-4 <?= $store->tax_option != 'active' ? 'd-none' : ''; ?> taxrate_two">
                                            <h6>Pajak 2</h6>
                                            <div class="form-group">
                                                <select class="form-control" name="tax_two">
                                                    @foreach ($data['taxrate'] as $tax)
                                                    <option value="<?= $tax->taxrate; ?>" @if($tax->taxrate == $store->tax_two) selected @endif><?= $tax->name; ?></option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-4 <?= $store->tax_option != 'active' ? 'd-none' : ''; ?> taxrate_tree">
                                            <h6>Pajak 3 </h6>
                                            <div class="form-group">
                                                <select class="form-control" name="tax_tree">
                                                    @foreach ($data['taxrate'] as $tax)
                                                    <option value="<?= $tax->taxrate; ?>" @if((float)preg_replace("/[^0-9\.]/", "." , $tax->taxrate) == $store->tax_tree) selected @endif><?= $tax->name; ?></option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <h6>Default Gudang</h6>
                                            <div class="form-group">
                                                <select class="form-control" name="warehouse_default_id">
                                                    <option value="">Gudang Utama</option>
                                                    @foreach ($data['warehouses'] as $warehouse)
                                                    <option value="<?= $warehouse->id; ?>" @if($warehouse->id == $store->warehouse_default_id) selected @endif ></option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>



                                        <div class="col-md-4 mb-4">
                                            <h6>Gunakan Shift Register</h6>
                                            <div class="form-group">
                                                <select class="form-control" name="shift_register" id="shift_register">
                                                    @if($store->shift_register == 'active')
                                                    <option value="active">Active</option>
                                                    <option value="no">Tidak</option>
                                                    @else
                                                    <option value="no">Tidak</option>
                                                    <option value="active">Active</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-4">
                                            <h6>Penggunaan Akutansi</h6>
                                            <div class="form-group">
                                                <input type="text" value="<?= $store->accountant_use == 'yes' ? 'Menggunakan Akutansi' : 'Tidak Menggunakan'; ?>" readonly class="form-control" required>
                                            </div>
                                        </div>


                                        <div class="col-md-12 mb-4">
                                            <h6>{{__('general.address')}}</h6>
                                            <div class="form-group">
                                                <textarea class="form-control" name="address" id="address" required>{{ old('address',$store->address) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-4">
                                            <h6>{{__('store.footer_text')}}</h6>
                                            <div class="form-group">
                                                <textarea class="form-control" name="footer_text" id="footer_text">{{ old('footer_text',$store->footer_text) }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-4">
                                            <h6>Logo Toko / Usaha </h6>
                                            <div class="form-group">
                                                <input class="dropify" type="file" id="logo" name="logo" data-default-file="{{ asset(old('logo',$store->logo ?? ''))}}">
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-end mt-4">
                                            <button class="btn btn-info mr-2">{{__('general.save')}}</button>
                                            <a class="btn btn-danger deletebutton" href="<?= route('store.delete', my_store()); ?>">Hapus Toko</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="notificationsetting" role="tabpanel">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="iq-header-title">
                                        <h4 class="card-title">Pengaturan Pemberitahuan</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{route('notification.store')}}" method="POST">
                                        @csrf
                                        <div class="row">
                                        <div class="col-lg-6 col-sm-12 mt-3">
                                                <label class="form-label">Opsi Device Whatsapp</label>
                                                <select class="form-control" name="type">
                                                    <option value="general"  @if(old('type',($settings ? $settings->type : '')) == 'general') selected @endif >Gunakan Opsi Bawaan</option>
                                                    <option value="personal" @if(old('type',($settings ? $settings->type : '')) == 'personal') selected @endif>Gunakan Device Sendiri</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6 col-sm-12 mt-3">
                                                <label class="form-label">Nomor Penerima Pesan</label> 
                                                <input type="number" name="phone" class="form-control" value="<?= $settings ? $settings->phone : ''; ?>">
                                            </div>
                                          
                                            <div class="col-lg-6 col-sm-12 mt-3">
                                                <label class="form-label">Add User</label>
                                                <select class="form-control" name="add">
                                                    <option value="">Pilih Template</option>
                                                    @foreach ($templates as $template)
                                                    <option value="<?= $template->id; ?>" @if(old('add',($settings ? $settings->user_add : '')) == $template->id) selected @endif ><?= $template->name; ?> </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                           
                                            <div class="col-lg-6 col-sm-12 mt-3">
                                                <label class="form-label">E-Commerce Order</label>
                                                <select class="form-control" name="ecommerce_order">
                                                    <option value="">Pilih Template</option>
                                                    @foreach ($templates as $template)
                                                    <option value="<?= $template->id; ?>" @if(old('ecommerce_order',($settings ? $settings->ecommerce_order : '')) == $template->id) selected @endif ><?= $template->name; ?> </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-6 col-sm-12 mt-3">
                                                <label class="form-label">E-Commerce Payment</label>
                                                <select class="form-control" name="ecommerce_payment">
                                                    <option value="">Pilih Template</option>
                                                    @foreach ($templates as $template)
                                                    <option value="<?= $template->id; ?>" @if(old('ecommerce_payment',($settings ? $settings->ecommerce_payment : '')) == $template->id) selected @endif ><?= $template->name; ?> </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-6 col-sm-12 mt-3">
                                                <label class="form-label">E-Commerce Shipping</label>
                                                <select class="form-control" name="ecommerce_shipping">
                                                    <option value="">Pilih Template</option>
                                                    @foreach ($templates as $template)
                                                    <option value="<?= $template->id; ?>" @if(old('ecommerce_shipping',($settings ? $settings->ecommerce_shipping : '')) == $template->id) selected @endif ><?= $template->name; ?> </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-6 col-sm-12 mt-3">
                                                <label class="form-label">E-Commerce Received Customer </label>
                                                <select class="form-control" name="ecommerce_received">
                                                    <option value="">Pilih Template</option>
                                                    @foreach ($templates as $template)
                                                    <option value="<?= $template->id; ?>" @if(old('ecommerce_received',($settings ? $settings->ecommerce_received : '')) == $template->id) selected @endif ><?= $template->name; ?> </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-6 col-sm-12 mt-3">
                                                <label class="form-label">Rma Add</label>
                                                <select class="form-control" name="rma_add">
                                                    <option value="">Pilih Template</option>
                                                    @foreach ($templates as $template)
                                                    <option value="<?= $template->id; ?>" @if(old('rma_add',($settings ? $settings->rma_add : '')) == $template->id) selected @endif ><?= $template->name; ?> </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-6 col-sm-12 mt-3">
                                                <label class="form-label">Rma Progress </label>
                                                <select class="form-control" name="rma_progress">
                                                    <option value="">Pilih Template</option>
                                                    @foreach ($templates as $template)
                                                    <option value="<?= $template->id; ?>" @if(old('rma_progress',($settings ? $settings->rma_progress : '')) == $template->id) selected @endif ><?= $template->name; ?> </option>
                                                    @endforeach
                                                </select>
                                            </div> 
                                            <div class="col-12 d-flex justify-content-end mt-4">
                                                <button class="btn btn-success" type="submit">
                                                    Simpan Perubahan
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
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
<script src="{{ asset('assets/vendors/dropify/js/dropify.min.js') }}"></script>
<script>
    $("#taxoption").on("change", function() {
        var value = $(this).val();
        if (value == 'active') {
            $(".taxrate_one").removeClass("d-none");
            $('.taxrate_two').removeClass('d-none');
            $('.taxrate_tree').removeClass('d-none');
        } else {
            $(".taxrate_one").addClass("d-none");
            $('.taxrate_two').addClass('d-none');
            $('.taxrate_tree').addClass('d-none');
        }
    });

    $(document).ready(function() {
        $('.dropify').dropify();
    });
</script>
@endsection