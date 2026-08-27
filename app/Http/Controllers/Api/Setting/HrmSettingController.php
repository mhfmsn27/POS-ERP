<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\HrmSettingRequest;
use App\Models\Admin\SettingsHrm;
use Illuminate\Support\Facades\Gate;

class HrmSettingController extends Controller
{

    public function data()
    {
        return SettingsHrm::first([
            'id',
            'min_check_out',
            'max_check_int',
            'min_check_int',
            'attendance_to_salary',
            'attendance_in_late',
            'attendance_to_cutting',
            'salary_tax'
        ]);
    }

    public function index()
    {
        $settings   = $this->data();

        return response()->json([
            'min_check_out'             => $settings->min_check_out ?? '17:00',
            'max_check_int'             => $settings->max_check_int ?? '08:00',
            'min_check_int'             => $settings->min_check_int ?? '07:00',
            'attendance_to_salary'      => $settings->attendance_to_salary,
            'attendance_in_late'        => $settings->attendance_in_late,
            'attendance_to_cutting'     => $settings->attendance_to_cutting,
            'salary_tax'                => $settings->salary_tax ?? 0
        ]);
    }

    public function store(HrmSettingRequest $request)
    {

        abort_if(Gate::denies('hrm'), 403);

        $settings   = $this->data();

        $settings->update([
            'min_check_out'             => $request->min_check_out,
            'max_check_int'             => $request->max_check_int,
            'min_check_int'             => $request->min_check_int,
            'attendance_to_salary'      => $request->attendance_to_salary,
            'attendance_in_late'        => $request->attendance_in_late,
            'attendance_to_cutting'     => $request->attendance_to_cutting,
            'salary_tax'                => $request->salary_tax
        ]);

        return response()->json([
            'status'        => true,
            'message'       => 'Perubahan berhasil di simpan',
        ]);

    }
}
