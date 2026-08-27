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
                                    <form action="{{ route('attendance.today_report') }}" method="GET" class="row">
                                        <div class="col-sm-6 col-md-6 mb-3">
                                            <label class="control-label">{{__('hrm.choose_designation')}} </label>
                                            <div class="input-group">
                                                <select class="form-control select2" id="designation" name="designation">
                                                    <option value="">{{__('hrm.choose_designation')}} </option>
                                                    @foreach ($designation as $s)
                                                    <option value="{{ $s->id }}" @if (isset($_GET['designation'])) @if ($s->id==$_GET['designation']) selected @endif @endif>{{ $s->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6 mb-3">
                                            <label class="control-label">{{__('general.date')}}</label>
                                            <div class="input-group">
                                                <input type="date" name="date" id="date" placeholder="Tanggal" class="form-control" value="{{ old('date') }}">
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
                                        <th>{{__('attendance.status')}}</th>
                                        <th>{{__('general.store')}}</th>
                                        <th>{{__('attendance.entry')}}</th>
                                        <th>{{__('attendance.out')}}</th>
                                        <th>{{__('attendance.late')}}</th>
                                        <th>{{__('attendance.total')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $c)
                                    <tr>
                                        <td>#</td>
                                        <td>{{ $c->user->name }}</td>
                                        <td>
                                            @if($c->today_attendance($date) == 'yes')
                                            <span class="badge bg-primary"><i class="fa fa-check-circle"></i></span>
                                            @else
                                            <span class="badge bg-danger"><i class="fa fa-times"></i></span>
                                            @endif
                                        </td>
                                        <td>{{ $c->user->name }}</td>
                                        <td>{{ $c->today_checkint($date) }}</td>
                                        <td>{{ $c->today_checkout($date) }}</td>
                                        <td>{{ $c->today_late($date) }}</td>
                                        <td>{{ $c->today_work($date) }}</td>
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