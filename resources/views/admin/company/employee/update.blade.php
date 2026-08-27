@extends('layouts.admin')
@section('content')
<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="row">

            <div class="col-12 mb-3">
                <nav aria-label="breadcrumb mt-4">
                    <ol class="breadcrumb iq-bg-primary">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="ri-home-4-line mr-1 float-left"></i>Perusahaan</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Edit Pegawai
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
                            <a href="<?= route('employee.index'); ?>" class="btn btn-info">Daftar Pegawai</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row" id="uEmployee">
                            @csrf
                            <div class="col-md-6 mb-4">
                                <h6>{{__('hrm.choose_department')}}</h6>
                                <div class="form-group">
                                    <select class="form-control" name="department" id="department">
                                        <h6>{{__('hrm.choose_department')}}</h6>
                                        @foreach($data as $d)
                                        <option value="{{$d->id}}" @if($d->id == old('department',$employee->designation->department->id ?? '')) selected @endif>{{$d->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h6>{{__('hrm.choose_designation')}}</h6>
                                <div class="form-group">
                                    <select class="form-control" name="designation_id" id="designation_id">
                                        <option value="{{ $employee->designation_id }}">{{ $employee->designation->name ?? '' }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h6>{{__('hrm.choose_user')}}</h6>
                                <div class="form-group">
                                    <select class="form-control" name="user_id" id="user_id">
                                        @foreach($user as $u)
                                        <option value="{{$u->id}}" @if($u->id == old('user_id',$employee->user_id)) selected @endif>{{$u->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h6>{{__('general.phone')}}</h6>
                                <div class="form-group">
                                    <input type="number" name="phone" value="{{old('phone',$employee->phone)}}" id="phone" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6>{{__('hrm.hbd')}}</h6>
                                <div class="form-group">
                                    <input type="date" name="date_birth" value="{{old('date_birth',$employee->date_birth)}}" id="date_birth" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6>{{__('hrm.salary_amount')}}</h6>
                                <div class="form-group">
                                    <input type="text" name="salary" value="{{old('salary',number_format($employee->salary))}}" id="salary" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12 mb-4">
                                <h6>{{__('general.address')}}</h6>
                                <div class="form-group">
                                    <textarea class="form-control" name="address" id="address">{{ old('address',$employee->address) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4">
                                <h6>{{__('hrm.about')}}</h6>
                                <div class="form-group">
                                    <input type="hidden" name="id" value="{{ $employee->id }}">
                                    <textarea class="form-control" name="about" id="about">{{ old('about',$employee->about) }}</textarea>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button class="btn btn-info">{{__('general.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        $("select[name='department']").change(function() {
            var url = domainpath + "/pos-admin/hrm/get-designation/" + $(this).val();
            $("select[name='designation_id']").load(url);
            return false;
        });

        $("#amount").on("keyup", function() {
            var amount = $("#amount").val();
            $("#amount").val(formatRupiah(amount.toString()))
        });

        $("#salary").on("keyup", function() {
            var salary = $("#salary").val();
            $("#salary").val(formatRupiah(salary.toString()));
        });
    });
</script>
@endsection

@endsection