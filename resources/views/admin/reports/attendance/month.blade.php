@extends('layouts.admin')
@section('content')
<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="accordion" id="accordionSearching">
                        <div class="accordion-item rounded">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-toggle="collapse" data-target="#searchdata" aria-expanded="false" aria-controls="searchdata">
                                    <i class="fa fa-search" style="margin-right: 5px;"></i> {{__('general.search')}}
                                </button>
                            </h2>
                            <div id="searchdata" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-parent="#accordionSearching">
                                <div class="accordion-body">
                                    <form action="{{ route('attendance.month_report') }}" method="GET" class="row">
                                        <div class="col-sm-6 col-md-4 mb-3">
                                            <label class="control-label">{{__('hrm.choose_designation')}}</label>
                                            <div class="input-group">
                                                <select class="form-control select2" id="designation" name="designation">
                                                    <option value="">{{__('hrm.choose_designation')}} </option>
                                                    @foreach ($designation as $s)
                                                    <option value="{{ $s->id }}" @if (isset($_GET['designation'])) @if ($s->id==$_GET['designation']) selected @endif @endif>{{ $s->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-4 mb-3">
                                            <label class="control-label">{{__('report.choose_year')}}</label>
                                            <div class="input-group">
                                                <select class="form-control select2" id="year" name="year">
                                                    <option value="">{{__('report.choose_year')}}</option>
                                                    @foreach ($year as $t => $tahun )
                                                    <option value="{{ $t }}" @if (isset($_GET['year'])) @if ($t==$_GET['year']) selected @endif @endif>{{ $tahun }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4 mb-3">
                                            <label class="control-label">{{__('report.choose_month')}}</label>
                                            <div class="input-group">
                                                <select class="form-control select2" id="month" name="month">
                                                    <option value="">{{__('report.choose_month')}} </option>
                                                    @foreach ($month as $m => $bulan )
                                                    <option value="{{ $m }}" @if (isset($_GET['month'])) @if ($m==$_GET['month']) selected @endif @endif>{{ $bulan }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" onclick="searchProduct()"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">{{$page}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('hrm.employee_name')}}</th>
                                        <?php
                                        $no = 1;
                                        for ($x = 0; $x < $day_; $x++) {
                                            echo '<th>' . $no++ . '</th>';
                                        } ?>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $c)
                                    <tr>
                                        <td>#</td>
                                        <td>{{ $c->user->name }}</td>
                                        <?php
                                        $no = 1;
                                        for ($x = 0; $x < $day_; $x++) {
                                            if ($c->month_attendance($year_, $month_, "0" . $no++) == 'yes') {
                                                echo '<td><i class="fa fa-check-circle" style="color:green;"></i></td>';
                                            } else {
                                                echo '<td><i class="fa fa-times" style="color:red;"></i></td>';
                                            }
                                        } ?>
                                        <td>{{ $c->month_total($year_,$month_) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection