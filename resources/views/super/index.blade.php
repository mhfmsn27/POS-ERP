@extends('layouts.super')

@section('content')

<div class="main-content-body">
    <div class="row row-sm">
       
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="card overflow-hidden project-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="my-auto">
                            <svg enable-background="new 0 0 469.682 469.682" version="1.1" class="me-4 ht-60 wd-60 my-auto primary" viewBox="0 0 469.68 469.68" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                                <path d="m120.41 298.32h87.771c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449h-87.771c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449z" />
                                <path d="m291.77 319.22h-171.36c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h171.36c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z" />
                                <path d="m291.77 361.01h-171.36c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h171.36c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z" />
                                <path d="m420.29 387.14v-344.82c0-22.987-16.196-42.318-39.183-42.318h-224.65c-22.988 0-44.408 19.331-44.408 42.318v20.376h-18.286c-22.988 0-44.408 17.763-44.408 40.751v345.34c0.68 6.37 4.644 11.919 10.449 14.629 6.009 2.654 13.026 1.416 17.763-3.135l31.869-28.735 38.139 33.959c2.845 2.639 6.569 4.128 10.449 4.18 3.861-0.144 7.554-1.621 10.449-4.18l37.616-33.959 37.616 33.959c5.95 5.322 14.948 5.322 20.898 0l38.139-33.959 31.347 28.735c3.795 4.671 10.374 5.987 15.673 3.135 5.191-2.98 8.232-8.656 7.837-14.629v-74.188l6.269-4.702 31.869 28.735c2.947 2.811 6.901 4.318 10.971 4.18 1.806 0.163 3.62-0.2 5.224-1.045 5.493-2.735 8.793-8.511 8.361-14.629zm-83.591 50.155-24.555-24.033c-5.533-5.656-14.56-5.887-20.376-0.522l-38.139 33.959-37.094-33.959c-6.108-4.89-14.79-4.89-20.898 0l-37.616 33.959-38.139-33.959c-6.589-5.4-16.134-5.178-22.465 0.522l-27.167 24.033v-333.84c0-11.494 12.016-19.853 23.51-19.853h224.65c11.494 0 18.286 8.359 18.286 19.853v333.84zm62.693-61.649-26.122-24.033c-4.18-4.18-5.224-5.224-15.673-3.657v-244.51c1.157-21.321-15.19-39.542-36.51-40.699-0.89-0.048-1.782-0.066-2.673-0.052h-185.47v-20.376c0-11.494 12.016-21.42 23.51-21.42h224.65c11.494 0 18.286 9.927 18.286 21.42v333.32z" />
                                <path d="m232.21 104.49h-57.47c-11.542 0-20.898 9.356-20.898 20.898v104.49c0 11.542 9.356 20.898 20.898 20.898h57.469c11.542 0 20.898-9.356 20.898-20.898v-104.49c1e-3 -11.542-9.356-20.898-20.897-20.898zm0 123.3h-57.47v-13.584h57.469v13.584zm0-34.482h-57.47v-67.918h57.469v67.918z" />
                            </svg>
                        </div>
                        <div class="project-content">
                            <h6>Merchant Baru Bulan ini</h6>
                            <ul>
                                <li>
                                    <strong>Bulan ini</strong>
                                    <span>{{number_format($summary['merchant_monthly'])}}</span>
                                </li>

                                <li>
                                    <strong>Hari ini</strong>
                                    <span>{{number_format($summary['merchant_daily'])}}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <div class="card  overflow-hidden project-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="my-auto">
                            <svg enable-background="new 0 0 438.891 438.891" class="me-4 ht-60 wd-60 my-auto danger" version="1.1" viewBox="0 0 438.89 438.89" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                                <path d="m347.97 57.503h-39.706v-17.763c0-5.747-6.269-8.359-12.016-8.359h-30.824c-7.314-20.898-25.6-31.347-46.498-31.347-20.668-0.777-39.467 11.896-46.498 31.347h-30.302c-5.747 0-11.494 2.612-11.494 8.359v17.763h-39.707c-23.53 0.251-42.78 18.813-43.886 42.318v299.36c0 22.988 20.898 39.706 43.886 39.706h257.04c22.988 0 43.886-16.718 43.886-39.706v-299.36c-1.106-23.506-20.356-42.068-43.886-42.319zm-196.44-5.224h28.735c5.016-0.612 9.045-4.428 9.927-9.404 3.094-13.474 14.915-23.146 28.735-23.51 13.692 0.415 25.335 10.117 28.212 23.51 0.937 5.148 5.232 9.013 10.449 9.404h29.78v41.796h-135.84v-41.796zm219.43 346.91c0 11.494-11.494 18.808-22.988 18.808h-257.04c-11.494 0-22.988-7.314-22.988-18.808v-299.36c1.066-11.964 10.978-21.201 22.988-21.42h39.706v26.645c0.552 5.854 5.622 10.233 11.494 9.927h154.12c5.98 0.327 11.209-3.992 12.016-9.927v-26.646h39.706c12.009 0.22 21.922 9.456 22.988 21.42v299.36z" />
                                <path d="m179.22 233.57c-3.919-4.131-10.425-4.364-14.629-0.522l-33.437 31.869-14.106-14.629c-3.919-4.131-10.425-4.363-14.629-0.522-4.047 4.24-4.047 10.911 0 15.151l21.42 21.943c1.854 2.076 4.532 3.224 7.314 3.135 2.756-0.039 5.385-1.166 7.314-3.135l40.751-38.661c4.04-3.706 4.31-9.986 0.603-14.025-0.19-0.211-0.391-0.412-0.601-0.604z" />
                                <path d="m329.16 256.03h-120.16c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h120.16c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z" />
                                <path d="m179.22 149.98c-3.919-4.131-10.425-4.364-14.629-0.522l-33.437 31.869-14.106-14.629c-3.919-4.131-10.425-4.364-14.629-0.522-4.047 4.24-4.047 10.911 0 15.151l21.42 21.943c1.854 2.076 4.532 3.224 7.314 3.135 2.756-0.039 5.385-1.166 7.314-3.135l40.751-38.661c4.04-3.706 4.31-9.986 0.603-14.025-0.19-0.211-0.391-0.412-0.601-0.604z" />
                                <path d="m329.16 172.44h-120.16c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h120.16c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z" />
                                <path d="m179.22 317.16c-3.919-4.131-10.425-4.363-14.629-0.522l-33.437 31.869-14.106-14.629c-3.919-4.131-10.425-4.363-14.629-0.522-4.047 4.24-4.047 10.911 0 15.151l21.42 21.943c1.854 2.076 4.532 3.224 7.314 3.135 2.756-0.039 5.385-1.166 7.314-3.135l40.751-38.661c4.04-3.706 4.31-9.986 0.603-14.025-0.19-0.21-0.391-0.411-0.601-0.604z" />
                                <path d="m329.16 339.63h-120.16c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h120.16c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z" />
                            </svg>
                        </div>
                        <div class="project-content">
                            <h6>Transaksi Bulan ini</h6>
                            <ul>
                                <li>
                                    <strong>Pendapatan Bulan ini</strong>
                                    <span>Rp {{number_format($summary['monthly_payment'])}}</span>
                                </li>

                                <li>
                                    <strong>Total Pendapatan</strong>
                                    <span>Rp {{number_format($summary['total_payment'])}}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- row -->
    <div class="row row-sm ">
        <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12">
            <div class="card overflow-hidden">
                <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-10">Analisis Transaksi</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                    <p class="tx-12 text-muted mb-2">Analisis transaksi pembelian paket yang dilakukan oleh merchant di bawah naungan Anda</p>
                </div>
                <div class="card-body pd-y-7">

                    <canvas id="transactiondata" class="ht-300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
            <div class="card overflow-hidden">
                <div class="card-body pb-3">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-10">Harus Di Follow Up</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                    <p class="tx-12 text-muted mb-3">Berikut daftar 10 Toko yang akan habis masa paketnya dan perlu di follow Up</p>
                    <div class="table-responsive mb-0 projects-stat tx-14">
                        <table class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap  ">
                            <thead>
                                <tr>
                                    <th>Detail Merchant</th>
                                    <th>Masa Kadaluarsa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mustFollow as $must)
                                <tr>
                                    <td>
                                        <div class="project-names">
                                            {{$must->store->name ?? ''}} ( {{$must->merchant->name ?? ''}} )
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge bg-success">{{$must->last_expire_date}}</div>
                                    </td>
                                </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /row -->

    <!-- row -->
    <div class="row row-sm">

        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <div class="card overflow-hidden">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-10 mt-2">Merchant Baru</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                    <p class="tx-12 text-muted mb-0">Di Bawah ini Daftar Merchant baru namun belum mengambil paket langganan</p>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap  ">
                        <thead>
                            <tr>
                                <th>Merchant</th>
                                <th>Owner Phone</th>
                                <th>Ditambahkan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($merchantNotPackage as $notPackage)
                            <tr>
                                <td>
                                    <div class="project-names">
                                        {{$notPackage->business_name ?? ''}}
                                    </div>
                                </td>

                                <td>
                                    {{$notPackage->owner->phone ?? ''}}
                                </td>
                                <td>
                                    {{$notPackage->created_at->format("Y-m-d")}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-6 col-md-12 col-sm-12">
            <div class="card card-dashboard-events">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-10">Transaksi Belum Di Bayar</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                    <p class="tx-12 text-muted mb-0">Di Bawah ini merupakan transaksi yang telah di buat oleh Merchant namun belum melakukan pembayaran</p>
                </div>
                <div class="card-body p-3">
                    <table class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap  ">
                        <thead>
                            <tr>
                                <th>Merchant</th>
                                <th>Toko</th>
                                <th>Paket</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notPayment as $pay)
                            <tr>
                                <td>
                                    <div class="project-names">
                                        {{$pay->merchant->name ?? ''}}
                                    </div>
                                </td>

                                <td>
                                    {{$pay->store->name ?? ''}}
                                </td>
                                <td>
                                    {{$pay->package->group->name ?? ''}} {{$pay->package->name ?? ''}}
                                </td>
                                <td>
                                    {{number_format($pay->grand_total)}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /row -->

    <!-- row -->
    <div class="row row-sm ">
        <div class="col-md-12 col-xl-12">
            <div class="card overflow-hidden review-project">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-10">Daftar Merchant Baru</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                    <p class="tx-12 text-muted mb-3">Di bawah ini daftar merchant terbaru yang baru saja mengambil paket langganan</p>
                    <div class="table-responsive mb-0">
                        <table class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap table-striped ">
                            <thead>
                                <tr>
                                    <th>Nama Merchant</th>
                                    <th>Nama Toko</th>
                                    <th>Owner</th>
                                    <th>Paket Di Ambil</th>
                                    <th>Masa Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($business as $b)
                                <tr>
                                    <td>
                                        {{$b->merchant->name ?? ''}}
                                    </td>
                                    <td>
                                        {{$b->name}}
                                    </td>
                                    <td>
                                        {{$b->merchant->owner->name ?? ''}}
                                    </td>
                                    <td>
                                        {{$b->store_package->package->group->name ?? ''}} {{$b->store_package->package->name ?? ''}}
                                    </td>
                                    <td>
                                        {{$b->store_package->end_date ?? ''}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /row -->
</div>

@endsection

@section('scripts')
<script src="{{asset('admin/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<script>
    setTimeout(function() {
        project();
    }, 2000);


    function project() {

        $.ajax({
            url: '/administrator/administrator/analisis',
            type: 'GET',
            data: '',
            success: function(data) {

                var penjualan = data.analisis_penjualan.penjualan
                var tanggal = data.analisis_penjualan.tanggal


                var canvas = document.getElementById("transactiondata");
                var areaData = {
                    labels: tanggal,
                    datasets: [{
                        data: penjualan,
                        backgroundColor: [
                            'rgba(251, 73, 102,.5)'
                        ],
                        borderColor: [
                            'rgba(251, 73, 102,.8)'
                        ],
                        borderWidth: 2,
                        fill: 'origin',
                        label: "total budget"
                    }, ]
                };
                var areaOptions = {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        xAxes: [{
                            display: true,
                            ticks: {
                                display: true,
                                fontColor: "#8e9cad",
                                fontSize: "13",
                            },
                            gridLines: {
                                display: true,
                                drawBorder: false,
                                color: 'rgb(193, 184, 184,0.2)',
                                zeroLineColor: '#000'
                            }
                        }],
                        yAxes: [{
                            display: false,
                            ticks: {
                                display: true,
                                autoSkip: false,
                                maxRotation: 0,
                                stepSize: 15,
                                min: 0,
                                max: 250
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        enabled: true
                    },
                    elements: {
                        line: {
                            tension: .3
                        },
                        point: {
                            radius: 0
                        }
                    }
                }
                var salesChartCanvas = $("#transactiondata").get(0).getContext("2d");
                var salesChart = new Chart(salesChartCanvas, {
                    type: 'line',
                    data: areaData,
                    options: areaOptions
                });
            },

            cache: false,
            contentType: false,
            processData: false,
        })



    }
</script>
@endsection