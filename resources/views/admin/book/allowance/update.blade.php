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
                            <a href="#"><i class="ri-home-4-line mr-1 float-left"></i>Buku Besar</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Tunjangan
                        </li>
                    </ol>
                </nav>
            </div>


            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">{{$page}}</h4>
                        </div>
                        <div>
                            <a href="{{route('allowance.index')}}" class="btn btn-info">Daftar Tunjangan</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="uAllowance" method="POST" class="form form-horizontal">
                            @csrf
                            <div class="form-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{ __('hrm.allowance_name') }} *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="name" value="{{ old('name',$allowance->name) }}" id="name" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('hrm.choose_department')}} ( {{__('general.optional')}} )</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select class="form-control" name="department">
                                            @if($allowance->designation_id != 0)
                                            <option value="{{ $allowance->designation->department_id }}">{{ $allowance->designation->department->name }}</option>
                                            @else
                                            <option value="">{{__('hrm.all_employee')}}</option>
                                            @endif
                                            @foreach ($department as $d)
                                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('hrm.choose_designation')}} ( {{__('general.optional')}} )</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="hidden" name="id" value="{{ $allowance->id }}">
                                        <select class="form-control" name="designation_id">
                                            <option value="{{ $allowance->designation_id }}">{{ $allowance->designation->name ?? __('hrm.all_employee') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('hrm.choose_circle')}} *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <select class="form-control" name="priode">
                                            @foreach ($priode as $p => $i)
                                            <option value="{{ $p }}" @if ($p==old('priode',$allowance->priode)) selected @endif>{{ $i }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>{{__('hrm.amount_allowance')}} *</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" name="amount" value="{{ old('amount',number_format($allowance->amount)) }}" id="amount" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-12 d-flex justify-content-end">
                                        <button class="btn btn-info me-1 mb-1">{{ __('general.save') }}</button>
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

@section('scripts')
<script src="{{ asset('assets/vendors/dropify/js/dropify.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("select[name='department']").change(function() {
            var url = domainpath + "/pos-admin/buku-besar/allowances/get-designation/" + $(this).val();
            $("select[name='designation_id']").load(url);
            return false;
        });

        $("#amount").on("keyup", function() {
            var amount = $("#amount").val();
            $("#amount").val(formatRupiah(amount.toString()))
        });
    });
</script>
@endsection
@endsection