@extends('layouts.super')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-header d-block d-flex justify-content-between">
                <div>
                    <div class="card-title mb-2">Edit Paket Layanan</div>
                    <p class="mb-1">Anda dapat memperbaharui data paket layanan di bawah ini</p>
                </div>
                <div>
                    <a class="btn btn-info" href="{{route('admin.package.index')}}">
                        Daftar Paket
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card mg-b-20">
            <div class="card-body">
                <div class="mb-4 main-content-label">Informasi Umum Layanan</div>
                <x-admin.validation-component></x-admin.validation-component>
                <form class="form-horizontal" action="{{route('admin.package.edit',$package->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3"> <label class="form-label">Nama Paket</label> </div>
                            <div class="col-md-9"> <input type="text" required name="name" class="form-control" value="<?= $package->name; ?>" placeholder="Masukkan Nama Paket"> </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3"> <label class="form-label">Harga Layanan</label> </div>
                            <div class="col-md-9"> <input type="number" required name="price" class="form-control" value="<?= (int)$package->price; ?>" placeholder="Masukkan Harga Layanan"> </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3"> <label class="form-label">Limit Hari / Expire</label> </div>
                            <div class="col-md-9"> <input type="number" name="limit_day" required class="form-control" value="<?= (int)$package->limit_day; ?>" placeholder="Masukkan Limit Hari"> </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3"> <label class="form-label">Deskripsi Singkat ( Opsional ) </label> </div>
                            <div class="col-md-9">
                                <textarea class="form-control" name="description"><?= $package->description; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-4 main-content-label">Informasi Detail Item Paket</div>
                        <div class="col-12 d-flex justify-content-start">
                            <button class="btn btn-info btn-sm" type="button" id="addItem">
                                Tambah Item List
                            </button>
                        </div>
                        <div class="col-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($package->details as $detail)
                                    <tr id="detailItem">
                                        <td>
                                            <input class="form-control" name="detail[]" placeholder="Masukkan Detail Item" value="<?= $detail->name; ?>" required>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-danger deleteItem" type="button">
                                                <i class="fe fe-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-success" type="submit">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div> <!-- /Col --> <!-- Col -->
</div>

@section('scripts')
<script>
    $("#addItem").on("click", function() {
        var newItem = `<tr id="detailItem">
                            <td>
                                <input class="form-control" name="detail[]" placeholder="Masukkan Detail Item" required>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-danger deleteItem" type="button">
                                    <i class="fe fe-trash"></i>
                                </button>
                            </td>
                        </tr>`;

        $("#detailItem").after(newItem);
    });

    $("body").on("click", ".deleteItem", function() {
        $(this).parents("#detailItem").remove();
    });
</script>
@endsection

@endsection