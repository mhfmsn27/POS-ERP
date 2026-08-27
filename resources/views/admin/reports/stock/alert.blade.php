@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="row">

            <div class="col-md-12 col-12">
                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="iq-header-title">
                            <div class="input-group">
                                <input type="text" class="form-control" id="name" placeholder="{{__('produk.name')}}" value="" placeholder="Cari Nama Produk">
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" id="btnAutoPo" onclick="generateAutoPo()">
                                <i class="ri-shopping-cart-2-line"></i> Buat Draf PO Otomatis
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="products_table">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Toko</th>
                                        <th>Sisa Stok</th>
                                        <th>Gambar</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.js') }}"></script>
<script>
    $(document).ready(function() {


        var product_table = $('#products_table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            aaSorting: [
                [1, 'asc']
            ],
            ajax: {
                "url": "{{route('stock.alert')}}",
                "data": function(d) {
                    d.name = $('#name').val();
                    d = datatable_poshub_callback(d);
                }
            },

            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'store',
                    name: 'store'
                },
                {
                    data: 'stock',
                    name: 'stock'
                },
                {
                    data: 'image',
                    name: 'image'
                }
            ],

        });

        $("body").on("keyup", "#name", function() {
            product_table.ajax.reload();
        });

    });

    function generateAutoPo() {
        if (!confirm('Apakah Anda ingin membuat Draf Pesanan Pembelian (PO) otomatis untuk semua produk yang stoknya menipis?')) return;

        const btn = document.getElementById('btnAutoPo');
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line spin"></i> Memproses...';

        fetch('{{ url("/api/inventory/stock-alerts/generate-auto-po") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-shopping-cart-2-line"></i> Buat Draf PO Otomatis';
            if (res.status) {
                alert('Sukses!\n' + res.message + '\nTotal Nilai Estimasi: ' + res.total_formatted);
            } else {
                alert(res.message || 'Terjadi kesalahan.');
            }
        })
        .catch(e => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-shopping-cart-2-line"></i> Buat Draf PO Otomatis';
            alert('Gagal menghubungi server.');
        });
    }
</script>
@endsection
@endsection