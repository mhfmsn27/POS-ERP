@extends('layouts.super') 

@section('content')
<div class="container-fluid"> 
    <div class="row row-sm">  
        <div class="col-12">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="mb-4 main-content-label">Pengaturan Notifikasi</div>
                    <x-admin.validation-component></x-admin.validation-component>
                    <form class="form-horizontal" action="{{route('admin.notification.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                       
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">Nomor Penerima Pesan</label>
                                    <input type="hidden" name="type" value="general"/>
                                    <input type="number" name="phone" class="form-control" value="<?=$settings ? $settings->phone : '';?>">
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">OTP Hapus Toko</label>
                                    <select class="form-control" name="delete_store">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('delete_store',($settings ? $settings->delete_store : '')) == $template->id) selected @endif><?=$template->name;?></option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">User Registration</label>
                                    <select class="form-control" name="register">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('register',($settings ? $settings->user_register : '')) == $template->id) selected @endif><?=$template->name;?></option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">Add User</label>
                                    <select class="form-control" name="add">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('add',($settings ? $settings->user_add : '')) == $template->id) selected @endif ><?=$template->name;?> </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">Add Store</label>
                                    <select class="form-control" name="add_store">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('add_store',($settings ? $settings->add_store : '')) == $template->id) selected @endif ><?=$template->name;?> </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">E-Commerce Order</label>
                                    <select class="form-control" name="ecommerce_order">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('ecommerce_order',($settings ? $settings->ecommerce_order : '')) == $template->id) selected @endif ><?=$template->name;?> </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">E-Commerce Payment</label>
                                    <select class="form-control" name="ecommerce_payment">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('ecommerce_payment',($settings ? $settings->ecommerce_payment : '')) == $template->id) selected @endif ><?=$template->name;?> </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">E-Commerce Shipping</label>
                                    <select class="form-control" name="ecommerce_shipping">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('ecommerce_shipping',($settings ? $settings->ecommerce_shipping : '')) == $template->id) selected @endif ><?=$template->name;?> </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">E-Commerce Received Customer </label>
                                    <select class="form-control" name="ecommerce_received">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('ecommerce_received',($settings ? $settings->ecommerce_received : '')) == $template->id) selected @endif ><?=$template->name;?> </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">Rma Add</label>
                                    <select class="form-control" name="rma_add">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('rma_add',($settings ? $settings->rma_add : '')) == $template->id) selected @endif ><?=$template->name;?> </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">Rma Progress </label>
                                    <select class="form-control" name="rma_progress">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('rma_progress',($settings ? $settings->rma_progress : '')) == $template->id) selected @endif ><?=$template->name;?> </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">Choose Package Store</label>
                                    <select class="form-control" name="package">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('package',($settings ? $settings->package_buy : '')) == $template->id) selected @endif ><?=$template->name;?> </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12 mt-3">
                                    <label class="form-label">Package Payment</label>
                                    <select class="form-control" name="package_payment">
                                        <option value="">Pilih Template</option>
                                        @foreach ($templates as $template)
                                            <option value="<?=$template->id;?>" @if(old('package_payment',($settings ? $settings->package_payment : '')) == $template->id) selected @endif ><?=$template->name;?> </option>
                                        @endforeach
                                    </select>
                                </div> 
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button class="btn btn-success" type="submit">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
 