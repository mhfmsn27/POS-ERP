@extends('layouts.admin')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/vendors/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('ecommerce/css/tab.css')}}">
@endsection
<div class="content-page">
      <div class="container-fluid">

            <div class="row">

                  <!-- Component -->
                  <x-ecommerce-tab-setting-component></x-ecommerce-tab-setting-component>
                  <x-admin.validation-component></x-admin.validation-component>
                  <!-- End Component -->

                  <div class="col-md-12 col-12">
                        <div class="card card-block card-stretch card-height">
                              <div class="card-header d-flex justify-content-between">
                                    <div class="iq-header-title">
                                          <h4>Data Kecamatan</h4>
                                    </div>
                              </div>
                              <div class="card-body">
                                    <div class="table-responsive">
                                          <table class="table table-bordered" id="provinceContent">
                                                <thead>
                                                      <tr>
                                                            <th>Nama Kecamatan</th>
                                                            <th>Nama Kota</th>
                                                            <th>Tipe</th>
                                                            <th>Nama Provinsi</th>
                                                            <th>Status</th>
                                                            <th width="110px"><span class="fa fa-cogs"></span></th>
                                                      </tr>
                                                </thead>
                                          </table>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</div>

@section('scripts')
<script src="{{ asset('assets/vendors/select3/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.js') }}"></script>
<script>
      $(document).ready(function() {
            const sell_table = $('#provinceContent').DataTable({
                  processing: true,
                  serverSide: true,
                  aaSorting: [
                        [3, 'asc']
                  ],
                  ajax: {
                        "url": domain + domainpath + '/pos-admin/ecommerce/settings/district',
                        "data": function(d) {}
                  },
                  columnDefs: [{
                        targets: [3],
                        orderable: true,
                        searchable: false,
                  }, ],
                  columns: [{
                              data: 'name',
                              name: 'name'
                        },
                        {
                              data: 'city_name',
                              name: 'city_name'
                        },
                        {
                              data: 'city_type',
                              name: 'city_type'
                        },
                        {
                              data: 'province_name',
                              name: 'province_name'
                        },
                        {
                              data: 'my_status',
                              name: 'my_status'
                        },
                        {
                              data: 'action',
                              name: 'action'
                        }
                  ],

            });

      });
</script>
@endsection
@endsection